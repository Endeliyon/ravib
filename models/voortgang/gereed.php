<?php
	/* Copyright (c) by Hugo Leisink <hugo@leisink.net>
	 * Licensed under the RAVIB license.
	 */

	class voortgang_gereed_model extends process_model {
		public function get_data($code) {
			if ($code == null) {
				return false;
			}

			$padding = strlen($code) % 4;
			if ($padding > 0) {
				$padding = 4 - $padding;
				$code .= str_repeat("=", $padding);
			}

			if (($data = json_decode(base64_decode(strtr($code, "-_", "+/")), true)) == null) {
				return false;
			}

			$signature = $data["signature"];
			unset($data["signature"]);

			if ($this->borrow("voortgang")->get_signature($data) != $signature) {
				return false;
			}

			return $data;
		}

		public function get_task($case_id, $iso_measure_id) {
			$query = "select c.name, t.info, t.done, concat(m.number, %s, m.name) as measure, u.fullname ".
			         "from cases c, progress_tasks t, iso_measures m, users u ".
			         "where c.id=%d and c.id=t.case_id and t.iso_measure_id=%d and m.id=t.iso_measure_id and u.id=t.actor_id";
			if (($result = $this->db->execute($query, " ", $case_id, $iso_measure_id)) == false) {
				return false;
			}

			return $result[0];
		}

		public function mark_as_done($case_id, $iso_measure_id) {
			$query = "update progress_tasks set done=%d where case_id=%d and iso_measure_id=%d";
			return $this->db->query($query, YES, $case_id, $iso_measure_id) !== false;
		}

		public function send_notification($case_id, $iso_measure_id) {
			if (($case = $this->get_case($case_id)) == false) {
				return false;
			} else if (($user = $this->db->entry("users", $case["user_id"])) == false) {
				return false;
			} else if (($progress = $this->borrow("voortgang")->get_progress($case_id, $iso_measure_id)) == false) {
				return false;
			} else if (($actor = $this->borrow("voortgang/personen")->get_person($case_id, $progress["actor_id"])) == false) {
				return false;
			} else if (($measure = $this->db->entry("iso_measures", $iso_measure_id)) == false) {
				return false;
			}

			if (($reviewer = $this->borrow("voortgang/personen")->get_person($case_id, $progress["reviewer_id"])) != false) {
				$reviewer = $reviewer["name"];
			} else {
				$reviewer = "-";
			}

			if (($message = file_get_contents("../extra/taak_gereed.txt")) === false) {
				exit("Can't load message template.\n");
			}

			$replace = array(
				"NAME"        => $user["fullname"],
				"REVIEWER"    => $reviewer,
				"CASE"        => $case["name"],
				"INFORMATION" => $progress["info"],
				"MEASURE"     => $measure["number"]." ".$measure["name"],
				"ACTOR"       => $actor["name"]);

			$mail = new email("Gereedmelding van taak inzake ".$case["name"], "no-reply@ravib.nl", "RAVIB");
			$mail->set_message_fields($replace);
			$mail->message($message);

			return $mail->send($user["email"], $user["fullname"]);
		}
	}
?>
