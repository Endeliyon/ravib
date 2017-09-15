<?php
	/* Copyright (C) by Hugo Leisink <hugo@leisink.net>
	 * This file is part of the Banshee PHP framework
	 * http://www.banshee-php.org/
	 */

	class wachtwoord_controller extends controller {
		private function show_password_form($key) {
			$this->output->add_javascript("banshee/".PASSWORD_HASH.".js");
			$this->output->add_javascript("wachtwoord.js");
			$this->output->run_javascript("hash = window['".PASSWORD_HASH."'];");

			$this->output->open_tag("reset");
			$this->output->add_tag("key", $key);
			$this->output->add_tag("username", $_SESSION["reset_password_username"]);
			$this->output->close_tag();
		}

		public function execute() {
			if ($this->model->key_oke($_GET["key"])) {
				/* Step 3: show password form
				 */
				$this->show_password_form($_GET["key"]);
			} else if ($_SERVER["REQUEST_METHOD"] == "POST") {
				if ($_POST["submit_button"] == "Opsturen") {
					/* Step 2: send password link
					 */
					if (($user = $this->model->get_user($_POST["username"], $_POST["email"])) != false) {
						$_SESSION["reset_password_key"] = random_string(32);
						$_SESSION["reset_password_username"] = $_POST["username"];

						$this->model->send_password_link($user, $_SESSION["reset_password_key"]);
					}
					$this->output->add_tag("link_sent");
				} else if ($_POST["submit_button"] == "Wachtwoord opslaan") {
					/* Step 4: Save password
					 */
					if ($this->model->key_oke($_POST["key"]) == false) {
						$this->output->add_tag("request");
					} else if ($this->model->password_oke($_SESSION["reset_password_username"], $_POST) == false) {
						$this->show_password_form($_POST["key"]);
					} else if ($this->model->save_password($_SESSION["reset_password_username"], $_POST) == false) {
						$this->output->add_message("Fout bij het opslaan van het wachtwoord.");
						$this->show_password_form($_POST["key"]);
					} else {
						$this->output->add_tag("result", "Het nieuwe wachtwoord is opgeslagen.", array("url" => ""));
						unset($_SESSION["reset_password_key"]);
						unset($_SESSION["reset_password_username"]);
					}
				} else {
					$this->output->add_tag("request");
				}
			} else {
				/* Step 1: show request form
				 */
				$this->output->add_tag("request");
			}
		}
	}
?>
