<?php
	/* Copyright (c) by Hugo Leisink <hugo@leisink.net>
	 * Licensed under the RAVIB license.
	 */

	class dreigingen_model extends process_model {
		public function get_threat_categories() {
			$query = "select * from threat_categories";

			return $this->db->execute($query);
		}

		public function get_threats($case_id, $get_measures = true) {
			$query = "select t.*, i.chance, i.impact, i.handle, i.action, i.current, i.argumentation ".
			         "from threats t left join case_threat i on i.threat_id=t.id and i.case_id=%d ".
			         "order by t.number";

			if (($threats = $this->db->execute($query, $case_id)) === false) {
				return false;
			}

			if ($get_measures) {
				$query = "select m.* from iso_measures m, controls c, cases a ".
						 "where m.id=c.iso_measure_id and a.id=%d and a.iso_standard_id=m.iso_standard_id and c.threat_id=%d ".
						 "order by id";
				foreach ($threats as $i => $threat) {
					if (($threats[$i]["measures"] = $this->db->execute($query, $case_id, $threat["id"])) === false) {
						return false;
					}
				}
			}

			return $threats;
		}

		public function get_bia_items($case_id) {
			return $this->borrow("bia")->get_items($case_id);
		}

		public function get_bia_threat($case_id) {
			$query = "select * from case_bia_threat where case_id=%d";

			if (($bia_threat = $this->db->execute($query, $case_id)) === false) {
				return false;
			}

			$result = array();

			foreach ($bia_threat as $item) {
				$bia_id = (int)$item["bia_id"];
				$threat_id = (int)$item["threat_id"];

				if (is_array($result[$threat_id]) == false) {
					$result[$threat_id] = array();
				}
				array_push($result[$threat_id], $bia_id);
			}

			return $result;
		}

		public function save_value($case_id, $key, $value) {
			list($key, $threat_id) = explode("_", $key);

			if (in_array($key, array("chance", "impact", "handle", "action", "current", "argumentation")) == false) {
				return false;
			}

			$query = "select id from case_threat where case_id=%d and threat_id=%d limit 1";
			if (($result = $this->db->execute($query, $case_id, $threat_id)) === false) {
				return false;
			}

			if (count($result) == 0) {
				/* Insert
				 */
				$data = array(
					"id"            => null,
					"case_id"       => (int)$case_id,
					"threat_id"     => (int)$threat_id,
					"chance"        => null,
					"impact"        => null,
					"handle"        => null,
					"action"        => null,
					"current"       => null,
					"argumentation" => null);
				$data[$key] = $value;

				return $this->db->insert("case_threat", $data);
			} else {
				/* Update
				 */
				$id = (int)$result[0]["id"];

				$data = array($key => $value);
				$data[$key] = $value;

				return $this->db->update("case_threat", $id, $data);
			}
		}

		public function save_bia_threat($case_id, $bia_id, $threat_id) {
			if ($this->db->execute("select * from bia where id=%d", $bia_id) == false) {
				return false;
			}

			$query = "select * from case_bia_threat where case_id=%d and bia_id=%d and threat_id=%d";

			if ($this->db->execute($query, $case_id, $bia_id, $threat_id) == false) {
				$data = array("case_id" => $case_id, "bia_id" => $bia_id, "threat_id" => $threat_id);
				if ($this->db->insert("case_bia_threat", $data) === false) {	
					return false;
				}
			} else {
				$query = "delete from case_bia_threat where case_id=%d and bia_id=%d and threat_id=%d";
				if ($this->db->query($query, $case_id, $bia_id, $threat_id) === false) {
					return false;
				}
			}

			return true;
		}
	}
?>
