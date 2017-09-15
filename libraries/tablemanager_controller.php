<?php
	/* Copyright (C) by Hugo Leisink <hugo@leisink.net>
	 * This file is part of the Banshee PHP framework
	 * http://www.banshee-php.org/
	 */

	abstract class tablemanager_controller extends controller {
		protected $name = "Table";
		protected $pathinfo_offset = 1;
		protected $back = null;
		protected $icon = null;
		protected $page_size = 25;
		protected $pagination_links = 7;
		protected $pagination_step = 7;
		protected $foreign_null = "---";
		protected $log_column = null;
		protected $browsing = "pagination";
		protected $enable_search = false;
		private   $table_class = "table table-striped table-hover table-condensed";

		/* Show overview
		 *
		 * INPUT:  array( string key => string value[, ...] )
		 * OUTPUT: -
		 * ERROR:  -
		 */
		protected function show_overview() {
			switch ($this->browsing) {
				case "alphabetize":
					$alphabet = new alphabetize($this->output, "tableadmin_".$this->model->table);
					if ($_POST["submit_button"] == "Search") {
						$alphabet->reset();
					}

					if (($items = $this->model->get_items($alphabet->char)) === false) {
						$this->output->add_tag("result", "Error while creating overview.");
						return;
					}
					break;
				case "pagination":
					if (($item_count = $this->model->count_items()) === false) {
						$this->output->add_tag("result", "Error while counting items.");
						return;
					}

					$paging = new pagination($this->output, "tableadmin_".$this->model->table, $this->page_size, $item_count);
					if ($_POST["submit_button"] == "Search") {
						$paging->reset();
					}

					if (($items = $this->model->get_items($paging->offset, $paging->size)) === false) {
						$this->output->add_tag("result", "Error while creating overview.");
						return;
					}
					break;
				case "datatables":
					$this->output->add_javascript("jquery/jquery.js");
					$this->output->add_javascript("banshee/jquery.datatables.js");
					$this->output->run_javascript("$(document).ready(function(){ $('table.datatable').dataTable(); });");
					$this->output->add_css("banshee/datatables.css");
					$this->table_class = "datatable";
					$this->enable_search = false;
				default:
					if (($items = $this->model->get_items()) === false) {
						$this->output->add_tag("result", "Error while creating overview.");
						return;
					}
			}

			$params = array(
				"class"        => $this->table_class,
				"allow_create" => show_boolean($this->model->allow_create));
			$this->output->open_tag("overview", $params);

			/* Labels
			 */
			$this->output->open_tag("labels", array("name" => strtolower($this->name)));
			foreach ($this->model->elements as $name => $element) {
				$args = array(
					"name"     => $name,
					"overview" => show_boolean($element["overview"]));
				if ($element["overview"]) {
					$this->output->add_tag("label", $element["label"], $args);
				}
			}
			$this->output->close_tag();

			/* Values
			 */
			$this->output->open_tag("items");
			foreach ($items as $item) {
				$this->output->open_tag("item", array("id" => $item["id"]));
				foreach ($item as $name => $value) {
					$element = $this->model->elements[$name];
					if ($element["overview"]) {
						switch ($element["type"]) {
							case "boolean":
								$value = show_boolean($value);
								break;
							case "date":
								$value = date("j F Y", strtotime($value));
								break;
							case "timestamp":
								$value = date("j F Y H:i", strtotime($value));
								break;
							case "foreignkey":
								if ($value === null) {
									$value = $this->foreign_null;
								} else if (($result = $this->db->entry($element["table"], $value)) != false) {
									if (is_array($element["column"]) == false) {
										$value = $result[$element["column"]];
									} else {
										$values = array();
										foreach ($element["column"] as $column) {
											array_push($values, $result[$column]);
										}
										$value = implode(" ", $values);
									}
								}
								break;
						}
						$this->output->add_tag("value", $value, array("name" => $name));
					}
				}
				$this->output->close_tag();
			}
			$this->output->close_tag();

			switch ($this->browsing) {
				case "alphabetize":
					$alphabet->show_browse_links();
					break;
				case "pagination":
					$paging->show_browse_links($this->pagination_links, $this->pagination_step);
					break;
			}

			if ($this->enable_search) {
				$this->output->add_tag("search", $_SESSION["tablemanager_search_".$this->model->table]);
			}

			$this->output->close_tag();
		}

		/* Show create / update form
		 *
		 * INPUT:  array( string key => string value[, ...] )
		 * OUTPUT: -
		 * ERROR:  -
		 */
		protected function show_item_form($item) {
			$args = array(
				"name"         => strtolower($this->name),
				"allow_delete" => show_boolean($this->model->allow_delete));

			if (isset($item["id"]) == false) {
				if ($this->model->allow_create == false) {	
					$this->show_overview();
					return;
				}
			} else {
				$args["id"] = $item["id"];
				if ($this->model->allow_update == false) {	
					$this->show_overview();
					return;
				}
			}

			$this->output->open_tag("edit");

			$this->output->open_tag("form", $args);
			foreach ($this->model->elements as $name => $element) {
				if (($name == "id") || $element["readonly"]) {
					continue;
				}

				$this->output->open_tag("element", array(
					"name" => $name,
					"type" => $element["type"]));

				if (isset($element["label"])) {
					$this->output->add_tag("label", $element["label"]);
				}

				if ($element["type"] == "boolean") {
					$item[$name] = show_boolean($item[$name]);
				} else if ($element["type"] == "timestamp") {
					$item[$name] = date("Y-m-d H:i", strtotime($item[$name]));
				}

				if ($element["type"] != "blob") {
					$this->output->add_tag("value", $item[$name]);
				}

				if ($element["type"] == "foreignkey") {
					$element["options"] = array();
					if ($element["required"] == false) {
						$element["options"][null] = $this->foreign_null;
					}
					if (is_array($element["column"]) == false) {
						$cols = array($element["column"]);
					} else {
						$cols = $element["column"];
					}
					$qcols = implode(",", array_fill(1, count($cols), "%S"));

					$query = "select id,".$qcols." from %S order by ".$qcols;
					if (($options = $this->db->execute($query, $cols, $element["table"], $cols)) != false) {
						foreach ($options as $option) {
							$values = array();
							foreach ($cols as $col) {
								array_push($values, $option[$col]);
							}
							$element["options"][$option["id"]] = implode(" ", $values);
						}
					}
				}

				switch ($element["type"]) {
					case "date":
						$this->output->add_javascript("jquery/jquery-ui.js");
						$this->output->add_javascript("banshee/datepicker.js");
						$this->output->add_css("jquery/jquery-ui.css");
						break;
					case "timestamp":
						$this->output->add_javascript("jquery/jquery-ui.js");
						$this->output->add_javascript("banshee/jquery.timepicker.js");
						$this->output->add_javascript("banshee/datetimepicker.js");
						$this->output->add_css("jquery/jquery-ui.css");
						$this->output->add_css("banshee/timepicker.css");
						break;
					case "ckeditor":
						$this->output->add_javascript("ckeditor/ckeditor.js");
						$this->output->add_javascript("banshee/start_ckeditor.js");
						break;
				}

				if (($element["type"] == "enum") || ($element["type"] == "foreignkey")) {
					$this->output->open_tag("options");
					foreach ($element["options"] as $value => $label) {
						$this->output->add_tag("option", $label, array("value" => $value));
					}
					$this->output->close_tag();
				}

				$this->output->close_tag();
			}
			$this->output->close_tag();

			$this->output->close_tag();
		}

		/* Handle user submit
		 *
		 * INPUT:  -
		 * OUTPUT: -
		 * ERROR:  -
		 */
		protected function handle_submit() {
			$item = strtolower($this->name);

			if ($_POST["submit_button"] == "Save ".$item) {
				/* Add file upload to $_POST
				 */
				foreach ($this->model->elements as $name => $element) {
					if (($element["type"] == "blob") && isset($_FILES[$name])) {
						if ($_FILES[$name]["error"] == 0) {
							$_POST[$name] = file_get_contents($_FILES[$name]["tmp_name"]);
						}
					}
				}

				/* Save item
				 */
				if ($this->model->save_oke($_POST) == false) {
					$this->show_item_form($_POST);
				} else if (isset($_POST["id"]) == false) {
					/* Create item
					 */
					if ($this->model->create_item($_POST) === false) {
						$this->output->add_message("Error while creating ".$item.".");
						$this->show_item_form($_POST);
					} else {
						$name = $this->db->last_insert_id;
						if ($this->log_column != null) {
							$name .= ":".$_POST[$this->log_column];
						}
						$this->user->log_action("%s %S created", strtolower($this->name), $name);

						$this->show_overview();
					}
				} else {
					/* Update item
					 */
					if ($this->model->update_item($_POST) === false) {
						$this->output->add_message("Error while updating ".$item.".");
						$this->show_item_form($_POST);
					} else {
						$name = $_POST["id"];
						if ($this->log_column != null) {
							$name .= ":".$_POST[$this->log_column];
						}
						$this->user->log_action("%s %s updated", strtolower($this->name), $name);

						$this->show_overview();
					}
				}
			} else if ($_POST["submit_button"] == "Delete ".$item) {
				/* Delete item
				 */
				if ($this->model->delete_oke($_POST["id"]) == false) {
					$this->show_item_form($_POST);
				} else if ($this->model->delete_item($_POST["id"]) === false) {
					$this->output->add_message("Error while deleting ".$item.".");
					$this->show_item_form($_POST);
				} else {
					$name = $_POST["id"];
					if ($this->log_column != null) {
						if (($item = $this->model->get_item($_POST["id"])) != false) {
							$name .= ":".$item[$this->log_column];
						}
					}
					$this->user->log_action("%s %s deleted", strtolower($this->name), $name);

					$this->show_overview();
				}
			} else if ($_POST["submit_button"] == "Search") {
				/* Search item
				 */
				$_SESSION["tablemanager_search_".$this->model->table] = $_POST["search"];
				$this->show_overview();
			} else {
				$this->show_overview();
			}
		}

		/* Main function
		 *
		 * INPUT:  -
		 * OUTPUT: true
		 * ERROR:  false
		 */
		public function execute() {
			$this->output->title = $this->name." administration";

			if (is_a($this->model, "tablemanager_model") == false) {
				print "Tablemanager model has not been defined.\n";
				return false;
			}

			/* Check class settings
			 */
			if ($this->model->class_settings_oke() == false) {
				return false;
			}

			/* Start
			 */
			$this->output->add_css("banshee/tablemanager.css");

			$this->output->open_tag("tablemanager");

			$this->output->add_tag("name", $this->name);
			if ($this->back !== null) {
				$this->output->add_tag("back", $this->back);
			}
			if ($this->icon !== null) {
				$this->output->add_tag("icon", $this->icon);
			}

			if ($_SERVER["REQUEST_METHOD"] == "POST") {
				/* Handle forum submit
				 */
				$this->handle_submit();
			} else if ($this->page->pathinfo[$this->pathinfo_offset] == "new") {
				/* Show form for new item
				 */
				$item = array();
				foreach ($this->model->elements as $name => $element) {
					if (isset($element["default"])) {
						$item[$name] = $element["default"];
					} else if ($element["type"] == "date") {
						$item[$name] = date("Y-m-d");
					} else if ($element["type"] == "timestamp") {
						$item[$name] = date("Y-m-d H:i");
					}
				}
				$this->show_item_form($item);
			} else if (valid_input($this->page->pathinfo[$this->pathinfo_offset], VALIDATE_NUMBERS, VALIDATE_NONEMPTY)) {
				/* Show form for existing item
				 */
				if (($item = $this->model->get_item($this->page->pathinfo[$this->pathinfo_offset])) == false) {
					$this->output->add_tag("result", $this->name." not found.");
				} else {
					$this->show_item_form($item);
				}
			} else {
				/* Show item overview
				 */
				if (count($_GET) == 0) {
					$_SESSION["tablemanager_search_".$this->model->table] = null;
				}
				$this->show_overview();
			}

			$this->output->close_tag();

			return true;
		}
	}
?>
