<?php
	/* Copyright (C) by Hugo Leisink <hugo@leisink.net>
	 * This file is part of the Banshee PHP framework
	 * http://www.banshee-php.org/
	 */

	/* For internal usage. Only change if you know what you're doing!
	 */
	define("BANSHEE_VERSION", "5.1");
	define("ADMIN_ROLE_ID", 1);
	define("USER_ROLE_ID", 2);
	define("YES", 1);
	define("NO", 0);
	define("USER_STATUS_DISABLED", 0);
	define("USER_STATUS_CHANGEPWD", 1);
	define("USER_STATUS_ACTIVE", 2);
	define("PASSWORD_HASH", "sha256");
	define("SESSION_NAME", "WebsiteSessionID");
	define("EURO", html_entity_decode("&euro;"));
	define("HOUR", 3600);
	define("DAY", 86400);
	define("PAGE_MODULE", "banshee/page");
	define("ERROR_MODULE", "banshee/error");
	define("LOGIN_MODULE", "banshee/login");
	define("LOGOUT_MODULE", "logout");
	define("FPDF_FONTPATH", "../extra/fpdf_fonts/");
	define("FILES_PATH", "files");
	define("TLS_CERT_SERIAL_VAR", "TLS_CERT_SERIAL");

	define("PIA_BEGIN", "1.1");
	define("PIA_END", "end");

	/* Auto class loader
	 *
	 * INPUT:  string class name
	 * OUTPUT: -
	 * ERROR:  -
	 */
	function __autoload($class_name) {
		$rename = array(
			"https"               => "http",
			"jpeg_image"          => "image",
			"png_image"           => "image",
			"gif_image"           => "image",
			"pop3s"               => "pop3",
			"banshee_website_ssl" => "banshee_website");

		$class_name = strtolower($class_name);
		if (isset($rename[$class_name])) {
			$class_name = $rename[$class_name];
		}

		$locations = array("libraries", "libraries/database");
		foreach ($locations as $location) {
			if (file_exists($file = "../".$location."/".$class_name.".php")) {
				include_once($file);
				break;
			}
		}
	}

	/* Convert mixed to boolean
	 *
	 * INPUT:  mixed
	 * OUTPUT: boolean
	 * ERROR:  -
	 */
	function is_true($bool) {
		if (is_string($bool)) {
			$bool = strtolower($bool);
		}

		return in_array($bool, array(true, YES, "1", "yes", "true", "on"), true);
	}

	/* Convert mixed to boolean
	 *
	 * INPUT:  mixed
	 * OUTPUT: boolean
	 * ERROR:  -
	 */
	function is_false($bool) {
		return (is_true($bool) === false);
	}

	/* Convert boolean to string
	 *
	 * INPUT:  boolean
	 * OUTPUT: string "yes"|"no"
	 * ERROR:  -
	 */
	function show_boolean($bool) {
		return (is_true($bool) ? "yes" : "no");
	}

	/* Convert a page path to a module path
	 *
	 * INPUT:  array / string page path
	 * OUTPUT: array / string module path
	 * ERROR:  -
	 */
	function page_to_module($page) {
		if (is_array($page) == false) {
			if (($pos = strrpos($page, ".")) !== false) {
				$page = substr($page, 0, $pos);
			}
		} else foreach ($page as $i => $item) {
			$page[$i] = page_to_module($item);
		}

		return $page;
	}

	/* Convert a page path to a page type
	 *
	 * INPUT:  array / string page path
	 * OUTPUT: array / string page type
	 * ERROR:  -
	 */
	function page_to_type($page) {
		if (is_array($page) == false) {
			if (($pos = strrpos($page, ".")) !== false) {
				$page = substr($page, $pos);
			} else {
				$page = "";
			}
		} else foreach ($page as $i => $item) {
			$page[$i] = page_to_type($item);
		}

		return $page;
	}

	/* Check for module existence
	 *
	 * INPUT:  string module
	 * OUTPUT: bool module exists
	 * ERROR:  -
	 */
	function module_exists($module) {
		if (in_array($module, config_file("public_modules"))) {
			return true;
		} else if (in_array($module, config_file("private_modules"))) {
			return true;
		}

		return false;
	}

	/* Check for library existence
	 *
	 * INPUT:  string library
	 * OUTPUT: bool library exists
	 * ERROR:  -
	 */
	function library_exists($library) {
		return file_exists("../libraries/".$library.".php");
	}

	/* Handle table sort
	 */
	function handle_table_sort($key, $columns, $default) {
		if (isset($_SESSION[$key]) == false) {
			$_SESSION[$key] = $default;
		}

		if (isset($_GET["order"]) == false) {
			return;
		}

		if (in_array($_GET["order"], $columns) == false) {
			return;
		}

		if (is_array($default) == false) {
			$_SESSION[$key] = $_GET["order"];
			return;
		}

		$max = count($default) - 1;
		for ($i = 0; $i < $max; $i++) {
			if ($_SESSION[$key][$i] == $_GET["order"]) {
				return;
			}
		}

		array_pop($_SESSION[$key]);
		array_unshift($_SESSION[$key], $_GET["order"]);
	}

	/* Log debug information
	 *
	 * INPUT:  string format[, mixed arg...]
	 * OUTPUT: true
	 * ERROR:  false
	 */
	function debug_log($info) {
		if (func_num_args() > 1) {
			$args = func_get_args();
			array_shift($args);
			$info = vsprintf($action, $args);
		} else if (is_array($info)) {
			foreach ($info as $key => $value) {
				$info[$key] = "\t".$key." => ".chop($value);
			}
			$info = "array:\n".implode("\n", $info);
		}

		if (($fp = fopen("../logfiles/debug.log", "a")) == false) {
			return false;
		}

		fputs($fp, sprintf("%s|%s|%s|%s\n", $_SERVER["REMOTE_ADDR"], date("D d M Y H:i:s"), $_SERVER["REQUEST_URI"], $info));
		fclose($fp);

		return true;
	}

	/* Flatten array to new array with depth 1
	 *
	 * INPUT:  array data
	 * OUTPUT: array data
	 * ERROR:  -
	 */
	function array_flatten($data) {
		$result = array();
		foreach ($data as $item) {
			if (is_array($item)) {
				$result = array_merge($result, array_flatten($item));
			} else {
				array_push($result, $item);
			}
		}

		return $result;
	}

	/* Localized date string
	 *
	 * INPUT:  string format[, integer timestamp]
	 * OUTPUT: string date
	 * ERROR:  -
	 */
	function date_string($format, $timestamp = null) {
		if ($timestamp === null) {
			$timestamp = time();
		}

		$days_of_week = config_array(DAYS_OF_WEEK);
		$months_of_year = config_array(MONTHS_OF_YEAR);

		$format = strtr($format, "lDFM", "#$%&");
		$result = date($format, $timestamp);

		$day = $days_of_week[(int)date("N", $timestamp) - 1];
		$result = str_replace("#", $day, $result);

		$day = substr($days_of_week[(int)date("N", $timestamp) - 1], 0, 3);
		$result = str_replace("$", $day, $result);

		$month = $months_of_year[(int)date("n", $timestamp) - 1];
		$result = str_replace("%", $month, $result);

		$month = substr($months_of_year[(int)date("n", $timestamp) - 1], 0, 3);
		$result = str_replace("&", $month, $result);

		return $result;
	}

	/* Load configuration file
	 *
	 * INPUT:  string configuration file[, bool remove comments]
	 * OUTPUT: array( key => value[, ...] )
	 * ERROR:  -
	 */
	function config_file($config_file, $remove_comments = true) {
		static $cache = array();

		if (isset($cache[$config_file])) {
			return $cache[$config_file];
		}

		if (substr($config_file, 0, 1) != "/") {
			$config_file = "../settings/".$config_file.".conf";
		}
		if (file_exists($config_file) == false) {
			return array();
		}

		$config = array();
		foreach (file($config_file) as $line) {
			if ($remove_comments) {
				$line = trim(preg_replace("/(^| )#.*/", "", $line));
			}
			$line = rtrim($line);

			if ($line === "") {
				continue;
			}

			if (($prev = count($config) - 1) == -1) {
				array_push($config, $line);
			} else if (substr($config[$prev], -1) == "\\") {
				$config[$prev] = rtrim(substr($config[$prev], 0, strlen($config[$prev]) - 1)) . ltrim($line);
			} else {
				array_push($config, $line);
			}
		}

		$cache[$config_file] = $config;

		return $config;
	}

	/* Convert configuration line to array
	 *
	 * INPUT:  string config line[, bool look for key-value]
	 * OUTPUT: array config line
	 * ERROR:  -
	 */
	function config_array($line, $key_value = true) {
		$items = explode("|", $line);

		if ($key_value == false) {
			return $items;
		}

		$result = array();
		foreach ($items as $item) {
			list($key, $value) = explode(":", $item, 2);
			if ($value === null) {
				array_push($result, $key);
			} else {
				$result[$key] = $value;
			}
		}

		return $result;
	}

	/* Website configuration
	 */
	if (isset($_ENV["banshee_config_file"])) {
		$config_file = $_ENV["banshee_config_file"];
	} else {
		$config_file = "website";
	}

	foreach (config_file($config_file) as $line) {
		list($key, $value) = explode("=", chop($line), 2);
		define(trim($key), trim($value));
	}

	/* PHP settings
	 */
	ini_set("magic_quotes_runtime", 0);
	ini_set("zlib.output_compression", "Off");
	date_default_timezone_set("Europe/Amsterdam");
?>
