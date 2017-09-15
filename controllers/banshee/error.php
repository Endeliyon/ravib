<?php
	/* Copyright (C) by Hugo Leisink <hugo@leisink.net>
	 * This file is part of the Banshee PHP framework
	 * http://www.banshee-php.org/
	 */

	class banshee_error_controller extends controller {
		public function execute() {
			header("Status: ".$this->page->http_code);

			$this->output->add_tag("website_error", $this->page->http_code);
			$this->output->add_tag("webmaster_email", $this->settings->webmaster_email);
		}
	}
?>
