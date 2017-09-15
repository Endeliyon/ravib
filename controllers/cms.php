<?php
	/* Copyright (C) by Hugo Leisink <hugo@leisink.net>
	 * This file is part of the Banshee PHP framework
	 * http://www.banshee-php.org/
	 */

	class cms_controller extends controller {
		public function execute() {
			$menu = array(
				"Authentication & authorization" => array(
					"Users"         => array("cms/user", "users.png"),
					"Roles"         => array("cms/role", "roles.png"),
					"Organisations" => array("cms/organisation", "organisations.png"),
					"Access"        => array("cms/access", "access.png"),
					"Action log"    => array("cms/action", "action.png")),
				"Risk analysis" => array(
					"Standards"     => array("cms/standards", "standards.png"),
					"Measures"      => array("cms/measures", "measures.png"),
					"Threats"       => array("cms/threats", "threats.png"),
					"Validation"    => array("cms/validate", "validate.png"),
					"PIA"           => array("cms/pia", "pia.png")),
				"Content" => array(
					"Files"         => array("cms/file", "file.png"),
					"Languages"     => array("cms/language", "language.png"),
					"Menu"          => array("cms/menu", "menu.png"),
					"Pages"         => array("cms/page", "page.png"),
					"Settings"      => array("cms/settings", "settings.png")));

			/* ISO standard selection 
			 */
			if ($_SERVER["REQUEST_METHOD"] == "POST") {
				$_SESSION["iso_standard"] = $_POST["iso_standard"];
			}

			if (($standards = $this->model->get_standards()) === false) {
				$this->output->add_tag("result", "Error retrieving ISO standards.");
				return false;
			}

			$this->output->open_tag("iso_standards");
			foreach ($standards as $standard) {
				$params = array(
					"id"       => $standard["id"],
					"selected" => show_boolean($standard["id"] == $_SESSION["iso_standard"]));
				$this->output->add_tag("standard", $standard["name"], $params);
			}
			 $this->output->close_tag();

			/* Show warnings
			 */
			if (module_exists("setup")) {
				$this->output->add_system_warning("The setup module is still available. Remove it from settings/public_modules.conf.");
			}

			if (is_true(DEBUG_MODE)) {
				$this->output->add_system_warning("Website is running in debug mode. Set DEBUG_MODE in settings/website.conf to 'no'.");
			}

			if ($this->page->pathinfo[1] != null) {	
				$this->output->add_system_warning("The administration module '%s' does not exist.", $this->page->pathinfo[1]);
			}

			/* Show icons
			 */
			if (is_false(MULTILINGUAL)) {
				unset($menu["Content"]["Languages"]);
			}

			$access_list = page_access_list($this->db, $this->user);
			$private_modules = config_file("private_modules");

			$this->output->open_tag("menu");

			foreach ($menu as $text => $section) {

				$this->output->open_tag("section", array(
					"text"  => $text,
					"class" => strtr(strtolower($text), " &", "__")));

				foreach ($section as $text => $info) {
					list($module, $icon) = $info;

					if (in_array($module, $private_modules) == false) {
						continue;
					}

					if (isset($access_list[$module])) {
						$access = $access_list[$module] > 0;
					} else {
						$access = true;
					}

					$this->output->add_tag("entry", $module, array(
						"text"   => $text,
						"access" => show_boolean($access),
						"icon"   => $icon));
				}

				$this->output->close_tag();
			}

			$this->output->close_tag();
		}
	}
?>
