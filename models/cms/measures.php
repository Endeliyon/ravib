<?php
	/* Copyright (c) by Hugo Leisink <hugo@leisink.net>
	 * Licensed under the RAVIB license.
	 */

	class cms_measures_model extends model {
		public function get_iso_standard($iso_standard) {
			return $this->borrow("cms/standards")->get_item($iso_standard);
		}

		public function count_measures() {
			$query = "select count(*) as count from iso_measures where iso_standard_id=%d";

			if (($result = $this->db->execute($query, $_SESSION["iso_standard"])) == false) {
				return false;
			}

			return $result[0]["count"];
		}

		public function get_measures($offset, $limit) {
			$query = "select m.*, (select count(*) from controls where iso_measure_id=m.id) as links from iso_measures m ".
			         "where iso_standard_id=%d order by id limit %d,%d";

			return $this->db->execute($query, $_SESSION["iso_standard"], $offset, $limit);
		}

		public function get_measure($measure_id) {
			if (($result = $this->db->entry("iso_measures", $measure_id)) === false) {
				return false;
			}

			$query = "select * from controls where iso_measure_id=%d";
			if (($links = $this->db->execute($query, $measure_id)) === false) {
				return false;
			}

			$result["threat_links"] = array();
			foreach ($links as $link) {
				array_push($result["threat_links"], $link["threat_id"]);
			}
			
			return $result;
		}

		public function get_threats() {
			$query = "select * from threats order by number";

			return $this->db->execute($query);
		}

		public function save_oke($measure) {
			$result = true;

			if ((trim($measure["number"]) == "") || (trim($measure["name"]) == "")) {
				$this->output->add_message("Please, fill in the number and name.");
				$result = false;
			}

			return $result;
		}

		private function save_threat_links($threat, $measure_id) {
			if (is_array($threat["threat_links"]) == false) {
				return true;
			}

			foreach ($threat["threat_links"] as $threat_id) {
				$data = array(
					"iso_measure_id" => $measure_id,
					"threat_id"      => $threat_id);
				if ($this->db->insert("controls", $data) === false) {
					return false;
				}
			}

			return true;
		}

		public function create_measure($measure) {
			$keys = array("id", "iso_standard_id", "number", "name");

			$measure["id"] = null;
			$measure["iso_standard_id"] = $_SESSION["iso_standard"];

			if ($this->db->query("begin") === false) {	
				return false;
			}

			if ($this->db->insert("iso_measures", $measure, $keys) === false) {
				$this->db->query("rollback");
				return false;
			}
			$measure_id = $this->db->last_insert_id;

			if ($this->save_threat_links($measure, $measure_id) == false) {
				$this->db->query("rollback");
				return false;
			}

			return $this->db->query("commit") !== false;
		}

		public function update_measure($measure) {
			$keys = array("number", "name");

			if ($this->db->query("begin") === false) {	
				return false;
			}

			if ($this->db->update("iso_measures", $measure["id"], $measure, $keys) === false) {
				$this->db->query("rollback");
				return false;
			}

			$query = "delete from controls where iso_measure_id=%d";
			if ($this->db->query($query, $measure["id"], $_SESSION["iso_standard"]) === false) {
				$this->db->query("rollback");
				return false;
			}

			if ($this->save_threat_links($measure, $measure["id"]) == false) {
				$this->db->query("rollback");
				return false;
			}

			return $this->db->query("commit") !== false;
		}

		public function delete_oke($measure) {
			return true;
		}

		public function delete_measure($measure_id) {
			$queries = array(
				array("delete from controls where iso_measure_id=%d", $measure_id),
				array("delete from overruled where iso_measure_id=%d", $measure_id),
				array("delete from iso_measures where id=%d", $measure_id));

			return $this->transaction($queries);
		}
	}
?>
