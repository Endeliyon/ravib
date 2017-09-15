<?php
	/* Copyright (c) by Hugo Leisink <hugo@leisink.net>
	 * Licensed under the RAVIB license.
	 */

	class voortgang_controller extends process_controller {
		private function show_measures($case_id) {
			if (isset($_GET["order"])) {
				$_SESSION["progress_order"] = $_GET["order"];
			} else if ($_SESSION["progress_order"] == null) {
				$_SESSION["progress_order"] = "isonr";
			}

			$this->show_breadcrumbs($case_id);

			$this->output->add_javascript("voortgang.js");

			if (($measures = $this->model->get_measures($case_id)) === false) {
				$this->output->add_tag("result", "Error reading measures.\n");
				return;
			}

			if (($standard = $this->model->get_standard($this->case["iso_standard_id"])) === false) {
				$this->output->add_tag("result", "Error fetching ISO standard.\n");
				return;
			}

			if (($measure_categories = $this->model->get_measure_categories($this->case["iso_standard_id"])) === false) {
				$this->output->add_tag("result", "Error fetching measure categories.\n");
				return;
			}

			usort($measures, array($this->model, "sort_measures"));

			$today = strtotime("today");

			$done = $pending = $overdue = $idle = 0;
			foreach ($measures as $i => $measure) {
				if ($measure["done"]) {
					$done++;
				} else if ($measure["deadline"] == null) {
					$idle++;
				} else if ($measure["deadline"] < $today) {
					$overdue++;
					$measures[$i]["overdue"] = true;
				} else {
					$pending++;
				}
			}

			$this->output->open_tag("overview");

			$total = count($measures);
			$done = round(100 * $done / $total);
			$overdue = round(100 * $overdue / $total);
			$pending = round(100 * $pending / $total);
			$idle = round(100 * $idle / $total);

			if (($delta = ($done + $overdue + $pending + $idle - 100)) != 0) {
				$values = array(
					"done" => $done,
					"overdue" => $overdue,
					"pending" => $pending,
					"idle"    => $idle);
				arsort($values);
				$key = key($values);
				$$key -= $delta;
			}

			$this->output->add_tag("done", $done);
			$this->output->add_tag("overdue", $overdue);
			$this->output->add_tag("pending", $pending);
			$this->output->add_tag("idle", $idle);

			$categories = array();
			foreach ($measures as $i => $measure) {
				list($section) = explode(".", $measure["number"]);
				if (isset($categories[$section]) == false) {
					$categories[$section] = array(0, 0);
				}
				if ($measure["done"]) {
					$categories[$section][0]++;
				}
				$categories[$section][1]++;
			}

			$this->output->open_tag("categories");
			foreach ($categories as $key => $category) {
				$args = array("key" => $key.": ".$measure_categories[$key]." (".$category[0]." / ".$category[1].")");
				$this->output->add_tag("category", round(100 * $category[0] / $category[1]), $args);
			}
			$this->output->close_tag();

			$params = array(
				"case_id" => $case_id,
				"iso"     => $standard["name"]);
			$this->output->open_tag("measures", $params);

			$main_cat_id = 0;

			foreach ($measures as $measure) {
				list($id) = explode(".", $measure["number"], 2);
				if ($_SESSION["progress_order"] != "isonr") {
					$args = array();
				} else if ($id != $main_cat_id) {
					$args = array("category" => $measure_categories[$id]);
					$main_cat_id = $id;
				} else {
					$args = array();
				}

				$measure["relevant"] = show_boolean($measure["relevant"]);
				$measure["overdue"] = show_boolean($measure["overdue"]);
				if ($measure["deadline"] != null) {
					$measure["deadline"] = date("j M Y", $measure["deadline"]);
				}
				$measure["done"] = show_boolean($measure["done"]);
				$this->output->record($measure, "measure", $args);
			}

			$this->output->close_tag();

			$this->output->close_tag();
		}

		private function edit_progress($case_id, $progress) {
			if (($people = $this->model->get_people($case_id)) === false) {
				return;
			}

			if (count($people) == 0) {
				$this->output->add_message("Je hebt nog geen personen toegevoegd aan dit project. Ga daarvoor een scherm terug en klik onderaan op de knop 'Personen'.");
			}

			if (($measure = $this->model->get_iso_measure($progress["iso_measure_id"])) === false) {
				return;
			}

			$this->output->add_javascript("banshee/datepicker.js");

			$this->output->open_tag("edit");

			$this->output->open_tag("people");
			$this->output->record(array("name" => ""), "person", array("id" => 0));
			foreach ($people as $person) {
				$this->output->record($person, "person");
			}
			$this->output->close_tag();

			$this->output->open_tag("progress");
			$this->output->add_tag("case_id", $case_id);
			$this->output->add_tag("actor_id", $progress["actor_id"]);
			$this->output->add_tag("reviewer_id", $progress["reviewer_id"]);
			$this->output->add_tag("iso_measure_id", $progress["iso_measure_id"]);
			$this->output->add_tag("measure", sprintf("%s %s", $measure["number"], $measure["name"]));
			$this->output->add_tag("deadline", $progress["deadline"]);
			$this->output->add_tag("info", $progress["info"]);
			$this->output->add_tag("done", show_boolean($progress["done"]));
			$this->output->add_tag("hours_planned", $progress["hours_planned"] + 0);
			$this->output->add_tag("hours_invested", $progress["hours_invested"] + 0);
			$this->output->close_tag();

			$this->output->close_tag();
		}

		public function execute() {
			if ($this->user->username == "demo") {
				$this->output->add_system_warning("Vanuit het demo-account worden geen notificaties verstuurd.");
			}

			$case_id = $this->page->pathinfo[1];
			if ($this->valid_case_id($case_id) == false) {
				return;
			}

			$this->output->add_javascript("jquery/jquery-ui.js");
			$this->output->add_css("jquery/jquery-ui.css");

			if ($_SERVER["REQUEST_METHOD"] == "POST") {
				/* Save progress
				 */
				if ($this->model->progress_oke($case_id, $_POST) == false) {
					$this->edit_progress($case_id, $_POST);
				} else if ($this->model->save_progress($case_id, $_POST) == false) {
					$this->output->add_message("Fout bij opslaan van de voortgang.");
					$this->edit_progress($case_id, $_POST);
				} else {
					if ($_POST["submit_button"] == "Opslaan met notificatie") {
						$this->model->send_notifications($case_id, $_POST);
					}
					$this->show_measures($case_id);
				}
			} else if (valid_input($this->page->pathinfo[2], VALIDATE_NUMBERS, VALIDATE_NONEMPTY) == false) {
				/* Show overview
				 */
				$this->show_measures($case_id);
			} else if (($progress = $this->model->get_progress($case_id, $this->page->pathinfo[2])) === false) {
				/* Progress not found
				 */
				$this->output->add_tag("result", "Voortgang niet gevonden.", array("url" => $this->page->module."/".$case_id));
			} else {
				/* Progress form
				 */
				$progress["iso_measure_id"] = $this->page->pathinfo[2];
				$this->edit_progress($case_id, $progress);
			}
		}
	}
?>
