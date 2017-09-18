<?php
	/* Copyright (C) by Hugo Leisink <hugo@leisink.net>
	 * This file is part of the Banshee PHP framework
	 * http://www.banshee-php.org/
	 */

	class setup_model extends model {
		/* Determine next step
		 */
		public function step_to_take() {
			$missing = $this->missing_php_extensions();
			if (count($missing) > 0) {
				return "php_extensions";
			}

			exec("which mysql", $output, $result);
			if ($result != 0) {
				return "mysql_client";
			}

			if ($this->db->connected == false) {
				$db = new MySQLi_connection(DB_HOSTNAME, DB_DATABASE, DB_USERNAME, DB_PASSWORD);
			} else { 
				$db = $this->db;
			}

			if ($db->connected == false) {
				/* No database connection
				 */
				if ((DB_HOSTNAME == "localhost") && (DB_DATABASE == "ravib") && (DB_USERNAME == "ravib") && (DB_PASSWORD == "ravib")) {
					return "db_settings";
				} else if (strpos(DB_PASSWORD, "'") !== false) {
					$this->output->add_system_message("De enkele quote is niet toegestaan in het database wachtwoord.");
					return "db_settings";
				}

				return "create_db";
			}

			$result = $db->execute("show tables like %s", "settings");
			if (count($result) == 0) {
				return "import_sql";
			}

			if ($this->settings->database_version < $this->latest_database_version()) {
				return "update_db";
			}

			$result = $db->execute("select password from users where username=%s", "admin");
			if ($result[0]["password"] == "none") {
				return "credentials";
			}

			return "done";
		}

		/* Missing PHP extensions
		 */
		public function missing_php_extensions() {
			static $missing = null;

			if ($missing !== null) {
				return $missing;
			}

			$missing = array();
			foreach (array("libxml", "mysqli", "xsl") as $extension) {
				if (extension_loaded($extension) == false) {
					array_push($missing, $extension);
				}
			}

			return $missing;
		}

		/* Remove datase related error messages
		 */
		public function remove_database_errors() {
			$errors = explode("\n", rtrim(ob_get_contents()));
			ob_clean();

			foreach ($errors as $error) {
				if (strtolower(substr($error, 0, 14)) != "mysqli_connect") {
					print $error;
				}
			}
		}

		/* Create the MySQL database
		 */
		public function create_database($username, $password) {
			$db = new MySQLi_connection(DB_HOSTNAME, "mysql", $username, $password);

			if ($db->connected == false) {
				$this->output->add_message("Fout bij verbinden naar de database.");
				return false;
			}

			$db->query("begin");

			/* Create database
			 */
			$query = "create database if not exists %S character set utf8";
			if ($db->query($query, DB_DATABASE) == false) {
				$db->query("rollback");
				$this->output->add_message("Fout bij het aanmaken van de database.");
				return false;
			}

			/* Create user
			 */
			$query = "select count(*) as count from user where User=%s";
			if (($users = $db->execute($query, DB_USERNAME)) === false) {
				$db->query("rollback");
				$this->output->add_message("Fout bij controleren van gebruiker.");
				return false;
			}

			if ($users[0]["count"] == 0) {
				$query = "create user %s@%s identified by %s";
				if ($db->query($query, DB_USERNAME, DB_HOSTNAME, DB_PASSWORD) == false) {
					$db->query("rollback");
					$this->output->add_message("Fout bij aanmaken van gebruiker.");
					return false;
				}
			} else {
				$login_test = new MySQLi_connection(DB_HOSTNAME, DB_DATABASE, DB_USERNAME, DB_PASSWORD);
				if ($login_test->connected == false) {
					$db->query("rollback");
					$this->output->add_message("Ongeldige credentials in settings/website.conf.");
					return false;
				}
			}

			/* Set access rights
			 */
			$rights = array(
				"select", "insert", "update", "delete",
				"create", "drop", "alter", "index", "lock tables",
				"create view", "show view");

			$query = "grant ".implode(", ", $rights)." on %S.* to %s@%s";
			if ($db->query($query, DB_DATABASE, DB_USERNAME, DB_HOSTNAME) == false) {
				$db->query("rollback");
				$this->output->add_message("Fout bij instellen van toegangsrechten.");
				return false;
			}

			/* Commit changes
			 */
			$db->query("commit");
			$db->query("flush privileges");
			unset($db);

			return true;
		}

		/* Import SQL script from file
		 */
		public function import_sql() {
			exec("mysql -h '".DB_HOSTNAME."' -u '".DB_USERNAME."' --password='".DB_PASSWORD."' '".DB_DATABASE."' < ../database/mysql.sql", $output, $result);
			if ($result != 0) {
				$this->output->add_message("Fout bij importeren van SQL bestand.");
				return false;
			}

			$this->db->query("update users set status=%d", USER_STATUS_CHANGEPWD);
			$this->settings->secret_website_code = random_string(32);

			return true;
		}

		/* Collect latest database version from update_database() function
		 */
		private function latest_database_version() {
			$old_db = $this->db;
			$old_settings = $this->settings;
			$this->db = new dummy_object();
			$this->settings = new dummy_object();
			$this->settings->database_version = 0;

			$this->update_database();
			$version = $this->settings->database_version;

			unset($this->db);
			unset($this->settings);
			$this->db = $old_db;
			$this->settings = $old_settings;

			return $version;
		}

		/* Update database
		 */
		public function update_database() {
			system("../database/private_modules");

			return true;
		}

		/* Set administrator password
		 */
		public function set_admin_credentials($post_data) {
			$username = $post_data["username"];
			$password = $post_data["password"];
			$repeat = $post_data["repeat"];

			$result = true;

			if (valid_input($username, VALIDATE_LETTERS, VALIDATE_NONEMPTY) == false) {
				$this->output->add_message("De gebruikersnaam dient enkel uit kleine letters te bestaan.");
				$result = false;
			}

			if ($password != $repeat) {
				$this->output->add_message("De gekozen wachtwoorden komen niet overeen.");
				$result = false;
			}

			if ($result == false) {
				return false;
			}

			if (is_false($post_data["password_hashed"])) {
				$password = hash(PASSWORD_HASH, $password.hash(PASSWORD_HASH, $username));
			}

			$query = "update users set username=%s, password=%s, status=%d where username=%s";
			if ($this->db->query($query, $username, $password, USER_STATUS_ACTIVE, "admin") === false) {
				$this->output->add_message("Fout tijdens het instellen van het wachtwoord.");
				return false;
			}

			return true;
		}
	}

	class dummy_object {
		private $cache = array();

		public function __set($key, $value) {
			$this->cache[$key] = $value;
		}

		public function __get($key) {
			return $this->cache[$key];
		}

		public function __call($func, $args) {
			return false;
		}
	}
?>
