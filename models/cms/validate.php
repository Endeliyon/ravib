<?php
	/* Copyright (c) by Hugo Leisink <hugo@leisink.net>
	 * Licensed under the RAVIB license.
	 */

	class cms_validate_model extends model {
		public function linked_threats() {
			$query = "select *,(select count(*) from controls c, iso_measures m ".
			                   "where threat_id=t.id and c.iso_measure_id=m.id and m.iso_standard_id=%d) as links ".
			         "from threats t order by links,number";
			if (($threats = $this->db->execute($query, $_SESSION["iso_standard"])) === false) {
				return false;
			}

			return $threats;
		}

		public function linked_measures() {
			$query = "select *,(select count(*) from controls where iso_measure_id=m.id) as links ".
			         "from iso_measures m where iso_standard_id=%d order by links,id";
			if (($measures = $this->db->execute($query, $_SESSION["iso_standard"])) === false) {
				return false;
			}

			return $measures;
		}
	}
?>
