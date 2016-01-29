<?php

/**
 * Email
 *
 * This libraries defines automatic emails
 * sent to users during their toolwatch adventure.
 *
 * ------------------
 *  AUTOMATIC EMAILS
 * ------------------
 *
 * ADD_FIRST_WATCH : Send an email to registered users
 * 48 hours after their sign up if they didn't add a watch
 *
 * CHECK_ACCURACY : Send an email 24 hours after an user
 * synchronized a watch to remind him to take the second measure
 *
 * ADD_SECOND_WATCH : Send an email after this first completed measure
 * if the user only has one registered watch
 *
 * CHECK_ACCURACY_1_WEEK : 1 week after a completed measure,
 * we remind the user to check the precision again
 *
 * START_NEW_MEASURE : One month after a completed measure, we
 * send a reminder to check the accuracy again
 *
 * -------------
 * EVENT EMAILS
 * -------------
 *
 * SIGN_UP : Classical email on signup (email of facebook)
 *
 * RESET_PASSWORD : Send a new password to users
 *
 * NEW_ACCURACY : Send an email after a complete measure with
 * the measure accuracy. This is email has an .icc setting a
 * calendar (iCal, Google Cal, ...) event. The event is
 * for measuring the watch again in one month.
 *
 * Automatic emails are cron based. A call to hooks/emails is made
 * at fixed intervals.
 *
 * Event emails are based on the observer pattern. This model
 * observes other relevant model such as user of measure.
 *
 */
class Auto_email {

	/**
	 * Constantes for emails ids.
	 * Theses ids are used to ensure that
	 * an email isn't sent twice for an user.
	 */
	public $ADD_FIRST_WATCH       = 1;
	public $CHECK_ACCURACY        = 2;
	public $ADD_SECOND_WATCH      = 3;
	public $START_NEW_MEASURE     = 4;
	public $COMEBACK              = 5;
	public $START_FIRST_MEASURE   = 6;
	public $CHECK_ACCURACY_1_WEEK = 7;

	public $idToType = ["ADD_FIRST_WATCH", "CHECK_ACCURACY", "ADD_SECOND_WATCH",
		"START_NEW_MEASURE", "COMEBACK", "START_FIRST_MEASURE", "CHECK_ACCURACY_1_WEEK"];

	private $hour           = 3600;
	private $day            = 86400;
	private $cancelledEmail = 1;
	private $timeOffset 		= 0;
	private $time;
	private $lastBatchDate  = 0;
	private $emailBatchModel;

	/**
	 * Load model, library and helpers
	 */
	function __construct() {

		$this->CI =& get_instance();

		$this->CI->load->library("mandrill");
		$this->CI->load->library("__");
		$this->CI->load->model("watch");
		$this->CI->load->model("measure");
		$this->CI->load->model("user");
		$this->CI->load->helper("email_content");
		$this->CI->load->library("mcapi");
	}

	/**
	 * Obverved model call this method one something
	 * noticieable happen on their side. This method
	 * will send event emails
	 *
	 * @param  MY_MODEL $subject An instance of the model calling
	 * @param  String $event   Which event has been triggered
	 * @param  Array $data    data related to the event
	 */
	public function updateObserver($subject, $event, $data) {
		switch ($event) {
			case "SIGN_UP":
			case "SIGN_UP_FB":
				return $this->signup($data);
				break;
			case "NEW_ACCURACY":
				return $this->newResult($data['measure']);
				break;
			case "RESET_PASSWORD":
				return $this->resetPassword($data['email'], $data['token']);
				break;
		}
	}

