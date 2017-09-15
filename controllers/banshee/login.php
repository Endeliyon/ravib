<?php
	/* Copyright (C) by Hugo Leisink <hugo@leisink.net>
	 * This file is part of the Banshee PHP framework
	 * http://www.banshee-php.org/
	 */

	class banshee_login_controller extends controller {
		public function execute() {
			header("Status: 401");

			$this->output->description = "Login";
			$this->output->keywords = "login";
			$this->output->title = "Login";

			$this->output->add_javascript("banshee/".PASSWORD_HASH.".js");
			$this->output->add_javascript("banshee/login.js");
			$this->output->run_javascript("set_focus(); hash = window['".PASSWORD_HASH."'];");

			$this->output->open_tag("login", array(
				"wachtwoord" => show_boolean(module_exists("wachtwoord"))));

			$this->output->add_tag("url", $_SERVER["REQUEST_URI"]);

			if ($_SERVER["REQUEST_METHOD"] != "POST") {
				$this->output->add_tag("bind");
			} else {
				$this->output->add_tag("username", $_POST["username"]);
				if (is_true($_POST["bind_ip"])) {
					$this->output->add_tag("bind");
				}
			}

			$this->output->add_tag("remote_addr", $_SERVER["REMOTE_ADDR"]);
			$this->output->add_tag("challenge", $_SESSION["challenge"]);

			if ($_SERVER["REQUEST_METHOD"] == "POST") {
				if (strpos($_POST["username"], "'") !== false) {
					$this->output->add_message("Sorry, this application does not support SQL injection.");
					header("X-Hiawatha-Monitor: exploit_attempt");
				} else {
					$this->output->add_message("Login incorrect");
				}
			}

			$this->output->close_tag();
		}
	}
?>
