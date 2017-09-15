<?php
	/* Copyright (C) by Hugo Leisink <hugo@leisink.net>
	 * This file is part of the Banshee PHP framework
	 * http://www.banshee-php.org/
	 */

	class email {
		protected $to = array();
		protected $cc = array();
		protected $bcc = array();
		protected $from = null;
		protected $reply_to = null;
		protected $subject = null;
		protected $message = null;
		protected $attachments = array();
		protected $images = array();
		protected $sender_address = null;
		protected $message_fields = array();
		protected $field_format = "[%s]";

		/* Constructor
		 *
		 * INPUT:  string subject[, string e-mail][, string name]
		 * OUTPUT: -
		 * ERROR:  -
		 */
		public function __construct($subject, $from_address = null, $from_name = null) {
			$subject = explode("\n", $subject);
			$this->subject = trim(array_shift($subject));

			if ($this->valid_address($from_address)) {
				$this->from = $this->make_address($from_address, $from_name);
				$this->sender_address = $from_address;
			}
		}

		/* Validate an e-mail address
		*
		* INPUT:  string e-mail address
		* OUTPUT: boolean e-mail address oke
		* ERROR:  -
		*/
		public static function valid_address($email) {
			$forbidden = array("10minutemail.com", "burnthespam.info", "deadaddress.com", "e4ward.com",
				"eyepaste.com", "armyspy.com", "cuvox.de", "dayrep.com", "einrot.com", "fleckens.hu",
				"gustr.com", "jourrapide.com", "rhyta.com", "superrito.com", "teleworm.us", "filzmail.com",
				"getairmail.com", "gishpuppy.com", "guerrillamail.com", "incognitomail.org", "jetable.org",
				"mailcatch.com", "mailexpire.com", "mailinator.com", "mailmoat.com", "meltmail.com",
				"trbvm.com", "temp-mail.org", "mt2014.com", "mytrashmail.com", "trashymail.com",
				"no-spam.ws", "onewaymail.com", "shitmail.org", "crapmail.org", "spambox.us",
				"spamgourmet.com", "tempemail.net", "yopmail.com");

			list(, $domain) = explode("@", $email, 2);
			if (in_array(strtolower($domain), $forbidden)) {
				return false;
			}

			return preg_match("/^[0-9A-Za-z]([-_.~]?[0-9A-Za-z])*@[0-9A-Za-z]([-.]?[0-9A-Za-z])*\\.[A-Za-z]{2,4}$/", $email) === 1;
		}

		/* Combine name and e-mail address
		 *
		 * INPUT:  string e-mail address, string name
		 * OUTPUT: string combined name and address
		 * ERROR:  -
		 */
		protected function make_address($address, $name) {
			$address = strtolower($address);

			if ($name == null) {
				return $address;
			}

			$parts = explode("\n", $name);
			$name = trim(array_shift($parts));

			return $name." <".$address.">";
		}

		/* Set reply-to
		 *
		 * INPUT:  string e-mail address[, string name]
		 * OUTPUT: boolean valid e-mail address
		 * ERROR:  -
		 */
		public function reply_to($address, $name = null) {
			if ($this->valid_address($address) == false) {
				return false;
			}

			$this->reply_to = $this->make_address($address, $name);
			$this->sender_address = $address;

			return true;
		}

		/* Add recipient
		 *
		 * INPUT:  string e-mail address[, string name]
		 * OUTPUT: boolean valid e-mail address
		 * ERROR:  -
		 */
		public function to($address, $name = null) {
			if ($this->valid_address($address) == false) {
				return false;
			}

			array_push($this->to, $this->make_address($address, $name));

			return true;
		}

		/* Add recipient from database
		 *
		 * INPUT:  object database, int user id
		 * OUTPUT: boolean valid user id and valid e-mail address
		 * ERROR:  -
		 */
		public function to_user_id($db, $user_id) {
			if (($user = $db->entry("users", $user_id)) == false) {
				return false;
			}

			return $this->to($user["email"], $user["fullname"]);
		}

		/* Add Carbon Copy recipient
		 *
		 * INPUT:  string e-mail address[, string name]
		 * OUTPUT: boolean valid e-mail address
		 * ERROR:  -
		 */
		public function cc($address, $name = null) {
			if ($this->valid_address($address) == false) {
				return false;
			}

			array_push($this->cc, $this->make_address($address, $name));

			return true;
		}

		/* Add Blind Carbon Copy recipient
		 *
		 * INPUT:  string e-mail address[, string name]
		 * OUTPUT: boolean valid e-mail address
		 * ERROR:  -
		 */
		public function bcc($address, $name = null) {
			if ($this->valid_address($address) == false) {
				return false;
			}

			array_push($this->bcc, $this->make_address($address, $name));

			return true;
		}

		/* Set e-mail message
		 *
		 * INPUT:  string message[, string content type]
		 * OUTPUT: -
		 * ERROR:  -
		 */
		public function message($message) {
			$message = str_replace("\r\n", "\n", $message);

			if ((substr($message, 0, 6) == "<body>") && (substr(rtrim($message), -7) == "</body>")) {
				$message = "<html>\n".rtrim($message)."\n</html>";
			}

			$this->message = $message;
		}

		/* Add e-mail attachment
		 *
		 * INPUT:  string filename[, string content][, string content type]
		 * OUTPUT: true
		 * ERROR:  false
		 */
		public function add_attachment($filename, $content = null, $content_type = null) {
			if ($content == null) {
				/* Load content from file
				 */
				if (file_exists($filename) == false) {
					return false;
				}
				if (($content = file_get_contents($filename, FILE_BINARY)) == false) {
					return false;
				}
				$content_type = mime_content_type($filename);
				$filename = basename($filename);
			}

			if ($content_type == null) {
				$content_type = "application/octet-stream";
			}

			/* Add attachment
			 */
			array_push($this->attachments, array(
				"filename"     => $filename,
				"content"      => $content,
				"content_type" => $content_type));

			return true;
		}

		/* Add inline image
		 *
		 * INPUT:  string filename
		 * OUTPUT: string content ID
		 * ERROR:  false
		 */
		public function add_image($filename) {
			if (file_exists($filename) == false) {
				return false;
			}
			if (($content = file_get_contents($filename, FILE_BINARY)) == false) {
				return false;
			}

			$content_type = mime_content_type($filename);
			$content_id = sha1($content);

			/* Add attachment
			 */
			array_push($this->images, array(
				"content"      => $content,
				"content_type" => $content_type,
				"content_id"   => $content_id));

			return $content_id;
		}

		/* Set field values for message
		 *
		 * INPUT:  array fields
		 * OUPTUT: true
		 * ERROR:  false
		 */
		public function set_message_fields($data = null) {
			if ($data === null) {
				$data = array();
			} else if (is_array($data) == false) {
				return false;
			}

			$this->message_fields = array();
			foreach ($data as $key => $value) {
				$key = sprintf($this->field_format, $key);
				$this->message_fields[$key] = $value;
			}

			return true;
		}

		/* Populate fields in message
		 *
		 * INPUT:  string message
		 * OUTPUT: string message
		 * ERROR:  -
		 */
		private function populate_message_fields($message) {
			foreach ($this->message_fields as $key => $value) {
				$message = str_replace($key, $value, $message);
			}

			return $message;
		}

		/* Generate e-mail message block
		 *
		 * INPUT:  string boundary, string content-type, string message
		 * OUTPUT: string body block
		 * ERROR:  -
		 */
		private function message_block($boundary, $content_type, $message) {
			$message = $this->populate_message_fields($message);

			if ($content_type == "text/plain") {
				$message = str_replace("\n", "", $message);
				$message = str_replace("</th><th>", "</th> <th>", $message);
				$message = str_replace("</td><td>", "</td> <td>", $message);
				$message = str_replace("</tr>", "</tr>\n", $message);
				$message = str_replace("</table>", "</table>\n", $message);
				$message = str_replace("<br>", "<br>\n", $message);
				$message = str_replace("</p>", "</p>\n\n", $message);
				$message = str_replace("<div", "\n<div", $message);
				$message = preg_replace('/<head>.*<\/head>/', "", $message);
				$message = preg_replace('/<a href="(.*)"/', '[$1] <a href=""', $message);
				$message = strip_tags($message);
			}

			$format =
				"--%s\n".
				"Content-Type: %s\n".
				"Content-Transfer-Encoding: 7bit\n\n".
				"%s\n\n";

			return sprintf($format, $boundary, $content_type, $message);
		}

		/* Convert HTML message and inline images to message body
		 *
		 * INPUT:  string boundary
		 * OUTPUT: string body block
		 * ERROR:  -
		 */
		private function html_message($boundary) {
			$message = "";
			$image_count = count($this->images);

			/* Create multipart/related block
			 */
			if ($image_count > 0) {
				$message .= "--".$boundary."\n";
				$boundary = substr(sha1($boundary), 0, 20);
				$message .= "Content-Type: multipart/related; boundary=".$boundary."\n\n";
			}

			/* Add HTML message
			 */
			$message .= $this->message_block($boundary, "text/html", $this->message);

			/* Add inline images
			 */
			if ($image_count > 0) {
				$format =
					"--%s\n".
					"Content-Disposition: inline\n".
					"Content-Type: %s\n".
					"Content-ID: <%s>\n".
					"Content-Transfer-Encoding: base64\n\n".
					"%s\n\n";

				foreach ($this->images as $image) {
					$content = base64_encode($image["content"]);
					$content = wordwrap($content, 70, "\n", true);
					$message .= sprintf($format, $boundary, $image["content_type"], $image["content_id"], $content);
				}

				$message .= "--".$boundary."--\n\n";
			}

			return $message;
		}

		/* Send e-mail
		 *
		 * INPUT:  [string e-mail address recipient][, string name recipient]
		 * OUTPUT: true
		 * ERROR:  false
		 */
		public function send($to_address = null, $to_name = null) {
			if ($to_address !== null) {
				if ($this->to($to_address, $to_name) == false) {
					return false;
				}
			}

			if (count($this->to) == 0) {
				return false;
			}

			if (count($this->message) === null) {
				$this->message("");
			}

			$attachment_count = count($this->attachments);
			$email_boundary = substr(sha1(time()), 0, 20);

			$message_contains_html = (substr($this->message, 0, 6) == "<html>") &&
			                         (substr(rtrim($this->message), -7) == "</html>");

			/* E-mail content
			 */
			if ($attachment_count == 0) {
				/* No attachments
				 */
				if ($message_contains_html == false) {
					/* One message
					 */
					$headers = array("Content-Type: text/plain");
					$message = $this->populate_message_fields($this->message);
				} else {
					/* Multiple messages
					 */
					$headers = array("Content-Type: multipart/alternative; boundary=".$email_boundary);
					$message = "This is a multi-part message in MIME format.\n";
					$message .= $this->message_block($email_boundary, "text/plain", $this->message);
					$message .= $this->html_message($email_boundary);
				}
			} else {
				/* With attachments
				 */
				$headers = array("Content-Type: multipart/mixed; boundary=".$email_boundary);
				$message = "This is a multi-part message in MIME format.\n";

				if ($message_contains_html == false) {
					/* One message
					 */
					$message .= $this->message_block($email_boundary, "text/plain", $this->message);
				} else {
					/* Multiple messages
					 */
					$message_boundary = substr(sha1($email_boundary), 0, 20);
					$message .= "--".$email_boundary."\n".
						"Content-Type: multipart/alternative; boundary=".$message_boundary."\n\n";
					$message .= $this->message_block($message_boundary, "text/plain", $this->message);
					$message .= $this->html_message($message_boundary);
					$message .= "--".$message_boundary."--\n\n";
				}

				/* Add attachments
				 */
				$format .= 
					"--%s\n".
					"Content-Disposition: attachment;\n".
					"\tfilename=\"%s\"\n".
					"Content-Type: %s;\n".
					"\tname=\"%s\"\n".
					"Content-Transfer-Encoding: base64\n\n".
					"%s\n\n";

				foreach ($this->attachments as $attachment) {
					$content = base64_encode($attachment["content"]);
					$content = wordwrap($content, 70, "\n", true);
					$message .= sprintf($format, $email_boundary, $attachment["filename"],
						$attachment["content_type"], $attachment["filename"], $content);
				}
			}

			if ($message_contains_html || ($attachment_count > 0)) {
				$message .= "--".$email_boundary."--\n";
			}

			array_push($headers, "MIME-Version: 1.0");
			array_push($headers, "User-Agent: Banshee PHP framework e-mail library (http://www.banshee-php.org/)");

			/* Sender
			 */
			if ($this->from != null) {
				array_push($headers, "From: ".$this->from);
			}
			if ($this->reply_to != null) {
				array_push($headers, "Reply-To: ".$this->reply_to);
			}
			$sender = ($this->sender_address !== null) ? "-f".$this->sender_address : null;

			/* Carbon Copies
			 */
			if (count($this->cc) > 0) {
				array_push($headers, "CC: ".implode(", ", $this->cc));
			}

			/* Blind Carbon Copies
			 */
			if (count($this->bcc) > 0) {
				array_push($headers, "BCC: ".implode(", ", $this->bcc));
			}

			/* Secure mail headers
			 */
			foreach ($headers as &$header) {
				$header = str_replace("\n", "", $header);
				$header = str_replace("\r", "", $header);
				unset($header);
			}

			/* Send the e-mail
			 */
			if (mail(implode(", ", $this->to), $this->subject, $message, implode("\n", $headers), $sender) == false) {
				return false;
			}

			unset($message);

			$this->to = array();
			$this->cc = array();
			$this->bcc = array();

			return true;
		}
	}
?>
