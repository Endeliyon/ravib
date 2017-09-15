<?php
	/* Copyright (c) by Hugo Leisink <hugo@leisink.net>
	 * Licensed under the RAVIB license.
	 */

	class cms_threats_categories_model extends tablemanager_model {
		protected $table = "threat_categories";
		protected $order = "name";
		protected $elements = array(
			"name" => array(
				"label"    => "Name",
				"type"     => "varchar",
				"overview" => true,
				"required" => true));

		public function delete_oke($item_id) {
			$result = true;

			$query = "select count(*) as count from threats where category_id=%d";
			if (($threats = $this->db->execute($query, $item_id)) === false) {
				$this->output->add_message("Error counting associated threats.");
				$result = false;
			} else if ($threats[0]["count"] > 0) {
				$this->output->add_message("Category in use.");
				$result = false;
			}

			return $result;
		}
	}
?>
