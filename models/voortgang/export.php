<?php
	/* Copyright (c) by Hugo Leisink <hugo@leisink.net>
	 * Licensed under the RAVIB license.
	 */

	class voortgang_export_model extends process_model {
		public function get_progress($case_id) {
			return $this->borrow("voortgang")->get_measures($case_id);
		}

		public function get_standard($standard_id) {
			return $this->borrow("voortgang")->get_standard($standard_id);
		}
	}
?>
