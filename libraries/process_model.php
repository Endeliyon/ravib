<?php
	/* Copyright (c) by Hugo Leisink <hugo@leisink.net>
	 * Licensed under the RAVIB license.
	 */

	abstract class process_model extends model {
		protected $risk_matrix = null;
		protected $risk_matrix_labels = null;
		protected $risk_matrix_chance = null;
		protected $risk_matrix_impact = null;
		protected $threat_handle_labels = null;
		protected $availability_score = null;
		protected $integrity_score = null;
		protected $confidentiality_score = null;
		protected $asset_value = null;
		protected $asset_value_labels = null;

		/* Constructor
		 *
		 * INPUT: object database, object settings, object user, object page, object output[, object language]
		 * OUTPUT: -
		 * ERROR:  -
		 */
		public function __construct() {
			$arguments = func_get_args();
			call_user_func_array(array("parent", "__construct"), $arguments);

			$this->risk_matrix = config_array(RISK_MATRIX);
			foreach ($this->risk_matrix as $i => $row) {
				$this->risk_matrix[$i] = explode(",", $row);
			}

			$this->risk_matrix_labels = config_array(RISK_MATRIX_LABELS);
			$this->risk_matrix_chance = config_array(RISK_MATRIX_CHANCE);
			$this->risk_matrix_impact = config_array(RISK_MATRIX_IMPACT);

			$this->threat_handle_labels = config_array(THREAT_HANDLE_LABELS);

			$this->availability_score = config_array(AVAILABILITY_SCORE);
			$this->integrity_score = config_array(INTEGRITY_SCORE);
			$this->confidentiality_score = config_array(CONFIDENTIALITY_SCORE);

			$this->asset_value = array();
			foreach (config_array(ASSET_VALUE) as $i => $line) {
				$layer = explode(" - ", $line);
				$this->asset_value[$i] = array();
				foreach ($layer as $j => $row) {
					$this->asset_value[$i][$j] = explode(",", $row);
				}
			}
			$this->asset_value_labels = array_reverse(config_array(ASSET_VALUE_LABELS), true);
		}

		/* Magic method get
		 *
		 * INPUT:  string key
		 * OUTPUT: mixed value
		 * ERROR:  null
		 */
		public function __get($key) {
			switch ($key) {
				case "risk_matrix": return $this->risk_matrix;
				case "risk_matrix_labels": return $this->risk_matrix_labels;
				case "risk_matrix_chance": return $this->risk_matrix_chance;
				case "risk_matrix_impact": return $this->risk_matrix_impact;
				case "threat_handle_labels": return $this->threat_handle_labels;
				case "availability_score": return $this->availability_score;
				case "integrity_score": return $this->integrity_score;
				case "confidentiality_score": return $this->confidentiality_score;
			}

			return null;
		}

		public function get_case($case_id) {
			$query = "select * from cases where id=%d and organisation_id=%d limit 1";

			if (($result = $this->db->execute($query, $case_id, $this->user->organisation_id)) == false) {
				return false;
			}

			return $result[0];
		}
	}
?>
