<?php
	/* Copyright (c) by Hugo Leisink <hugo@leisink.net>
	 * Licensed under the RAVIB license.
	 */

	abstract class pia_model extends model {
		public function get_pia_case($pia_id) {
			$query = "select * from pia_cases where id=%d and organisation_id=%d limit 1";

			if (($result = $this->db->execute($query, $pia_id, $this->user->organisation_id)) == false) {
				return false;
			}

			return $result[0];
		}

		public function trace_pia($pia_id) {
			$query = "select * from pia_rules";
			if (($result = $this->db->execute($query)) == false) {
				return false;
			}

			$rules = array();
			foreach ($result as $rule) {
				$rules[$rule["number"]] = $rule;
			}

			$query = "select p.*, r.number from pia p, pia_rules r, pia_cases c ".
			         "where p.pia_rule_id=r.id and p.pia_case_id=c.id and c.id=%d and c.organisation_id=%d";
			if (($result = $this->db->execute($query, $pia_id, $this->user->organisation_id)) === false) {
				return false;
			}

			$answers = array();
			foreach ($result as $item) {
				$answers[$item["number"]] = $item;
			}

			$max_steps = count($rules);
			$current = PIA_BEGIN;

			$trace = array();
			do {
				if (isset($rules[$current]) == false) {
					return false;
				} else if (isset($answers[$current]) == false) {
					break;
				}

				$rule = $rules[$current];
				$answer = $answers[$current]["answer"] ? "yes" : "no";
				$rule["answer"] = $answer;
				$rule["comment"] = $answers[$current]["comment"];

				array_push($trace, $rule);

				$current = $rule[$answer."_next"];

				if ($max_steps-- == 0) {
					return false;
				}
			} while ($current != PIA_END);

			return $trace;
		}
	}
?>
