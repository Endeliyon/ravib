<?php
	/* Copyright (c) by Hugo Leisink <hugo@leisink.net>
	 * Licensed under the RAVIB license.
	 */

	class voortgang_gereed_controller extends process_controller {
		public function execute() {
			if ($_SERVER["REQUEST_METHOD"] == "POST") {
				$code = $_POST["code"];
			} else {
				$code = $this->page->pathinfo[2];
			}

			if (($data = $this->model->get_data($code)) == false) {
				$this->output->add_tag("result", "Ongeldige code.");
				return;
			}

			if (($task = $this->model->get_task($data["case_id"], $data["iso_measure_id"])) == false) {
				return;
			}

			if ($task["done"] == YES) {
				$this->output->add_tag("result", "Deze taak is reeds afgerond.");
				return;
			}

			if ($_SERVER["REQUEST_METHOD"] == "POST") {
				$this->model->mark_as_done($data["case_id"], $data["iso_measure_id"]);
				$this->model->send_notification($data["case_id"], $data["iso_measure_id"]);
				$this->output->add_tag("result", "De taak is gereedgemeld.");
			} else {
				$this->output->open_tag("form");
				$this->output->add_tag("code", $this->page->pathinfo[2]);
				$this->output->record($task);
				$this->output->close_tag();
			}
		}
	}
?>
