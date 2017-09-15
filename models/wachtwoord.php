<?php
	/* Copyright (C) by Hugo Leisink <hugo@leisink.net>
	 * This file is part of the Banshee PHP framework
	 * http://www.banshee-php.org/
	 */

	class wachtwoord_model extends model {
		public function get_user($username, $email) {
			$query = "select * from users where username=%s and email=%s";

			if (($result = $this->db->execute($query, $username, $email)) == false) {
				return false;
			}

			return $result[0];
		}

		public function key_oke($key) {
			return (empty($key) == false) && ($key == $_SESSION["reset_password_key"]);
		}

		public function send_password_link($user, $key) {
			$message = file_get_contents("../extra/wachtwoord_reset.txt");
			$replace = array(
				"FULLNAME" => $user["fullname"],
				"HOSTNAME" => $_SERVER["SERVER_NAME"],
				"PROTOCOL" => $_SERVER["HTTP_SCHEME"],
				"KEY"      => $key);

			$email = new email("Nieuw wachtwoord voor ".$_SERVER["SERVER_NAME"], $this->settings->webmaster_email);
			$email->set_message_fields($replace);
			$email->message($message);
			$email->send($user["email"], $user["fullname"]);
		}

		public function password_oke($username, $password) {
			$result = true;

			if (($password["password"] == "") || ($password["password"] == hash(PASSWORD_HASH, $username))) {
				$this->output->add_message("Lege wachtwoorden zijn niet toegestaan.");
				$result = false;
			} else if ($password["password"] != $password["repeat"]) {
				$this->output->add_message("De wachtwoorden zijn niet gelijk.");
				$result = false;
			}

			return $result;
		}

		public function save_password($username, $password) {
			if ($username == "") {
				return false;
			} else if (is_false($password["password_hashed"])) {
				$password["password"] = hash(PASSWORD_HASH, $password["password"].hash(PASSWORD_HASH, $username));
			}

			$query = "update users set password=%s where username=%s";

			return $this->db->query($query, $password["password"], $username) != false;
		}
	}
?>
