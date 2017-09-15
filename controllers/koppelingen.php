<?php
	/* Copyright (c) by Hugo Leisink <hugo@leisink.net>
	 * Licensed under the RAVIB license.
	 */

	class koppelingen_controller extends controller {
		public function execute() {
			if (isset($_SESSION["iso_standard"]) == false) {
				$_SESSION["iso_standard"] = $this->settings->default_iso_standard;
			}

			if ($_SERVER["REQUEST_METHOD"] == "POST") {
				$_SESSION["iso_standard"] = $_POST["iso_standard"];
			}

			$this->output->title = "Koppelingen tussen dreigingen en maatregelen";
			$this->output->keywords = "koppelingen";

			$this->output->add_javascript("koppelingen.js");

			if (($standards = $this->model->get_iso_standards()) == false) {
				$this->output->add_tag("result", "Error retrieving ISO standards.");
				return;
			}

			if (($categories = $this->model->get_threat_categories()) == false) {
				$this->output->add_tag("result", "Error getting threat categories.");
				return;
			}

			if (($threats = $this->model->get_threats()) == false) {
				$this->output->add_tag("result", "Error getting threats.");
				return;
			}

			if (($measures = $this->model->get_measures($_SESSION["iso_standard"])) == false) {
				$this->output->add_tag("result", "Error getting ISO measures.");
				return;
			}

			if (($controls = $this->model->get_controls($_SESSION["iso_standard"])) == false) {
				$this->output->add_tag("result", "Error getting controls.");
				return;
			}

			$this->output->open_tag("links");

			if (count($standards) > 1) {
				$this->output->open_tag("iso_standards");
				foreach ($standards as $standard) {
					$params = array(
						"id"       => $standard["id"],
						"selected" => show_boolean($standard["id"] == $_SESSION["iso_standard"]));
					$this->output->add_tag("standard", $standard["name"], $params);
				}
				$this->output->close_tag();
			}

			/* Threats
			 */
			$links = array();
			foreach ($controls as $control) {
				if (is_array($links[$control["threat_id"]]) == false) {
					$links[$control["threat_id"]] = array();
				}
				array_push($links[$control["threat_id"]], $control["iso_measure_id"]);
			}

			$this->output->open_tag("threats");
			$category_id = 0;
			foreach ($threats as $threat) {
				if ($threat["category_id"] != $category_id) {
					$category_id = $threat["category_id"];
					$this->output->add_tag("category", $categories[$category_id]);
				}

				$this->output->open_tag("threat", array("id" => $threat["id"]));
				$this->output->add_tag("number", $threat["number"]);
				$this->output->add_tag("threat", $threat["threat"]);
				$this->output->add_tag("description", $threat["description"]);
				if (is_array($links[$threat["id"]])) {
					foreach ($links[$threat["id"]] as $measure_id) {
						$measure = $measures[$measure_id];
						$this->output->add_tag("measure", $measure["number"]." ".$measure["name"]);
					}
				}
				$this->output->add_tag("confidentiality", $threat["confidentiality"]);
				$this->output->add_tag("integrity", $threat["integrity"]);
				$this->output->add_tag("availability", $threat["availability"]);
				$this->output->close_tag();

			}
			$this->output->close_tag();

			/* ISO measures
			 */
			$links = array();
			foreach ($controls as $control) {
				if (is_array($links[$control["iso_measure_id"]]) == false) {
					$links[$control["iso_measure_id"]] = array();
				}
				array_push($links[$control["iso_measure_id"]], $control["threat_id"]);
			}


			$this->output->open_tag("measures");
			foreach ($measures as $measure) {
				$this->output->open_tag("measure", array("id" => $measure["id"]));
				$this->output->add_tag("number", $measure["number"]);
				$this->output->add_tag("measure", $measure["name"]);

				if (is_array($links[$measure["id"]])) {
					sort($links[$measure["id"]]);
					foreach ($links[$measure["id"]] as $threat_id) {
						$threat = $threats[$threat_id];
						$this->output->add_tag("threat", $threat["number"].". ".$threat["threat"]);
					}
				} else {
					#printf("Error for measure number %d\n", $measure["id"]);
				}
				$this->output->close_tag();
			}
			$this->output->close_tag();

			$this->output->close_tag();
		}
	}
?>
