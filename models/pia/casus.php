<?php
	/* Copyright (c) by Hugo Leisink <hugo@leisink.net>
	 * Licensed under the RAVIB license.
	 */

	class pia_casus_model extends pia_model {
		public function get_pia_cases() {
			$query = "select id, name, UNIX_TIMESTAMP(date) as date ".
			         "from pia_cases where organisation_id=%d order by date desc";

			return $this->db->execute($query, $this->user->organisation_id);
		}

		public function save_oke($case) {
			$result = true;

			if (trim($case["name"]) == "") {
				$this->output->add_message("Geef een naam op.");
				$result = false;
			} else {
				$query = "select count(*) as count from pia_cases where name=%s and organisation_id=%d";
				$params = array($case["name"], $this->user->organisation_id);

				if (isset($case["id"])) {
					$query .= " and id!=%d";
					array_push($params, $case["id"]);
				}

				if (($result = $this->db->execute($query, $params)) === false) {
					return false;
				}
				if ($result[0]["count"] > 0) {
					$this->output->add_message("Er bestaat al een zaak met deze naam.");
					$result = false;
				}
			}

			return $result;
		}

		public function create_pia_case($case) {
			$keys = array("id", "organisation_id", "name", "date", "description");

			$case["id"] = null;
			$case["organisation_id"] = $this->user->organisation_id;

			return $this->db->insert("pia_cases", $case, $keys);
		}

		public function update_pia_case($case) {
			$keys = array("name", "date", "description");

			return $this->db->update("pia_cases", $case["id"], $case, $keys);
		}

		public function delete_pia_case($case_id) {
			$queries = array(
				array("delete from pia where pia_case_id=%d", $case_id),
				array("delete from pia_cases where id=%d", $case_id));

			return $this->db->transaction($queries);
		}
	}
?>
