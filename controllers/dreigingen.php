<?php
	/* Copyright (c) by Hugo Leisink <hugo@leisink.net>
	 * Licensed under the RAVIB license.
	 */

	class dreigingen_controller extends process_controller {
		private function save_input($case_id, $key, $value) {
			$this->output->add_tag("key", $key);

			if ($this->valid_case_id($case_id) == false) {
				$this->output->add_tag("result", "error");
				return;
			}

			if ($this->model->save_value($case_id, $key, $value) === false) {
				$this->output->add_tag("result", "error");
				return;
			}

			$this->output->add_tag("result", "oke");
		}

		private function save_bia_threat($case_id, $bia_id, $threat_id) {
			$this->output->add_tag("key", "bt_".$bia_id."_".$threat_id);

			if ($this->valid_case_id($case_id) == false) {
				$this->output->add_tag("result", "error");
				return;
			}

			if ($this->model->save_bia_threat($case_id, $bia_id, $threat_id) === false) {
				$this->output->add_tag("result", "error");
				return;
			}

			$this->output->add_tag("result", "oke");
		}

		private function show_form() {
			$case_id = $this->page->pathinfo[1];
			if ($this->valid_case_id($case_id) == false) {
				return;
			}

			$this->show_breadcrumbs($case_id);

			/* Get threat information
			 */
			if (($categories = $this->model->get_threat_categories()) === false) {
				$this->output->add_tag("result", "Error getting threats", array("url" => ""));
				return;
			} else if (($threats = $this->model->get_threats($case_id)) === false) {
				$this->output->add_tag("result", "Error getting threats", array("url" => ""));
				return;
			} else if (($bia_items = $this->model->get_bia_items($case_id)) === false) {
				$this->output->add_tag("result", "Error getting BIA items", array("url" => ""));
				return;
			} else if (($bia_threat = $this->model->get_bia_threat($case_id)) === false) {
				$this->output->add_tag("result", "Error getting BIA/threat links", array("url" => ""));
				return;
			}

			/* Build form
			 */
			$this->output->add_javascript("jquery/jquery-ui.js");
			$this->output->add_javascript("dreigingen.js");

			$this->output->open_tag("riskanalysis", array("case_id" => $this->page->pathinfo[1]));

			/* Chance values
			 */
			$this->output->open_tag("chance");
			$this->output->add_tag("value", "", array("value" => 0));
			foreach ($this->model->risk_matrix_chance as $value => $label) {
				$this->output->add_tag("value", $label, array("value" => $value + 1));
			}
			$this->output->close_tag();

			/* Impact values
			 */
			$this->output->open_tag("impact");
			$this->output->add_tag("value", "", array("value" => 0));
			foreach ($this->model->risk_matrix_impact as $value => $label) {
				$this->output->add_tag("value", $label, array("value" => $value + 1));
			}
			$this->output->close_tag();

			/* Handle values
			 */
			$this->output->open_tag("handle");
			$this->output->add_tag("option", "");
			foreach ($this->model->threat_handle_labels as $option) {
				$this->output->add_tag("option", $option);
			}
			$this->output->close_tag();

			$cia = array("confidentiality", "integrity", "availability");

			$category_id = 0;
			$this->output->open_tag("threats");
			foreach ($threats as $threat) {
				foreach ($cia as $type) {
					if ($threat[$type] == "-") {
						$threat[$type] = "";
					}
				}

				$threat["chance"] = (int)$threat["chance"];
				$threat["impact"] = (int)$threat["impact"];

				$args = array();
				if ($threat["category_id"] != $category_id) {
					$category_id = $threat["category_id"];
					$args["category"] = $categories[$category_id - 1]["name"];
				}

				$threat["systems"] = array();
				foreach ($bia_items as $item) {
					if (($checked = $bia_threat[(int)$threat["id"]]) == null) {
						$checked = array();
					}

					$level = 1;
					foreach ($cia as $type) {
						if ($threat[$type] == "p") {
							if ($item[$type] > $level) {
								$level = min((int)$item[$type], 3);
							}
						} else if ($threat[$type] == "s") {
							if (($item[$type] >= 3) && ($level == 1)) {
								$level = 2;
							}
						}
					}

					array_push($threat["systems"], array(
						"id"      => $item["id"],
						"checked" => show_boolean(in_array($item["id"], $checked)),
						"level"   => $level));
				}

				$this->output->record($threat, "threat", $args, true);
			}
			$this->output->close_tag();

			/* BIA items
			 */
			$this->output->open_tag("bia");
			foreach ($bia_items as $item) {
				$this->output->add_tag("item", $item["item"], array("id" => $item["id"]));
			}
			$this->output->close_tag();

			$this->output->close_tag();
		}

		private function export_threats() {
			$case_id = $this->page->pathinfo[1];
			if ($this->valid_case_id($case_id) == false) {
				return;
			}

			if (($threats = $this->model->get_threats($case_id)) === false) {
				$this->output->add_tag("result", "Error getting threats", array("url" => ""));
				return;
			}

			$csv = new csvfile();
			$csv->add_line("#", "Dreiging", "Kans", "Impact", "Aanpak",
			               "Huidige situatie / huidige maatregelen",
			               "Gewenste situatie / te nemen acties",
			               "Argumentatie voor gemaakte keuze");

			$chance = config_array(RISK_MATRIX_CHANCE);
			$impact = config_array(RISK_MATRIX_IMPACT);

			foreach ($threats as $threat) {	
				$threat["chance"] = $chance[$threat["chance"] - 1];
				$threat["impact"] = $impact[$threat["impact"] - 1];

				$csv->add_line(array($threat["id"], $threat["threat"],
				              $threat["chance"], $threat["impact"], $threat["handle"],
				              $threat["current"], $threat["action"], $threat["argumentation"]));
			}

			$case_name = $this->generate_filename($this->case["name"]);

			$this->output->disable();
			header("Content-Type: text/csv");
			header("Content-Disposition: attachment; filename=\"".$case_name." dreigingen.csv\"");
			print $csv->to_string();
		}

		public function execute() {
			if ($this->page->ajax_request) {
				if (isset($_POST["key"])) {
					$this->save_input($_POST["case_id"], $_POST["key"], $_POST["value"]);
				} else if (isset($_POST["bia_id"])) {
					$this->save_bia_threat($_POST["case_id"], $_POST["bia_id"], $_POST["threat_id"]);
				} else {
					$this->output->add_tag("result", "error");
				}
			} else if ($_POST["submit_button"] == "Exporteer dreigingen") {
				$this->export_threats();
			} else {
				$this->show_form();
			}
		}
	}
?>
