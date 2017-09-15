<?php
	/* Copyright (C) by Hugo Leisink <hugo@leisink.net>
	 * This file is part of the Banshee PHP framework
	 * http://www.banshee-php.org/
	 */

	abstract class tablemanager_model extends model {
		private $valid_types = array("integer", "varchar", "text", "ckeditor",
			"boolean", "date", "timestamp", "enum", "foreignkey", "blob", "float");
		protected $table = null;
		protected $order = "id";
		protected $desc_order = false;
		protected $elements = null;
		protected $alphabetize_column = null;
		protected $allow_create = true;
		protected $allow_update = true;
		protected $allow_delete = true;

		/* Constructor
		 *
		 * INPUT:  core objects
		 * OUTPUT: -
		 * ERROR:  -
		 */
		public function __construct() {
			$arguments = func_get_args();
			call_user_func_array(array("parent", "__construct"), $arguments);

			/* Table order
			 */
			$key = "tablemanager_order_".$this->table;
			if (isset($_SESSION[$key]) == false) {
				$_SESSION[$key] = $this->order;
				$_SESSION[$key."_desc"] = $this->desc_order;
            }
			$this->order = &$_SESSION[$key];
			$this->desc_order = &$_SESSION[$key."_desc"];

			if (in_array($_GET["order"], array_keys($this->elements))) {
				if ($_SESSION[$key] == $_GET["order"]) {
					$this->desc_order = $this->desc_order == false;
				} else {
					$this->order = $_GET["order"];
					$this->desc_order = false;
				}
			}

			/* Determine alphabetizing column
			 */
			if ($this->alphabetize_column === null) {
				foreach ($this->elements as $column => $element) {
					if (in_array($element["type"], array("varchar", "text"))) {
						$this->alphabetize_column = $column;
						break;
					}
				}

				if ($this->alphabetize_column === null) {
					$items = array_keys($this->elements);
					$this->alphabetize_column = array_shift($items);
				}
			}

			/* Add identifier column
			 */
			if (isset($this->elements["id"]) == false) {
				$this->elements = array_merge(
					array(
						"id"    => array(
						"label" => "Id",
						"type"  => "integer")),
					$this->elements);
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
				case "table": return $this->table;
				case "elements": return $this->elements;
				case "allow_create": return $this->allow_create;
				case "allow_update": return $this->allow_update;
				case "allow_delete": return $this->allow_delete;
			}

			return null;
		}

		/* Fix variables
		 *
		 * INPUT:  array( string key => string value )
		 * OUTPUT: array( string key => mixed value )
		 * ERROR:  -
		 */
		private function fix_variables($item) {
			foreach ($this->elements as $name => $element) {
				switch ($element["type"]) {
					case "boolean":
						$item[$name] = is_true($item[$name]) ? YES : NO;
						break;
					case "integer":
						$item[$name] = (int)$item[$name];
						break;
				}
			}

			return $item;
		}

		/* Add tables with foreign key links to search query
		 *
		 * INPUT:  string query, array arguments
		 * OUTPUT: -
		 * ERROR:  -
		 */
		protected function add_search_tables(&$query, &$args) {
			$tables = array($this->table);

			foreach ($this->elements as $key => $element) {
				if ($element["type"] == "foreignkey") {
					if (in_array($element["table"], $tables) == false) {
						$query .= " left join %S on %S.%S=%S.%S";
						array_push($args, $element["table"], $this->table, $key, $element["table"], "id");
					}
				}
			}
		}

		/* Get search filter
		 *
		 * INPUT:  string query, array arguments, string search
		 * OUTPUT: -
		 * ERROR:  -
		 */
		protected function add_search_filter(&$query, &$args, $search) {
			$filter = array();
			foreach ($this->elements as $key => $element) {
				switch ($element["type"]) {
					case "boolean":
						if (in_array(strtolower($search), array("yes", "no"))) {
							array_push($filter, "%S=%d");
							array_push($args, $key, is_true($search) ? YES : NO);
						}
						break;
					case "date":
						array_push($filter, "DATE_FORMAT(%S.%S, %s) like %s");
						array_push($args, $this->table, $key, "%W %d %M %Y", "%".$search."%");
						break;
					case "foreignkey":
						if (is_array($element["column"]) == false) {
							array_push($filter, "%S.%S like %s");
							array_push($args, $element["table"], $element["column"], "%".$search."%");
						} else {
							$concat = array();
							foreach ($element["column"] as $column) {
								array_push($concat, "%S.%S");
								array_push($args, $element["table"], $column);
							}
							array_push($filter, "concat(".implode(", ", $concat).") like %s");
							array_push($args, "%".$search."%");
						}
						break;
					default:
						array_push($filter, "%S.%S like %s");
						array_push($args, $this->table, $key, "%".$search."%");
				}
			}

			$query .= " (".implode(" or ", $filter).")";
		}

		/* Count all items
		 *
		 * INPUT:  -
		 * OUTPUT: int number of items
		 * ERROR:  false;
		 */
		public function count_items() {
			$query = "select count(*) as count from %S";
			$args = array($this->table);

			if (($search = $_SESSION["tablemanager_search_".$this->table]) != null) {
				$this->add_search_tables($query, $args);
				$query .= " where";
				$this->add_search_filter($query, $args, $search);
			}

			if (($result = $this->db->execute($query, $args)) == false) {
				return false;
			}

			return $result[0]["count"];
		}

		/* Get all items
		 *
		 * INPUT:  string start character | int item offset, int item count | null
		 * OUTPUT: array( string key => string value[, ...] )
		 * ERROR:  false
		 */
		public function get_items() {
			if (is_array($this->order) == false) {
				$order = $this->desc_order ? "%S desc" : "%S";
			} else {
				$order = implode(", ", array_fill(0, count($this->order), "%S"));
			}

			$args = array($this->table, "id");
			foreach ($this->elements as $column => $element) {
				if ($element["overview"]) {
					array_push($args, $this->table, $column);
				}
			}

			$query = "select ".implode(",", array_fill(0, count($args) / 2, "%S.%S"))." from %S";
			array_push($args, $this->table);

			if (($search = $_SESSION["tablemanager_search_".$this->table]) != null) {
				$this->add_search_tables($query, $args);
				$query .= " where";
				$this->add_search_filter($query, $args, $search);
			}

			switch (func_num_args()) {
				case 0:
					/* No browsing
					 */
					$query .= " order by ".$order;
					array_push($args, $this->order);
					break;
				case 1:
					/* Alphabetize
					 */
					list($char) = func_get_args();

					$query .= ($search == null) ? " where " : " and ";

					if ($char == "0") {
						$query .= "(ord(lower(substr(%S, 1, 1)))<ord(%s) or ord(lower(substr(%S, 1, 1)))>ord(%s))";
						array_push($args, $this->alphabetize_column, "a", $this->alphabetize_column, "z");
					} else {
						$query .= "(%S like %s)";
						array_push($args, $this->alphabetize_column, $char."%");
					}
					break;
				case 2:
					/* Pagination
					 */
					list($offset, $count) = func_get_args();

					$query .= " order by ".$order." limit %d,%d";
					array_push($args, $this->order, $offset, $count);
					break;
				default:
					return false;
			}

			return $this->db->execute($query, $args);
		}

		/* Get item by its id
		 *
		 * INPUT:  int item indentifier
		 * OUTPUT: array( string key => string value[, ...] )
		 * ERROR:  false
		 */
		public function get_item($item_id) {
			static $cache = array();

			if (isset($cache[$item_id]) == false) {
				if (($item = $this->db->entry($this->table, $item_id)) === false) {
					return false;
				}

				$cache[$item_id] = $item;
			}

			return $cache[$item_id];
		}

		/* Validate user input for saving
		 *
		 * INPUT:  array( string key => string value[, ...] )
		 * OUTPUT: boolean item validation oke
		 * ERROR:  -
		 */
		public function save_oke($item) {
			$result = true;

			if (isset($item["id"]) == false) {
				if ($this->allow_create == false) {	
					return false;
				}
			} else {
				if ($this->allow_update == false) {	
					return false;
				}
			}

			foreach ($this->elements as $name => $element) {
				if (($name == "id") || $element["readonly"]) {
					continue;
				}

				if (($element["required"]) && ($element["type"] != "boolean") && (trim($item[$name]) == "")) {
					if (($element["type"] != "blob") || (isset($item["id"]) == false)) {
						$this->output->add_message("The field ".$element["label"]." cannot be empty.");
						$result = false;
					}
				}

				if (trim($item[$name]) != "") {
					switch ($element["type"]) {
						case "date":
							if (valid_date($item[$name]) == false) {
								$this->output->add_message("The field ".$element["label"]." doesn't contain a valid date.");
								$result = false;
							}
							break;
						case "timestamp":
							if (valid_timestamp($item[$name]) == false) {
								$this->output->add_message("The field ".$element["label"]." doesn't contain a valid timestamp.");
								$result = false;
							}
							break;
						case "enum":
							if (in_array($item[$name], array_keys($element["options"])) == false) {
								$this->output->add_message("The field ".$element["label"]." doesn't contain a valid value.");
								$result = false;
							}
							break;
						case "integer":
							if (is_numeric($item[$name]) == false) {
								$this->output->add_message("The field ".$element["label"]." should be numerical.");
								$result = false;
							}
							break;
					}
				}

				if ($element["unique"]) {
					$query = "select count(*) as count from %S where %S=%s";
					$args = array($this->table, $name, $item[$name]);
					if (isset($item["id"])) {
						$query .= " and id!=%d";
						array_push($args, $item["id"]);
					}
					if (($current = $this->db->execute($query, $args)) == false) {
						$this->output->add_message("Error checking item uniqueness.");
					} else if ($current[0]["count"] > 0) {
						$this->output->add_message($element["label"]." already exists.");
						$result = false;
					}
				}
			}

			return $result;
		}

		/* Validate user input for deleting
		 *
		 * INPUT:  int item identifier
		 * OUTPUT: true
		 * ERROR:  false
		 */
		public function delete_oke($item_id) {
			if ($this->allow_delete == false) {	
				$this->output->add_message("You are not allowed to delete items.");
				return false;
			}

			if (valid_input($item_id, VALIDATE_NUMBERS, VALIDATE_NONEMPTY) == false) {
				$this->output->add_message("Invalid item id.");
				return false;
			}

			return true;
		}

		/* Create item in database
		 *
		 * INPUT:  array( string key => string value[, ...] )
		 * OUTPUT: true
		 * ERROR:  false
		 */
		public function create_item($item) {
			$keys = array();
			foreach ($this->elements as $name => $element) {
				if ($element["virtual"]) {
					continue;
				}
				array_push($keys, $name);
			}

			$item = $this->fix_variables($item);
			$item["id"] = null;

			foreach ($keys as $key) {
				$element = $this->elements[$key];

				if (($element["type"] == "foreignkey") && ($element["required"] == false)) {
					if ($item[$key] == "") {
						$item[$key] = null;
					}
				}

				if (($element["null"] === true) && ($item[$key] == "")) {
					$item[$key] = null;
				}
			}

			return $this->db->insert($this->table, $item, $keys);
		}

		/* Update item in database
		 *
		 * INPUT:  array( string key => string value[, ...] )
		 * OUTPUT: true
		 * ERROR:  false
		 */
		public function update_item($item) {
			$keys = array();
			foreach ($this->elements as $name => $element) {
				if (($name == "id") || $element["readonly"] || $element["virtual"]) {
					continue;
				}
				if (($element["type"] == "blob") && (isset($item[$name]) == false)) {
					continue;
				}
				array_push($keys, $name);
			}

			$item = $this->fix_variables($item);

			foreach ($keys as $key) {
				$element = $this->elements[$key];

				if (($element["type"] == "foreignkey") && ($element["required"] == false)) {
					if ($item[$key] == "") {
						$item[$key] = null;
					}
				}

				if (($element["null"] === true) && ($item[$key] == "")) {
					$item[$key] = null;
				}
			}

			return $this->db->update($this->table, $item["id"], $item, $keys);
		}

		/* Delete item from database
		 *
		 * INPUT:  int item identifier
		 * OUTPUT: true
		 * ERROR:  false
		 */
		public function delete_item($item_id) {
			return $this->db->delete($this->table, $item_id);
		}

		/* Check class settings
		 *
		 * INPUT:  -
		 * OUTPUT: true
		 * ERROR:  false
		 */
		public function class_settings_oke() {
			$class_oke = true;

			$ckeditors = 0;

			if ($this->table == null) {
				print "Table not set in ".get_class($this)."\n";
				$class_oke = false;
			}
			if (is_array($this->elements) == false) {
				print "Elements not set in ".get_class($this)."\n";
				$class_oke = false;
			} else foreach ($this->elements as $name => &$element) {
				if (is_int($name)) {
					print "Numeric element names are not allowed in ".get_class($this)."\n";
					$class_oke = false;
				}

				if (isset($element["label"]) == false) {
					print "Label in element '".$name."' not set in ".get_class($this)."\n";
					$class_oke = false;
				}

				if (in_array($element["type"], $this->valid_types) == false) {
					print "Unknown type in element '".$name."' in ".get_class($this)."\n";
					$class_oke = false;
				}
				switch ($element["type"]) {
					case "enum":
						if (is_array($element["options"]) == false) {
							print "Options in element '".$name."' not set in ".get_class($this)."\n";
							$class_oke = false;
						}
						break;
					case "foreignkey":
						if ((isset($element["table"]) == false) || (isset($element["column"]) == false)) {
							print "Table or column in element '".$name."' not set in ".get_class($this)."\n";
							$class_oke = false;
						}
						break;
					case "ckeditor":
						if (++$ckeditor > 1) {
							print "More than one element of type 'ckeditor' in ".get_class($this).".\n";
							$class_oke = false;
						}
						break;
				}

				$defaults = array(
					"overview" => false,
					"required" => false,
					"unique"   => false,
					"readonly" => false,
					"virtual"  => false);
				foreach ($defaults as $key => $value) {
					if (isset($element[$key]) == false) {
						$element[$key] = $value;
					}
				}

				unset($element);
			}

			return $class_oke;
		}
	}
?>
