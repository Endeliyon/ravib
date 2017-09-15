<?php
	/* Copyright (C) by Hugo Leisink <hugo@leisink.net>
	 * This file is part of the Banshee PHP framework
	 * http://www.banshee-php.org/
	 */

	class cms_model extends model {
		public function get_standards() {
			return $this->borrow("cms/standards")->get_items();
		}
	}
?>
