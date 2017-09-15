<?php
	/* Copyright (c) by Hugo Leisink <hugo@leisink.net>
	 * Licensed under the RAVIB license.
	 */

	class rapport_controller extends process_controller {
		private function draw_risk_matrix($pdf, $threats, $relevant) {
			$matrix = array();
			foreach ($threats as $threat) {
				if (($threat["chance"] === null) || ($threat["impact"] === null)) {
					continue;
				} else if ($relevant == ($threat["handle"] == $this->model->threat_handle_labels[THREAT_ACCEPT])) {
					continue;
				}

				if (is_array($matrix[$threat["chance"] - 1]) == false) {
					$matrix[$threat["chance"] - 1] = array_fill(0, count($this->model->risk_matrix_impact), "");
				}

				$matrix[$threat["chance"] - 1][$threat["impact"] - 1]++;
			}

			$pdf->SetFont("helvetica", "", 10);

			$cell_width = 29;
			$cell_height = 8;

			$pdf->Cell($cell_width, $cell_height);
			$pdf->Cell(150, $cell_height, "impact", 0, 0, "C");
			$pdf->Ln();
			$pdf->Cell($cell_width, $cell_height, "kans");
			foreach ($this->model->risk_matrix_impact as $impact) {
				$pdf->Cell($cell_width, $cell_height, $impact, 1, 0, "C");
			}
			$pdf->Ln();
			$max_y = count($this->model->risk_matrix_impact) - 1;
			foreach (array_reverse($this->model->risk_matrix_chance) as $y => $chance) {
				$pdf->Cell($cell_width, $cell_height, $chance, 1);
				foreach ($this->model->risk_matrix_impact as $x => $impact) {
					$risk = $this->model->risk_matrix_labels[$this->model->risk_matrix[$max_y - $y][$x]];
					$pdf->SetColor($risk);
					$pdf->Cell($cell_width, $cell_height, $matrix[$max_y - $y][$x], 1, 0, "C", true);
				}
				$pdf->Ln();
			}

			$pdf->Ln(8);
		}

		private function draw_bia_threat_matrix($pdf, $case_id, $bia, $threats_raw) {
			if (($bia_threat = $this->model->get_bia_threats($case_id)) == false) {
				return;
			}

			$threats = array();
			foreach ($threats_raw as $threat) {
				$threats[(int)$threat["id"]] = $threat;
			}
			unset($threats_raw);

			$matrix = array();
			foreach ($bia as $nr => $item) {
				$matrix[$nr] = array_fill(0, count($this->model->risk_matrix_labels), "");
				foreach ($bia_threat as $bt) {
					if ($bt["bia_id"] != $item["id"]) {
						continue;
					}

					if ($threats[$bt["threat_id"]]["handle"] == $this->model->threat_handle_labels[THREAT_ACCEPT]) {
						continue;
					}

					$change = $threats[$bt["threat_id"]]["chance"] - 1;
					$impact = $threats[$bt["threat_id"]]["impact"] - 1;
					$risk = $this->model->risk_matrix[$change][$impact];

					if ($risk !== null) {
						$matrix[$nr][$risk]++;
					}
				}
			}

			if (count($matrix) == 0) {
				return;
			}

			if (count($bia) > 10) {
				$pdf->AddPage();
			} else {
				$pdf->Ln(15);
			}

			$pdf->Write(5, "Onderstaand overzicht geeft per informatiesysteem het aantal dreigingen en de hoogte ervan aan.");
			$pdf->Ln(10);

			$pdf->Cell(75, 6, "", 0, 0);
			foreach ($this->model->risk_matrix_labels as $label) {
				$pdf->Cell(26, 6, $label, 0, 0, "C");
			}
			$pdf->Ln(6);

			foreach ($bia as $nr => $item) {
				$pdf->Cell(75, 6, ($nr + 1).": ".$item["item"], 0, 0);
				foreach ($matrix[$nr] as $level => $risk) {
					$label = $this->model->risk_matrix_labels[$level];
					$pdf->SetColor($label);
					$pdf->Cell(26, 6, $risk, 1, 0, "C", true);
				}
				$pdf->Ln(6);
			}
		}

		private function sort_measures($measure_a, $measure_b) {
			if (($measure_a["risk"] == "") && ($measure_b["risk"] != "")) {
				return 1;
			} else if (($measure_a["risk"] != "") && ($measure_b["risk"] == "")) {
				return -1;
			}

			if (is_false($measure_a["relevant"]) && is_true($measure_b["relevant"])) {
				return 1;
			} else if (is_true($measure_a["relevant"]) && is_false($measure_b["relevant"])) {
				return -1;
			}

			if ($measure_a["risk_value"] < $measure_b["risk_value"]) {
				return 1;
			} else if ($measure_a["risk_value"] > $measure_b["risk_value"]) {
				return -1;
			}

			if ($measure_a["asset_value"] < $measure_b["asset_value"]) {
				return 1;
			} else if ($measure_a["asset_value"] > $measure_b["asset_value"]) {
				return -1;
			}

			return version_compare($measure_a["number"], $measure_b["number"]);
		}

		private function sort_threats($threat_a, $threat_b) {
			if (($threat_a["relevant"] == false) && $threat_b["relevant"]) {
				return 1;
			} else if ($threat_a["relevant"] && ($threat_b["relevant"] == false)) {
				return -1;
			}

			if ($threat_a["risk_value"] < $threat_b["risk_value"]) {
				return 1;
			} else if ($threat_a["risk_value"] > $threat_b["risk_value"]) {
				return -1;
			}

			$systems_a = is_array($threat_a["systems"]) ? count($threat_a["systems"]) : 0;
			$systems_b = is_array($threat_b["systems"]) ? count($threat_b["systems"]) : 0;
			if ($systems_a < $systems_b) {
				return 1;
			} elseif ($systems_a > $systems_b) {
				return -1;
			}

			if ($threat_a["number"] > $threat_b["number"]) {
				return 1;
			} else if ($threat_a["number"] < $threat_b["number"]) {
				return -1;
			}

			return 0;
		}

		private function valid_url($url) {
			list($protocol,, $hostname, $path) = explode("/", $url, 4);

			switch ($protocol) {
				case "http:": $http = new HTTP($hostname); break;
				case "https:": $http = new HTTPS($hostname); break;
				default: return false;
			}

			if (($result = $http->HEAD("/".$path)) == false) {
				return false;
			}

			if ($result["status"] != 200) {
				return false;
			}

			return true;
		}

		private function generate_report($case_id) {
			if (($bia = $this->model->get_bia($case_id)) === false) {
				return;
			}

			if (($threats = $this->model->get_threats($case_id)) === false) {
				return;
			}

			if (($measures = $this->model->get_measures($case_id)) === false) {
				return;
			}

			$ra_date = date_string("j F Y", strtotime($this->case["date"]));
			$ra_standard = $this->model->get_iso_standard($this->case["iso_standard_id"]);

			$this->output->disable();
			$pdf = new PDF_report($this->case["title"]);
			$pdf->SetTitle($this->case["name"]);
			$pdf->SetAuthor($this->user->fullname);
			$pdf->SetSubject("Rapportage risicoanalyse voor informatiebeveiliging");
			$pdf->SetKeywords("RAVIB, risicoanalyse, rapportage");
			$pdf->SetCreator("RAVIB - https://www.ravib.nl/");
			$pdf->AliasNbPages();

			/* Title
			 */
			$pdf->AddPage();
			$pdf->Bookmark("Titelpagina");
			if ($this->valid_url($this->case["logo"])) {
				try {
					$pdf->Image($this->case["logo"], 15, 20, 0, 25);
				} catch (Exception $e) {
					$pdf->SetFont("helvetica", "", 8);
					$pdf->Cell(0, 0, "[ invalid logo image ]");
				}
			}
			$pdf->SetFont("helvetica", "B", 16);
			$pdf->Ln(100);
			$pdf->Cell(0, 0, "Risicoanalyse voor informatiebeveiliging", 0, 1, "C");
			$pdf->SetFont("helvetica", "", 12);
			$pdf->Ln(10);
			$pdf->Cell(0, 0, $this->case["title"], 0, 1, "C");
			$pdf->Ln(10);
			$pdf->Cell(0, 0, $this->user->fullname.", ".$ra_date, 0, 1, "C");
			$pdf->Ln(40);
			$pdf->SetFont("helvetica", "I", 12);
			$pdf->Cell(0, 0, "Vertrouwelijk", 0, 1, "C");
			$pdf->SetFont("helvetica", "", 12);
			$pdf->AddPage();

			/* Risk matrix
			 */
			$pdf->AddPage();
			$pdf->Bookmark("Risicomatrices");
			$pdf->AddChapter("Risicomatrix van de dreigingen");
			$pdf->Write(5, "In onderstaande tabel zijn de dreigingen waarvan het risico wordt geaccepteerd, niet meegenomen.");
			$pdf->Ln(8);

			$this->draw_risk_matrix($pdf, $threats, true);

			/* Measure matrix
			 */
			$pdf->AddChapter("Verdeling urgentie ISO maatregelen");
			$pdf->Write(5, "In onderstaande tabel zijn de maatregelen tegen de dreigingen waarvan het risico wordt geaccepteerd, niet meegenomen.");
			$pdf->Ln(8);

			$pdf->SetFont("helvetica", "", 10);

			$matrix = array();
			foreach ($measures as $measure) {
				$highest_risk = -1;

				if ($measure["relevant"] == false) {
					continue;
				}

				foreach ($measure["threats"] as $threat) {
					if (($threat["chance"] == 0) || ($threat["impact"] == 0) || ($threat["handle"] == "")) {
						continue;
					}
					if ($threat["relevant"] == false) {
						continue;
					}
					$risk = $this->model->risk_matrix[$threat["chance"] - 1][$threat["impact"] - 1];
					if ($risk >= $highest_risk) {
						$highest_risk = $risk;
					}
				}

				$matrix[$highest_risk]++;
			}

			foreach ($this->model->risk_matrix_labels as $i => $level) {
				$pdf->Cell(30, 8, $level, 1, 0, "C");
			}
			$pdf->Ln(8);
			foreach ($this->model->risk_matrix_labels as $i => $level) {
				$pdf->SetColor($this->model->risk_matrix_labels[$i]);
				$pdf->Cell(30, 8, sprintf("%d", $matrix[$i]), 1, 0, "C", true);
			}
			$pdf->Ln(15);

			/* Risk matrix for accepted risks
			 */
			$pdf->AddChapter("Risicomatrix van de geaccepteerde dreigingen");
			$pdf->Write(5, "Onderstaande tabel bevat een overzicht van de dreigingen waarvan het risico wordt geaccepteerd.");
			$pdf->Ln(8);

			$this->draw_risk_matrix($pdf, $threats, false);

			/* Impact values
			 */
			$impact_values = json_decode($this->case["impact"], true);
			if ($impact_values[0] != "") {
				$pdf->AddChapter("Invulling van de impact");
				$pdf->Write(5, "De impact heeft voor deze risicoanalyse de volgende invulling:");
				$pdf->Ln(8);

				foreach ($this->model->risk_matrix_impact as $i => $impact) {
					$pdf->SetFont("helvetica", "B", 10);
					$pdf->Cell(27, 5, $impact.": ");
					$pdf->SetFont("helvetica", "", 10);
					$pdf->Write(5, $impact_values[$i]);
					$pdf->Ln(5);
				}
			}

			/* Business Impact Anlysis
			 */
			$pdf->AddPage();
			$pdf->Bookmark("Business Impact Analyse");
			$pdf->AddChapter("Business Impact Analyse");
			$pdf->Write(5, "Dit hoofdstuk bevat een overzicht van de informatiesystemen waarop de risicoanalyse is uitgevoerd en de informatie die tijdens de business impact analyse is aangereikt.");
			$pdf->Ln(12);

			$pdf->SetFont("helvetica", "B", 11);
			$pdf->Cell(75, 6, "Informatiesysteem", "B");
			$pdf->Cell(20, 6, "Beschik.", "B");
			$pdf->Cell(20, 6, "Integri.", "B");
			$pdf->Cell(25, 6, "Vertrouw.", "B");
			$pdf->Cell(20, 6, "Waarde", "B");
			$pdf->Cell(10, 6, "SE", "B");
			$pdf->Cell(10, 6, "Loc", "B");
			$pdf->Ln(8);

			foreach ($bia as $nr => $item) {
				$pdf->SetFont("helvetica", "B", 10);
				$pdf->Cell(75, 5, ($nr + 1).": ".$item["item"]);
				$pdf->SetFont("helvetica", "", 10);
				$pdf->Cell(20, 5, $this->model->availability_score[$item["availability"] - 1]);
				$pdf->Cell(20, 5, $this->model->integrity_score[$item["integrity"] - 1]);
				$pdf->Cell(25, 5, $this->model->confidentiality_score[$item["confidentiality"] - 1]);
				$pdf->Cell(20, 5, $this->model->asset_value($item));
				$pdf->Cell(10, 5, is_true($item["owner"]) ? "ja" : "nee");
				$pdf->Cell(10, 5, substr($item["location"], 0, 2));
				$pdf->Ln(5);

				$pdf->AddTextBlock("Omschrijving", $item["description"]);
				$pdf->AddTextBlock("Impact van incident", $item["impact"]);
				$pdf->Ln(1);
			}

			$this->draw_bia_threat_matrix($pdf, $case_id, $bia, $threats);

			/* Threats
			 */
			$pdf->AddPage();
			$pdf->Bookmark("Dreigingen");

			$pdf->AddChapter("Dreigingen");
			$pdf->Write(5, "Dit hoofdstuk bevat een overzicht van alle dreigingen en de informatie die is aangereikt tijdens de dreigingsanalyse.");
			$pdf->Ln(12);

			$pdf->SetFont("helvetica", "B", 11);
			$pdf->Cell(7, 6, "", "B");
			$pdf->Cell(153, 6, "Dreiging", "B");
			$pdf->Cell(0, 6, "Risico", "B");
			$pdf->Ln(8);

			$pdf->SetFont("helvetica", "", 10);

			foreach ($threats as $i => $threat) {
				$threats[$i]["risk_value"] = $this->model->risk_matrix[$threat["chance"] - 1][$threat["impact"] - 1];
				$threats[$i]["relevant"] = $threat["handle"] != $this->model->threat_handle_labels[THREAT_ACCEPT];
				$threats[$i]["systems"] = $this->model->get_systems($case_id, $threat["id"]);
			}

			if ($_POST["sort_by_risk"]) {
				usort($threats, array($this, "sort_threats"));
			}

			foreach ($threats as $threat) {
				$risk = $this->model->risk_matrix_labels[$threat["risk_value"]];

				if ($threat["relevant"] == false) {
					if (is_false($_POST["not_relevant"])) {
						continue;
					} else {
						$risk = "(".$risk.")";
					}
				}

				$pdf->Cell(7, 5, $threat["number"].":");
				$chance = $this->model->risk_matrix_chance[$threat["chance"] - 1];
				$impact = $this->model->risk_matrix_impact[$threat["impact"] - 1];

				$pdf->MultiCell(165, 5, $threat["threat"]);
				$pdf->Cell(7, 5);
				$line = sprintf("Kans: %s         Impact: %s         Aanpak: %s", $chance, $impact, $threat["handle"]);
				$pdf->Cell(153, 5, $line);
				if ($threat["relevant"]) {
					$pdf->SetColor($risk);
				} else {
					$pdf->SetFillColor(255, 255, 255);
				}
				$pdf->Cell(0, 5, $risk, 0, 0, "C", true);
				$pdf->Ln(5);

				if ($threat["action"] != "") {
					$pdf->Cell(7, 5);
					$pdf->MultiCell(150, 5, "Gewenste situatie / te nemen actie:");
					$pdf->Cell(15, 5);
					$pdf->MultiCell(142, 5, $threat["action"]);
				}

				if ($threat["current"] != "") {
					$pdf->Cell(7, 5);
					$pdf->MultiCell(150, 5, "Huidige situatie / huidige maatregelen:");
					$pdf->Cell(15, 5);
					$pdf->MultiCell(142, 5, $threat["current"]);
				}

				if ($threat["argumentation"] != "") {
					$pdf->Cell(7, 5);
					$pdf->MultiCell(150, 5, "Argumentatie voor gemaakte keuze:");
					$pdf->Cell(15, 5);
					$pdf->MultiCell(142, 5, $threat["argumentation"]);
				}

				if ($threat["systems"] != false) {
					$list = array();
					foreach ($threat["systems"] as $system) {
						$elem = $system["item"];
						if (($value = $this->model->asset_value($system)) != "") {
							$elem .= " (".$value.")";
						}
						array_push($list, $elem);
					}

					$pdf->Cell(7, 5);
					$pdf->MultiCell(163, 5, "Betrokken systemen: ".implode(", ", $list));
				}

				$pdf->Ln(1);
			}

			/* ISO measures
			 */
			$pdf->AddPage();
			$pdf->Bookmark("Maatregelen");

			if (($bia_threat = $this->model->get_bia_threats($case_id)) === false) {
				return;
			}

			$threat_asset_value = array();
			foreach ($bia_threat as $i => $bt) {
				$asset_value = $bt["availability"] * $bt["integrity"] * $bt["confidentiality"];
				if (is_array($threat_asset_value[$bt["threat_id"]])) {
					if ($threat_asset_value[$bt["threat_id"]]["value"] >= $asset_value) {
						continue;
					}
				}

				$threat_asset_value[$bt["threat_id"]] = array(
					"value" => $asset_value,
					"label" => $this->model->asset_value($bt));
			}

			foreach ($measures as $i => $measure) {
				$measures[$i]["asset_value"] = 0;
				foreach ($measure["threats"] as $threat) {
					if ($measures[$i]["asset_value"] < $threat_asset_value[$threat["number"]]["value"]) {
						$measures[$i]["asset_value"] = $threat_asset_value[$threat["number"]]["value"];
						$measures[$i]["asset_label"] = $threat_asset_value[$threat["number"]]["label"];
					}
				}
			}

			if ($_POST["sort_by_risk"]) {
				usort($measures, array($this, "sort_measures"));
			}

			$pdf->AddChapter("Maatregelen uit ".$ra_standard["name"]);
			$pdf->Write(5, "Dit hoofdstuk bevat een overzicht van de maatregelen uit de ".$ra_standard["name"]." standaard die zijn geselecteerd op basis van de opgegeven dreigingen.");
			$pdf->Ln(12);

			$pdf->SetFont("helvetica", "B", 11);
			$pdf->Cell(15, 6, "", "B");
			$pdf->Cell(145, 6, "Maatregel", "B");
			$pdf->Cell(0, 6, "Urgentie", "B");
			$pdf->Ln(8);
			$pdf->SetFont("helvetica", "", 10);
			foreach ($measures as $measure) {
				if (count($measure["threats"]) == 0) {
					continue;
				}

				if (($measure["relevant"] == false) && is_false($_POST["not_relevant"])) {
					continue;
				}

				/* Export ISO measure
				 */
				$pdf->Cell(15, 5, $measure["number"]);
				$pdf->SetFont("helvetica", "B", 10);
				$pdf->Cell(145, 5, $measure["name"]);
				$pdf->SetFont("helvetica", "", 10);

				if ($measure["relevant"] == false) {
					$pdf->SetColor();
					if ($measure["risk"] != "") {
						$measure["risk"] = "(".$measure["risk"].")";
					}
				} else {
					$pdf->SetColor($measure["risk"]);
				}

				$pdf->Cell(0, 5, $measure["risk"], 0, 0, "C", true);
				$pdf->Ln(5);

				if ($measure["asset_value"] > 0) {
					$pdf->Cell(15, 5, "");
					$pdf->Cell(140, 5, "Hoogste waarde betrokken informatiesystemen: ".$measure["asset_label"]);
					$pdf->Ln(5);
				}

				/* Export threats
				 */
				$pdf->SetFillColor(255, 255, 255);
				foreach ($measure["threats"] as $threat) {
					if (is_false($_POST["not_relevant"]) && ($threat["relevant"] == false)) {
						continue;
					}

					$pdf->Cell(15, 5, "");
					$line = sprintf("%s: %s [ %s, %s ]", $threat["number"], $threat["threat"], $threat["risk"], $threat["handle"]);
					$pdf->MultiCell(150, 5, $line);
				}
				$pdf->Ln(2);
			}

			/* Output
			 */
			$case_name = $this->generate_filename($this->case["name"]);
			$pdf->Output($case_name." - risicoanalyse.pdf", "I");
		}

		private function generate_soa($case_id) {
			if (($measures = $this->model->get_measures($case_id)) === false) {
				return;
			}

			if (($measure_categories = $this->model->get_measure_categories($this->case["iso_standard_id"])) === false) {
				return;
			}

			$this->output->disable();

			$csv = new csvfile();
			if (is_true($_POST["semicolon"])) {
				$csv->separator = ";";
			}

			/* Title bar
			 */
			$csv->add_line("Versie <jaartal>", "", "", "Reden voor selectie");
			$csv->add_line("", "Maatregel", "", "WV", "CV", "PI", "RA", "Van toepassing", "Toelichting / Documentatie");

			/* ISO measures
			 */
			$cat = "";
			foreach ($measures as $measure) {
				$parts = explode(".", $measure["number"]);
				if ($cat != $parts[0]) {
					$cat = $parts[0];
					$csv->add_line("", $measure_categories[$cat]);
				}

				$ra = $measure["relevant"] ? "x" : "";

				$csv->add_line("", $measure["number"], $measure["name"], "", "", "", $ra, "JA");
			}

			$csv->add_line();
			$csv->add_line("WV: Wettelijke verplichting, CV: Contractuele verplichting, PI: Procesinrichting, RA: Risicoanalyse");

			if (($case = $this->model->get_case($case_id)) != false) {
				$case_name = $this->generate_filename($case["name"]);
			} else {
				$case_name = "SOA_werkblad_".$case_id;
			}

			/* Output
			 */
			header("Content-Type: text/csv");
			header("Content-Disposition: attachment; filename=\"".$case_name.".csv\"");
			print $csv->to_string();
		}

		private function show_form($case_id) {
			$this->show_breadcrumbs($case_id);

			$this->output->add_tag("report", "", array("case_id" => $case_id));
		}

		public function execute() {
			$case_id = $this->page->pathinfo[1];
			if ($this->valid_case_id($case_id) == false) {
				return;
			}

			if ($_SERVER["REQUEST_METHOD"] == "POST") {
				if ($_POST["submit_button"] == "Rapportage maken") {
					$this->generate_report($case_id);
				} else if ($_POST["submit_button"] == "SOA werkblad") {
					$this->generate_soa($case_id);
				} else {
					$this->show_form($case_id);
				}
			} else {
				$this->show_form($case_id);
			}
		}
	}
?>