	/**
	 * Check which emails should be sent now
	 * according to the email specifications
	 *
	 * @param  $timeOffset seconds to add or retrieve
	 * to time().
	 * @return Array       email sent
	 */
	public function cronCheck($timeOffset = 0) {

	 /**
	  * The computed time will be used
	  * to compute the rules. However, the time
	  * at which the email will be sent will be
	  * $time - $timeOffset (@see sendAtString).
	  *
	  * The whole purpose of this $timeOffset
	  * variable is for testing / staging.
	  * We want to be able to compute the rules
	  * in the future and past. However, we want
	  * the computed emails to be sent right away.
	  *
	  * Overall, this should be 0 everytime unless
	  * testing or staging.
	  * @var long
	  */
		$this->time = time() + $timeOffset;
		$this->timeOffset = $timeOffset;

		//Creating empty array to store email to send
		$emailsUserSent    = array();
		$emailsWatchSent   = array();
		$emailsMeasureSent = array();

		$this->emailBatchModel = new MY_MODEL("email_batch");

		$this->lastBatchDate = $this->findLastBatchDate();

		//Apply all the rules for emails
		//The emails arrays are sent by references and
		//updated in the different methods
		$this->inactiveUser($emailsUserSent);
		$this->userWithoutWatch($emailsUserSent);
		$this->userWithWatchWithoutMeasure($emailsWatchSent);
		$this->userWithOneCompleteMeasureAndOneWatch($emailsUserSent);
		$this->checkAccuracy($emailsMeasureSent);
		$this->checkAccuracyOneWeek($emailsMeasureSent);
		$this->startANewMeasure($emailsWatchSent);

		if(ENVIRONMENT === "development"){
			$date = new DateTime("@$time");
			echo "<h1> Emails sent at " . $date->format('Y-m-d H:i:s') . "</h1>";

			$this->showSentEmails($emailsUserSent, "User emails");
			$thus->showSentEmails($emailsWatchSent, "Watch emails");
			$thus->showSentEmails($emailsMeasureSent, "Measure emails");
		}


		$this->emailBatchModel->insert(
			array("time"=>$this->time,
			"amount" => sizeof($emailsMeasureSent)
				+ sizeof($emailsWatchSent)
				+ sizeof($emailsMeasureSent)
			)
		);


		return array(
			'users' 	 => $emailsUserSent,
			'watches'  => $emailsWatchSent,
			'measures' => $emailsMeasureSent
		);
	}

	/**
	 * Returns the last time we sent emails
	 * @return Long
	 */
	private function findLastBatchDate(){


		if($this->emailBatchModel->count_all() === 0){
			throw new Exception("Email Batch model can't be empty", 1);
		}

		return (float) $this->emailBatchModel
			->select("time")
			->order_by("id", "desc")
			->limit(1)
			->find_all()[0]->time;
	}

	/**
	 * Convenient function to display the emails
	 * being generated by cronCheck
	 *
	 * @param  Array $emails
	 * @param  String $title  A nice title to distinguish between
	 * email types
	 */
	private function showSentEmails($emails, $title){

		echo "<h2> ".$title." </h2>";

		foreach ($emails as $email) {
			echo 'TO ' . $this->CI->user->find_by('userId', $email['userId'])->email
				. " " . $this->idToType[$email['emailType']];
			echo '\n'; var_dump($email['mandrill']); echo '\n';
			echo $email['content'];
		}
	}

