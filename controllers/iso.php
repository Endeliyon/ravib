<?php
	/* Copyright (c) by Hugo Leisink <hugo@leisink.net>
	 * Licensed under the RAVIB license.
	 */

	class iso_controller extends process_controller {
		public function execute() {
			$case_id = $this->page->pathinfo[1];
			if ($this->valid_case_id($case_id) == false) {
				return;
			}

			$this->show_breadcrumbs($case_id);

			if ($_SERVER["REQUEST_METHOD"] == "POST") {
				$this->model->update_overruled_measures($case_id, $_POST["iso_measures"]);
			}

			if (($measures = $this->model->get_measures($case_id)) === false) {
				$this->output->add_tag("result", "Error reading measures.\n");
				return;
			}

			if (($measure_categories = $this->model->get_measure_categories($this->case["iso_standard_id"])) === false) {
				$this->output->add_tag("result", "Error fetching measure categories.\n");
				return;
			}

			$this->output->add_javascript("iso.js");

			if (($standard = $this->model->get_standard($this->case["iso_standard_id"])) === false) {
				$this->output->add_tag("result", "Error fetching ISO standard.\n");
				return;
			}
			$params = array(
				"case_id" => $case_id,
				"iso"     => $standard["name"]);

			$main_cat_id = 0;

			$this->output->open_tag("measures", $params);
			foreach ($measures as $measure) {
				$args = array(
					"id"        => $measure["id"],
					"relevant"  => show_boolean($measure["relevant"]),
					"overruled" => show_boolean($measure["overruled"]),
					"select"    => $measure["relevant"] == $measure["overruled"] ? "meenemen" : "negeren");

				list($id) = explode(".", $measure["number"], 2);
				if ($id != $main_cat_id) {
					$args["category"] = $measure_categories[$id];
					$main_cat_id = $id;
				}

				$this->output->open_tag("measure", $args);

				$this->output->add_tag("number", $measure["number"]);
				$this->output->add_tag("name", $measure["name"]);

				foreach ($measure["threats"] as $threat) {
					$param = array(
						"number"   => $threat["number"],
						"risk"     => $threat["risk"],
						"handle"   => $threat["handle"],
						"relevant" => show_boolean($threat["relevant"]));
					$this->output->add_tag("threat", $threat["threat"], $param);
				}

				$this->output->add_tag("risk", $measure["risk"]);

				$this->output->close_tag();
			}
			$this->output->close_tag();
		}
	}
?>
