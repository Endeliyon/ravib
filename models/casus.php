<?php
	/* Copyright (c) by Hugo Leisink <hugo@leisink.net>
	 * Licensed under the RAVIB license.
	 */

	class casus_model extends process_model {
		public function get_cases() {
			$query = "select c.id, c.name, c.organisation, c.iso_standard_id, UNIX_TIMESTAMP(c.date) as date, s.name as iso_standard, ".
			         "(select count(*) from bia where case_id=c.id) as bia_count, ".
			         "(select count(*) from case_threat where case_id=c.id) as threat_count, ".
			         "(select count(*) from overruled where case_id=c.id) as overruled_count, ".
			         "(select count(*) from progress_people where case_id=c.id) as task_count ".
			         "from cases c, iso_standards s ".
			         "where c.iso_standard_id=s.id and c.organisation_id=%d and visible=%d order by date desc";

			return $this->db->execute($query, $this->user->organisation_id, YES);
		}

		public function get_iso_standards() {
			$query = "select * from iso_standards";
			if ($this->user->is_admin == false) {
				$query .= " where active=%d";
			}
			$query .= " order by id desc";

			return $this->db->execute($query, YES);
		}

		public function start_crumb($case) {
			if ($case["bia_count"] == 0) {
				return "bia";
			}
			
			if ($case["threat_count"] < 50) {
				return "dreigingen";
			}

			if ($case["overruled_count"] == 0) {
				return "iso";
			}

			if ($case["task_count"] == 0) {
				return "rapport";
			}

			return "voortgang";
		}

		public function save_oke($case) {
			$result = true;

			if (trim($case["name"]) == "") {
				$this->output->add_message("Geef de naam van de casus op.");
				$result = false;
			}
			
			if (trim($case["organisation"]) == "") {
				$this->output->add_message("Geef de naam van de organisatie op.");
				$result = false;
			}

			if ($result) {
				$query = "select count(*) as count from cases where name=%s and organisation=%s and organisation_id=%d";
				$params = array($case["name"], $case["organisation"], $this->user->organisation_id);

				if (isset($case["id"])) {
					$query .= " and id!=%d";
					array_push($params, $case["id"]);
				}

				if (($result = $this->db->execute($query, $params)) === false) {
					return false;
				}
				if ($result[0]["count"] > 0) {
					$this->output->add_message("Er bestaat al een zaak met deze naam voor de opgegeven organisatie.");
					$result = false;
				}
			}

			return $result;
		}

		public function create_case($case) {
			$keys = array("id", "organisation_id", "iso_standard_id", "name", "organisation", "date", "scope", "impact", "logo", "visible");

			$case["id"] = null;
			$case["organisation_id"] = $this->user->organisation_id;
			if (trim($case["logo"]) == "") {	
				$case["logo"] = null;
			}
			$case["visible"] = YES;

			return $this->db->insert("cases", $case, $keys);
		}

		public function update_case($case) {
			$keys = array("iso_standard_id", "name", "organisation", "date", "scope", "impact", "logo");

			if (trim($case["logo"]) == "") {	
				$case["logo"] = null;
			}

			return $this->db->update("cases", $case["id"], $case, $keys);
		}

		public function set_visibility($visibility) {
			$query = "update cases set visible=%d where organisation_id=%d";
			if ($this->db->query($query, NO, $this->user->organisation_id) === false) {
				return false;
			}

			if (is_array($visibility) == false) {
				return true;
			} else if (($count = count($visibility)) == 0) {
				return true;
			}

			$values = array_fill(1, $count, "%d");
			$query = "update cases set visible=%d where organisation_id=%d and id in (".implode(", ", $values).")";
			return $this->db->query($query, YES, $this->user->organisation_id, $visibility) !== false;
		}

		public function show_all_cases() {
			$query = "update cases set visible=%d where organisation_id=%d";
			return $this->db->query($query, YES, $this->user->organisation_id) !== false;
		}

		public function delete_case($case_id) {
			$queries = array(
				array("delete from progress_tasks where case_id=%d", $case_id),
				array("delete from progress_people where case_id=%d", $case_id),
				array("delete from case_bia_threat where case_id=%d", $case_id),
				array("delete from bia where case_id=%d", $case_id),
				array("delete from case_threat where case_id=%d", $case_id),
				array("delete from overruled where case_id=%d", $case_id),
				array("delete from cases where id=%d", $case_id));

			return $this->db->transaction($queries);
		}
	}
?>
