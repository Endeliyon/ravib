<?php
	/* Copyright (C) by Hugo Leisink <hugo@leisink.net>
	 * This file is part of the Banshee PHP framework
	 * http://www.banshee-php.org/
	 */

	ob_start();
	require("../libraries/error.php");
	require("../libraries/banshee.php");
	require("../libraries/security.php");

	/* Abort on dangerous PHP settings
	 */
	check_PHP_setting("allow_url_include", 0);
	check_PHP_setting("magic_quotes_gpc", 0);
	check_PHP_setting("register_globals", 0);

	/* Create core objects
	 */
	$_database = new MySQLi_connection(DB_HOSTNAME, DB_DATABASE, DB_USERNAME, DB_PASSWORD);
	$_settings = new settings($_database);
	$_session  = new session($_database, $_settings);
	$_user     = new user($_database, $_settings, $_session);
	$_page     = new page($_database, $_settings, $_user);
	$_output   = new output($_database, $_settings, $_page);
	if (is_true(MULTILINGUAL)) {
		$_language = new language($_database, $_page, $_output);
	}

	/* Prevent Cross-Site Request Forgery
	 */
	prevent_csrf($_output, $_user);

	/* User switch warning
	 */
	if (isset($_SESSION["user_switch"])) {
		$real_user = $_database->entry("users", $_SESSION["user_switch"]);
		$_output->add_system_warning("User switch active! Switched from '%s' to '%s'.", $real_user["fullname"], $_user->fullname);
	}

	/* Include the model
	 */
	if (file_exists($file = "../models/".$_page->module.".php")) {
		include($file);
	}

	/* Add layout data to output XML
	 */
	$_output->open_tag("output");

	if ($_output->add_layout_data) {
		$_output->open_tag("banshee");
		$_output->add_tag("version", BANSHEE_VERSION);
		$_output->close_tag();
		$_output->add_tag("website_url", $_SERVER["SERVER_NAME"]);

		/* Page information
		 */
		$_output->add_tag("page", $_page->page, array(
			"url"      => $_page->url,
			"module"   => $_page->module,
			"type"     => $_page->type,
			"readonly" => show_boolean($_page->readonly)));

		/* User information
		 */
		if ($_user->logged_in) {
			$params = array("id" => $_user->id, "admin" => show_boolean($_user->is_admin));
			$_output->add_tag("user", $_user->fullname, $params);
		}

		/* Multilingual
		 */
		if ($_language !== null) {
			$_language->to_output();
		}

		/* Main menu
		 */
		if (is_true(WEBSITE_ONLINE)) {
			if ((substr($_page->url, 0, 4) == "/cms") || ($_output->layout == LAYOUT_CMS)) {
				/* CMS menu
				 */
				if (($_user->logged_in) && ($_page->page != "logout")) {
					$_output->open_tag("menu");
					$_output->record(array("link" => "/casus", "text" => "Website"), "item");
					$_output->record(array("link" => "/cms", "text" => "CMS"), "item");
					$_output->record(array("link" => "/logout", "text" => "Logout"), "item");
					$_output->close_tag();
				}
			} else {
				/* Normal menu
				 */
				$menu = new menu($_database, $_output);
				if (is_true(MENU_CHECK_RIGHTS)) {
					$menu->set_user($_user);
				}

				if ($_user->logged_in == false) {
					/* Public section
					 */
					$menu->set_start_point("public");
					$_output->run_javascript("$('nav.navbar a.inloggen').addClass('btn btn-xs btn-success');");
				} else if (substr($_page->url, 0, 6) == "cms") {
					/* CMS
					 */
					$menu->set_start_point("admin");
				} else {
					/* Private section
					 */
					$menu->set_start_point("private");
				}

				$menu->to_output();
			}
		}

		/* Stylesheet
		 */
		$_output->add_css("banshee/bootstrap.css");
		$_output->add_css("banshee/bootstrap-theme.css");
		$_output->add_css("banshee/layout_".$_output->layout.".css");
		$_output->add_css($_page->module.".css");

		/* Javascripts
		 */
		$_output->add_javascript("jquery/jquery.js");
		$_output->add_javascript("banshee/bootstrap.js");

		$_output->open_tag("content", array("mobile" => show_boolean($_output->mobile)));
	}

	/* Include the controller
	 */
	if (file_exists($file = "../controllers/".$_page->module.".php")) {
		include($file);

		$controller_class = str_replace("/", "_", $_page->module)."_controller";
		if (class_exists($controller_class) == false) {
			print "Controller class '".$controller_class."' does not exist.\n";
		} else if (is_subclass_of($controller_class, "controller") == false) {
			print "Controller class '".$controller_class."' does not extend 'controller'.\n";
		} else {
			$_controller = new $controller_class($_database, $_settings, $_user, $_page, $_output, $_language);
			$method = "execute";

			if (is_true(URL_PARAMETERS)) {
				$reflection = new reflectionobject($_controller);
				$param_count = count($reflection->getmethod($method)->getParameters());
				unset($reflection);

				$params = array_pad($_page->parameters, $param_count, null);
				call_user_func_array(array($_controller, $method), $params);
			} else {
				$_controller->$method();
			}
			unset($_controller);

			if ($_output->disabled) {
				print ob_get_clean();
				exit;
			}

			while ($_output->depth > 2) {
				print "System error: controller didn't close an open tag.";
				$_output->close_tag();
			}
		}
	}

	if ($_output->add_layout_data) {
		$_output->close_tag();
	}

	/* Handle errors
	 */
	$errors = ob_get_contents();
	ob_clean();

	if ($errors != "") {
		$error_handler = new website_error_handler($_output, $_settings, $_user);
		$error_handler->execute($errors);
		unset($error_handler);
	}

	/* Close output
	 */
	$_output->close_tag();

	/* Output content
	 */
	$output = $_output->generate();
	if ((($last_errors = ob_get_clean()) != "") && ($_page->module != "setup")) {
		$last_errors = "Fatal errors:\n".$last_errors;

		header_remove("Content-Encoding");
		header("Content-Length: ".strlen($last_errors));
		header("Content-Type: text/plain");
		print $last_errors;
	} else {
		print $output;
	}
?>
