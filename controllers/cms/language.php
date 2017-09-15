<?php
	/* Copyright (C) by Hugo Leisink <hugo@leisink.net>
	 * This file is part of the Banshee PHP framework
	 * http://www.banshee-php.org/
	 */

	class cms_language_controller extends tablemanager_controller {
		protected $name = "Language";
		protected $pathinfo_offset = 2;
		protected $icon = "language.png";
		protected $back = "cms";

		public function execute() {
			if (is_a($this->language, "language")) {
				parent::execute();
			} else {
				$this->output->open_tag("tablemanager");
				$this->output->add_tag("name", "Language");
				$this->output->add_tag("result", "Multiple languages are not supported by this website.", array("url" => "admin", "seconds" => "5"));
				$this->output->close_tag();
			}
		}
	}
?>
