<?php
	/* Copyright (c) by Hugo Leisink <hugo@leisink.net>
	 * Licensed under the RAVIB license.
	 */

	class risicomatrix_controller extends controller {
		public function execute() {
			$risk_matrix = config_array(RISK_MATRIX);
			$risk_matrix_labels = config_array(RISK_MATRIX_LABELS);
			$risk_matrix_chance = config_array(RISK_MATRIX_CHANCE);
			$risk_matrix_impact = config_array(RISK_MATRIX_IMPACT);

			$this->output->open_tag("matrix");

			$this->output->open_tag("row");
			$this->output->add_tag("cell", "");
			foreach ($risk_matrix_impact as $impact) {
				$this->output->add_tag("cell", $impact);
			}
			$this->output->close_tag();

			$chance = 4;
			foreach (array_reverse($risk_matrix) as $row) {
				$row = explode(",", $row);

				$this->output->open_tag("row");
				$this->output->add_tag("cell", $risk_matrix_chance[$chance--]);
				foreach ($row as $cell) {
					$this->output->add_tag("cell", $risk_matrix_labels[$cell], array("class" => "risk_".$cell));
				}
				$this->output->close_tag();
			}
			$this->output->close_tag();
		}
	}
?>
