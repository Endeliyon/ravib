<?php
	/* Copyright (c) by Hugo Leisink <hugo@leisink.net>
	 * Licensed under the RAVIB license.
	 */

	class bia_model extends process_model {
		public function get_items($case_id) {
			$query = "select * from bia where case_id=%d order by item";

			return $this->db->execute($query, $case_id);
		}

		public function asset_value($item) {
			$a = $item["availability"] - 1;
			$i = $item["integrity"] - 1;
			$c = $item["confidentiality"] - 1;

			$asset_value = $this->asset_value[$i][$c][$a];

			return $this->asset_value_labels[$asset_value];
		}

		public function get_item($item_id, $case_id) {
			$query = "select * from bia where id=%d and case_id=%d limit 1";

			if (($result = $this->db->execute($query, $item_id, $case_id)) == false) {
				return false;
			}

			return $result[0];
		}

		public function save_oke($item) {
			$result = true;

			if (trim($item["item"]) == "") {
				$this->output->add_message("Vul een informatiesysteem in.");
				$result = false;
			}

			return $result;
		}

		public function create_item($item) {
			$keys = array("id", "case_id", "item", "description", "impact", "availability", "integrity", "confidentiality", "owner", "location");

			$item["id"] = null;
			$item["owner"] = is_true($item["owner"]) ? YES : NO;

			return $this->db->insert("bia", $item, $keys);
		}

		public function update_item($item) {
			$keys = array("item", "description", "impact", "availability", "integrity", "confidentiality", "owner", "location");

			$item["owner"] = is_true($item["owner"]) ? YES : NO;

			$id = array(
				"id"      => (int)$item["id"],
				"case_id" => (int)$item["case_id"]);

			return $this->db->update("bia", $id, $item, $keys);
		}

		public function delete_oke($item_id, $case_id) {
			$query = "select count(1) as count from bia where id=%d and case_id=%d";
			if (($result = $this->db->execute($query, $item_id, $case_id)) === false) {
				return false;
			}

			return $result[0]["count"] == 1;
		}

		public function delete_item($item_id, $case_id) {
			$queries = array(
				array("delete from case_bia_threat where bia_id=%d", $item_id),
				array("delete from bia where id=%d", $item_id));

			return $this->db->transaction($queries);
		}
	}
?>
