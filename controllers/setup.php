<?php
	/* Copyright (C) by Hugo Leisink <hugo@leisink.net>
	 * This file is part of the Banshee PHP framework
	 * http://www.banshee-php.org/
	 */

	class setup_controller extends controller {
		public function execute() {
			if ($_SERVER["HTTP_SCHEME"] != "https") {
				$this->output->add_system_warning("Let op! Deze verbinding is onversleuteld!");
			}

			if ($_SERVER["REQUEST_METHOD"] == "POST") {
				if ($_POST["submit_button"] == "Create database") {
					$this->model->create_database($_POST["username"], $_POST["password"]);
				} else if ($_POST["submit_button"] == "Importeer SQL") {
					$this->model->import_sql();
				} else if ($_POST["submit_button"] == "Database bijwerken") {
					$this->model->update_database();
				} else if ($_POST["submit_button"] == "PIA bijwerken") {
					$this->model->update_pia();
				} else if ($_POST["submit_button"] == "Set password") {
					$this->model->set_admin_credentials($_POST);
				}
			}

			$step = $this->model->step_to_take();
			$this->output->open_tag($step);
			switch ($step) {
				case "php_extensions":
					$missing = $this->model->missing_php_extensions();
					foreach ($this->model->missing_php_extensions() as $extension) {
						$this->output->add_tag("extension", $extension);
					}
					break;
				case "mysql_client":
					break;
				case "db_settings":
					$this->model->remove_database_errors();
					break;
				case "create_db":
					$this->model->remove_database_errors();
					$username = isset($_POST["username"]) ? $_POST["username"] : "root";
					$this->output->add_tag("username", $username);
					$this->output->run_javascript("document.getElementById('password').focus()");
					break;
				case "import_sql":
					ob_clean();
					break;
				case "update_db":
					ob_clean();
					break;
				case "update_pia":
					ob_clean();
					break;
				case "credentials":
					if ($_POST["submit_button"] != "Set password") {
						$_POST["username"] = "admin";
					}

					$this->output->add_javascript("banshee/".PASSWORD_HASH.".js");
					$this->output->add_javascript("setup.js");
					$this->output->run_javascript("hash = window['".PASSWORD_HASH."'];");

					$this->output->add_tag("username", $_POST["username"]);
					ob_clean();
					break;
				case "done":
					ob_clean();
					break;
			}
			$this->output->close_tag();
		}
	}
?>
