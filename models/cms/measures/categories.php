<?php
	/* Copyright (c) by Hugo Leisink <hugo@leisink.net>
	 * Licensed under the RAVIB license.
	 */

	class cms_measures_categories_model extends tablemanager_model {
		protected $table = "iso_measure_categories";
		protected $order = "number";
		protected $elements = array(	
			"iso_standard_id" => array(
				"label"    => "Standard",
				"type"     => "integer",
				"overview" => false,
				"required" => true,
				"readonly" => true),
			"number" => array(
				"label"    => "Number",
				"type"     => "integer",
				"overview" => true,
				"required" => true),
			"name" => array(
				"label"    => "Name",
				"type"     => "varchar",
				"overview" => true,
				"required" => true));

		public function get_iso_standard($iso_standard) {
			return $this->borrow("cms/standards")->get_item($iso_standard);
		}

		public function get_items() {
			$query = "select * from %S where iso_standard_id=%d order by %S";

			return $this->db->execute($query, $this->table, $_SESSION["iso_standard"], $this->order);
		}

		public function create_item($item) {
			$item["iso_standard_id"] = $_SESSION["iso_standard"];

			parent::create_item($item);
		}
	}
?>
