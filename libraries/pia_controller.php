<?php
	/* Copyright (c) by Hugo Leisink <hugo@leisink.net>
	 * Licensed under the RAVIB license.
	 */

	abstract class pia_controller extends controller {
		protected $pia = null;
		protected $breadcrumbs = array(
			"pia/casus"     => "PIA casussen",
			"pia/pia/#"     => "Privacy Impact Assessment",
			"pia/rapport/#" => "Rapportage");

		protected function valid_pia_id($pia_id) {
			if (valid_input($pia_id, VALIDATE_NUMBERS, VALIDATE_NONEMPTY) == false) {
				$this->output->add_tag("result", "Geen PIA casus opgegeven.", array("url" => $this->settings->start_page));
				return false;
			} else if (($this->pia = $this->model->get_pia_case($pia_id)) == false) {
				$this->output->add_tag("result", "Onbekende PIA casus.", array("url" => $this->settings->start_page));
				return false;
			}

			$this->output->add_tag("pia", $this->pia["name"]);

			return true;
		}
	}
?>
