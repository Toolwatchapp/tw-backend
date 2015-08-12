<?php 

class Email extends MY_Model {

	public $ADD_FIRST_WATCH = 1;
	public $CHECK_ACCURACY  = 2;
	public $ADD_SECOND_WATCH  = 3;
	public $START_NEW_MEASURE = 4;

	private $hour = 3600;
	private $cancelledEmail = 1;

	function __construct() {
		parent::__construct();
		$this->table_name = "email";
		$this->load->library('mandrill', 'pUOMLUusBKdoR604DpcOnQ');
		$this->load->library('__');
		$this->load->model('emailmeasure');
	}

	private function sendMandrillEmail($subject, $content, $recipientName,
		$recipientEmail, $tag, $sendAt) {

		$message = array(
			'html'       => $content,
			'subject'    => $subject,
			'from_email' => 'hello@toolwatch.io',
			'from_name'  => 'Toolwatch',
			'to'         => array(
				array(
					'email' => $recipientEmail,
					'name'  => $recipientName,
					'type'  => 'to',
				)
			),
			'headers'                  => array('Reply-To' => 'hello@toolwatch.io'),
			'important'                => false,
			'track_opens'              => true,
			'track_clicks'             => true,
			'tags'                     => array($tags),
			'google_analytics_campaign'=> $tags,
			'google_analytics_domains' => array('toolwatch.io'),
			'metadata'                 => array('website' => 'toolwatch.io')
		);
		$async   = false;
		$ip_pool = 'Main Pool';
		$send_at = $sendAt;
		return $this->mandrill->messages->send($message, $async, $ip_pool, $send_at);
	}

	/**
	* Cancel all emails to $userId of $type that would be send between 
	* $inMin and $inMax
	*/
	private function cancelScheduled($userId, $type, $inMin = 0, $inMax = PHP_INT_MAX){

		$plannedEmails = $this->select()
				            ->where('userId', $userId)
				            ->where('type', $type)
				            ->where('plannedAt >', $inMin)
				            ->where('plannedAt <', $inMax)
				            ->where('status !=', $this->cancelledEmail)
				            ->find_all();

		foreach($plannedEmails as $email){

			$this->mandrill->messages->cancelScheduled($email->mandrillId);
			$this->update($email->id, array('status' => 'canceled'));

		}

		return $plannedEmails;
		
	}

	private function sendAtString($scheduleTime) {
		return date('Y-', $scheduleTime).date('m-', $scheduleTime).(date('d', $scheduleTime)).' '.(date('H', $scheduleTime)-1).':'.(date('i', $scheduleTime)).date(':s', $scheduleTime);
	}

	private function timeAtHoursFromNow($inHours){
		return time()+$inHours*$this->hour;
	}

	private function insertNewEmail($user, $mandrillResult, $plannedAt, $type, $measures =  array()){
		
		$data = array(
			'userId'    => $user->userId,
			'mandrillId'     => $mandrillResult['_id'],
			'MandrillStatus' => $mandrillResult['status'],
			'reject_reason' => $mandrillResult['reject_reason'],
			'plannedAt' => $plannedAt,
			'status' => 0,
			'type' => $type
		);

		if($this->insert($data)){
			return $this->insert_id();
		}

		return false;
	}


	private function checkAccuracyIn($user, $measureId, $watchId, $time, $delta, $template, $mandrillTag){
		//Cancel email that would be sent in +/- 2 hours of this one
		$canceledEmails = $this->cancelScheduled($user->userId, $this->CHECK_ACCURACY,
						  						 $time - $delta, $time + $delta);

		//Find the measures contained in the canceled emails
		$measuresToSend = $this->emailmeasure->select()
						->where_in('emailId', __::pluck($canceledEmails, 'id'))
						->find_all();

		//extract the unique watch ids contained in $measuresToSend
		$watchIds = __::uniq(__::pluck($measuresToSend, 'watchId'));

		//Get the watch brand and name corresponding to $watchIds + $watchId
		$data['watches'] = $this->watch->select('brand, name')
			->where_in('watchId', __::union($watchIds, $watchId))
			->find_all();

		//Send the Mandrill Email
		$result = $this->sendMandrillEmail(
			'It\'s time to check your watch\'s accuracy !'
			$this->load->view($template, $data, true),
			$user->name.' '.$user->firstname,
			$user->email,
			$mandrillTag,
			$this->sendAtString($time)
		);

		// Insert the Mandrill email in our DB
		$insertedId = $this->insertNewEmail($user, $result, $time, $this->CHECK_ACCURACY);

		// Update the measures to reflects their new belonging email
		if($insertedId !== FALSE){

			$updateData = array('emailId' => $insertedId);

			$this->emailmeasure->update(__::pluck($measuresToSend, 'id'), 'id');

			$inserData = array('emailId' => $insertedId, 
				'measureId' => $measureId,
				'watchId' => $watchId
			);

			$this->emailmeasure->insert($inserData);
		}
	}

