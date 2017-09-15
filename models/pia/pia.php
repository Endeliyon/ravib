<?php
	/* Copyright (c) by Hugo Leisink <hugo@leisink.net>
	 * Licensed under the RAVIB license.
	 */

	class pia_pia_model extends pia_model {
		public function get_pia_rule($number) {
			if (($rule = $this->db->entry("pia_rules", $number, "number")) == false) {
				return false;
			}

			if ($rule["title"] == null) {
				$query = "select title from pia_rules where id<%d and title is not null order by id desc limit 1";
				if (($title = $this->db->execute($query, $rule["id"])) != false) {
					$rule["title"] = $title[0]["title"];
				}
			}

			return $rule;
		}

		public function get_pia_answer($case_id, $number) {
			$query = "select p.* from pia p, pia_rules r, pia_cases c ".
			         "where pia_case_id=%d and p.pia_rule_id=r.id and p.pia_case_id=c.id ".
			         "and r.number=%s and c.organisation_id=%d";

			if (($result = $this->db->execute($query, $case_id, $number, $this->user->organisation_id)) == false) {
				return false;
			}

			return $result[0];
		}

		public function count_pia_rules() {
			$query = "select count(*) as count from pia_rules";

			if (($result = $this->db->execute($query)) === false) {
				return false;
			}

			return (int)$result[0]["count"];
		}

		public function valid_answer($answer) {
			return in_array($answer, array("no", "yes"));
		}

		public function save_answer($case_id, $number, $data) {
			if (trim($data["comment"]) == "") {
				$data["comment"] = null;
			}

			if (($current = $this->get_pia_answer($case_id, $number)) == false) {
				if (($rule = $this->get_pia_rule($number)) == false) {
					return false;
				}
				$rule_id = (int)$rule["id"];

				$item = array(
					"id"          => NULL,
					"pia_case_id" => $case_id,
					"pia_rule_id" => $rule_id,
					"answer"      => is_true($data["answer"]) ? YES : NO,
					"comment"     => $data["comment"]);
				return $this->db->insert("pia", $item);
			} else {
				$item = array(
					"answer"  => is_true($data["answer"]) ? YES : NO,
					"comment" => $data["comment"]);
				return $this->db->update("pia", $current["id"], $item) !== false;
			}
		}

		public function next_rule($number, $answer) {
			if (($rule = $this->get_pia_rule($number)) == false) {
				return false;
			}

			$answer = is_true($answer) ? "yes" : "no";

			return $rule[$answer."_next"];
		}
	}
?>
