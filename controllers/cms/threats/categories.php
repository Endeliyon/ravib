<?php
	/* Copyright (c) by Hugo Leisink <hugo@leisink.net>
	 * Licensed under the RAVIB license.
	 */

	class cms_threats_categories_controller extends tablemanager_controller {
		protected $name = "Threat category";
		protected $pathinfo_offset = 3;
		protected $back = "cms/threats";
		protected $icon = "threat_categories.png";
		protected $browsing = null;
	}
?>
