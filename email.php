<?php
	
	class Email{
		
        public $sendgrid_api_key = "";
		public $send_from = "noreply@martin.co.ke";
		public $send_from_name = "Martin Nzuki";
		public $unsubscribe_url = 'https://martin.co.ke/mailing/unsubscribe'; // Link to unsubscribe required in emails. Read more: https://support.google.com/mail/answer/81126?hl=en
        public $send_to = "hello@martin.co.ke";
        public $send_to_name = "Martin";
        public $subject = "";
        public $message = "";
		public $template = './email-templates/no-link.php';
		
		public function send_email(){
            $email = new \SendGrid\Mail\Mail(); 
            $email->setFrom($this->send_from, $this->send_from_name);
            $email->setSubject($this->subject);
			$email->addTo($this->send_to, $this->send_to_name);
			$msg = $this->message;
			$msg = $this->parse_html_mail();
			$email->addContent("text/html", $msg);
            $sendgrid = new \SendGrid($this->sendgrid_api_key);
            try {
                $response = $sendgrid->send($email);
                // print $response->statusCode() . "\n";
                // print_r($response->headers());
                // print $response->body() . "\n";
                return true;
            } catch (Exception $e) {
                // echo 'Caught exception: '. $e->getMessage() ."\n";
                return false;
            }
		}

		private function parse_html_mail(){
			$content = file_get_contents($this->template);
			$content = str_replace('%subject%',$this->subject, $content);
			$content = str_replace('%name%',$this->send_to_name, $content);
			$content = str_replace('%message%',$this->message, $content);
			$content = str_replace('%unsubscribe%',$this->unsubscribe_url, $content);
			return $content;
		}
  	}