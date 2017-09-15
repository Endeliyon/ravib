<?php
	/* Copyright (c) by Hugo Leisink <hugo@leisink.net>
	 * Licensed under the RAVIB license.
	 */

	class casus_controller extends process_controller {
		private function show_overview() {
			if ($this->user->username == "demo") {
				$this->output->add_system_warning("De gegevens in dit demo-account worden elke nacht gewist. U wordt daarbij uitgelogd.");
				$this->output->add_system_warning("Let op, andere gebruikers van dit demo-account kunnen alle gegevens in dit account inzien!");
			}

			if (($cases = $this->model->get_cases()) === false) {
				$this->output->add_tag("result", "Database error.");
				return;
			}

			$this->output->open_tag("overview");
			foreach ($cases as $case) {
				$case["date"] = date_string("j M Y", $case["date"]);
				$case["start"] = $this->model->start_crumb($case);
				$this->output->record($case, "case");
			}

			$this->output->close_tag();
		}

		private function show_case_form($case) {
			if (($standards = $this->model->get_iso_standards()) == false) {
				$this->output->add_tag("result", "Error retrieving ISO standards.");
				return;
			}

			$this->output->add_css("jquery/jquery-ui.css");
			$this->output->add_javascript("jquery/jquery-ui.js");
			$this->output->add_javascript("banshee/datepicker.js");
			$this->output->add_javascript("banshee/datepicker-nl.js");

			$this->output->open_tag("edit");

			$this->output->open_tag("standards");
			foreach ($standards as $standard) {
				$this->output->add_tag("standard", $standard["name"], array("id" => $standard["id"]));
			}
			$this->output->close_tag();

			if (($impact = json_decode($case["impact"], true)) == null) {
				$impact = array();
			}
			unset($case["impact"]);

			$this->output->record($case, "case");

			$this->output->open_tag("impact");
			foreach ($this->model->risk_matrix_impact as $i => $label) {
				$this->output->add_tag("value", $impact[$i], array("label" => $label));
			}
			$this->output->close_tag();

			$this->output->close_tag();
		}

		public function execute() {
			$this->output->title = "Casussen";

			if ($_SERVER["REQUEST_METHOD"] == "POST") {
				if ($_POST["submit_button"] == "Casus opslaan") {
					if (is_array($_POST["impact"]) == false) {
						$_POST["impact"] = array();
					} else {
						$_POST["impact"] = array_values($_POST["impact"]);
						$_POST["impact"] = array_slice($_POST["impact"], 0, 5);
					}
					$_POST["impact"] = json_encode($_POST["impact"]);

					/* Save case
					 */
					if ($this->model->save_oke($_POST) == false) {
						$this->show_case_form($_POST);
					} else if (isset($_POST["id"]) === false) {
						/* Create case
						 */
						if ($this->model->create_case($_POST) == false) {
							$this->output->add_message("Fout bij aanmaken casus.");
							$this->show_case_form($_POST);
						} else {
							$this->user->log_action("case created");
							$this->show_overview();
						}
					} else {
						/* Update case
						 */
						if ($this->valid_case_id($_POST["id"]) == false) {
							$this->output->add_message("Ongeldige case id.");
							$this->show_case_form($_POST);
						} else if ($this->model->update_case($_POST) === false) {
							$this->output->add_message("Fout tijdens bijwerken casus.");
							$this->show_case_form($_POST);
						} else {
							$this->user->log_action("case updated");
							$this->show_overview();
						}
					}
				} else if ($_POST["submit_button"] == "Casus verwijderen") {
					/* Delete case
					 */
					if ($this->valid_case_id($_POST["id"]) == false) {
						$this->output->add_message("Ongeldige case id.");
						$this->show_case_form($_POST);
					} else if ($this->model->delete_case($_POST["id"]) == false) {
						$this->output->add_message("Fout bij verwijderen casus.");
						$this->show_case_form($_POST);
					} else {
						$this->user->log_action("case deleted");
						$this->show_overview();
					}
				} else if ($_POST["submit_button"] == "Zet zichtbaarheid") {
					$this->model->set_visibility($_POST["visible"]);
					$this->show_overview();
				} else if ($_POST["submit_button"] == "Toon alle casussen") {
					$this->model->show_all_cases();
					$this->show_overview();
				} else {
					$this->show_overview();
				}
			} else if ($this->page->pathinfo[1] === "new") {
				/* New case
				 */
				$impact = str_replace("{E}", EURO, DEFAULT_IMPACT);
				$impact = json_encode(config_array($impact, false));
				$case = array(
					"date"   => date("Y-m-d"),
					"impact" => $impact);
				$this->show_case_form($case);
			} else if (valid_input($this->page->pathinfo[1], VALIDATE_NUMBERS, VALIDATE_NONEMPTY)) {
				/* Edit case
				 */
				if (($case = $this->model->get_case($this->page->pathinfo[1])) === false) {
					$this->output->add_tag("result", "Opdracht niet gevonden.\n");
				} else {
					$this->show_case_form($case);
				}
			} else {
				/* Show overview
				 */
				$this->show_overview();
			}
		}
	}
?>
