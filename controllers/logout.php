<?php
	/* Copyright (C) by Hugo Leisink <hugo@leisink.net>
	 * This file is part of the Banshee PHP framework
	 * http://www.banshee-php.org/
	 */

	class logout_controller extends controller {
		public function execute() {
			if ($this->user->logged_in) {
				header("Status: 401");

				if (isset($_SESSION["user_switch"]) == false) {
					$this->user->logout();
					$url = $this->settings->start_page;
				} else {
					$this->user->log_action("switched back to self");
					$_SESSION["user_id"] = $_SESSION["user_switch"];
					unset($_SESSION["user_switch"]);
					$url = "cms/switch";
				}

				$this->output->add_tag("result", "U bent nu uitgelogd.", array("url" => $url));
			} else {
				$this->output->add_tag("result", "U bent niet ingelogd.", array("url" => $this->settings->start_page));
			}
		}
	}
?>
