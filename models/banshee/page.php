<?php
	/* Copyright (C) by Hugo Leisink <hugo@leisink.net>
	 * This file is part of the Banshee PHP framework
	 * http://www.banshee-php.org/
	 */

	class banshee_page_model extends model {
		public function get_page($url) {
			return $this->db->entry("pages", $url, "url");
		}
	}
?>
