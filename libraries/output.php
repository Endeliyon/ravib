<?php
	/* Copyright (C) by Hugo Leisink <hugo@leisink.net>
	 * This file is part of the Banshee PHP framework
	 * http://www.banshee-php.org/
	 */

	final class output extends XML {
		private $settings = null;
		private $page = null;
		private $mode = null;
		private $language = null;
		private $description = null;
		private $keywords = null;
		private $system_messages = array();
		private $system_warnings = array();
		private $messages = array();
		private $javascripts = array();
		private $onload_javascript = array();
		private $alternates = array();
		private $title = null;
		private $css_links = array();
		private $inline_css = null;
		private $content_type = "text/html; charset=utf-8";
		private $layout = LAYOUT_SITE;
		private $disabled = false;
		private $mobile = false;
		private $add_layout_data = true;
		private $hiawatha_cache_time = null;
		private $http_status = 200;

		/* Constructor
		 *
		 * INPUT:  object database, object settings, object page
		 * OUTPUT: -
		 * ERROR:  -
		 */
		public function __construct($db, $settings, $page) {
			parent::__construct($db);
			$this->settings = $settings;
			$this->page = $page;

			if ($this->page->ajax_request) {
				$this->mode = "xml";
				$this->add_layout_data = false;
			} else if (isset($_GET["output"])) {
				if (($this->mode = $_GET["output"]) == "raw") {
					$this->mode = "xml";
				} else {
					$this->add_layout_data = false;
				}
			}

			$this->language = $this->settings->default_language;
			$this->description = $this->settings->head_description;

			$this->set_layout();

			/* Mobile devices
			 */
			$mobiles = array("iPhone", "Android");
			foreach ($mobiles as $mobile) {
        		if (strpos($_SERVER["HTTP_USER_AGENT"], $mobile) !== false) {
		            $this->mobile = true;
				}
			}
		}

		/* Constructor
		 *
		 * INPUT:  object database, boolean AJAX request
		 * OUTPUT: -
		 * ERROR:  -
		 */
		public function __destruct() {
			$_SESSION["previous_layout"] = $this->layout;
		}

		/* Magic method get
		 *
		 * INPUT:  string key
		 * OUTPUT: mixed value
		 * ERROR:  null
		 */
		public function __get($key) {
			switch ($key) {
				case "mode": return $this->mode;
				case "language": return $this->language;
				case "description": return $this->description;
				case "keywords": return $this->keywords;
				case "title": return $this->title;
				case "inline_css": return $this->inline_css;
				case "content_type": return $this->content_type;
				case "layout": return $this->layout;
				case "disabled": return $this->disabled;
				case "mobile": return $this->mobile;
				case "add_layout_data": return $this->add_layout_data;
				case "http_status": return $this->http_status;
			}

			return parent::__get($key);
		}

		/* Magic method set
		 *
		 * INPUT:  string key, mixed value
		 * OUTPUT: -
		 * ERROR:  -
		 */
		public function __set($key, $value) {
			switch ($key) {
				case "mode": $this->mode = $value; break;
				case "language": $this->language = $value; break;
				case "description": $this->description = $value; break;
				case "keywords": $this->keywords = $value; break;
				case "title": $this->title = $value; break;
				case "inline_css": $this->inline_css = $value; break;
				case "content_type": $this->content_type = $value; break;
				case "add_layout_data": $this->add_layout_data = $value; break;
				case "http_status": $this->http_status = $value; break;
				default: trigger_error("Unknown output variable: ".$key);
			}
		}

		/* Disable the output library
		 *
		 * INPUT:  -
		 * OUTPUT: -
		 * ERROR:  -
		 */
		public function disable() {
			$this->disabled = true;
			parent::clear_buffer();
		}

		/* Allow caching of output by Hiawatha
		 *
		 * INPUT:  -
		 * OUTPUT: -
		 * ERROR:  -
		 */
		public function allow_hiawatha_cache($time = null) {
			if ($time === null) {
				$this->hiawatha_cache_time = $this->settings->hiawatha_cache_default_time;
			} else if ((int)$time > 0) {
				$this->hiawatha_cache_time = (int)$time;
			}
		}

		/* Determines whether the Hiawatha cache should be enabled or not
		 *
		 * INPUT:  -
		 * OUTPUT: boolean activate Hiawatha cache
		 * ERROR:  -
		 */
		private function activate_hiawatha_cache() {
			static $result = true;

			if ($result == false) {
				return false;
			}

			list($webserver) = explode(" ", $_SERVER["SERVER_SOFTWARE"], 2);

			if ($webserver != "Hiawatha") {
				$result = false;
			} else if ($this->settings->hiawatha_cache_enabled == false) {
				$result = false;
			} else if ($this->hiawatha_cache_time === null) {
				$result = false;
			} else if (isset($_SESSION["user_switch"])) {	
				$result = false;
			} else if (is_true(DEBUG_MODE)) {
				$result = false;
			} else if ($_SERVER["REQUEST_METHOD"] != "GET") {
				$result = false;
			} else if (count($this->system_messages) > 0) {
				$result = false;
			} else if (count($this->system_warnings) > 0) {
				$result = false;
			} else if (count($this->messages) > 0) {
				$result = false;
			} else if ($this->page->http_code != 200) {
				$result = false;
			}

			return $result;
		}

		/* Add CSS link to output
		 *
		 * INPUT:  string CSS filename
		 * OUTPUT: boolean CSS file exists
		 * ERROR:  -
		 */
		public function add_css($css) {
			if (file_exists("css/".$css) == false) {
				return false;
			}
			$css = "/css/".$css;

			if (in_array($css, $this->css_links) == false) {
				array_push($this->css_links, $css);
			}

			return true;
		}

		/* Add javascript link
		 *
		 * INPUT:  string link
		 * OUTPUT: -
		 * ERROR:  -
		 */
		public function add_javascript($script) {
			if ((substr($script, 0, 7) != "http://") && (substr($script, 0, 8) != "https://")) {
				if (file_exists("js/".$script) == false) {
					if (is_true(DEBUG_MODE)) {
						printf("Javascript %s not found.\n", $script);
					}
					return false;
				}

				$script = "/js/".$script;
			}

			if (in_array($script, $this->javascripts) == false) {
				array_push($this->javascripts, $script);
			}

			return true;
		}

		/* Set onload function of body tag
		 *
		 * INPUT:  string javascript code
		 * OUTPUT: -
		 * ERROR:  -
		 */
		public function run_javascript($code) {
			array_push($this->onload_javascript, rtrim($code, ";").";");
		}

		/* Add alternate link
		 *
		 * INPUT:  string title, string type, string url
		 * OUTPUT: -
		 * ERROR:  -
		 */
		public function add_alternate($title, $type, $url) {
			array_push($this->alternates, array(
				"title" => $title,
				"type"  => $type,
				"url"   => $url));
		}

		/* Set page layout
		 *
		 * INPUT:  string layout
		 * OUTPUT: bool layout accepted
		 * ERROR:  -
		 */
		public function set_layout($layout = null) {
			if ($layout === null) {
				$inherit_layout = array(LOGOUT_MODULE, "password");
				if (substr($this->page->url, 0, 4) == "/cms") {
					$this->layout = LAYOUT_CMS;
				} else if (in_array($this->page->module, $inherit_layout)) {
					if ($_SESSION["previous_layout"] == LAYOUT_CMS) {
						$this->layout = LAYOUT_CMS;
					}
				}
			} else {
				if (file_exists("../views/banshee/layout_".$layout.".xslt") == false) {
					return false;
				}

				$this->layout = $layout;
			}

			return true;
		}

		/* Add system message to output
		 *
		 * INPUT:  string format[, string arg, ...]
		 * OUTPUT: -
		 * ERROR:  -
		 */
		public function add_system_message() {
			if (func_num_args() == 0) {
				return;
			}

			$args = func_get_args();
			$format = array_shift($args);

			array_push($this->system_messages, vsprintf($format, $args));
		}

		/* Add system warning to output
		 *
		 * INPUT:  string format[, string arg,...]
		 * OUTPUT: -
		 * ERROR:  -
		 */
		public function add_system_warning() {
			if (func_num_args() == 0) {
				return;
			}

			$args = func_get_args();
			$format = array_shift($args);

			array_push($this->system_warnings, vsprintf($format, $args));
		}

		/* Add message to message buffer
		 *
		 * INPUT:  string format[, string arg,...]
		 * OUTPUT: -
		 * ERROR:  -
		 */
		public function add_message($message) {
			if (func_num_args() == 0) {
				return;
			}

			$args = func_get_args();
			$format = array_shift($args);

			array_push($this->messages, vsprintf($format, $args));
		}

		/* Close XML tag
		 *
		 * INPUT:  -
		 * OUTPUT: -
		 * ERROR:  -
		 */
		public function close_tag() {
			if ($this->add_layout_data && ($this->depth == 1)) {
				/* System messages
				 */
				if (count($this->system_messages) > 0) {
					$this->open_tag("system_messages");
					foreach ($this->system_messages as $message) {
						$this->add_tag("message", $message);
					}
					$this->close_tag();
				}

				/* System warnings
				 */
				if (count($this->system_warnings) > 0) {
					$this->open_tag("system_warnings");
					foreach ($this->system_warnings as $warning) {
						$this->add_tag("warning", $warning);
					}
					$this->close_tag();
				}

				/* Messages
				 */
				if (count($this->messages) > 0) {
					$this->open_tag("messages");
					foreach ($this->messages as $message) {
						$this->add_tag("message", $message);
					}
					$this->close_tag();
				}

				$this->open_tag("layout_".$this->layout);

				/* Header information
				 */
				if (($this->keywords != null) && ($this->settings->head_keywords != null)) {
					$this->keywords .= ", ".$this->settings->head_keywords;
				} else if ($this->settings->head_keywords != null) {
					$this->keywords = $this->settings->head_keywords;
				}
				$this->add_tag("description", $this->description);
				$this->add_tag("keywords", $this->keywords);
				$this->add_tag("title", $this->settings->head_title, array("page" => $this->title));
				$this->add_tag("language", $this->language);

				/* Cascading Style Sheets
				 */
				$this->open_tag("styles");
				foreach ($this->css_links as $css) {
					$this->add_tag("style", $css);
				}
				$this->close_tag();
				$this->add_tag("inline_css", $this->inline_css);

				/* Javascripts
				 */
				$params = array();
				if (count($this->onload_javascript) > 0) {
					$params["onload"] = implode(" ", $this->onload_javascript);
				}
				$this->open_tag("javascripts", $params);
				foreach ($this->javascripts as $javascript) {
					$this->add_tag("javascript", $javascript);
				}
				$this->close_tag();

				/* Alternates
				 */
				$this->open_tag("alternates");
				foreach ($this->alternates as $alternate) {
					$this->add_tag("alternate", $alternate["title"], array(
						"type"  => $alternate["type"],
						"url" => $alternate["url"]));
				}
				$this->close_tag();

				$this->close_tag();
			}

			parent::close_tag();
		}

		/* Mask transform function
		 *
		 * INPUT:  string XSLT filename
		 * OUTPUT: false
		 * ERROR:  -
		 */
		public function transform($xslt_file) {
			return false;
		}

		/* Optimize array from XML for JSON
		 *
		 * INPUT:  array data
		 * OUTPUT: array data
		 * ERROR:  -
		 */
		private function optimize_for_json($data) {
			if (is_array($data) == false) {
				return $data;
			}

			$made_array = array();

			$result = array();
			foreach ($data as $item) {
				$key = $item["name"];
				$value = $this->optimize_for_json($item["content"]);

				/* Attributes
				 */
				if (count($item["attributes"]) > 0) {
					if (is_array($value) == false) {
						$value = array($value);
					}
					$attr = array();
					foreach ($item["attributes"] as $attrib_key => $attrib_value) {
						if (($attrib_key == "id") && is_numeric($attrib_value)) {
							$attrib_value = (int)$attrib_value;
						}
						$attr["@".$attrib_key] = $attrib_value;
					}
					$value = array_merge($attr, $value);
				}

				/* Values
				 */
				if (isset($result[$key])) {
					if ($made_array[$key] == false) {
						$result[$key] = array($result[$key]);
						$made_array[$key] = true;
					}
					array_push($result[$key], $value);
				} else {
					$result[$key] = $value;
					$made_array[$key] = false;
				}
			}

			return $result;
		}

		/* Check if it's ok to gzip output
		 *
		 * INPUT:  str output
		 * OUTPUT: bool ok to gzip output
		 * ERROR:  -
		 */
		private function can_gzip_output($data) {
			if (headers_sent()) {	
				return false;
			} else if (($encodings = $_SERVER["HTTP_ACCEPT_ENCODING"]) === null) {
				return false;
			} else if ($this->activate_hiawatha_cache()) {
				return false;
			}

			$encodings = explode(",", $encodings);
			foreach ($encodings as $encoding) {
				if (trim($encoding) == "gzip") {
					return true;
				}
			}

			return false;
		}

		/* Generate output via XSLT
		 *
		 * INPUT:  -
		 * OUTPUT: -
		 * ERROR:  -
		 */
		public function generate() {
			if ($this->disabled) {
				return;
			}

			if ((headers_sent() == false) && ($this->http_status != 200)) {
				header(sprintf("Status: %d", $this->http_status));
			}

			switch ($this->mode) {
				case "json":
					$data = $this->array;
					$data = $this->optimize_for_json($data);
					header("Content-Type: application/json");
					$result = json_encode($data["output"]);
					break;
				case "xml":
					header("Content-Type: text/xml");
					$result = $this->document;
					break;
				case "data":
					header("Content-Type: text/plain");
					$result = $this->document;
					break;
				case null:
					$xslt_file = "../views/".$this->page->view.".xslt";
					if (($result = parent::transform($xslt_file)) === false) {
						header("Status: 500");
						header("Content-Type: text/plain");
						$result = "Banshee: Fatal XSL Transformation error.\n";
						if (file_exists($xslt_file) == false) {
							$result .= sprintf("%s: file not found.\n", substr($xslt_file, 3));
						} else {
							$result .= sprintf("%s: invalid XML.\n", substr($xslt_file, 3));
						}
						break;
					}

					/* Print headers
					 */
					if (headers_sent() == false) {
						header("X-Frame-Options: sameorigin");
						if ($this->activate_hiawatha_cache()) {
							header("X-Hiawatha-Cache: ".$this->hiawatha_cache_time);
						}
						header("Content-Type: ".$this->content_type);
						header("Content-Language: ".$this->language);
						if (is_false(ini_get("zlib.output_compression"))) {
							if ($this->can_gzip_output($result)) {
								header("Content-Encoding: gzip");
								$result = gzencode($result, 6);
							}
							header("Content-Length: ".strlen($result));
						}
						header("Vary: Accept-Encoding");
						header("X-Powered-By: Banshee PHP framework v".BANSHEE_VERSION);
					}
					break;
				default:
					$result = "Unknown output type";
			}

			return $result;
		}
	}
?>
