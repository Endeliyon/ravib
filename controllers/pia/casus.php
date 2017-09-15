<?php
	/* Copyright (c) by Hugo Leisink <hugo@leisink.net>
	 * Licensed under the RAVIB license.
	 */

	class pia_casus_controller extends pia_controller {
		private function show_overview() {
			if ($this->user->username == "demo") {
				$this->output->add_system_warning("De gegevens in dit demo-account worden elke nacht gewist. U wordt daarbij uitgelogd.");
				$this->output->add_system_warning("Let op, andere gebruikers van dit demo-account kunnen alle gegevens in dit account inzien!");
			}

			if (($cases = $this->model->get_pia_cases()) === false) {
				$this->output->add_tag("result", "Database error.");
				return;
			}

			$this->output->open_tag("overview");
			foreach ($cases as $case) {
				$case["date"] = date_string("j F Y", $case["date"]);
				$this->output->record($case, "case");
			}

			$this->output->close_tag();
		}

		private function show_case_form($case) {
			$this->output->add_css("jquery/jquery-ui.css");

			$this->output->add_javascript("jquery/jquery-ui.js");
			$this->output->add_javascript("banshee/datepicker.js");

			$this->output->open_tag("edit");
			$this->output->record($case, "case");
			$this->output->close_tag();
		}

		public function execute() {
			$this->output->title = "Casussen";
			$_SESSION["pia_current"] = $this->model->first_rule;

			if ($_SERVER["REQUEST_METHOD"] == "POST") {
				if ($_POST["submit_button"] == "Casus opslaan") {
					/* Save case
					 */
					if ($this->model->save_oke($_POST) == false) {
						$this->show_case_form($_POST);
					} else if (isset($_POST["id"]) === false) {
						/* Create case
						 */
						if ($this->model->create_pia_case($_POST) == false) {
							$this->output->add_message("Fout bij aanmaken casus.");
							$this->show_case_form($_POST);
						} else {
							$this->user->log_action("case created");
							$this->show_overview();
						}
					} else {
						/* Update case
						 */
						if ($this->valid_pia_id($_POST["id"]) == false) {
							$this->output->add_message("Ongeldige case id.");
							$this->show_case_form($_POST);
						} else if ($this->model->update_pia_case($_POST) === false) {
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
					if ($this->valid_pia_id($_POST["id"]) == false) {
						$this->output->add_message("Ongeldige case id.");
						$this->show_case_form($_POST);
					} else if ($this->model->delete_pia_case($_POST["id"]) == false) {
						$this->output->add_message("Fout bij verwijderen casus.");
						$this->show_case_form($_POST);
					} else {
						$this->user->log_action("case deleted");
						$this->show_overview();
					}
				} else {
					$this->show_overview();
				}
			} else if ($this->page->pathinfo[2] === "new") {
				/* New case
				 */
				$case = array("date" => date("Y-m-d"));
				$this->show_case_form($case);
			} else if (valid_input($this->page->pathinfo[2], VALIDATE_NUMBERS, VALIDATE_NONEMPTY)) {
				/* Edit case
				 */
				if (($case = $this->model->get_pia_case($this->page->pathinfo[2])) === false) {
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
