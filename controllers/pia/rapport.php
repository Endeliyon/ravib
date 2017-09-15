<?php
	/* Copyright (c) by Hugo Leisink <hugo@leisink.net>
	 * Licensed under the RAVIB license.
	 */

	class pia_rapport_controller extends pia_controller {
		private function get_int_part($str) {
			$pos = 0;
			while (is_numeric(substr($str, $pos, 1)) == false) {
				$pos++;
			}

			return (int)substr($str, $pos);
		}

		private function sort_law_sections($a, $b) {
			$a = $this->get_int_part($a);
			$b = $this->get_int_part($b);

			return $a < $b ? -1 : 1;
		}

		private function generate_pdf($pia_id) {
			if (($trace = $this->model->trace_pia($pia_id)) === false) {
				$this->output->add_tag("result", "Fout tijdens ophalen van PIA resultaten.", array("url" => "pia/casus"));
				return;
			}

			$this->output->disable();
			$pdf = new PDF_report($this->pia["name"]);
			$pdf->SetAuthor($this->user->fullname);
			$pdf->SetCreator("RAVIB.nl");
			$pdf->AliasNbPages();

			/* Title
			 */
			$pdf->AddPage();
			$pdf->SetFont("helvetica", "B", 16);
			$pdf->Ln(100);
			$pdf->Cell(0, 0, "Privacy Impact Assessment", 0, 1, "C");
			$pdf->SetFont("helvetica", "", 12);
			$pdf->Ln(10);
			$pdf->Cell(0, 0, $this->pia["name"], 0, 1, "C");
			$pdf->Ln(10);
			$pdf->Cell(0, 0, $this->user->fullname.", ".date("j F Y", strtotime($this->pia["date"])), 0, 1, "C");

			$pdf->AddPage();

			/* Affected laws
			 */
			$laws = array();
			foreach ($trace as $item) {
				if ($item["law_section"] == "") {
					continue;
				}

				$sections = explode(",", $item["law_section"]);
				foreach ($sections as $section) {
					$section = trim($section);

					if ($laws[$section] === null) {
						$laws[$section] = 1;
					} else {
						$laws[$section]++;
					}
				}
			}
			uksort($laws, array($this, "sort_law_sections"));

			if (count($laws) > 0) {
				$pdf->SetFont("helvetica", "B", 12);
				$pdf->Write(4, "Geraakte wetsartikelen");
				$pdf->Ln(6);
				$pdf->SetFont("helvetica", "", 10);
				$pdf->SetFillColor(224);

				$pdf->Cell(60, 6, "Wetsartikel", 1, 0, "L", true);
				$pdf->Cell(30, 6, "Aantal keren", 1, 0, "L", true);
				$pdf->Ln(6.5);
				foreach ($laws as $law => $count) {
					$pdf->Cell(60, 6, $law, 1);
					$pdf->Cell(30, 6, $count, 1);
					$pdf->Ln(6);
				}

				$pdf->Ln(6);
			}

			/* PIA result
			 */
			$pdf->SetFont("helvetica", "B", 12);
			$pdf->Write(4, "Resultaten");
			$pdf->Ln(6);
			$pdf->SetFont("helvetica", "", 10);
			$pdf->SetFillColor(240);

			foreach ($trace as $item) {
				if (($item[$item["answer"]] == "") && ($item["comment"] == null)) {
					if (is_false($_POST["not_relevant"])) {
						continue;
					}
				}

				$answer = is_true($item["answer"]) ? "Ja" : "Nee";
				$pdf->add_text($item["number"].": ".$item["question"]);
				if ($item["information"] != "") {
					$pdf->add_text($item["information"]);
				}
				$pdf->Ln(1);
				$pdf->add_text($answer.". ".$item[$item["answer"]], "LRTB");

				if ($item["comment"] != null) {
					$pdf->Ln(1);
					$pdf->add_text("Opmerking: ".$item["comment"]);
				}

				if ($item["law_section"] != "") {
					$pdf->Ln(1);
					$pdf->add_text("Geraakt wetsartikel: ".$item["law_section"], "LRTB");
				}

				$pdf->Ln(5);
			}

			header("Content-Disposition: attachment; filename=pia_rapport_".$pia_id.".pdf");

			$pdf->Output();
		}

		public function execute() {
			$pia_id = $this->page->pathinfo[2];
			if ($this->valid_pia_id($pia_id) == false) {
				return;
			}

            if ($_SERVER["REQUEST_METHOD"] == "POST") {
				$this->generate_pdf($pia_id);
			} else {
				$this->show_breadcrumbs($pia_id);

				$this->output->add_tag("report", array("pia_id" => $pia_id));
			}
		}
	}
?>
