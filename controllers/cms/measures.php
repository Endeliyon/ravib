<?php
	/* Copyright (C) by Hugo Leisink <hugo@leisink.net>
	 * This file is part of the Banshee PHP framework
	 * http://www.banshee-php.org/
	 */

	class cms_measures_controller extends controller {
		private function show_overview() {
			if (($measure_count = $this->model->count_measures()) === false) {
				$this->output->add_tag("result", "Database error.");
				return;
			}

			$paging = new pagination($this->output, "measures", $this->settings->admin_page_size, $measure_count);

			if (($measures = $this->model->get_measures($paging->offset, $paging->size)) === false) {
				$this->output->add_tag("result", "Database error.");
				return;
			}

			$this->output->open_tag("overview");

			$this->output->open_tag("measures");
			foreach ($measures as $measure) {
				$this->output->record($measure, "measure");
			}
			$this->output->close_tag();

			$paging->show_browse_links();

			$this->output->close_tag();
		}

		private function show_measure_form($measure) {
			if (($threats = $this->model->get_threats()) === false) {
				$this->output->add_tag("result", "Database error.");
				return;
			}

			$this->output->open_tag("edit");

			$this->output->record($measure, "measure");

			$this->output->open_tag("threats");
			foreach ($threats as $threat) {
				$params = array(
					"id"      => $threat["id"],
					"checked" => show_boolean(in_array($threat["id"], $measure["threat_links"])));
				$this->output->open_tag("threat", $params);
				$this->output->add_tag("number", $threat["number"]);
				$this->output->add_tag("title", $threat["number"].". ".$threat["threat"]);
				$this->output->add_tag("description", $threat["description"]);
				$this->output->close_tag();
			}
			$this->output->close_tag();

			$this->output->close_tag();
		}

		public function execute() {
			$this->output->add_css("includes/standard.css");

			if (($standard = $this->model->get_iso_standard($_SESSION["iso_standard"])) != false) {
				$this->output->add_tag("standard", $standard["name"]);
			}

			if ($_SERVER["REQUEST_METHOD"] == "POST") {
				if ($_POST["submit_button"] == "Save ISO measure") {
					/* Save measure
					 */
					if ($this->model->save_oke($_POST) == false) {
						$this->show_measure_form($_POST);
					} else if (isset($_POST["id"]) === false) {
						/* Create measure
						 */
						if ($this->model->create_measure($_POST) === false) {
							$this->output->add_message("Error creating measure.");
							$this->show_measure_form($_POST);
						} else {
							$this->user->log_action("measure created");
							$this->show_overview();
						}
					} else {
						/* Update measure
						 */
						if ($this->model->update_measure($_POST) === false) {
							$this->output->add_message("Error updating measure.");
							$this->show_measure_form($_POST);
						} else {
							$this->user->log_action("measure updated");
							$this->show_overview();
						}
					}
				} else if ($_POST["submit_button"] == "Delete ISO measure") {
					/* Delete measure
					 */
					if ($this->model->delete_oke($_POST) == false) {
						$this->show_measure_form($_POST);
					} else if ($this->model->delete_measure($_POST["id"]) === false) {
						$this->output->add_message("Error deleting measure.");
						$this->show_measure_form($_POST);
					} else {
						$this->user->log_action("measure deleted");
						$this->show_overview();
					}
				} else {
					$this->show_overview();
				}
			} else if ($this->page->pathinfo[2] === "new") {
				/* New measure
				 */
				$measure = array();
				$this->show_measure_form($measure);
			} else if (valid_input($this->page->pathinfo[2], VALIDATE_NUMBERS, VALIDATE_NONEMPTY)) {
				/* Edit measure
				 */
				if (($measure = $this->model->get_measure($this->page->pathinfo[2])) === false) {
					$this->output->add_tag("result", "Case not found.\n");
				} else {
					$this->show_measure_form($measure);
				}
			} else {
				/* Show overview
				 */
				$this->show_overview();
			}
		}
	}
?>
