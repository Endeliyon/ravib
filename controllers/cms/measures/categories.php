<?php
	/* Copyright (c) by Hugo Leisink <hugo@leisink.net>
	 * Licensed under the RAVIB license.
	 */

	class cms_measures_categories_controller extends tablemanager_controller {
		protected $name = "Measure category";
		protected $pathinfo_offset = 3;
		protected $back = "cms/measures";
		protected $icon = "measure_categories.png";
		protected $browsing = null;

		public function execute() {
			$this->output->add_css("includes/standard.css");

			if (($standard = $this->model->get_iso_standard($_SESSION["iso_standard"])) != false) {
				$this->output->add_tag("standard", $standard["name"]);
			}

			parent::execute();
		}
	}
?>
