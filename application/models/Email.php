<?php

class Email extends MY_Model {

	public $ADD_FIRST_WATCH       = 1;
	public $CHECK_ACCURACY        = 2;
	public $ADD_SECOND_WATCH      = 3;
	public $START_NEW_MEASURE     = 4;
	public $COMEBACK              = 5;
	public $START_FIRST_MEASURE   = 6;
	public $CHECK_ACCURACY_1_WEEK = 7;

	private $hour           = 3600;
	private $day            = 86400;
	private $cancelledEmail = 1;

	function __construct() {
		parent::__construct();
		$this->table_name = "Email";
		$this->load->library('mandrill', 'pUOMLUusBKdoR604DpcOnQ');
		$this->load->model('watch');
		$this->load->model('measure');
		$this->load->model('user');
		$this->load->library('__');
	}

	public function updateObserver($subject, $event, $data) {
		switch ($event) {
			case "SIGN_UP":
			case "SIGN_UP_FB":
				return $this->signup($data);
				break;
			case "NEW_ACCURACY":
				return $this->newResult($data['user'], $data['watch'], $data['measure']);
				break;
			case "RESET_PASSWORD":
				return $this->resetPassword($data['user'], $data['token']);
				break;
		}
	}

	public function cronCheck($time = null) {

		if($time === null){
			$time = time();
		}

		$emailsUserSent    = array();
		$emailsWatchSent   = array();
		$emailsMeasureSent = array();

		$this->inactiveUser($time, $emailsUserSent);
		$this->userWithoutWatch($time, $emailsUserSent);
		$this->userWithWatchWithoutMeasure($time, $emailsWatchSent);
		$this->userWithOneCompleteMeasureAndOneWatch($time, $emailsUserSent);
		$this->checkAccuracy($time, $emailsMeasureSent);
		$this->checkAccuracyOneWeek($time, $emailsMeasureSent);
		$this->startANewMeasure($time, $emailsWatchSent);

		return array(
			'users' 	 => $emailsUserSent,
			'watches'  => $emailsWatchSent,
			'measures' => $emailsMeasureSent
		);
	}

	private function sendMandrillEmail($subject, $content, $recipientName,
		$recipientEmail, $tags, $sendAt, $attachments = null) {

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
			'headers'   => array(
				'Reply-To' => 'hello@toolwatch.io',
			),
			'important'                 => false,
			'track_opens'               => true,
			'track_clicks'              => true,
			'tags'                      => array($tags),
			'google_analytics_campaign' => $tags,
			'google_analytics_domains'  => array('toolwatch.io'),
			'metadata'                  => array(
				'website'                  => 'toolwatch.io',
			)
		);

		if ($attachments !== null) {
			$message['attachments'] = $attachments;
		}

