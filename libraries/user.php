<?php
	/* Copyright (C) by Hugo Leisink <hugo@leisink.net>
	 * This file is part of the Banshee PHP framework
	 * http://www.banshee-php.org/
	 */

	final class user {
		private $db = null;
		private $settings = null;
		private $session = null;
		private $logged_in = false;
		private $record = array();
		private $is_admin = false;

		/* Constructor
		 *
		 * INPUT:  object database, object settings, object session
		 * OUTPUT: -
		 * ERROR:  -
		 */
		public function __construct($db, $settings, $session) {
			$this->db = $db;
			$this->settings = $settings;
			$this->session = $session;

			/* Basic HTTP Authentication for web services
			 */
			if (isset($_SERVER["HTTP_AUTHORIZATION"])) {
				list($method, $auth) = explode(" ", $_SERVER["HTTP_AUTHORIZATION"], 2);
				if (($method == "Basic") && (($auth = base64_decode($auth)) !== false)) {
					list($username, $password) = explode(":", $auth, 2);
					if ($this->login_password($username, $password, false) == false) {
						header("Status: 401");
					} else {
						$this->bind_to_ip();
					}
				}
			}

			if (isset($_SESSION["user_id"])) {
				if (time() - $_SESSION["last_private_visit"] >= $this->settings->session_timeout) {
					$this->logout();
				} else if (($_SESSION["binded_ip"] === NO) || ($_SESSION["binded_ip"] === $_SERVER["REMOTE_ADDR"])) {
					$this->load_user_record($_SESSION["user_id"]);
				}
			}
		}

		/* Magic method get
		 *
		 * INPUT:  string key
		 * OUTPUT: mixed value
		 * ERROR:  null
		 */
		public function __get($key) {
			switch ($key) {
				case "logged_in": return $this->logged_in;
				case "is_admin": return $this->is_admin;
				case "do_not_track": return $_SERVER["HTTP_DNT"] == 1;
				case "session_via_database": return $this->session->using_database;
				default:
					if (isset($this->record[$key])) {
						return $this->record[$key];
					}
			}

			return null;
		}

		/* Store user information from database in $this->record
		 *
		 * INPUT:  int user identifier
		 * OUTPUT: -
		 * ERROR:  -
		 */
		private function load_user_record($user_id) {
			if (($this->record = $this->db->entry("users", $user_id)) == false) {
				$this->logout();
			} else if ($this->record["status"] == USER_STATUS_DISABLED) {
				$this->logout();
			} else {
				$this->logged_in = true;

				$this->record["role_ids"] = array();
				$query = "select role_id from user_role where user_id=%d";
				if (($roles = $this->db->execute($query, $this->record["id"])) != false) {
					foreach ($roles as $role) {
						array_push($this->record["role_ids"], $role["role_id"]);
						if ((int)$role["role_id"] === (int)ADMIN_ROLE_ID) {
							$this->is_admin = true;
						}
					}
				}
			}
		}

		/* Login user
		 *
		 * INPUT:  int user id
		 * OUTPUT: -
		 * ERROR:  -
		 */
		private function login($user_id) {
			$this->load_user_record($user_id);
			$this->log_action("user logged-in");

			$_SESSION["user_id"] = $user_id;
			$_SESSION["binded_ip"] = NO;
			$_SESSION["last_private_visit"] = time();

			$this->session->set_user_id($user_id);

			unset($_SESSION["challenge"]);
		}

		/* Verify user credentials
		 *
		 * INPUT:  string username, string password, boolean challenge-response method used
		 * OUTPUT: boolean login correct
		 * ERROR:  -
		 */
		public function login_password($username, $password, $use_challenge_response_method) {
			$query = "select * from users where username=%s and status!=%d limit 1";
			if (($data = $this->db->execute($query, $username, USER_STATUS_DISABLED)) == false) {
				header("X-Hiawatha-Monitor: failed_login");
				sleep(1);
				return false;
			}
			$user = $data[0];

			usleep(rand(0, 10000));

			if ($use_challenge_response_method) {
				if (hash(PASSWORD_HASH, $_SESSION["challenge"].$user["password"]) === $password) {
					$this->login((int)$user["id"]);
				}
			} else {
				if ($user["password"] === hash(PASSWORD_HASH, $password.hash(PASSWORD_HASH, $username))) {
					$this->login((int)$user["id"]);
				}
			}

			if ($this->logged_in == false) {
				header("X-Hiawatha-Monitor: failed_login");
				sleep(1);
			}

			return $this->logged_in;
		}

		/* Verify one time key
		 *
		 * INPUT:  string one time key
		 * OUTPUT: boolean key valid
		 * ERROR:  -
		 */
		public function login_one_time_key($key) {
			if ($key == "") {
				return false;
			}

			usleep(rand(0, 100000));

			$query = "select * from users where one_time_key=%s and status!=%d limit 1";
			if (($data = $this->db->execute($query, $key, USER_STATUS_DISABLED)) == false) {
				header("X-Hiawatha-Monitor: failed_login");
				sleep(1);
				return false;
			}
			$user = $data[0];

			$query = "update users set one_time_key=null where id=%d";
			$this->db->query($query, $user["id"]);

			$this->login((int)$user["id"]);
			$this->bind_to_ip();

			return true;
		}

		/* Logout current user
		 *
		 * INPUT:  -
		 * OUTPUT: -
		 * ERROR:  -
		 */
		public function logout() {
			$this->log_action("user logged-out");

			$this->session->reset();

			$this->logged_in = false;
			$this->record = array();
			$this->is_admin = false;
		}

		/* Checks if user has access to page
		 *
		 * INPUT:  string page identifier
		 * OUTPUT: boolean user has access to page
		 * ERROR:  -
		 */
		public function access_allowed($page) {
			static $access = array();

			/* Always access
			 */
			$allowed = array(LOGOUT_MODULE);
			if ($this->is_admin || in_array($page, $allowed)) {
				return true;
			}

			/* Public module
			 */
			if (in_array($page, page_to_module(config_file("public_modules")))) {
				return true;
			}

			/* Public page in database
			*/
			$query = "select count(*) as count from pages where url=%s and private=%d";
			if (($result = $this->db->execute($query, "/".$page, NO)) == false) {
				return false;
			} else if ($result[0]["count"] > 0) {
				return true;
			}

			/* No roles, no access
			 */
			if (count($this->record["role_ids"]) == 0) {
				return false;
			}

			/* Cached?
			 */
			if (isset($access[$page])) {	
				return $access[$page];
			}

			/* Check access
			 */
			$conditions = $rids = array();
			foreach ($this->record["role_ids"] as $rid) {
				array_push($conditions, "%d");
				array_push($rids, $rid);
			}

			if (in_array($page, page_to_module(config_file("private_modules")))) {
				/* Pages on disk (modules)
				 */
				$query = "select %S from roles where id in (".implode(", ", $conditions).")";
				if (($access = $this->db->execute($query, $page, $rids)) == false) {
					return false;
				}
			} else {
				/* Pages in database
				 */
				$query = "select a.level from page_access a, pages p ".
				         "where a.page_id=p.id and p.url=%s and a.level>0 ".
				         "and a.role_id in (".implode(", ", $conditions).")";
				if (($access = $this->db->execute($query, "/".$page, $rids)) == false) {
					return false;
				}
			}

			$access[$page] = max(array_flatten($access)) > 0;

			return $access[$page];
		}

		/* Bind current session to IP address
		 *
		 * INPUT:  -
		 * OUTPUT: -
		 * ERROR:  -
		 */
		public function bind_to_ip() {
			$_SESSION["binded_ip"] = $_SERVER["REMOTE_ADDR"];
		}

		/* Verify if user has a certain role
		 *
		 * INPUT:  int role identifier / string role name
		 * OUTPUT: boolean user has role
		 * ERROR:  -
		 */
		public function has_role($role) {
			if (is_int($role)) {
				return in_array($role, $this->record["role_ids"]);
			} else if (is_string($role)) {
				if (($entry = $this->db->entry("roles", $role, "name")) != false) {
					return $this->has_role((int)$entry["id"]);
				}
			} else if (is_array($role)) {
				foreach ($role as $item) {
					if ($this->has_role($item)) {
						return true;
					}
				}
			}

			return false;
		}

		/* Log user action
		 *
		 * INPUT:  string action
		 * OUTPUT: true
		 * ERROR:  false
		 */
		public function log_action($action) {
			if (func_num_args() > 1) {
				$args = func_get_args();
				array_shift($args);
				$action = vsprintf($action, $args);
			}

			$mesg = $_SERVER["REMOTE_ADDR"]."|".date("D d M Y H:i:s")."|";
			if ($this->logged_in == false) {
				$mesg .= "-";
			} else if (isset($_SESSION["user_switch"]) == false) {
				$mesg .= $this->id;
			} else {
				$mesg .= $_SESSION["user_switch"].":".$this->id;
			}
			$mesg .= "|".$action."\n";

			if (($fp = fopen("../logfiles/actions.log", "a")) == false) {
				return false;
			}

			fputs($fp, $mesg);
			fclose($fp);

			return true;
		}
	}
?>
