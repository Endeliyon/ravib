<?php
	/* Copyright (c) by Hugo Leisink <hugo@leisink.net>
	 * Licensed under the RAVIB license.
	 */

	class cms_pia_model extends tablemanager_model {
		protected $table = "pia_rules";
		protected $elements = array(
			"number" => array(
				"label"    => "Number",
				"type"     => "varchar",
				"overview" => true,
				"required" => true),
			"title" => array(
				"label"    => "Title",
				"type"     => "varchar",
				"default"  => "",
				"overview" => false,
				"required" => false,
				"null"     => true),
			"question" => array(
				"label"    => "Question",
				"type"     => "text",
				"default"  => "<p></p>",
				"overview" => true,
				"required" => true),
			"information" => array(
				"label"    => "Information",
				"type"     => "text",
				"default"  => "<p></p>",
				"overview" => false,
				"required" => false),
			"yes" => array(
				"label"    => "Answer yes",
				"type"     => "text",
				"default"  => "<p></p>",
				"overview" => false,
				"required" => false),
			"yes_next" => array(
				"label"    => "Next when yes",
				"type"     => "varchar",
				"overview" => true,
				"required" => true),
			"no" => array(
				"label"    => "Answer no",
				"type"     => "text",
				"default"  => "<p></p>",
				"overview" => false,
				"required" => false),
			"no_next" => array(
				"label"    => "Next when no",
				"type"     => "varchar",
				"overview" => true,
				"required" => true),
			"law_section" => array(
				"label"    => "Section of the law",
				"type"     => "varchar",
				"overview" => false,
				"required" => false));
	}
?>
