<?php
	/* Copyright (c) by Hugo Leisink <hugo@leisink.net>
	 * Licensed under the RAVIB license.
	 */

	class cms_settings_controller extends tablemanager_controller {
		protected $name = "Setting";
		protected $pathinfo_offset = 2;
		protected $back = "cms";
		protected $icon = "settings.png";
		protected $page_size = 25;
		protected $pagination_links = 7;
		protected $pagination_step = 1;
		protected $foreign_null = "---";

		protected function show_item_form($item) {
			if ((is_true(DEBUG_MODE) == false) && isset($item["id"])) {
				if (($current = $this->model->get_item($item["id"])) === false) {
					$this->output->add_tag("result", "Database error.");
					return false;
				}

				$this->output->add_javascript("cms/settings.js");

				$this->output->open_tag("label");
				$this->output->add_tag("key", $current["key"]);
				$this->output->add_tag("type", $current["type"]);
				$this->output->close_tag();
			}

			parent::show_item_form($item);
		}

		protected function handle_submit() {
			parent::handle_submit();

			$cache = new cache($this->db, "settings");
			$cache->store("last_updated", time(), 365 * DAY);
		}
	}
?>