		$async   = false;
		$ip_pool = 'Main Pool';
		$send_at = $sendAt;
		return $this->mandrill->messages->send($message, $async, $ip_pool, $send_at);
	}

	private function sendAtString($scheduleTime) {
		return date('Y-', $scheduleTime).date('m-', $scheduleTime)
		.(date('d', $scheduleTime)).' '.(date('H', $scheduleTime)-1).':'
		.(date('i', $scheduleTime)).date(':s', $scheduleTime);
	}

	private function whereNotAlreadySentUser($emailType) {
		return '(select count(1) from email_user where user.userId '.
			'= email_user.userId and emailType = '.$emailType.') = ';
	}

	private function whereNotAlreadySentWatch($emailType) {
		return $this->where('(select count(1) from email_watch where watch.watchId '.
			'= email_watch.watchId and emailType = '.$emailType.')', 0);
	}

	private function whereNotAlreadySentMeasure($emailType) {
		return $this->where('(select count(1) from email_measure where measure.id '.
			'= email_measure.measureId and emailType = '.$emailType.')', 0);
	}

	private function addEmailToQueue($queue, $userId, $emailType, $time) {
		array_push($queue,
			array(
				'userId'    => $userId,
				'sendTime'  => $time,
				'emailType' => $emailType,
			)
		);
	}

	private function inactiveUser($time, $queuedEmail) {
		$inactiveUsers = $this
			->user
			->select()
			->where('lastLogin <=', $time-$this->day*100)
			->where($this->whereNotAlreadySentUser($this->COMEBACK), 0, false)
			->find_all();

		if ($inactiveUsers !== FALSE) {
			foreach ($inactiveUsers as $user) {
				$this->sendMandrillEmail(
					'We haven\'t seen you for a while ?',
					$this->load->view('email/comeback_100d', $user, true),
					$user->name.' '.$user->firstname,
					$user->email,
					'comeback_100d',
					$this->sendAtString($time)
				);

				$this->addEmailToQueue(
					$queuedEmail,
					$user->userId,
					$this->COMEBACK,
					$time
				);
			}
		}
	}

	private function userWithoutWatch($time, $queuedEmail) {

		$userWithoutWatch = $this
			->user
			->select('user.userId, user.name, firstname, email')
			->where('(select count(1) from watch where user.userId = watch.userId) =', 0)
			->where($this->whereNotAlreadySentUser($this->ADD_FIRST_WATCH), 0, false)
			->where('lastLogin <=', $time-$this->day)
			->find_all();

		if ($userWithoutWatch !== FALSE) {
			foreach ($userWithoutWatch as $user) {
				$this->sendMandrillEmail(
					'Let’s add a watch and start measuring! ⌚',
					$this->load->view('email/add-first-watch', $user, true),
					$user->name.' '.$user->firstname,
					$user->email,
					'add_first_watch_email',
					$this->sendAtString($time)
				);

				$this->addEmailToQueue(
					$queuedEmail,
					$user->userId,
					$this->ADD_FIRST_WATCH,
					$time
				);
			}
		}
	}

	private function userWithWatchWithoutMeasure($time, $queuedEmail) {
		$userWithWatchWithoutMeasure = $this
			->watch
			->select('user.name, user.firstname, email')
			->join('user', 'watch.userId = user.userId')
			->where('(select count(1) from measure where watch.watchId = measure.watchId) = ', 0)
			->where('creationDate <=', $time-$this->day)
			->where($this->whereNotAlreadySentUser($this->START_FIRST_MEASURE), 0, false)
			->find_all();

		if ($userWithWatchWithoutMeasure !== FALSE) {

			$this->__->groupBy($userWithWatchWithoutMeasure, 'email');

			foreach ($userWithWatchWithoutMeasure as $user) {

				$this->sendMandrillEmail(
					'Let’s start measuring! ⌚',
					$this->load->view('email/make-first-measure', $user, true),
					$user->name.' '.$user->firstname,
					$user->email,
					'make_first_measure_email',
					$this->sendAtString($time)
				);

				$this->addEmailToQueue(
					$queuedEmail,
					$user->userId,
					$this->START_FIRST_MEASURE,
					$time
				);
			}
		}
	}

	private function userWithOneCompleteMeasureAndOneWatch($time, $queuedEmail) {
		$twoDays = $time-$this->day*2;

		$userWithOneCompleteMeasureAndOneWatch = $this
			->user
			->select('user.userId, user.name, firstname, email')
			->where('(select count(1) from watch where user.userId = watch.userId) = ', 1)
			->where('(select count(1) from measure
					join watch on measure.watchId = watch.watchId
					where user.userId = watch.userId
					and measure.statusId = 2
					and measure.accuracyUserTime) <= ', $twoDays)
			->where($this->whereNotAlreadySentUser($this->ADD_SECOND_WATCH), 0, false)
			->find_all();

		if ($userWithOneCompleteMeasureAndOneWatch !== FALSE) {

			foreach ($userWithOneCompleteMeasureAndOneWatch as $user) {

				$this->sendMandrillEmail(
					'Add another watch ? ⌚',
					$this->load->view('email/add-second-watch', $user, true),
					$user->name.' '.$user->firstname,
					$user->email,
					'add_another_watch_email',
					$this->sendAtString($time)
				);

				$this->addEmailToQueue(
					$queuedEmail,
					$user->userId,
					$this->ADD_SECOND_WATCH,
					$time
				);
			}
		}
	}

	private function checkAccuracy($time, $queuedEmail) {
		$measureWithoutAccuracy = $this
			->measure
			->select('user.userId, user.name, user.firstname, email')
			->join('watch', 'watch.watchId = measure.watchId')
			->join('user', 'watch.userId = user.userId')
			->where('statusId', 1)
			->where('measureReferenceTime <=', $time-$this->day)
			->where($this->whereNotAlreadySentUser($this->CHECK_ACCURACY), 0, false)
			->as_array()
			->find_all();

		if ($measureWithoutAccuracy !== FALSE) {

			if(!is_array($measureWithoutAccuracy)){
				$measureWithoutAccuracy = array($measureWithoutAccuracy);
			}

			$this->__->groupBy($measureWithoutAccuracy, 'email');

			foreach ($measureWithoutAccuracy as $user) {

				$user = (object) $user;

				$this->sendMandrillEmail(
					'Let’s check your watch accuracy! ⌚',
					$this->load->view('email/remind-check-accuracy', $user, true),
					$user->name.' '.$user->firstname,
					$user->email,
					'check_accuracy_email',
					$this->sendAtString($time)
				);

				$this->addEmailToQueue(
					$queuedEmail,
					$user->userId,
					$this->CHECK_ACCURACY,
					$time
				);
			}
		}
	}

	private function checkAccuracyOneWeek($time, $queuedEmail) {
		$measureWithoutAccuracy = $this
			->measure
			->select('user.userId, user.name, user.firstname, email')
			->join('watch', 'watch.watchId = measure.watchId')
			->join('user', 'watch.userId = user.userId')
			->where('statusId', 1)
			->where('measureReferenceTime <=', $time-($this->day*7))
			->where($this->whereNotAlreadySentUser($this->CHECK_ACCURACY_1_WEEK), 0, false)
			->find_all();

		if ($measureWithoutAccuracy !== FALSE) {

			$this->__->groupBy($measureWithoutAccuracy, 'email');

			foreach ($measureWithoutAccuracy as $user) {

				$this->sendMandrillEmail(
					'Let’s check your watch accuracy! ⌚',
					$this->load->view('email/remind-check-accuracy', $user, true),
					$user->name.' '.$user->firstname,
					$user->email,
					'check_accuracy_email',
					$this->sendAtString($time)
				);

				$this->addEmailToQueue(
					$queuedEmail,
					$user->userId,
					$this->CHECK_ACCURACY_1_WEEK,
					$time
				);
			}
		}
	}

	private function startANewMeasure($time, $queuedEmail) {
		$userWithWatchWithoutMeasure = $this
			->measure
			->select('user.userId, user.name, user.firstname, email')
			->join('watch', 'watch.watchId = measure.watchId')
			->join('user', 'watch.userId = user.userId')
			->where('statusId', 2)
			->where('accuracyReferenceTime', $time-($this->day*30))
			->where($this->whereNotAlreadySentUser($this->START_NEW_MEASURE), 0, false)
			->find_all();

		if ($measureWithoutAccuracy !== FALSE) {

			$this->__->groupBy($measureWithoutAccuracy, 'email');

			foreach ($measureWithoutAccuracy as $user) {

				$this->sendMandrillEmail(
					'Let’s check your watch accuracy! ⌚',
					$this->load->view('email/remind-check-accuracy', $user, true),
					$user->name.' '.$user->firstname,
					$user->email,
					'check_accuracy_email',
					$this->sendAtString($time)
				);

				$this->addEmailToQueue(
					$queuedEmail,
					$user->userId,
					$this->START_NEW_MEASURE,
					$time
				);
			}
		}
	}

	private function signup($user) {

		$this->load->helper('mcapi');
		$api = new MCAPI('8d13b5ce53af2e80af803078bfd91e9e-us9');
		$api->listSubscribe('7f94c4aa71', $user->email, '');

		return $this->sendMandrillEmail(
			'Welcome to Toolwatch!',
			$this->load->view('email/signup', '', true),
			$user->name.' '.$user->firstname,
			$user->email,
			'signup',
			$this->sendAtString(time())
		);
	}

	private function resetPassword($user, $data) {
		return $this->sendMandrillEmail(
			'Your Toolwatch password',
			$this->load->view('email/reset-password', $data, true),
			$user->name.' '.$user->firstname,
			$user->email,
			'reset_password',
			$this->sendAtString(time())
		);
	}

	private function newResult($user, $watch, $measure) {

		$data['user']  = $user;
		$data['watch'] = $watch;

		$attachments = array();
		$description = "Check accuracy of my ".$watch->brand.' '.$watch->name;
		$this->load->helper('ics');
		$in30days = $this->timeAtHoursFromNow(30*24);

		array_push($attachments, array(
				'type'    => 'text/calendar',
				'name'    => 'Check my watch accuracy',
				'content' => generateBase64Ics($in30days, $in30days, $description, 'Check my watch accuracy', 'Toolwatch.io')
			));

		$this->sendMandrillEmail(
			'The result of your watch’s accuracy! ⌚',
			$this->load->view('email/watch-result', $data, true),
			$user->name.' '.$user->firstname,
			$user->email,
			'result_email',
			$this->sendAtString(time()),
			$attachments
		);

		// We don't store these ones as we don't want
		// to cancel them, ever.
		return true;
	}

}