	/**
	 * Send an email throught the mandrill api
	 *
	 * @param  String $subject
	 * @param  html 	$content
	 * @param  String $recipientName
	 * @param  String $recipientEmail
	 * @param  String $tags One tag for the email to see it in the
	 * Mandrill backend
	 * @param  String $sendAt The date at which to send the email
	 * @see sendAtString
	 * @param  Base64 $attachments  A Base64 representation of
	 * an attachment
	 * @return Array  Mandrill API Response
	 */
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
		$mandrillResponse =  $this->CI->mandrill->messages->send($message, $async, $ip_pool, $send_at);
		log_message('info', 'Mandrill email: ' . print_r($mandrillResponse, true));
		return $mandrillResponse;
	}

	/**
	 * Helper method to convert a timestamp in a Mandrill
	 * valid string
	 *
	 * @param  Long $scheduleTime timestamp
	 * @return String A Mandrill valide data as String
	 */
	private function sendAtString($scheduleTime) {

		//We remove $this->timeOffset to $scheduleTime in
		//order to send emails right away when exploring
		//computation in the future / past
		$scheduleTime = $scheduleTime  - $this->timeOffset;

		log_message('info', 'Date ' . print_r($scheduleTime, true));


		$returnValue =  date('Y-', $scheduleTime).date('m-', $scheduleTime)
		.(date('d', $scheduleTime)).' '.(date('H', $scheduleTime)).':'
		.(date('i', $scheduleTime)).':'.(date('s', $scheduleTime));

		log_message('info', 'Date ' . print_r($returnValue, true));


		return $returnValue;
	}

	/**
	 * Add a computed email to a $queue.
	 *
	 * @param Array $queue Received by reference and will be updated
	 * @param int $userId
	 * @param int $emailType
	 * @param long $time
	 * @param int $idTitle
	 * @param html $content
	 * @param array $mandrillResponse
	 */
	private function addEmailToQueue(&$queue, $userId, $emailType, $time,
		$idTitle, $content, $mandrillResponse) {
		array_push($queue,
			array(
				$idTitle    => $userId,
				'sentTime'  => $time,
				'emailType' => $emailType,
				'content'		=> $content,
				'mandrill'  => $mandrillResponse
			)
		);
	}

	private function getBatchUpperBound($timeCondition){
		return $this->time-$timeCondition;
	}

	private function getBatchLowerBound($timeCondition){
		return $this->time-$timeCondition-($this->time-$this->lastBatchDate);
	}

	/**
	 * Send email to incative user
	 *
	 * @param  long $time Time at which compute the rule
	 * @param  array $queuedEmail queue to store the computed email
	 */
	private function inactiveUser(&$queuedEmail) {
		$inactiveUsers = $this->CI
			->user
			->select()
			->where('lastLogin <', $this->getBatchUpperBound($this->day*100))
			->where('lastLogin >', $this->getBatchLowerBound($this->day*100))
			->find_all();

		if ($inactiveUsers !== FALSE) {
			foreach ($inactiveUsers as $user) {

				$emailcontent = $this->CI->load->view('email/generic',
					comebackContent($user->firstname), true);

				$this->addEmailToQueue(
					$queuedEmail,
					$user->userId,
					$this->COMEBACK,
					$this->time,
					'userId',
					$emailcontent,
					$this->sendMandrillEmail(
						'We haven\'t seen you for a while ? ⌚',
						$emailcontent,
						$user->name.' '.$user->firstname,
						$user->email,
						'comeback_100d',
						$this->sendAtString($this->time)
					)
				);
			}
		}
	}

	/**
	 * Send email to user who don't have any watch
	 *
	 * @param  long $time Time at which compute the rule
	 * @param  array $queuedEmail queue to store the computed email
	 */
	private function userWithoutWatch(&$queuedEmail) {

		$userWithoutWatch = $this->CI
			->user
			->select('user.userId, user.name, firstname, email, lastLogin')
			->where('(select count(1) from watch where user.userId = watch.userId) =', 0)
			->where('lastLogin <', $this->getBatchUpperBound($this->day))
			->where('lastLogin >', $this->getBatchLowerBound($this->day))
			->find_all();

		if ($userWithoutWatch !== FALSE) {
			foreach ($userWithoutWatch as $user) {

				$emailcontent = $this->CI->load->view('email/generic',
					addFirstWatchContent($user->firstname), true);

				$this->addEmailToQueue(
					$queuedEmail,
					$user->userId,
					$this->ADD_FIRST_WATCH,
					$this->time,
					'userId',
					$emailcontent,
					$this->sendMandrillEmail(
						'Let’s add a watch and start measuring! ⌚',
						$emailcontent,
						$user->name.' '.$user->firstname,
						$user->email,
						'add_first_watch_email',
						$this->sendAtString($this->time)
					)
				);
			}
		}
	}

	/**
	 * Send email to user witch a watch but without measure
	 * @param  long $time Time at which compute the rule
	 * @param  array $queuedEmail queue to store the computed email
	 */
	private function userWithWatchWithoutMeasure(&$queuedEmail) {
		$userWithWatchWithoutMeasure = $this->CI
			->watch
			->select('watch.watchId, watch.brand, watch.name as watchName,
			user.name, user.firstname, email')
			->join('user', 'watch.userId = user.userId')
			->where('(select count(1) from measure where watch.watchId = measure.watchId) = ', 0)
			->where('creationDate < ', $this->getBatchUpperBound($this->day))
			->where('creationDate > ', $this->getBatchLowerBound($this->day))
			->as_array()
			->find_all();

		if ($userWithWatchWithoutMeasure !== FALSE) {

			$this->CI->__->groupBy($userWithWatchWithoutMeasure, 'email');

			foreach ($userWithWatchWithoutMeasure as $user) {

				$user = (object) $user;

				$emailcontent = $this->CI->load->view('email/generic',
					makeFirstMeasureContent($user->firstname,
					$user->brand . ' ' . $user->watchName), true);

				$this->addEmailToQueue(
					$queuedEmail,
					$user->watchId,
					$this->START_FIRST_MEASURE,
					$this->time,
					'watchId',
					$emailcontent,
					$this->sendMandrillEmail(
						'Let’s start measuring! ⌚',
						$emailcontent,
						$user->name.' '.$user->firstname,
						$user->email,
						'make_first_measure_email',
						$this->sendAtString($this->time)
					)
				);
			}
		}
	}

	/**
	 * Send email to user With One Complet eMeasure And One Watch
	 *
	 * @param  long $time Time at which compute the rule
	 * @param  array $queuedEmail queue to store the computed email
	 */
	private function userWithOneCompleteMeasureAndOneWatch(&$queuedEmail) {

		$userWithOneCompleteMeasureAndOneWatch = $this->CI
			->user
			->select('user.userId, user.name, firstname, email')
			->where('(select count(1) from watch where user.userId = watch.userId) = ', 1)
			->where('(select count(1) from measure
					join watch on measure.watchId = watch.watchId
					where user.userId = watch.userId
					and measure.statusId = 2
					and measure.accuracyReferenceTime < '.$this->getBatchUpperBound($this->day*2).'
					and measure.accuracyReferenceTime > '.$this->getBatchLowerBound($this->day*2). ') = ', 1)
			->find_all();

		if ($userWithOneCompleteMeasureAndOneWatch !== FALSE) {

			foreach ($userWithOneCompleteMeasureAndOneWatch as $user) {

				// TODO: Why does this retrieve an array and
				// not an object ??
				$watch = (object) $this->CI->watch
					->select('brand, name')
					->find_by('userid', $user->userId);

				$emailcontent = $this->CI->load->view('email/generic',
					addSecondWatchContent($user->firstname,
					$watch->brand . " " .
					$watch->name)
					, true);

				$this->addEmailToQueue(
					$queuedEmail,
					$user->userId,
					$this->ADD_SECOND_WATCH,
					$this->time,
					'userId',
					$emailcontent,
					$this->sendMandrillEmail(
						'Add another watch ? ⌚',
						$emailcontent,
						$user->name.' '.$user->firstname,
						$user->email,
						'add_another_watch_email',
						$this->sendAtString($this->time)
					)
				);
			}
		}
	}

	/**
	 * Send email to user that need to check their accuracy
	 *
	 * @param  long $time Time at which compute the rule
	 * @param  array $queuedEmail queue to store the computed email
	 */
	private function checkAccuracy(&$queuedEmail) {

		$measureWithoutAccuracy = $this->CI
			->measure
			->select('measure.id as measureId, measure.*, watch.*,
								watch.name as watchName, user.userId, user.name,
								user.firstname, email')
			->join('watch', 'watch.watchId = measure.watchId')
			->join('user', 'watch.userId = user.userId')
			->where('statusId', 1)
			->where('measureReferenceTime <', $this->getBatchUpperBound($this->day))
			->where('measureReferenceTime >', $this->getBatchLowerBound($this->day))
			->as_array()
			->find_all();

		if ($measureWithoutAccuracy !== FALSE) {

			if(!is_array($measureWithoutAccuracy)){
				$measureWithoutAccuracy = array($measureWithoutAccuracy);
			}

			$this->CI->__->groupBy($measureWithoutAccuracy, 'email');

			foreach ($measureWithoutAccuracy as $user) {

				$user = (object) $user;

				$emailcontent = $this->CI->load->view('email/generic',
					checkAccuracyContent($user->firstname, $user), true);

				$this->addEmailToQueue(
					$queuedEmail,
					$user->measureId,
					$this->CHECK_ACCURACY,
					$this->time,
					'measureId',
					$emailcontent,
					$this->sendMandrillEmail(
						'Let’s check your watch accuracy! ⌚',
						$emailcontent,
						$user->name.' '.$user->firstname,
						$user->email,
						'check_accuracy_email',
						$this->sendAtString($this->time)
					)
				);
			}
		}
	}

	/**
	 * Send email to user to check their accuracy 1 week after
	 *
	 * @param  long $time Time at which compute the rule
	 * @param  array $queuedEmail queue to store the computed email
	 */
	private function checkAccuracyOneWeek(&$queuedEmail) {
		$measureWithoutAccuracy = $this->CI
			->measure
			->select('measure.id as measureId, watch.*, user.userId,
			measure.*, watch.name as watchName, user.name, user.firstname, email')
			->join('watch', 'watch.watchId = measure.watchId')
			->join('user', 'watch.userId = user.userId')
			->where('statusId', 1)
			->where('measureReferenceTime <', $this->getBatchUpperBound($this->day*7))
			->where('measureReferenceTime >', $this->getBatchLowerBound($this->day*7))
			->as_array()
			->find_all();

		if ($measureWithoutAccuracy !== FALSE) {

			$this->CI->__->groupBy($measureWithoutAccuracy, 'email');

			foreach ($measureWithoutAccuracy as $user) {

				$user = (object) $user;

				$emailcontent = $this->CI->load->view('email/generic',
					oneWeekAccuracyContent($user->firstname, $user), true);

				$this->addEmailToQueue(
					$queuedEmail,
					$user->measureId,
					$this->CHECK_ACCURACY_1_WEEK,
					$this->time,
					'measureId',
					$emailcontent,
					$this->sendMandrillEmail(
						'Let’s check your watch accuracy! ⌚',
						$emailcontent,
						$user->name.' '.$user->firstname,
						$user->email,
						'check_accuracy_email',
						$this->sendAtString($this->time)
					)
				);
			}
		}
	}

	/**
	 * Send email to user to start a new measure
	 *
	 * @param  long $time Time at which compute the rule
	 * @param  array $queuedEmail queue to store the computed email
	 */
	private function startANewMeasure(&$queuedEmail) {

		$userWithWatchWithoutMeasure = $this->CI
			->measure
			->select('watch.watchId, watch.name as watchName, watch.brand,
			user.userId, user.name, user.firstname, email, measure.*')
			->join('watch', 'watch.watchId = measure.watchId')
			->join('user', 'watch.userId = user.userId')
			->where('statusId', 2)
			->where('accuracyReferenceTime <', $this->getBatchUpperBound($this->day*30))
			->where('accuracyReferenceTime >', $this->getBatchLowerBound($this->day*30))
			->as_array()
			->find_all();

		if ($userWithWatchWithoutMeasure !== FALSE) {

			$this->CI->__->groupBy($userWithWatchWithoutMeasure, 'email');

			foreach ($userWithWatchWithoutMeasure as $user) {

				$user = (object) $user;

				$emailcontent = 	$this->CI->load->view('email/generic',
						oneMonthAccuracyContent($user->firstname, $user), true);

				$this->addEmailToQueue(
					$queuedEmail,
					$user->watchId,
					$this->START_NEW_MEASURE,
					$this->time,
					'watchId',
					$emailcontent,
					$this->sendMandrillEmail(
						'Let’s check your watch accuracy! ⌚',
						$emailcontent,
						$user->name.' '.$user->firstname,
						$user->email,
						'check_accuracy_email',
						$this->sendAtString($this->time)
					)
				);
			}
		}
	}

	/**
	 * Send the welcome email
	 * @param  User $user The newly created user
	 * @return Array mandrill API response
	 */
	private function signup($user) {

		$this->CI->mcapi->listSubscribe('7f94c4aa71', $user->email, '');

		return $this->sendMandrillEmail(
			'Welcome to Toolwatch! ⌚',
			$this->CI->load->view('email/signup', '', true),
			$user->name.' '.$user->firstname,
			$user->email,
			'signup',
			$this->sendAtString(time())
		);
	}

	/**
	 * Send a password token for reset $user
	 *
	 * @param $email $email
	 * @param String $token
	 */
	private function resetPassword($email, $token) {
		return $this->sendMandrillEmail(
			'Your Toolwatch password ⌚',
			$this->CI->load->view('email/reset-password', array('resetToken'=>$token), true),
			'',
			$email,
			'reset_password',
			$this->sendAtString(time())
		);
	}

	/**
	 * Send an email with the result of a measure
	 *
	 * @param  Measure $measure
	 */
	private function newResult($measure) {

		$attachments = array();
		$description = "Check the accuracy of my ".$measure->brand.' '.$measure->model;
		$this->CI->load->helper('ics');
		$in30days = time() + 30*24*60*60;

		array_push($attachments, array(
				'type'    => 'text/calendar',
				'name'    => 'Check my watch accuracy',
				'content' => generateBase64Ics($in30days, $in30days, $description, 'Check my watch accuracy', 'Toolwatch.io')
			));

		$emailcontent = $this->CI->load->view('email/generic',
					watchResultContent($measure->firstname, $measure->brand,
					$measure->model, $measure->accuracy), true);

		$this->sendMandrillEmail(
			'The result of your watch’s accuracy! ⌚',
			$this->CI->load->view('email/generic', $emailcontent, true),
			$measure->name.' '.$measure->firstname,
			$measure->email,
			'result_email',
			$this->sendAtString(time()),
			$attachments
		);

		// We don't store these ones as we don't want
		// to cancel them, ever.
		return true;
	}

}
