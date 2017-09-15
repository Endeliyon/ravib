<?php
	/* Copyright (c) by Hugo Leisink <hugo@leisink.net>
	 * Licensed under the RAVIB license.
	 */

	class voortgang_personen_model extends process_model {
		public function get_people($case_id) {
			$query = "select * from progress_people where case_id=%d order by name";

			return $this->db->execute($query, $case_id);
		}

		public function get_person($case_id, $person_id) {
			$query = "select id, name, email from progress_people where case_id=%d and id=%d";
			if (($result = $this->db->execute($query, $case_id, $person_id)) == false) {
				return false;
			}

			return $result[0];
		}

		public function save_oke($case_id, $person) {
			$result = true;

			if (trim($person["name"]) == "") {
				$this->output->add_message("Vul een naam in.");
				$result = false;
			}

			if (valid_email($person["email"]) == false) {
				$this->output->add_message("Vul een geldig e-mailadres in.");
				$result = false;
			}

			if (isset($person["id"])) {
				if ($this->get_person($case_id, $person["id"]) == false) {
					$result = false;
				}
			}

			return $result;
		}

		public function create_person($case_id, $person) {
			$keys = array("id", "case_id", "name", "email");

			$person["id"] = null;
			$person["case_id"] = $case_id;

			return $this->db->insert("progress_people", $person, $keys);
		}

		public function update_person($case_id, $person) {
			$keys = array("name", "email");

			return $this->db->update("progress_people", $person["id"], $person, $keys);
		}

		public function delete_oke($case_id, $person) {
			$result = true;

			if ($this->get_person($case_id, $person["id"]) == false) {
				$result = false;
			}

			return $result;
		}

		public function delete_person($person_id) {
			$query = "update progress_tasks set actor_id=null where actor_id=%d";
			if ($this->db->query($query, $person_id) === false) {
				return false;
			}

			$query = "update progress_tasks set reviewer_id=null where reviewer_id=%d";
			if ($this->db->query($query, $person_id) === false) {
				return false;
			}

			return $this->db->delete("progress_people", $person_id);
		}
	}
?>
