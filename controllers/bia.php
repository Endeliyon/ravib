<?php
	/* Copyright (c) by Hugo Leisink <hugo@leisink.net>
	 * Licensed under the RAVIB license.
	 */

	class bia_controller extends process_controller {
		private $location = array(
			"intern" => "intern (op eigen systeem)",
			"extern" => "extern (in eigen beheer, maar op systeem van 3e partij)",
			"saas"     => "SAAS oplossing");

		private function show_overview($case_id) {
			if (($items = $this->model->get_items($case_id)) === false) {
				$this->output->add_tag("result", "Database error.", array("url" => "bia/".$case_id));
				return;
			}

			$this->output->open_tag("overview", array("case_id" => $case_id));
			foreach ($items as $item) {
				$item["value"] = $this->model->asset_value($item);
				$item["availability"] = $this->model->availability_score[$item["availability"] - 1];
				$item["confidentiality"] = $this->model->confidentiality_score[$item["confidentiality"] - 1];
				$item["integrity"] = $this->model->integrity_score[$item["integrity"] - 1];
				$item["owner"] = is_true($item["owner"]) ? "ja" : "nee";
				$this->output->record($item, "item");
			}
			$this->output->close_tag();
		}

		private function show_bia_form($item) {
			$this->output->open_tag("edit", array("case_id" => $item["case_id"]));

			$this->output->open_tag("availability");
			foreach ($this->model->availability_score as $value => $label) {
				$this->output->add_tag("label", $label, array("value" => $value + 1));
			}
			$this->output->close_tag();

			$this->output->open_tag("confidentiality");
			foreach ($this->model->confidentiality_score as $value => $label) {
				$this->output->add_tag("label", $label, array("value" => $value + 1));
			}
			$this->output->close_tag();

			$this->output->open_tag("integrity");
			foreach ($this->model->integrity_score as $value => $label) {
				$this->output->add_tag("label", $label, array("value" => $value + 1));
			}
			$this->output->close_tag();

			$this->output->open_tag("location");
			foreach ($this->location as $value => $label) {
				$this->output->add_tag("label", $label, array("value" => $value));
			}
			$this->output->close_tag();

			$item["owner"] = show_boolean($item["owner"]);
			$this->output->record($item, "item");

			$this->output->close_tag();
		}

		private function export_overview($case_id) {
			if (($items = $this->model->get_items($case_id)) === false) {
				$this->output->add_tag("result", "Database error.", array("url" => "bia/".$case_id));
				return;
			}

			$pdf = new PDF_report($this->case["title"]);
			$pdf->SetTitle($this->case["name"]);
			$pdf->SetAuthor($this->user->fullname);
			$pdf->SetSubject("Overzicht informatiesystemen");
			$pdf->SetKeywords("RAVIB, overzicht, business impact analyse");
			$pdf->SetCreator("RAVIB - https://www.ravib.nl/");
			$pdf->AliasNbPages();

			$pdf->AddPage();
			$pdf->AddChapter("Overzicht informatiesystemen");
			$pdf->Ln(8);

			/* Informationsystems
			 */
			$pdf->SetFont("helvetica", "B", 11);
			$pdf->Cell(75, 6, "Informatiesysteem", "B");
			$pdf->Cell(20, 6, "Beschik.", "B");
			$pdf->Cell(20, 6, "Integri.", "B");
			$pdf->Cell(25, 6, "Vertrouw.", "B");
			$pdf->Cell(20, 6, "Waarde", "B");
			$pdf->Cell(10, 6, "SE", "B");
			$pdf->Cell(10, 6, "Loc", "B");
			$pdf->Ln(8);

			foreach ($items as $nr => $item) {
				$pdf->SetFont("helvetica", "B", 10);
				$pdf->Cell(75, 5, ($nr + 1).": ".$item["item"]);
				$pdf->SetFont("helvetica", "", 10);
				$pdf->Cell(20, 5, $this->model->availability_score[$item["availability"] - 1]);
				$pdf->Cell(20, 5, $this->model->integrity_score[$item["integrity"] - 1]);
				$pdf->Cell(25, 5, $this->model->confidentiality_score[$item["confidentiality"] - 1]);
				$pdf->Cell(20, 5, $this->model->asset_value($item));
				$pdf->Cell(10, 5, is_true($item["owner"]) ? "ja" : "nee");
				$pdf->Cell(10, 5, $item["location"]);
				$pdf->Ln(5);

				$pdf->AddTextBlock("Omschrijving", $item["description"]);
				$pdf->AddTextBlock("Impact van incident", $item["impact"]);
				$pdf->Ln(1);
			}

			/* Chance
			 */
			$pdf->AddPage();
			$pdf->AddChapter("Inschatten van de kans");
			$pdf->Write(5, "Denk bij het inschatten van de kans aan de volgende zaken:");
			$pdf->Ln(5);
			$pdf->Write(5, "- De benodigde kennis voor het misbruiken van een kwetsbaarheid.");
			$pdf->Ln(5);
			$pdf->Write(5, "- De capaciteit en beschikbare middelen van een actor.");
			$pdf->Ln(5);
			$pdf->Write(5, "- De wil en bereidheid van een actor.");
			$pdf->Ln(14);

			/* Impact values
			*/
			$pdf->AddChapter("Invulling van de impact");
			$pdf->Write(5, "De impact heeft voor deze risicoanalyse de volgende invulling:");
			$pdf->Ln(8);

			$impact_values = json_decode($this->case["impact"], true);
			foreach ($this->model->risk_matrix_impact as $i => $impact) {
				$pdf->SetFont("helvetica", "B", 10);
				$pdf->Cell(27, 5, $impact.": ");
				$pdf->SetFont("helvetica", "", 10);
				$pdf->Write(5, $impact_values[$i]);
				$pdf->Ln(5);
			}

			$pdf->Ln(10);

			$pdf->AddChapter("Schalen");
			$pdf->SetFont("helvetica", "B", 10);
			$pdf->Cell(35, 5, "Beschikbaarheid:");
			$pdf->SetFont("helvetica", "", 10);
			$pdf->MultiCell(140, 5, implode(", ", config_array(AVAILABILITY_SCORE)));
			$pdf->SetFont("helvetica", "B", 10);
			$pdf->Cell(35, 5, "Integriteit:");
			$pdf->SetFont("helvetica", "", 10);
			$pdf->MultiCell(140, 5, implode(", ", config_array(INTEGRITY_SCORE)));
			$pdf->SetFont("helvetica", "B", 10);
			$pdf->Cell(35, 5, "Vertrouwelijkheid:");
			$pdf->SetFont("helvetica", "", 10);
			$pdf->MultiCell(140, 5, implode(", ", config_array(CONFIDENTIALITY_SCORE)));
			$pdf->SetFont("helvetica", "B", 10);
			$pdf->Cell(35, 5, "Waarde:");
			$pdf->SetFont("helvetica", "", 10);
			$pdf->MultiCell(140, 5, implode(", ", config_array(ASSET_VALUE_LABELS)));

			/* Output
			 */
			$this->output->disable();
			$case_name = $this->generate_filename($this->case["name"]);
			$pdf->Output("BIA ".$case_name.".pdf", "I");
		}

		public function execute() {
			$case_id = $this->page->pathinfo[1];
			if ($this->valid_case_id($case_id) == false) {
				return;
			}

			$this->show_breadcrumbs($case_id);

			if ($_SERVER["REQUEST_METHOD"] == "POST") {
				$_POST["case_id"] = $case_id;

				if ($_POST["submit_button"] == "Informatiesysteem opslaan") {
					/* Save item
					 */
					if ($this->model->save_oke($_POST) == false) {
						$this->show_bia_form($_POST);
					} else if (isset($_POST["id"]) === false) {
						/* Create item
						 */
						if ($this->model->create_item($_POST) === false) {
							$this->output->add_message("Fout bij aanmaken informatiesysteem.");
							$this->show_bia_form($_POST);
						} else {
							$this->user->log_action("bia created");
							$this->show_overview($case_id);
						}
					} else {
						/* Update item 
						 */
						if ($this->model->update_item($_POST) === false) {
							$this->output->add_message("Fout tijdens bijwerken informatiesysteem.");
							$this->show_bia_form($_POST);
						} else {
							$this->user->log_action("bia updated");
							$this->show_overview($case_id);
						}
					}
				} else if ($_POST["submit_button"] == "Informatiesysteem verwijderen") {
					/* Delete item 
					 */
					if ($this->model->delete_oke($_POST["id"], $case_id) == false) {
						$this->output->add_message("Error deleting item.");
						$this->show_bia_form($_POST);
					} else if ($this->model->delete_item($_POST["id"], $case_id) == false) {
						$this->output->add_message("Error deleting item.");
						$this->show_bia_form($_POST);
					} else {
						$this->user->log_action("bia deleted");
						$this->show_overview($case_id);
					}
				} else {
					$this->show_overview($case_id);
				}
			} else if ($this->page->pathinfo[2] === "new") {
				/* New item
				 */
				$item = array("case_id" => $case_id);
				$this->show_bia_form($item);
			} else if ($this->page->pathinfo[2] === "export") {
				/* Export overview
				 */
				$this->export_overview($this->page->pathinfo[1]);
			} else if (valid_input($this->page->pathinfo[2], VALIDATE_NUMBERS, VALIDATE_NONEMPTY)) {
				/* Edit item
				 */
				if (($item = $this->model->get_item($this->page->pathinfo[2], $case_id)) === false) {
					$this->output->add_tag("result", "Item not found.\n", array("url" => "bia/".$case_id));
				} else {
					$this->show_bia_form($item);
				}
			} else {
				/* Show overview
				 */
				$this->show_overview($case_id);
			}
		}
	}
?>