	public function checkAccuracy($user, $measureId, $watchId){
		
		$this->checkAccuracyIn(
			$user, 
			$measureId, 
			$watchId, 
			$this->timeAtHoursFromNow(24),
			$this->hour * 2,
			'email/remind-check-accuracy'
			'check_accuracy_email'
		);

		$this->checkAccuracyIn(
			$user, 
			$measureId, 
			$watchIds, 
			$watchId->timeAtHoursFromNow(24*7),
			$this->hour * 2,
			'email/remind-check-accuracy'
			'check_accuracy_1w_email'
		);

	}

	private function startNewMeasure($user, $watch){

		$in30days = $this->timeAtHoursFromNow(30*24);

		$data['user'] = $user;
		$data['watch'] = $watch;

		$this->sendMandrillEmail(
			'How are you and your watch doing? ⌚'
			$this->load->view('email/start-new-measure', $data, true),
			$user->name.' '.$user->firstname,
			$user->email,
			'start_new_measure_email',
			$this->sendAtString($in30days)
		);

		return $this->insertNewEmail($user, $result, $in30days, $this->START_NEW_MEASURE);
	}

	public function newResult($user, $watch){

		$data['user'] = $user;
		$data['watch'] = $watch;

		$this->sendMandrillEmail(
			'You\'re a great Toolwatch user! '
			$this->load->view('email/watch-result', $data, true),
			$user->name.' '.$user->firstname,
			$user->email,
			'add_another_watch_email',
			$this->sendAtString(time())
		);

		$this->startNewMeasure($user, $watch);
		$this->addAnotherWatch($user, $watch);

		// We don't store these ones as we don't want
		// to cancel them, ever.
		return true;
	}

	/**
	 * Schedule an email to add another watch if the user have only 1 wacth and
	 * 1 complete measure
	 */
	private function addAnotherWatch($user, $firstWatch){

		if($this->watch->count_by('userId', $firstWatch->userId) === 1 
		&& $this->measure->count_by('watchId', $firstWatch->watchId) === 1){

			$in48Hours = $this->timeAtHoursFromNow(48);

			$data['user'] = $user;
			$data['firstWatch'] = $firstWatch;

			$result = $this->sendMandrillEmail(
				'You\'re a great Toolwatch user! '
				$this->load->view('email/add-second-watch', $data, true),
				$user->name.' '.$user->firstname,
				$user->email,
				'add_another_watch_email',
				$this->sendAtString($in48Hours)
			);
		}
	}

	/**
	 * Send email to add a watch 24 hours after signup
	 */
	private function addFirstWatch($user) {

		$in24Hours = $this->timeAtHoursFromNow(24);

		$result = $this->sendMandrillEmail(
			'Let\'s add a watch and start measuring!'
			$this->load->view('email/add-first-watch', '', true),
			$user->name.' '.$user->firstname,
			$user->email,
			'add_first_watch_email',
			$this->sendAtString($in24Hours)
		);
		
		return $this->insertNewEmail($user, $result, $in24Hours, $this->ADD_FIRST_WATCH);
	}


	public function secondWatchAdded(){
		$this->cancelScheduled($user->userId, $this->ADD_SECOND_WATCH);
	}

	public function firstWatchAdded(){
		$this->cancelScheduled($user->userId, $this->ADD_FIRST_WATCH);
	}
}