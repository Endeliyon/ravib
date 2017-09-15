<?php
	/* Copyright (C) by Hugo Leisink <hugo@leisink.net>
	 * This file is part of the Banshee PHP framework
	 * http://www.banshee-php.org/
	 */

	abstract class controller {
		protected $model = null;
		protected $db = null;
		protected $settings = null;
		protected $user = null;
		protected $page = null;
		protected $output = null;
		protected $language = null;

		/* Constructor
		 *
		 * INPUT:  object database, object settings, object user, object page, object output[, object language]
		 * OUTPUT: -
		 * ERROR:  -
		 */
		public function __construct($database, $settings, $user, $page, $output, $language = null) {
			$this->db = $database;
			$this->settings = $settings;
			$this->user = $user;
			$this->page = $page;
			$this->output = $output;
			$this->language = $language;

			$model_class = str_replace("/", "_", $page->module)."_model";
			if (class_exists($model_class)) {
				if (is_subclass_of($model_class, "model") == false) {
					print "Model class '".$model_class."' does not extend class 'model'.\n";
				} else {
					$this->model = new $model_class($database, $settings, $user, $page, $output, $language);
				}
			}
		}

		/* Show breadcrumbs
		 *
		 * INPUT:  int case id
		 * OUTPUT: -
		 * ERROR:  -
		 */
		protected function show_breadcrumbs($case_id) {
			$this->output->add_css("includes/breadcrumbs.css");

			$this->output->open_tag("breadcrumbs");
			foreach ($this->breadcrumbs as $link => $page) {
				$current = str_replace("/#", "", $link) == $this->page->page;
				$attr = array(
					"current" => show_boolean($current),
					"link"    => str_replace("#", $case_id, $link));
				$this->output->add_tag("crumb", $page, $attr);
			}
			$this->output->close_tag();
		}

		/* Default execute function
		 *
		 * INPUT:  -
		 * OUTPUT: -
		 * ERROR:  -
		 */
		public function execute() {
			if ($this->page->ajax_request == false) {
				print "Page controller has no execute() function.\n";
			}
		}
	}
?>
