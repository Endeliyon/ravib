<?php
	/* Copyright (c) by Hugo Leisink <hugo@leisink.net>
	 * Licensed under the RAVIB license.
	 */

	class cms_standards_model extends tablemanager_model {
		protected $table = "iso_standards";
		protected $elements = array(
			"name" => array(
				"label"    => "Standaard",
				"type"     => "varchar",
				"overview" => true,
				"required" => true),
			"active" => array(
				"label"    => "Active",
				"type"     => "boolean",
				"overview" => true));
		
		public function delete_oke($item_id) {
			$query = "select count(*) as count from cases where iso_standard_id=%d";

			if (($result = $this->db->execute($query, $item_id)) === false) {
				return false;
			}

			if ($result[0]["count"] > 0) {
				$this->output->add_message("This ISO standard is being used in a case.");
				return false;
			}

			return true;
		}

		public function delete_item($item_id) {
			parent::delete_item($item_id);
		}
	}
?>
