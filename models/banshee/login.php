<?php
	/* Copyright (C) by Hugo Leisink <hugo@leisink.net>
	 * This file is part of the Banshee PHP framework
	 * http://www.banshee-php.org/
	 */

	$login_successful = false;
	if ($_SERVER["REQUEST_METHOD"] == "POST") {
		/* Login via password
		 */
		if ($_user->login_password($_POST["username"], $_POST["password"], is_true($_POST["use_cr_method"]))) {
			if (is_true($_POST["bind_ip"])) {
				$_user->bind_to_ip();
			}
			$_SERVER["REQUEST_METHOD"] = "GET";
			$_POST = array();

			$login_successful = true;
		} else {
			$_user->log_action("login failed: %s", $_POST["username"]);
		}
	} else if (isset($_GET["login"])) {
		/* Login via one time key
		 */
		if ($_user->login_one_time_key($_GET["login"])) {
			$login_successful = true;
		}
	} else if (($_SERVER["HTTPS"] == "on") && isset($_SERVER[TLS_CERT_SERIAL_VAR])) {
		/* Login via client SSL certificate
		 */
		if ($_user->login_ssl_auth($_SERVER[TLS_CERT_SERIAL_VAR])) {
			$login_successful = true;
		}
	} else if (isset($_SESSION["challenge"]) == false) {
		/* Generate challenge
		 */
		$_SESSION["challenge"] = random_string(32);
	}

	/* Pre-login actions
	 */
	if ($login_successful) {
		/* Load requested page
		 */
		if (($next_page = ltrim($_page->url, "/")) == "") {
			$next_page = $_settings->start_page;
		}

		$_page->select_module($next_page);
		$_output->set_layout();
		if ($_page->module != LOGIN_MODULE) {
			if (file_exists($file = "../models/".$_page->module.".php")) {
				include($file);
			}
		}

		$_SESSION["iso_standard"] = $_settings->default_iso_standard;
	}
?>
