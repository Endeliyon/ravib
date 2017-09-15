<?php
	/* Copyright (c) by Hugo Leisink <hugo@leisink.net>
	 * Licensed under the RAVIB license.
	 */

	class vergelijk_controller extends process_controller {
		private function show_cases() {
			if (($cases = $this->model->get_cases()) === false) {
				$this->output->add_tag("result", "Database error.");
				return false;
			}

			$this->output->open_tag("overview");
			foreach ($cases as $case) {
				$this->output->record($case, "case");
			}
			$this->output->close_tag();
		}

		private function compare_cases($case_ids) {
			if (count($case_ids) < 2) {
				$this->output->add_message("Kies minimaal 2 casussen.");
				$this->show_cases();
				return;
			}

			sort($case_ids);

			$case_threats = array();
			foreach ($case_ids as $case_id) {
				if (($case = $this->model->get_case_threats($case_id)) === false) {
					$this->output->add_tag("result", "Database error.");
					return;
				}

				array_push($case_threats, $case);
			}

			if (($cases = $this->model->get_cases()) === false) {
				$this->output->add_tag("result", "Database error.");
				return;
			}

			if (($threats = $this->model->get_threats()) == false) {
				$this->output->add_tag("result", "Database error.");
				return;
			}

			/* Same ISO standard used?
			 */
			$iso_standard_id = -1;
			foreach ($cases as $case) {
				if (in_array($case["id"], $case_ids) == false) {
					continue;
				}
				if ($iso_standard_id == -1) {
					$iso_standard_id = $case["iso_standard_id"];
				} else if ($iso_standard_id != $case["iso_standard_id"]) {
					$this->output->add_message("Kies casussen met dezelfde ISO standaard.");
					$this->show_cases();
					return;
				}
			}

			/* Show comparison
			 */
			$this->output->open_tag("compare");
			
			$this->output->open_tag("cases");
			foreach ($case_ids as $case_id) {
				foreach ($cases as $case) {
					if ($case["id"] == $case_id) {
						$this->output->add_tag("case", $case["name"]);
					}
				}
			}
			$this->output->close_tag();

			foreach ($threats as $threat) {
				$this->output->open_tag("threat", array("id" => $threat["number"]));
				$this->output->add_tag("threat", $threat["threat"]);
				$this->output->open_tag("cases");
				foreach ($case_threats as $case) {
					$chance = $case[$threat["number"]]["chance"] - 1;
					$impact = $case[$threat["number"]]["impact"] - 1;
					$accept = show_boolean($case[$threat["number"]]["handle"] == $this->model->threat_handle_labels[THREAT_ACCEPT]);
					$risk = $this->model->risk_matrix_labels[$this->model->risk_matrix[$chance][$impact]];
					$this->output->add_tag("case", $risk, array("accept" => $accept));
				}
				$this->output->close_tag();
				$this->output->close_tag();
			}
			$this->output->close_tag();
		}

		public function execute() {
			if ($_SERVER["REQUEST_METHOD"] == "POST") {
				$this->compare_cases($_POST["compare"]);
			} else {
				$this->show_cases();
			}
		}
	}
?>
