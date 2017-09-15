<?php
	/* Copyright (c) by Hugo Leisink <hugo@leisink.net>
	 * Licensed under the RAVIB license.
	 */

	class cms_threats_model extends model {
		public function get_iso_standard($iso_standard) {
			return $this->borrow("cms/standards")->get_item($iso_standard);
		}

		public function count_threats() {
			$query = "select count(*) as count from threats order by number";

			if (($result = $this->db->execute($query)) == false) {
				return false;
			}

			return $result[0]["count"];
		}

		public function get_threats($offset, $limit) {
			$query = "select t.*,(select count(*) from controls c, iso_measures m ".
			           "where c.iso_measure_id=m.id and iso_standard_id=%d and threat_id=t.id) as links from threats t ".
			         "order by number limit %d,%d";

			return $this->db->execute($query, $_SESSION["iso_standard"], $offset, $limit);
		}

		public function get_threat($threat_id) {
			if (($result = $this->db->entry("threats", $threat_id)) === false) {
				return false;
			}

			$query = "select * from controls where threat_id=%d";
			if (($links = $this->db->execute($query, $threat_id)) === false) {
				return false;
			}

			$result["iso_links"] = array();
			foreach ($links as $link) {
				array_push($result["iso_links"], $link["iso_measure_id"]);
			}

			return $result;
		}

		public function get_categories() {
			$query = "select * from threat_categories order by name";

			return $this->db->execute($query);
		}

		public function get_iso_measures() {
			$query = "select * from iso_measures where iso_standard_id=%d";

			return $this->db->execute($query, $_SESSION["iso_standard"]);
		}

		public function save_oke($threat) {
			$result = true;

			if (valid_input($threat["number"], VALIDATE_NUMBERS, VALIDATE_NONEMPTY) == false) {
				$this->output->add_message("Number is not a number.");
				$result = false;
			}

			if (trim($threat["threat"]) == "") {
				$this->output->add_message("Fill in the threat.");
				$result = false;
			}

			if (trim($threat["description"]) == "") {
				$this->output->add_message("Fill in the description.");
				$result = false;
			}

			return $result;
		}

		private function save_iso_links($threat, $threat_id) {
			if (is_array($threat["iso_links"]) == false) {
				return true;
			}

			foreach ($threat["iso_links"] as $iso_measure_id) {
				$data = array(
					"iso_measure_id" => $iso_measure_id,
					"threat_id"      => $threat_id);
				if ($this->db->insert("controls", $data) === false) {
					return false;
				}
			}

			return true;
		}

		private function order_numbers($threat_id, $number) {
			$query = "update threats set number=number+1 where id!=%d and number>=%d";
			if ($this->db->query($query, $threat_id, $number) === false) {
				return false;
			}

			$query = "select id,number from threats order by number";
			if (($values = $this->db->execute($query)) === false) {
				return false;
			}

			for ($i = 0; $i < count($values); $i++) {
				$number = $i + 1;
				if ($values[$i]["number"] != $number) {
					if ($this->db->update("threats", $values[$i]["id"], array("number" => $number)) === false) {
						return false;
					}
				}
			}

			return true;
		}

		public function create_threat($threat) {
			$keys = array("id", "number", "threat", "description", "category_id", "confidentiality", "integrity", "availability");

			$threat["id"] = null;

			if ($this->db->query("begin") === false) {
				return false;
			}

			if ($this->db->insert("threats", $threat, $keys) === false) {
				$this->db->query("rollback");
				return false;
			}
			$threat_id = $this->db->last_insert_id;

			if ($this->save_iso_links($threat, $threat_id) == false) {
				$this->db->query("rollback");
				return false;
			}

			if ($this->order_numbers($threat_id, $threat["number"]) == false) {
				$this->db->query("rollback");
				return false;
			}

			return $this->db->query("commit") !== false;
		}

		public function update_threat($threat) {
			$keys = array("number", "threat", "description", "category_id", "confidentiality", "integrity", "availability");

			if ($this->db->query("begin") === false) {
				return false;
			}

			if ($this->db->update("threats", $threat["id"], $threat, $keys) === false) {
				$this->db->query("rollback");
				return false;
			}

			$query = "delete from controls where threat_id=%d and iso_measure_id in ".
			         "(select id from iso_measures where iso_standard_id=%d)";
			if ($this->db->query($query, $threat["id"], $_SESSION["iso_standard"]) === false) {
				$this->db->query("rollback");
				return false;
			}

			if ($this->save_iso_links($threat, $threat["id"]) == false) {
				$this->db->query("rollback");
				return false;
			}

			if ($this->order_numbers($threat["id"], $threat["number"]) == false) {
				$this->db->query("rollback");
				return false;
			}

			return $this->db->query("commit") !== false;
		}

		public function delete_oke($threat) {
			return true;
		}

		public function delete_threat($threat_id) {
			$queries = array(
				array("delete from controls where threat_id=%d", $threat_id),
				array("delete from case_threat where threat_id=%d", $threat_id),
				array("delete from threats where id=%d", $threat_id));

			return $this->db->transaction($queries);
		}
	}
?>
