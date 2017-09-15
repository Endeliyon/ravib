<?php
	/* Copyright (c) by Hugo Leisink <hugo@leisink.net>
	 * Licensed under the RAVIB license.
	 */

	class pia_rapport_model extends pia_model {
	}

	class PDF_report extends FPDF {
		private $title = null;

		public function __construct($title = null) {
			parent::__construct();

			$this->title = $title;
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

		public function Cell($w, $h = 0, $txt = "", $border = 0, $ln = 0, $align = "L", $fill = false, $link = "") {
			$txt = utf8_decode($txt);

			parent::Cell($w, $h, $txt, $border, $ln, $align, $fill, $link);
		}

		public function add_text($text, $border = 0) {
			$text = str_replace("<li>", "  - ", $text);
			$text = str_replace("<ol>\r\n", "", $text);
			$text = str_replace("<ul>\r\n", "", $text);
			$text = strip_tags($text);
			$text = str_replace("i".chr(0xcc).chr(0x88), chr(239), $text);
			$text = str_replace("u".chr(0xcc).chr(0x88), chr(252), $text);
			$text = str_replace("e".chr(0xcc).chr(0x82), chr(234), $text);
			$text = str_replace("e".chr(0xcc).chr(0x88), chr(235), $text);

			$this->MultiCell(0, 5, $text, $border, "L", $border !== 0);
		}
	}
?>
