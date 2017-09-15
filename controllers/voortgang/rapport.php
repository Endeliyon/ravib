<?php
	/* Copyright (c) by Hugo Leisink <hugo@leisink.net>
	 * Licensed under the RAVIB license.
	 */

	class voortgang_rapport_controller extends process_controller {
		private function draw_risk_matrix($pdf, $threats) {
			$matrix = array();
			foreach ($threats as $threat) {
				if (($threat["chance"] === null) || ($threat["impact"] === null)) {
					continue;
				} else if ($threat["handle"] == $this->model->threat_handle_labels[THREAT_ACCEPT]) {
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

		public function execute() {
			$case_id = $this->page->pathinfo[2];
			if ($this->valid_case_id($case_id) == false) {
				return;
			}

			if (($threats = $this->model->get_threats($case_id)) === false) {
				return;
			}

			if (($measures = $this->model->get_measures($case_id)) === false) {
				return;
			}

			if (($measure_categories = $this->model->get_measure_categories($this->case["iso_standard_id"])) === false) {
				return;
			}

			if (($controls = $this->model->get_controls($this->case["iso_standard_id"])) === false) {
				return;
			}

			if (($progress = $this->model->get_progress($case_id)) === false) {
				return;
			}

			$this->output->disable();

			$pdf = new PDF_report($this->case["name"]);
			$pdf->SetTitle($this->case["name"]);
			$pdf->SetAuthor($this->user->fullname);
			$pdf->SetSubject("Voortgangsrapportage aanpak informatiebeveiliging");
			$pdf->SetKeywords("RAVIB, informatiebeveiliging, voortgangsrapportage");
			$pdf->SetCreator("RAVIB - https://www.ravib.nl/");
			$pdf->AliasNbPages();

			/* Title
			 */
			$pdf->AddPage();
			$pdf->Bookmark("Titelpagina");
			$pdf->SetFont("helvetica", "B", 16);
			$pdf->Ln(100);
			$pdf->Cell(0, 0, "Voortgangsrapportage aanpak informatiebeveiliging", 0, 1, "C");
			$pdf->SetFont("helvetica", "", 12);
			$pdf->Ln(10);
			$pdf->Cell(0, 0, $this->case["name"], 0, 1, "C");
			$pdf->Ln(10);
			$pdf->Cell(0, 0, $this->user->fullname.", ".date_string("j F Y"), 0, 1, "C");

			/* Progress
			 */
			$pdf->AddPage();
			$pdf->Bookmark("Voortgang");
			$pdf->AddChapter("Voortgang");
			$pdf->Ln(8);

			$pdf->Write(5, "De voortgang van de implementatie, met in het groen het aantal afgeronde taken, in het geel het aantal nog lopende taken, in het rood het aantal taken waarvan de deadline is verstreken en in het zwart het aantal taken dat nog niet is toegekend aan een persoon.");
			$pdf->Ln(12);

			$today = strtotime("today");

			$done = $pending = $overdue = $idle = 0;
			foreach ($measures as $i => $measure) {
				if ($measure["done"]) {
					$done++;
					array_push($progress, $measure["id"]);
				} else if ($measure["deadline"] == null) {
					$idle++;
				} else if ($measure["deadline"] < $today) {
					$overdue++;
					$measures[$i]["overdue"] = true;
				} else {
					$pending++;
				}
			}

			$total = count($measures);
			$done = round(100 * $done / $total);
			$overdue = round(100 * $overdue / $total);
			$pending = round(100 * $pending / $total);
			$idle = round(100 * $idle / $total);

			if (($delta = ($done + $overdue + $pending + $idle - 100)) != 0) {
				$values = array(
					"done" => $done,
					"overdue" => $overdue,
					"pending" => $pending,
					"idle"    => $idle);
				arsort($values);
				$key = key($values);
				$$key -= $delta;
			}

			if ($done > 0) {
				$pdf->SetFillColor(0, 192, 0);
				$pdf->Cell(1.75 * $done, 5, $done."%", 1, 0, "C", true);
			}
			if ($pending > 0) {
				$pdf->SetFillColor(255, 255, 0);
				$pdf->Cell(1.75 * $pending, 5, $pending."%", 1, 0, "C", true);
			}
			if ($overdue > 0) {
				$pdf->SetFillColor(255, 0, 0);
				$pdf->Cell(1.75 * $overdue, 5, $overdue."%", 1, 0, "C", true);
			}
			if ($idle > 0) {
				$pdf->SetTextColor(255, 255, 255);
				$pdf->SetFillColor(0, 0, 0);
				$pdf->Cell(1.75 * $idle, 5, $idle."%", 1, 0, "C", true);
				$pdf->SetTextColor(0, 0, 0);
			}

			$pdf->Ln(30);
			$pdf->Write(5, "Onderstaand overzicht geeft het aantal afgeronde taken per categorie aan.");
			$pdf->Ln(12);

			$categories = array();
			foreach ($measures as $i => $measure) {
				list($section) = explode(".", $measure["number"]);
				if (isset($categories[$section]) == false) {
					$categories[$section] = array();
				}
				if ($measure["done"]) {
					$categories[$section][0]++;
				}
				$categories[$section][1]++;
			}

			foreach ($categories as $key => $category) {
				$percentage = round(100 * $category[0] / $category[1]);
				$pdf->Write(6, $key.". ".$measure_categories[$key].": ".$percentage."% (".$category[0]." / ".$category[1].")");
				$pdf->Ln(6);
				if ($percentage > 0) {
					$pdf->SetFillColor(92, 92, 255);
					$pdf->Cell(1.75 * $percentage, 3, "", 1, 0, "C", true);
				}
				if ($percentage < 100) {
					$pdf->SetFillColor(255, 255, 255);
					$pdf->Cell(175 - 1.75 * $percentage, 3, "", 1, 0, "C", true);
				}
				$pdf->Ln(6);
			}

			/* Risk matrix
			 */
			$pdf->AddPage();
			$pdf->Bookmark("Risicomatrices");
			$pdf->AddChapter("Risicomatrix van de dreigingen");
			$pdf->Write(5, "De oorspronkelijke risicomatrix.");
			$pdf->Ln(10);

			$this->draw_risk_matrix($pdf, $threats);

			$accept = $this->model->threat_handle_labels[THREAT_ACCEPT];
			foreach ($threats as $i => $threat) {
				if ($threat["handle"] == $accept) {
					continue;
				}

				$measure_ids = $controls[$threat["id"]];
				if (is_array($measure_ids) == false) {
					continue;
				}

				foreach ($measure_ids as $measure_id) {
					if (in_array($measure_id, $progress) == false) {
						continue 2;
					}
				}

				$threats[$i]["handle"] = $accept;
			}

			$pdf->Ln(20);
			$pdf->Write(5, "De risicomatrix waarin de voltooide actiepunten zijn verwerkt.");
			$pdf->Ln(10);
			$this->draw_risk_matrix($pdf, $threats);

			/* Output
			 */
			$case_name = $this->generate_filename($this->case["name"]);
			$pdf->Output($case_name." - voortgangsrapportage.pdf", "I");
		}
	}
?>
