<?php
	/* Copyright (c) by Hugo Leisink <hugo@leisink.net>
	 * Licensed under the RAVIB license.
	 */

	class cms_validate_controller extends controller {
		public function execute() {
			$this->output->add_css("includes/standard.css");

			if (($standard = $this->model->borrow("cms/standards")->get_item($_SESSION["iso_standard"])) != false) {
				$this->output->add_tag("standard", $standard["name"]);
			}

			if (($threats = $this->model->linked_threats()) === false) {
				$this->output->add_tag("result", "Database error.");
				return;
			}

			if (($measures = $this->model->linked_measures()) === false) {
				$this->output->add_tag("result", "Database error.");
				return;
			}

			$this->output->open_tag("validate");

			$this->output->open_tag("threats");
			foreach ($threats as $threat) {
				$this->output->record($threat, "threat");
			}
			$this->output->close_tag();

			$this->output->open_tag("measures");
			foreach ($measures as $measure) {
				$this->output->record($measure, "measure");
			}
			$this->output->close_tag();

			$this->output->close_tag();
		}
	}
?>
