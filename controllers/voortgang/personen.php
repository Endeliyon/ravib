<?php
	/* Copyright (c) by Hugo Leisink <hugo@leisink.net>
	 * Licensed under the RAVIB license.
	 */

	class voortgang_personen_controller extends process_controller {
		private function show_overview($case_id) {
			if (($people = $this->model->get_people($case_id)) === false) {
				$this->output->add_tag("result", "Database error.");
				return;
			}

			$this->output->open_tag("overview");

			$this->output->open_tag("people", array("case_id" => $case_id));
			foreach ($people as $person) {
				$this->output->record($person, "person");
			}
			$this->output->close_tag();

			$this->output->close_tag();
		}

		private function show_person_form($case_id, $person) {
			$this->output->open_tag("edit", array("case_id" => $case_id));
			$this->output->record($person, "person");
			$this->output->close_tag();
		}

		public function execute() {
			$case_id = $this->page->pathinfo[2];
			if ($this->valid_case_id($case_id) == false) {
				return;
			}

			if ($_SERVER["REQUEST_METHOD"] == "POST") {
				if ($_POST["submit_button"] == "Persoon opslaan") {
					/* Save person 
					 */
					if ($this->model->save_oke($case_id, $_POST) == false) {
						$this->show_person_form($case_id, $_POST);
					} else if (isset($_POST["id"]) === false) {
						/* Create person 
						 */
						if ($this->model->create_person($case_id, $_POST) === false) {
							$this->output->add_message("Error creating person.");
							$this->show_person_form($case_id, $_POST);
						} else {
							$this->user->log_action("Person created");
							$this->show_overview($case_id);
						}
					} else {
						/* Update person
						 */
						if ($this->model->update_person($case_id, $_POST) === false) {
							$this->output->add_message("Error updating person.");
							$this->show_person_form($case_id, $_POST);
						} else {
							$this->user->log_action("Person updated");
							$this->show_overview($case_id);
						}
					}
				} else if ($_POST["submit_button"] == "Persoon verwijderen") {
					/* Delete person 
					 */
					if ($this->model->delete_oke($case_id, $_POST) == false) {
						$this->show_person_form($case_id, $_POST);
					} else if ($this->model->delete_person($_POST["id"]) === false) {
						$this->output->add_message("Error deleting person.");
						$this->show_person_form($case_id, $_POST);
					} else {
						$this->user->log_action("Person deleted");
						$this->show_overview($case_id);
					}
				} else if ($_POST["submit_button"] == "search") {
					/* Search
					 */
					$_SESSION["person_search"] = $_POST["search"];
					$this->show_overview($case_id);
				} else {
					$this->show_overview($case_id);
				}
			} else if ($this->page->pathinfo[3] === "new") {
				/* New person 
				 */
				$person = array();
				$this->show_person_form($case_id, $person);
			} else if (valid_input($this->page->pathinfo[3], VALIDATE_NUMBERS, VALIDATE_NONEMPTY)) {
				/* Edit person 
				 */
				if (($person = $this->model->get_person($case_id, $this->page->pathinfo[3])) === false) {
					$this->output->add_tag("result", "Person not found.\n");
				} else {
					$this->show_person_form($case_id, $person);
				}
			} else {
				/* Show overview
				 */
				$this->show_overview($case_id);
			}
		}
	}
?>
