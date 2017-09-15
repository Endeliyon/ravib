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

			if ($this->settings->pia_version < 12) {
				return "update_pia";
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

		/* Add setting when missing
		 */
		private function ensure_setting($key, $type, $value) {
			if ($this->db->entry("settings", $key, "key") != false) {
				return true;
			}

			$entry = array(
				"key"   => $key,
				"type"  => $type,
				"value" => $value);
			return $this->db->insert("settings", $entry) !== false;
		}

		/* Update database
		 */
		public function update_database() {
			system("../database/private_modules");

			if ($this->settings->database_version < 1) {
				$this->settings->database_version = 1;
			}

			if ($this->settings->database_version < 2) {
				$this->ensure_setting("hiawatha_cache_enabled", "boolean", "false");
				$this->ensure_setting("hiawatha_cache_default_time", "integer", "3600");
				$this->ensure_setting("session_timeout", "integer", "3600");
				$this->ensure_setting("session_persistent", "boolean", "false");

				$this->settings->database_version = 2;
			}

			if ($this->settings->database_version < 3) {
				$this->db->query("alter table %S add %S varchar(100) not null after %S", "pia_rules", "law_section", "no_next");
				$this->db->query("alter table %S change %S %S tinyint unsigned not null", "bia", "integrity", "integrity");
				$this->db->query("alter table %S change %S %S tinyint unsigned not null", "bia", "confidentiality", "confidentiality");
				$this->db->query("alter table %S drop %S", "bia", "rpo");
				$this->db->query("alter table %S drop %S", "bia", "rto");
				$this->db->query("alter table %S add %S tinyint unsigned not null after %S", "bia", "availability", "impact");
				$this->db->query("alter table %S drop %S", "iso_measures", "description");

				$this->db->query("create table %S (%S int(10) unsigned not null, %S int(10) unsigned not null, ".
				                 "%S int(10) unsigned not null, key %S (%S), key %S (%S), key %S (%S), ".
				                 "constraint %S foreign key (%S) references %S (%S), ".
				                 "constraint %S foreign key (%S) references %S (%S), ".
				                 "constraint %S foreign key (%S) references %S (%S)".
				                 ") engine=innodb default charset=utf8",
					"case_bia_threat", "case_id", "bia_id", "threat_id",
					"bia_id", "bia_id", "threat_id", "threat_id", "case_id", "case_id",
					"case_bia_threat_ibfk_3", "threat_id", "threats", "id",
					"case_bia_threat_ibfk_1", "case_id", "cases", "id",
					"case_bia_threat_ibfk_2", "bia_id", "bia", "id");

				$this->settings->pia_version = 12;

				$this->settings->database_version = 3;
			}

			if ($this->settings->database_version < 4) {
				$this->db->query("alter table %S change %S %S varchar(256) character set utf8 ".
				                 "collate utf8_general_ci not null",
					"sessions", "session_id", "session_id");

				$this->db->query("alter table %S add %S text not null after %S", "cases", "impact", "description");
				$this->db->query("alter table %S add %S varchar(250) null after %S", "cases", "logo", "impact");

				$this->db->query("create table %S (%S int(10) unsigned NOT NULL auto_increment, ".
				                 "%S int(10) unsigned not null, %S tinyint(4) not null, ".
				                 "%S varchar(100) not null, primary key (%S), ".
				                 "key %S (%S), constraint %S foreign key (%S) references %S (%S) ".
				                 ") engine=InnoDB default charset=utf8", 
					"iso_measure_categories", "id", "iso_standard_id", "number", "name",
					"id", "iso_standard", "iso_standard_id",
					"iso_measure_categories_ibfk_1", "iso_standard_id", "iso_standards", "id");

				$this->db->query("create table %S (%S int(10) unsigned not null auto_increment, ".
				                 "%S int(10) unsigned not null, %S varchar(100) not null,".
				                 "%S varchar(100) not null, primary key (%S), key %S (%S), ".
				                 "constraint %S foreign key (%S) references %S (%S) ".
				                 ") engine=innodb default charset=utf8",
					"progress_people", "id", "case_id", "name", "email", "id", "case_id", "case_id",
					"progress_people_ibfk_1", "case_id", "cases", "id");

				$this->db->query("create table %S (%S int(10) unsigned not null, ".
				                 "%S int(10) unsigned default null, %S int(10) unsigned default null, ".
				                 "%S int(10) unsigned not null, %S date default null, ".
				                 "%S text not null, %S tinyint(1) not null, key %S (%S), ".
				                 "key %S (%S), key %S (%S), key %S (%S), ".
				                 "constraint %S foreign key (%S) references %S (%S), ".
				                 "constraint %S foreign key (%S) references %S (%S), ".
				                 "constraint %S foreign key (%S) references %S (%S), ".
				                 "constraint %S foreign key (%S) references %S (%S) ".
				                 ") engine=innodb default charset=utf8;",
					"progress_tasks", "case_id", "actor_id", "reviewer_id", "iso_measure_id", "deadline",
					"info", "done", "case_id", "case_id", "progress_people_id", "actor_id", "iso_measure_id",
					"iso_measure_id", "reviewer_id", "reviewer_id", "progress_tasks_ibfk_1", "case_id",
					"cases", "id", "progress_tasks_ibfk_3", "iso_measure_id", "iso_measures", "id",
					"progress_tasks_ibfk_4", "actor_id", "progress_people", "id", "progress_tasks_ibfk_5",
					"reviewer_id", "progress_people", "id");

				system("mysql -h '".DB_HOSTNAME."' -u '".DB_USERNAME."' --password='".DB_PASSWORD."' '".DB_DATABASE."' < ../database/imc.sql", $result);

				$this->db->query("update roles set %S=%d, %S=%d, %S=%d where id=%d", "voortgang", YES, "voortgang/personen", YES, "voortgang/rapport", YES, USER_ROLE_ID);

				$this->settings->database_version = 4;
			}

			if ($this->settings->database_version < 5) {
				$this->db->query("alter table %S add %S varchar(100) not null after %S",
					"cases", "organisation", "name");
				$this->db->query("alter table %S change %S %S text character set utf8 ".
				                 "collate utf8_general_ci not null",
					"cases", "description", "scope");
				$this->db->query("alter table %S add %S boolean not null after %S",
					"cases", "visible", "logo");
				$this->db->query("alter table %S change %S %S enum(%s, %s, %s) ".
				                 "character set utf8 collate utf8_general_ci not null",
					"bia", "location", "location", "intern", "extern", "saas");
				$this->db->query("alter table %S add %S text null after %S",
					"pia", "comment", "answer");

				$this->settings->database_version = 5;
			}

			if ($this->settings->database_version < 5) {
				$this->db->query("alter table %S add %S smallint unsigned not null after %S, ".
				                 "add %S smallint unsigned not null after %S",
					"progress_tasks", "hours_planned", "done", "hours_invested", "hours_planned");

				$this->settings->database_version = 6;
			}

			return true;
		}

		public function update_pia() {
			if ($this->settings->pia_version < 12) {
				if (($result = $this->db->execute("select count(*) as count from pia")) === false) {
					return false;
				}

				if ($result[0]["count"] > 0) {
					$this->output->add_message("De database bevat nog PIA's. Deze moeten verwijderd worden voordat de PIA regels kunnen worden bijgewerkt.");
					return false;
				}

				system("mysql -h '".DB_HOSTNAME."' -u '".DB_USERNAME."' --password='".DB_PASSWORD."' '".DB_DATABASE."' < ../database/pia.sql", $result);
				if ($result != 0) {
					$this->output->add_message("Fout bij het bijwerken van de PIA regels.");
					return false;
				}

				$this->settings->pia_version = 12;
			}

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
