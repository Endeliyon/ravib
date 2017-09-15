<?php
	/* Copyright (C) by Hugo Leisink <hugo@leisink.net>
	 * This file is part of the Banshee PHP framework
	 * http://www.banshee-php.org/
	 */

	class profile_model extends model {
		public function last_account_logs() {
			if (($fp = fopen("../logfiles/actions.log", "r")) == false) {
				return false;
			}

			$result = array();

			while (($line = fgets($fp)) !== false) {	
				list($ip, $timestamp, $user_id, $message) = explode("|", chop($line));

				if ($user_id == "-") {
					continue;
				} else if ($user_id != $this->user->id) {
					continue;
				}

				array_push($result, array(
					"ip"        => $ip,
					"timestamp" => $timestamp,
					"message"   => $message));
				if (count($result) > 15) {
					array_shift($result);
				}
			}

			fclose($fp);

			return array_reverse($result);
		}

		public function profile_oke($profile) {
			$result = true;

			if (trim($profile["fullname"]) == "") {
				$this->output->add_message("Vul uw naam in.");
				$result = false;
			}

			if (valid_email($profile["email"]) == false) {
				$this->output->add_message("Ongeldig e-mailadres.");
				$result = false;
			} else if (($check = $this->db->entry("users", $profile["email"], "email")) != false) {
				if ($check["id"] != $this->user->id) {
					$this->output->add_message("Het opgegeven e-mailadres bestaat al.");
					$result = false;
				}
			}

			$password_set = $profile["password"] != "";

			if (is_false($profile["password_hashed"])) {
				$profile["current"]  = hash(PASSWORD_HASH, $profile["current"].hash(PASSWORD_HASH, $this->user->username));
				$profile["password"] = hash(PASSWORD_HASH, $profile["password"].hash(PASSWORD_HASH, $this->user->username));
				$profile["repeat"]   = hash(PASSWORD_HASH, $profile["repeat"].hash(PASSWORD_HASH, $this->user->username));
			}

			if ($profile["current"] != $this->user->password) {
				$this->output->add_message("Het huidige wachtwoord is onjuist.");
				$result = false;
			}

			if ($password_set) {
				if ($profile["password"] != $profile["repeat"]) {
					$this->output->add_message("De nieuwe wachtwoorden komen niet overeen.");
					$result = false;
				} else if ($this->user->password == $profile["password"]) {
					$this->output->add_message("Het nieuwe wachtwoord moet verschillen van het huidige wachtwoord.");
					$result = false;
				}
			}

			return $result;
		}

		public function update_profile($profile) {
			$profile["status"] = USER_STATUS_ACTIVE;

			$keys = array("fullname", "email");
			if ($profile["password"] != "") {
				array_push($keys, "password");
				array_push($keys, "status");
				if (is_false($profile["password_hashed"])) {
					$profile["password"] = hash(PASSWORD_HASH, $profile["password"].hash(PASSWORD_HASH, $this->user->username));
				}
			}

			return $this->db->update("users", $this->user->id, $profile, $keys) !== false;
		}
	}
?>
