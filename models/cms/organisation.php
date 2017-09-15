<?php
	/* Copyright (C) by Hugo Leisink <hugo@leisink.net>
	 * This file is part of the Banshee PHP framework
	 * http://www.banshee-php.org/
	 */

	class cms_organisation_model extends tablemanager_model {
		protected $table = "organisations";
		protected $elements = array(
			"name" => array(
				"label"    => "Name",
				"type"     => "varchar",
				"overview" => true,
				"required" => true,
				"unique"   => true));

		public function get_users($organisation_id) {
			$query = "select * from users where organisation_id=%d order by fullname";

			return $this->db->execute($query, $organisation_id);
		}

		public function delete_oke($organisation_id) {
			if (parent::delete_oke($organisation_id) == false) {
				return false;
			}

			$query = "select count(*) as count from users where organisation_id=%d";

			if (($result = $this->db->execute($query, $organisation_id)) === false) {
				$this->output->add_message("Database error.");
				return false;
			}

			if ((int)$result[0]["count"] > 0) {
				$this->output->add_message("Organisation in use.");
				return false;
			}

			return true;
		}

		public function delete_item($organisation_id) {
			$queries = array(
				array("delete from pia where pia_case_id in (select id from pia_cases where organisation_id=%d)", $organisation_id),
				array("delete from pia_cases where organisation_id=%d", $organisation_id),
				array("delete from case_bia_threat where case_id in (select id from cases where organisation_id=%d)", $organisation_id),
				array("delete from case_threat where case_id in (select id from cases where organisation_id=%d)", $organisation_id),
				array("delete from bia where case_id in (select id from cases where organisation_id=%d)", $organisation_id),
				array("delete from overruled where case_id in (select id from cases where organisation_id=%d)", $organisation_id),
				array("delete from cases where organisation_id=%d", $organisation_id),
				array("delete from organisations where id=%d", $organisation_id));
				
			return $this->db->transaction($queries) !== false;
		}
	}
?>
