<?php
	/* Copyright (c) by Hugo Leisink <hugo@leisink.net>
	 * Licensed under the RAVIB license.
	 */

	class voortgang_export_controller extends process_controller {
		public function execute() {
			$case_id = $this->page->pathinfo[2];
			if ($this->valid_case_id($case_id) == false) {
				return;
			}

			if (($progress = $this->model->get_progress($case_id)) === false) {
				return;
			}

			if (($standard = $this->model->get_standard($this->case["iso_standard_id"])) === false) {
				return;
			}

			$csv = new csvfile();
			$csv->add_line("#", "Maatregel uit ".$standard["name"], "Risico", "Toegewezen aan", "Deadline", "Gereed", "Uren gepland", "Uren geïnvesteerd");
			foreach ($progress as $task) {
				$task["name"] = utf8_encode($task["name"]);
				if ($task["deadline"] != "") {
					$task["deadline"] = date("j M Y", $task["deadline"]);
				}
				$task["done"] = is_true($task["done"]) ? "ja" : "nee";

				$line = array($task["number"], $task["name"], $task["risk"], $task["person"], $task["deadline"], $task["done"], $task["hours_planned"] + 0, $task["hours_invested"] + 0);
				$csv->add_line($line);
			}

			/* Output
			 */
			$this->output->disable();
			$case_name = $this->generate_filename($this->case["name"]);
			header("Content-Type: text/csv");
			header("Content-Disposition: attachment; filename=\"".$case_name.".csv\"");
			print $csv->to_string();

		}
	}
?>
