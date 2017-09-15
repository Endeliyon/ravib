<?php
	/* Copyright (c) by Hugo Leisink <hugo@leisink.net>
	 * Licensed under the RAVIB license.
	 */

	class cms_threats_controller extends controller {
		private function show_overview() {
			if (($threat_count = $this->model->count_threats()) === false) {
				$this->output->add_tag("result", "Database error.");
				return;
			}

			$paging = new pagination($this->output, "admin_threats", $this->settings->admin_page_size, $threat_count);

			if (($threats = $this->model->get_threats($paging->offset, $paging->size)) === false) {
				$this->output->add_tag("result", "Database error.");
				return;
			}

			$this->output->open_tag("overview");

			$this->output->open_tag("threats");
			foreach ($threats as $threat) {
				$this->output->record($threat, "threat");
			}
			$this->output->close_tag();

			$paging->show_browse_links();

			$this->output->close_tag();
		}

		private function show_threat_form($threat) {
			if (($categories = $this->model->get_categories()) === false) {
				$this->output->add_tag("result", "Database error.");
				return;
			}

			if (($iso_measures = $this->model->get_iso_measures()) === false) {
				$this->output->add_tag("result", "Database error.");
				return;
			}

			$this->output->open_tag("edit");

			$this->output->record($threat, "threat");

			$this->output->open_tag("categories");
			foreach ($categories as $category) {
				$this->output->add_tag("category", $category["name"], array("id" => $category["id"]));
			}
			$this->output->close_tag();

			$this->output->open_tag("iso_measures");
			foreach ($iso_measures as $measure) {
				$params = array(
					"id"      => $measure["id"],
					"checked" => show_boolean(in_array($measure["id"], $threat["iso_links"])));
				$this->output->open_tag("measure", $params);
				$this->output->add_tag("title", $measure["number"]." ".$measure["name"]);
				$this->output->add_tag("description", $measure["description"]);
				$this->output->close_tag();
			}
			$this->output->close_tag();

			$this->output->open_tag("cia");
			$this->output->add_tag("option", "");
			$this->output->add_tag("option", "p");
			$this->output->add_tag("option", "s");
			$this->output->close_tag();

			$this->output->close_tag();
		}

		public function execute() {
			$this->output->add_css("includes/standard.css");

			if (($standard = $this->model->get_iso_standard($_SESSION["iso_standard"])) != false) {
				$this->output->add_tag("standard", $standard["name"]);
			}

			if ($_SERVER["REQUEST_METHOD"] == "POST") {
				if ($_POST["submit_button"] == "Save threat") {
					/* Save threat
					 */
					if ($this->model->save_oke($_POST) == false) {
						$this->show_threat_form($_POST);
					} else if (isset($_POST["id"]) === false) {
						/* Create threat
						 */
						if ($this->model->create_threat($_POST) === false) {
							$this->output->add_message("Error creating threat.");
							$this->show_threat_form($_POST);
						} else {
							$this->user->log_action("threat created");
							$this->output->remove_from_cache("koppelingen");
							header("X-Hiawatha-Cache-Remove: /koppelingen");
							$this->show_overview();
						}
					} else {
						/* Update threat
						 */
						if ($this->model->update_threat($_POST) === false) {
							$this->output->add_message("Error updating threat.");
							$this->show_threat_form($_POST);
						} else {
							$this->user->log_action("threat updated");
							$this->output->remove_from_cache("koppelingen");
							header("X-Hiawatha-Cache-Remove: /koppelingen");
							$this->show_overview();
						}
					}
				} else if ($_POST["submit_button"] == "Delete threat") {
					/* Delete threat
					 */
					if ($this->model->delete_oke($_POST) == false) {
						$this->show_threat_form($_POST);
					} else if ($this->model->delete_threat($_POST["id"]) === false) {
						$this->output->add_message("Error deleting threat.");
						$this->show_threat_form($_POST);
					} else {
						$this->user->log_action("threat deleted");
						$this->output->remove_from_cache("koppelingen");
						header("X-Hiawatha-Cache-Remove: /koppelingen");
						$this->show_overview();
					}
				} else {
					$this->show_overview();
				}
			} else if ($this->page->pathinfo[2] === "new") {
				/* New threat
				 */
				$threat = array("iso_links" => array());
				$this->show_threat_form($threat);
			} else if (valid_input($this->page->pathinfo[2], VALIDATE_NUMBERS, VALIDATE_NONEMPTY)) {
				/* Edit threat
				 */
				if (($threat = $this->model->get_threat($this->page->pathinfo[2])) === false) {
					$this->output->add_tag("result", "Threat not found.\n");
				} else {
					$this->show_threat_form($threat);
				}
			} else {
				/* Show overview
				 */
				$this->show_overview();
			}
		}
	}
?>
