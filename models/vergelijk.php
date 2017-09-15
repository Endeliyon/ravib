<?php
	/* Copyright (c) by Hugo Leisink <hugo@leisink.net>
	 * Licensed under the RAVIB license.
	 */

	class vergelijk_model extends process_model {
		public function get_cases() {
			return $this->borrow("casus")->get_cases();
		}

		public function get_threats() {
			$query = "select * from threats order by number";

			return $this->db->execute($query);
		}

		public function get_case_threats($case_id) {
			$query = "select t.number, c.chance, c.impact, c.handle ".
			         "from case_threat c, threats t ".
			         "where c.threat_id=t.id and c.case_id=%d order by number";

			if (($result = $this->db->execute($query, $case_id)) == false) {
				return false;
			}

			$case = array();
			foreach ($result as $threat) {
				$case[(int)$threat["number"]] = $threat;
			}

			return $case;
		}
	}
?>
