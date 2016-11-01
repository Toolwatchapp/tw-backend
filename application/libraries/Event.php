<?php

class Event {

	public function updateObserver($subject, $event, $data) {
		$this->add($event);
	}

	/**
	 * Send an event to the reporting tool
	 * @param String $event
	 * @codeCoverageIgnore
	 */
	public function add($event) {

		$this->CI =& get_instance();

		if (ENVIRONMENT === 'production') {

			$country = "undefined";

			if (isset($_SERVER["HTTP_CF_IPCOUNTRY"])) {
				$country = $_SERVER["HTTP_CF_IPCOUNTRY"];
			}

			$data = array(
				'ip'      => $_SERVER['REMOTE_ADDR'],
				'user_id' => $this->CI->session->userdata('userId')?
				$this->CI->session->userdata('userId'):0,
				'mobile'   => (int) $this->CI->agent->is_mobile(),
				'browser'  => $this->CI->agent->browser(),
				'platform' => str_replace(' ', '', $this->CI->agent->platform()),
				'country'  => $country,
				'date'     => str_replace(' ', 'T', date("Y-m-d H:i:s")),
				'event'    => $event
			);

			$data_string = json_encode($data);

			$ch = curl_init(event_url());

			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
			curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array(
					'Content-Type: application/json',
					'Content-Length: '.strlen($data_string))
			);

			$result = curl_exec($ch);
		}
	}
}
