<?php
	/* Copyright (c) by Hugo Leisink <hugo@leisink.net>
	 * Licensed under the RAVIB license.
	 */

	class koppelingen_model extends model {
		public function get_standard($standard_id) {
			if (($standard = $this->borrow("admin/standards")->get_item($standard_id)) === false) {
				return false;
			}

			return $standard["name"];
		}

		public function get_iso_standards() {
			return $this->borrow("casus")->get_iso_standards();
		}

		public function get_threat_categories() {
			$query = "select * from threat_categories";
			if (($categories = $this->db->execute($query)) == false) {
				return false;
			}

			$result = false;
			foreach ($categories as $category) {
				$result[$category["id"]] = $category["name"];
			}

			return $result;
		}

		public function get_threats() {
			$query = "select * from threats order by number";
			if (($threats = $this->db->execute($query)) === false) {
				return false;
			}

			$result = array();
			foreach ($threats as $threat) {
				foreach (array("availability", "integrity", "confidentiality") as $key) {
					if ($threat[$key] == "") {
						$threat[$key] = "-";
					}
				}
				$result[$threat["id"]] = $threat;
			}

			return $result;
		}

		public function get_measures($iso_standard) {
			$query = "select * from iso_measures where iso_standard_id=%d";
			if (($measures = $this->db->execute($query, $iso_standard)) === false) {
				return false;
			}

			$result = array();
			foreach ($measures as $measure) {
				$result[$measure["id"]] = $measure;
			}

			return $result;
		}

		public function get_controls($iso_standard) {
			$query = "select c.* from controls c, iso_measures i ".
			         "where c.iso_measure_id=i.id and i.iso_standard_id=%d";

			return $this->db->execute($query, $iso_standard);
		}
	}
?>
