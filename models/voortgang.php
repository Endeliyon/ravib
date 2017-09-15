<?php
	/* Copyright (c) by Hugo Leisink <hugo@leisink.net>
	 * Licensed under the RAVIB license.
	 */

	class voortgang_model extends process_model {
		public function get_standard($standard_id) {
			return $this->borrow("cms/standards")->get_item($standard_id);
		}

		public function get_iso_measure($measure_id) {
			return $this->db->entry("iso_measures", $measure_id);
		}

		public function get_measure_categories($standard_id) {
			return $this->borrow("iso")->get_measure_categories($standard_id);
		}

		public function get_measures($case_id) {
			$query = "select m.*, UNIX_TIMESTAMP(t.deadline) as deadline, p.name as person, p.email, ".
			                "o.case_id, t.info, t.done, t.hours_planned, t.hours_invested ".
			         "from cases c, iso_measures m ".
			         "left join overruled o on m.id=o.iso_measure_id and o.case_id=%d ".
			         "left join progress_tasks t on m.id=t.iso_measure_id and t.case_id=%d ".
			         "left join progress_people p on t.actor_id=p.id ".
			         "where c.id=%d and m.iso_standard_id=c.iso_standard_id";

			if (($measures = $this->db->execute($query, $case_id, $case_id, $case_id)) === false) {
				return false;
			}

			/* Get threats
			 */
			$query = "select t.id, t.number, t.threat, i.chance, i.impact, i.handle ".
			         "from controls c, threats t ".
			         "left join case_threat i on t.id=i.threat_id and i.case_id=%d ".
			         "where c.threat_id=t.id and c.iso_measure_id=%d";
			foreach ($measures as $m => $measure) {
				if (($threats = $this->db->execute($query, $case_id, $measure["id"])) === false) {
					return false;
				}

				$measures[$m]["overruled"] = $measure["case_id"] != null;
				unset($measures[$m]["case_id"]);
				$measures[$m]["relevant"] = false;
				$all_irrelevant = true;

				$highest_risk = -1;
				foreach ($threats as $t => $threat) {
					if (($threat["chance"] == 0) || ($threat["impact"] == 0) || ($threat["handle"] == "")) {
						continue;
					}

					$risk = $this->risk_matrix[$threat["chance"] - 1][$threat["impact"] - 1];
					if ($threat["handle"] != $this->threat_handle_labels[THREAT_ACCEPT]) {
						$all_irrelevant = false;
						if ($risk >= $highest_risk) {
							$highest_risk = $risk;
						}
						if ($measures[$m]["overruled"] == false) {
							$measures[$m]["relevant"] = true;
						}
					}
				}

				if ($all_irrelevant && ($measures[$m]["overruled"])) {
					$measures[$m]["relevant"] = true;
				}

				$measures[$m]["risk_value"] = $highest_risk;
				if ($highest_risk > -1) {
					$measures[$m]["risk"] = $this->risk_matrix_labels[$highest_risk];
				}

				if (($measures[$m]["relevant"] == false) && ($measure["deadline"] == null)) {
					$measures[$m]["done"] = true;
				} else {
					$measures[$m]["done"] = is_true($measure["done"]);
				}
			}

			return $measures;
		}

		public function sort_measures($a, $b, $order = null, $loop = 3) {
			if ($loop-- <= 0) {
				return 0;
			}

			if ($order == null) {
				$order = $_SESSION["progress_order"];
			}

			switch ($order) {
				case "deadline":
					if ($a["deadline"] == null) {
						$a["deadline"] = "9999-12-31";
					}
					if ($b["deadline"] == null) {
						$b["deadline"] = "9999-12-31";
					}
					if (($result = strcmp($a["deadline"], $b["deadline"])) == 0) {
						$result = $this->sort_measures($a, $b, "urgency", $loop);
					}
					break;
				case "person":
					if ($a["person"] == null) {
						$a["person"] = "zzzzzzzz";
					}
					if ($b["person"] == null) {
						$b["person"] = "zzzzzzzz";
					}
					if (($result = strcmp($a["person"], $b["person"])) == 0) {
						$result = $this->sort_measures($a, $b, "deadline", $loop);
					}
					break;
				case "urgency": 
					if ($a["relevant"] != $b["relevant"]) {
						$result = $a["relevant"] < $b["relevant"] ? 1 : 0;
					} else if (($result = strcmp($b["risk_value"], $a["risk_value"])) == 0) {
						$result = $this->sort_measures($a, $b, "deadline", $loop);
					}
					break;
				case "done":
					if (($result = strcmp($a["done"], $b["done"])) == 0) {
						$result = $this->sort_measures($a, $b, "urgency", $loop);
					}
					break;
				default:
					$result = version_compare($a["number"], $b["number"]);
			}

			return $result;
		}

		public function get_people($case_id) {
			return $this->borrow("voortgang/personen")->get_people($case_id);
		}

		public function get_progress($case_id, $iso_measure_id) {
			$query = "select * from progress_tasks where case_id=%d and iso_measure_id=%d";
			if (($result = $this->db->execute($query, $case_id, $iso_measure_id)) === false) {
				return false;
			}

			if (count($result) == false) {
				return array("deadline" => date("Y-m-d"));
			} else {
				return $result[0];
			}
		}

		public function progress_oke($case_id, $progress) {
			$result = true;

			if ($progress["actor_id"] != 0) {
				if ($this->borrow("voortgang/personen")->get_person($case_id, $progress["actor_id"]) == false) {
					$this->output->add_message("Onbekende persoon");
					$result = false;
				}

				if ($progress["deadline"] == null) {
					$this->output->add_message("Geef een deadline op.");
					$result = false;
				}

				if ($progress["actor_id"] == $progress["reviewer_id"]) {
					$this->output->add_message("De persoon die de taak uitvoerd kan niet de controleur zijn.");
					$result = false;
				}
			}

			return $result;
		}

		public function save_progress($case_id, $progress) {
			if ($this->db->query("begin") === false) {
				return false;
			}

			$query = "delete from progress_tasks where case_id=%d and iso_measure_id=%d";
			if ($this->db->query($query, $case_id, $progress["iso_measure_id"]) === false) {
				$this->db->query("rollback");
				return false;
			}

			if (($progress["deadline"] == "") || ($progress["actor_id"] == 0)) {
				$progress["deadline"] = null;
			}

			if (($progress["actor_id"] == 0) && ($progress["reviewer_id"] == 0) && ($progress["deadline"] == null) && ($progress["info"] == "") && ($progress["done"] == null) && ($progress["hours_planned"] == 0) && ($progress["hours_invested"] == 0)) {
				$query = "delete from progress_tasks where case_id=%d and iso_measure_id=%d";
				if ($this->db->query($query, $case_id, $progress["iso_measure_id"]) === false) {
					$this->db->query("rollback");
					return false;
				}
			} else {
				if ($progress["actor_id"] == 0) {
					$progress["actor_id"] = null;
				}
				if ($progress["reviewer_id"] == 0) {
					$progress["reviewer_id"] = null;
				}

				$data = array(
					"case_id"            => $case_id,
					"actor_id"           => $progress["actor_id"],
					"reviewer_id"        => $progress["reviewer_id"],
					"iso_measure_id"     => $progress["iso_measure_id"],
					"deadline"           => $progress["deadline"],
					"info"               => $progress["info"],
					"done"               => is_true($progress["done"]) ? YES : NO,
					"hours_planned"      => $progress["hours_planned"],
					"hours_invested"     => $progress["hours_invested"]);
				if ($this->db->insert("progress_tasks", $data) === false) {
					$this->db->query("rollback");
					return false;
				}
			}

			return $this->db->query("commit") !== false;
		}

		public function get_signature($data) {
			return hash_hmac("sha256", json_encode($data), $this->settings->secret_website_code);
		}

		public function send_notifications($case_id, $progress) {
			if ($this->user->username == "demo") {
				return true;
			}

			if ($progress["actor_id"] == 0) {
				return true;
			}

			if (($case = $this->get_case($case_id)) == false) {
				return false;
			} else if (($actor = $this->borrow("voortgang/personen")->get_person($case_id, $progress["actor_id"])) == false) {
				return false;
			}

			if (($reviewer = $this->borrow("voortgang/personen")->get_person($case_id, $progress["reviewer_id"])) == false) {
				$reviewer = array("name" => "-");
			}

			if (($measure = $this->db->entry("iso_measures", $progress["iso_measure_id"])) == false) {
				return false;
			}

			if (($message = file_get_contents("../extra/taak_toegewezen.txt")) === false) {
				exit("Can't load message template.\n");
			}

			/* Send actor e-mail
			 */
			$replace = array(
				"NAME"        => $actor["name"],
				"CASE"        => $case["name"],
				"INFORMATION" => $progress["info"],
				"MEASURE"     => $measure["number"]." ".$measure["name"],
				"REVIEWER"    => $reviewer["name"]);

			$mail = new email("Taak ".$measure["number"]." inzake ".$case["name"], "no-reply@ravib.nl", "RAVIB");
			$mail->set_message_fields($replace);
			$mail->message($message);

			if ($this->user->email != $actor["email"]) {
				$mail->cc($this->user->email, $this->user->fullname);
				$mail->reply_to($this->user->email, $this->user->fullname);
			}
			$mail->send($actor["email"], $actor["name"]);

			/* Send reviewer e-mail
			 */
			if ($progress["reviewer_id"] == 0) {
				return true;
			}

			if (($message = file_get_contents("../extra/taak_controle.txt")) === false) {
				exit("Can't load message template.\n");
			}

			$data = array(
				"case_id"        => $case_id,
				"iso_measure_id" => $progress["iso_measure_id"]);
			$data["signature"] = $this->get_signature($data);
			$code = rtrim(strtr(base64_encode(json_encode($data)), "+/", "-_"), "=");

			$link = "https://".$_SERVER["HTTP_HOST"]."/voortgang/gereed/".$code;

			$replace = array(
				"NAME"        => $reviewer["name"],
				"CASE"        => $case["name"],
				"INFORMATION" => $progress["info"],
				"MEASURE"     => $measure["number"]." ".$measure["name"],
				"ACTOR"       => $actor["name"],
				"LINK"        => $link);

			$mail = new email("Controle op taak ".$measure["number"]." inzake ".$case["name"], "no-reply@ravib.nl", "RAVIB");
			$mail->set_message_fields($replace);
			$mail->message($message);

			$mail->send($reviewer["email"], $reviewer["name"]);

			return true;
		}
	}
?>
