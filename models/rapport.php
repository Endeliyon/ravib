<?php
	/* Copyright (c) by Hugo Leisink <hugo@leisink.net>
	 * Licensed under the RAVIB license.
	 */

	class rapport_model extends process_model {
		public function get_iso_standard($iso_standard) {
			return $this->borrow("cms/standards")->get_item($iso_standard);
		}

		public function get_bia($case_id) {
			return $this->borrow("bia")->get_items($case_id);
		}

		public function get_threats($case_id) {
			return $this->borrow("dreigingen")->get_threats($case_id, false);
		}

		public function get_measures($case_id) {
			return $this->borrow("iso")->get_measures($case_id);
		}

		public function get_measure_categories($standard_id) {
			return $this->borrow("iso")->get_measure_categories($standard_id);
		}

		public function get_bia_threats($case_id) {
			$query = "select l.bia_id, l.threat_id, b.availability, b.integrity, b.confidentiality ".
			         "from bia b, case_bia_threat l where b.id=l.bia_id and l.case_id=%d";

			return $this->db->execute($query, $case_id);
		}

		public function get_systems($case_id, $threat_id) {
			$query = "select b.item, b.availability, b.integrity, b.confidentiality ".
			         "from case_bia_threat l, bia b ".
			         "where l.bia_id=b.id and l.case_id=%d and l.threat_id=%d ".
			         "order by item";

			return $this->db->execute($query, $case_id, $threat_id);
		}

		public function asset_value($item) {
			return $this->borrow("bia")->asset_value($item);
		}
	}
?>
