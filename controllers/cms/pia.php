<?php
	/* Copyright (c) by Hugo Leisink <hugo@leisink.net>
	 * Licensed under the RAVIB license.
	 */

	class cms_pia_controller extends tablemanager_controller {
		protected $name = "PIA rule";
		protected $pathinfo_offset = 2;
		protected $back = "cms";
		protected $icon = "pia.png";
		protected $page_size = 25;
		protected $pagination_links = 7;
		protected $pagination_step = 1;
		protected $foreign_null = "---";
		protected $browsing = "pagination";

		protected function handle_submit() {
			foreach ($_POST as $key => $value) {
				$_POST[$key] = str_replace("- ", "", $value);
			}

			parent::handle_submit();
		}
	}
?>
