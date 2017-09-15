<?php
	/* Copyright (c) by Hugo Leisink <hugo@leisink.net>
	 * Licensed under the RAVIB license.
	 */

	class iso_model extends process_model {
		public function update_overruled_measures($case_id, $iso_measures) {
			$query = "delete from overruled where case_id=%d";
			if ($this->db->query($query, $case_id) === false) {
				return false;
			}

			if (is_array($iso_measures) == false) {
				return true;
			}

			foreach ($iso_measures as $iso_measure_id) {
				$data = array(
					"case_id"        => $case_id,
					"iso_measure_id" => $iso_measure_id);

				if ($this->db->insert("overruled", $data) === false) {
					return false;
				}
			}

			return false;
		}

		public function get_standard($standard_id) {
			return $this->borrow("cms/standards")->get_item($standard_id);
		}

		public function get_measures($case_id) {
			$query = "select m.*, o.case_id from cases c, iso_measures m ".
			         "left join overruled o on m.id=o.iso_measure_id and o.case_id=%d ".
			         "where c.id=%d and m.iso_standard_id=c.iso_standard_id";

			if (($measures = $this->db->execute($query, $case_id, $case_id)) === false) {
				return false;
			}

			/* Get threats
			 */
			$query = "select t.id, t.number, t.threat, i.chance, i.impact, i.handle, i.action, i.current, i.argumentation ".
			         "from controls c, threats t left join case_threat i ".
			         "on t.id=i.threat_id and i.case_id=%d ".
			         "where c.threat_id=t.id and c.iso_measure_id=%d ".
			         "order by t.number";
			foreach ($measures as $m => $measure) {
				if (($measures[$m]["threats"] = $this->db->execute($query, $case_id, $measure["id"])) === false) {
					return false;
				}

                $measures[$m]["overruled"] = $measure["case_id"] != null;
				unset($measures[$m]["case_id"]);
				$measures[$m]["relevant"] = false;
				$all_irrelevant = true;

				$highest_risk = -1;
				foreach ($measures[$m]["threats"] as $t => $threat) {
					if (($threat["chance"] == 0) || ($threat["impact"] == 0) || ($threat["handle"] == "")) {
						continue;
					}

					$risk = $this->risk_matrix[$threat["chance"] - 1][$threat["impact"] - 1];

					$measures[$m]["threats"][$t]["relevant"] = ($threat["handle"] != $this->threat_handle_labels[THREAT_ACCEPT]);
					if ($measures[$m]["threats"][$t]["relevant"]) {
						$all_irrelevant = false;
						if ($risk >= $highest_risk) {
							$highest_risk = $risk;
						}
						if ($measures[$m]["overruled"] == false) {
							$measures[$m]["relevant"] = true;
						}
					}

					$measures[$m]["threats"][$t]["risk"] = $this->risk_matrix_labels[$risk];
				}

				if ($all_irrelevant && ($measures[$m]["overruled"])) {
					$measures[$m]["relevant"] = true;
				}

				if ($highest_risk > -1) {
					$measures[$m]["risk_value"] = $highest_risk;
					$measures[$m]["risk"] = $this->risk_matrix_labels[$highest_risk];
				}
			}

			return $measures;
		}

		public function get_measure_categories($standard_id) {
			$query = "select * from iso_measure_categories where iso_standard_id=%d order by number";
			if (($categories = $this->db->execute($query, $standard_id)) === false) {
				return false;
			}

			$result = array();
			foreach ($categories as $category) {
				$result[(int)$category["number"]] = $category["name"];
			}

			return $result;
		}
	}
?>
