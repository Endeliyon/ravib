<?php
	/* Copyright (c) by Hugo Leisink <hugo@leisink.net>
	 * Licensed under the RAVIB license.
	 */

	abstract class process_controller extends controller {
		protected $case = null;
		protected $breadcrumbs = array(
			"casus"        => "Casussen",
			"bia/#"        => "BIA",
			"dreigingen/#" => "Dreigingen",
			"iso/#"        => "Maatregelen",
			"rapport/#"    => "Rapportage",
			"voortgang/#"  => "Voortgang");

		public function __construct() {
			$arguments = func_get_args();
			call_user_func_array(array("parent", "__construct"), $arguments);

			$this->output->add_javascript("includes/help.js");
			$this->output->add_css("includes/help.css");
		}

		protected function generate_filename($str) {
			$valid = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789 ";

			$len = strlen($str);
			for ($i = 0; $i < $len; $i++) {
				if (strpos($valid, substr($str, $i, 1)) === false) {
					$str = substr($str, 0, $i)."-".substr($str, $i + 1);
				}
			}

			return preg_replace('/-+/', "-", $str);
		}

		protected function valid_case_id($case_id) {
			if (valid_input($case_id, VALIDATE_NUMBERS, VALIDATE_NONEMPTY) == false) {
				$this->output->add_tag("result", "Geen casus opgegeven.", array("url" => $this->settings->page_after_login));
				return false;
			} else if (($this->case = $this->model->get_case($case_id)) == false) {
				$this->output->add_tag("result", "Onbekende casus.", array("url" => $this->settings->page_after_login));
				return false;
			}

			$this->case["title"] = $this->case["name"];
			if ($this->case["organisation"] != "") {
				$this->case["title"] = $this->case["organisation"]." :: ".$this->case["title"];
			}

			$this->output->add_tag("case", $this->case["title"], array("id" => $case_id));

			return true;
		}
	}
?>
