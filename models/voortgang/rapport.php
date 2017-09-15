<?php
	/* Copyright (c) by Hugo Leisink <hugo@leisink.net>
	 * Licensed under the RAVIB license.
	 */

	class voortgang_rapport_model extends process_model {
		public function get_threats($case_id) {
			return $this->borrow("dreigingen")->get_threats($case_id, false);
		}

		public function get_measure_categories($standard_id) {
			return $this->borrow("iso")->get_measure_categories($standard_id);
		}

		public function get_measures($case_id) {
			return $this->borrow("voortgang")->get_measures($case_id);
		}

		public function get_controls($iso_standard_id) {
			$query = "select * from controls c, iso_measures i where c.iso_measure_id=i.id and i.iso_standard_id=%d";

			if (($controls = $this->db->execute($query, $iso_standard_id)) === false) {
				return false;
			}

			$result = array();
			foreach ($controls as $control) {
				$id = $control["threat_id"];

				if (is_array($result[$id]) == false) {
					$result[$id] = array();
				}

				array_push($result[$id], $control["iso_measure_id"]);
			}

			ksort($result);

			return $result;
		}

		public function get_progress($case_id) {
			$query = "select * from progress_tasks where case_id=%d";

			if (($tasks = $this->db->execute($query, $case_id)) === false) {
				return false;
			}

			$result = array();
			foreach ($tasks as $task) {
				if (is_true($task["done"])) {
					array_push($result, $task["iso_measure_id"]);
				}
			}

			sort($result);

			return $result;
		}
	}
?>
