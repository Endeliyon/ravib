<?php
	/* Copyright (c) by Hugo Leisink <hugo@leisink.net>
	 * Licensed under the RAVIB license.
	 */

	class cms_standards_controller extends tablemanager_controller {
		protected $name = "ISO Standard";
		protected $pathinfo_offset = 2;
		protected $back = "cms";
		protected $icon = "standards.png";
		protected $page_size = 25;
		protected $pagination_links = 7;
		protected $pagination_step = 1;
		protected $foreign_null = "---";
		protected $browsing = "pagination";
	}
?>
