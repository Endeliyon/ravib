<?php
	/* Copyright (c) by Hugo Leisink <hugo@leisink.net>
	 * Licensed under the RAVIB license.
	 */

	class pia_pia_controller extends pia_controller {
		private function show_rule($rule) {
			if (($answer = $this->model->get_pia_answer($this->pia["id"], $rule["number"])) != false) {
				$rule["answer"] = show_boolean($answer["answer"]);
				$rule["comment"] = $answer["comment"];
				$this->output->run_javascript("show_answer('".$rule["answer"]."')");
			}

			$max = $this->model->count_pia_rules();
			$rule["percentage"] = round(100 * ($rule["id"] - 1) / $max);

			$this->output->record($rule, "rule", array("case_id" => $this->pia["id"]));
		}

		public function execute() {
			$pia_id = $this->page->pathinfo[2];
			if ($this->valid_pia_id($pia_id) == false) {
				return;
			}

			$this->show_breadcrumbs($pia_id);

			if (isset($_SESSION["pia_current"]) == false) {
				if (($trace = $this->model->trace_pia($pia_id)) == false) {
					$_SESSION["pia_current"] = PIA_BEGIN;
				} else {
					$last = array_pop($trace);
					$_SESSION["pia_current"] = $last[$last["answer"]."_next"];
				}

			}
			$current_rule = &$_SESSION["pia_current"];

			if ($_SERVER["REQUEST_METHOD"] == "POST") {
				if ($_POST["submit_button"] == "Verder") {
					/* Verder
					 */
					if ($this->model->valid_answer($_POST["answer"]) == false) {
						$this->output->add_message("Kies uw antwoord.");
					} else if ($this->model->save_answer($pia_id, $current_rule, $_POST) == false) {
						$this->output->add_message("Fout tijdens opslaan van antwoord.");
					} else if (($current_rule = $this->model->next_rule($current_rule, $_POST["answer"])) == false) {
						$this->output->add_message("Fout bij ophalen van volgende vraag.");
					}
				} else if ($_POST["submit_button"] == "Terug") {
					/* Terug
					 */
					if (($trace = $this->model->trace_pia($pia_id)) == false) {
						$current_rule = PIA_BEGIN;
					} else foreach ($trace as $item) {
						if ($item[$item["answer"]."_next"] === $current_rule) {
							$current_rule = $item["number"];
							break;
						}
					}
				} else if ($_POST["submit_button"] == "Naar begin") {
					/* Naar begin
					 */
					$current_rule = PIA_BEGIN;
				}
			}

			$this->output->add_javascript("pia/pia.js");

			if ($current_rule == PIA_END) {
				$this->output->add_tag("ready", "", array("case_id" => $pia_id));
			} else if (($rule = $this->model->get_pia_rule($current_rule)) == false) {
				$this->output->add_tag("result", "Fout bij ophalen PIA regel.", array("url" => "pia/casus"));
			} else {
				$this->show_rule($rule);
			}
		}
	}
?>
