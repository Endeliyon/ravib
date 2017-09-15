<?php
	/* Copyright (c) by Hugo Leisink <hugo@leisink.net>
	 * Licensed under the RAVIB license.
	 */

	class PDF_report extends FPDF {
		private $title = null;

		public function __construct($title = null) {
			parent::__construct();

			$this->title = $title;
			$this->SetMargins(15, 10);
			$this->SetAutoPageBreak(true, 25);
		}

		public function Header() {
			$this->SetY(5);
			$this->SetFont("helvetica", "", 8);
			$this->Cell(0, 8, $this->title, 0, 0, "R");
			$this->Ln(12);
		}

		public function Footer() {
			$this->SetY(-15);
			$this->SetFont("helvetica", "I", 8);
			$this->Cell(0, 8, "RAVIB.nl");
			$this->Cell(0, 8, "Pagina ".$this->PageNo().'/{nb}', 0, 0, "R");
		}

		public function AddPage($orientation = "", $size = "", $rotation = 0) {
			parent::AddPage($orientation, $size, $rotation);
			$this->Ln(10);
		}

		public function Cell($w, $h = 0, $txt = "", $border = 0, $ln = 0, $align = "L", $fill = false, $link = "") {
			$txt = str_replace(EURO, "{E}", $txt);
			$txt = utf8_decode($txt);
			$txt = str_replace("{E}", chr(128), $txt);

			parent::Cell($w, $h, $txt, $border, $ln, $align, $fill, $link);
		}

		public function SetColor($risk = null) {
			switch ($risk) {
				case "kritiek":
					$this->SetFillColor(255, 0, 0);
					break;
				case "hoog":
					$this->SetFillColor(255, 112, 0);
					break;
				case "gemiddeld":
					$this->SetFillColor(255, 255, 0);
					break;
				case "laag":
					$this->SetFillColor(0, 255, 0);
					break;
				default:
					$this->SetFillColor(255, 255, 255);
			}
		}

		public function AddChapter($title) {
			$this->SetFont("helvetica", "B", 12);
			$this->Write(5, $title);
			$this->Ln(6);
			$this->SetFont("helvetica", "", 10);
		}

		public function AddTextBlock($title, $text) {
			if ($text == "") {
				return;
			}

			$lines = explode("\n", $text);
			if (count($lines) > 1) {
				$this->Cell(4, 5);
				$this->Write(5, $title.":");
				$this->Ln(5);
				$tab = 8;
			} else {
				$lines[0] = $title.": ".$lines[0];
				$tab = 4;
			}
			foreach ($lines as $line) {
				if ($line == "") {
					continue;
				}
				$this->Cell($tab, 5);
				$this->Write(5, $line);
				$this->Ln(5);
			}
		}
	}
?>
