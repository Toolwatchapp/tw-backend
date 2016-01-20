<?php

/**
 * Email Model
 *
 * This model defines automatic emails
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
class Email extends MY_Model {

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

	/**
	 * Load model, library and helpers
	 */
	function __construct() {
		parent::__construct();
		$this->table_name = "Email";
		$this->load->library('mandrill');
		$this->load->library('__');
		$this->load->model('watch');
		$this->load->model('measure');
		$this->load->model('user');
		$this->load->helper('email_content');
		$this->load->helper('mcapi');
		$this->mcapi = new MCAPI(getenv("MC_APIKEY"));
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
				return $this->resetPassword($data['user'], $data['token']);
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
		$time = time() + $timeOffset;
		$this->timeOffset = $timeOffset;

		//Creating empty array to store email to send
		$emailsUserSent    = array();
		$emailsWatchSent   = array();
		$emailsMeasureSent = array();

		//Apply all the rules for emails
		//The emails arrays are sent by references and
		//updated in the different methods
		$this->inactiveUser($time, $emailsUserSent);
		$this->userWithoutWatch($time, $emailsUserSent);
		$this->userWithWatchWithoutMeasure($time, $emailsWatchSent);
		$this->userWithOneCompleteMeasureAndOneWatch($time, $emailsUserSent);
		$this->checkAccuracy($time, $emailsMeasureSent);
		$this->checkAccuracyOneWeek($time, $emailsMeasureSent);
		$this->startANewMeasure($time, $emailsWatchSent);

		//Store all sent email in order to send them only once
		$this->insertAll($emailsUserSent, new MY_Model('email_user'));
		$this->insertAll($emailsWatchSent, new MY_Model('email_watch'));
	  $this->insertAll($emailsMeasureSent, new MY_Model('email_measure'));

		if(ENVIRONMENT === "development"){
			$date = new DateTime("@$time");
			echo "<h1> Emails sent at " . $date->format('Y-m-d H:i:s') . "</h1>";

			$this->showSentEmails($emailsUserSent, "User emails");
			$thus->showSentEmails($emailsWatchSent, "Watch emails");
			$thus->showSentEmails($emailsMeasureSent, "Measure emails");
		}

		return array(
			'users' 	 => $emailsUserSent,
			'watches'  => $emailsWatchSent,
			'measures' => $emailsMeasureSent
		);
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
			echo 'TO ' . $this->user->find_by('userId', $email['userId'])->email
				. " " . $this->idToType[$email['emailType']];
			echo '\n'; var_dump($email['mandrill']); echo '\n';
			echo $email['content'];
		}
	}

	/**
	 * Helper method to batch insert in a model
	 * @param  Array $array data to batch insert
	 * @param  MY_MODEL $model Model to insert
	 */
	private function insertAll($array, $model){
		if(is_array($array) && sizeof($array) !== 0){


			foreach ($array as &$insertion) {
				//The content key is used for unit testing.
				//It stores the email as html so we can test it.
				//However, it doesn't make sense to store the html
				//in the database.
				//Here we unset the 'content' key of each insertion
				//in order to avoid the database insertion of the
				//html.
				unset($insertion['content']);

				//As per mandrill specification https://mandrillapp.com/api/docs/messages.php.html
				//A mandrill api call will return
		    // Array
		    // (
		    //     [0] => Array
		    //         (
		    //             [email] => recipient.email@example.com
		    //             [status] => sent
		    //             [reject_reason] => hard-bounce
		    //             [_id] => abc123abc123abc123abc123abc123
		    //         )
		    // )
		    // If the status isn't sent, we don't save the email
		    // as sent and log the reject reason.
				if($insertion['mandrill'][0]['status'] != "sent"){

					log_message('error', 'Mandrill failled for '
						. $insertion['mandrill'][0]['email'] . ' reason '
						. $insertion['mandrill'][0]['reject_reason']);

					//Unset the failled insertion so we don't store it on
					//our side
					unset($array[$insertion]);

				}else{
					//If the email was sent; unset mandrill as it's not
					//a field in the db. Very much like content.
					unset($insertion['mandrill']);
				}
			}

			$model->insert_batch($array);
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
		return $this->mandrill->messages->send($message, $async, $ip_pool, $send_at);
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
		return date('Y-', $scheduleTime).date('m-', $scheduleTime)
		.(date('d', $scheduleTime)).' '.(date('H', $scheduleTime)-1).':'
		.(date('i', $scheduleTime)).date(':s', $scheduleTime);
	}

	/**
	 * Helper thate created a subquery on email_user
	 * and emailType
	 * @param  int $emailType The type of the email to look for
	 * @return String The subquery
	 */
	private function whereNotAlreadySentUser($emailType) {
		return '(select count(1) from email_user where user.userId '.
			'= email_user.userId and emailType = '.$emailType.') = ';
	}

	/**
	 * Helper thate created a subquery on email_watch
	 * and emailType
	 * @param  int $emailType The type of the email to look for
	 * @return String The subquery
	 */
	private function whereNotAlreadySentWatch($emailType) {
		return '(select count(1) from email_watch where watch.watchId '.
			'= email_watch.watchId and emailType = '.$emailType.') = ';
	}

	/**
	 * Helper thate created a subquery on email_measure
	 * and emailType
	 * @param  int $emailType The type of the email to look for
	 * @return String The subquery
	 */
	private function whereNotAlreadySentMeasure($emailType) {
		return '(select count(1) from email_measure where measure.id '.
			'= email_measure.measureId and emailType = '.$emailType.') = ';
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

	/**
	 * Send email to incative user
	 *
	 * @param  long $time Time at which compute the rule
	 * @param  array $queuedEmail queue to store the computed email
	 */
	private function inactiveUser($time, &$queuedEmail) {
		$inactiveUsers = $this
			->user
			->select()
			->where('lastLogin <=', $time-$this->day*100)
			->where($this->whereNotAlreadySentUser($this->COMEBACK), 0, false)
			->find_all();

		if ($inactiveUsers !== FALSE) {
			foreach ($inactiveUsers as $user) {

				$emailcontent = $this->load->view('email/generic',
					comebackContent($user->firstname), true);


				$this->addEmailToQueue(
					$queuedEmail,
					$user->userId,
					$this->COMEBACK,
					$time,
					'userId',
					$emailcontent,
					$this->sendMandrillEmail(
						'We haven\'t seen you for a while ? ⌚',
						$emailcontent,
						$user->name.' '.$user->firstname,
						$user->email,
						'comeback_100d',
						$this->sendAtString($time)
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
	private function userWithoutWatch($time, &$queuedEmail) {

		$userWithoutWatch = $this
			->user
			->select('user.userId, user.name, firstname, email')
			->where('(select count(1) from watch where user.userId = watch.userId) =', 0)
			->where($this->whereNotAlreadySentUser($this->ADD_FIRST_WATCH), 0)
			->where('lastLogin <=', $time-$this->day)
			->find_all();

		if ($userWithoutWatch !== FALSE) {
			foreach ($userWithoutWatch as $user) {

				$emailcontent = $this->load->view('email/generic',
					addFirstWatchContent($user->firstname), true);

				$this->addEmailToQueue(
					$queuedEmail,
					$user->userId,
					$this->ADD_FIRST_WATCH,
					$time,
					'userId',
					$emailcontent,
					$this->sendMandrillEmail(
						'Let’s add a watch and start measuring! ⌚',
						$emailcontent,
						$user->name.' '.$user->firstname,
						$user->email,
						'add_first_watch_email',
						$this->sendAtString($time)
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
	private function userWithWatchWithoutMeasure($time, &$queuedEmail) {
		$userWithWatchWithoutMeasure = $this
			->watch
			->select('watch.watchId, watch.brand, watch.name as watchName,
			user.name, user.firstname, email')
			->join('user', 'watch.userId = user.userId')
			->where('(select count(1) from measure where watch.watchId = measure.watchId) = ', 0)
			->where('creationDate <=', $time-$this->day)
			->where($this->whereNotAlreadySentWatch($this->START_FIRST_MEASURE), 0, false)
			->as_array()
			->find_all();

		if ($userWithWatchWithoutMeasure !== FALSE) {

			$this->__->groupBy($userWithWatchWithoutMeasure, 'email');

			foreach ($userWithWatchWithoutMeasure as $user) {

				$user = (object) $user;

				$emailcontent = $this->load->view('email/generic',
					makeFirstMeasureContent($user->firstname,
					$user->brand . ' ' . $user->watchName), true);

				$this->addEmailToQueue(
					$queuedEmail,
					$user->watchId,
					$this->START_FIRST_MEASURE,
					$time,
					'watchId',
					$emailcontent,
					$this->sendMandrillEmail(
						'Let’s start measuring! ⌚',
						$emailcontent,
						$user->name.' '.$user->firstname,
						$user->email,
						'make_first_measure_email',
						$this->sendAtString($time)
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
	private function userWithOneCompleteMeasureAndOneWatch($time, &$queuedEmail) {
		$twoDays = $time-$this->day*2;

		$userWithOneCompleteMeasureAndOneWatch = $this
			->user
			->select('user.userId, user.name, firstname, email')
			->where('(select count(1) from watch where user.userId = watch.userId) = ', 1)
			->where('(select count(1) from measure
					join watch on measure.watchId = watch.watchId
					where user.userId = watch.userId
					and measure.statusId = 2
					and measure.accuracyReferenceTime <= '.$twoDays.' ) = ', 1)
			->where($this->whereNotAlreadySentUser($this->ADD_SECOND_WATCH), 0, false)
			->find_all();

		if ($userWithOneCompleteMeasureAndOneWatch !== FALSE) {

			foreach ($userWithOneCompleteMeasureAndOneWatch as $user) {

				// TODO: Why does this retrieve an array and
				// not an object ??
				$watch = (object) $this->watch
					->select('brand, name')
					->find_by('userid', $user->userId);

				$emailcontent = $this->load->view('email/generic',
					addSecondWatchContent($user->firstname,
					$watch->brand . " " .
					$watch->name)
					, true);

				$this->addEmailToQueue(
					$queuedEmail,
					$user->userId,
					$this->ADD_SECOND_WATCH,
					$time,
					'userId',
					$emailcontent,
					$this->sendMandrillEmail(
						'Add another watch ? ⌚',
						$emailcontent,
						$user->name.' '.$user->firstname,
						$user->email,
						'add_another_watch_email',
						$this->sendAtString($time)
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
	private function checkAccuracy($time, &$queuedEmail) {

		$measureWithoutAccuracy = $this
			->measure
			->select('measure.id as measureId, measure.*, watch.*,
								watch.name as watchName, user.userId, user.name,
								user.firstname, email')
			->join('watch', 'watch.watchId = measure.watchId')
			->join('user', 'watch.userId = user.userId')
			->where('statusId', 1)
			->where('measureReferenceTime <=', $time-$this->day)
			->where($this->whereNotAlreadySentMeasure($this->CHECK_ACCURACY), 0, false)
			->as_array()
			->find_all();

		if ($measureWithoutAccuracy !== FALSE) {

			if(!is_array($measureWithoutAccuracy)){
				$measureWithoutAccuracy = array($measureWithoutAccuracy);
			}

			$this->__->groupBy($measureWithoutAccuracy, 'email');

			foreach ($measureWithoutAccuracy as $user) {

				$user = (object) $user;

				$emailcontent = $this->load->view('email/generic',
					checkAccuracyContent($user->firstname, $user), true);

				$this->addEmailToQueue(
					$queuedEmail,
					$user->measureId,
					$this->CHECK_ACCURACY,
					$time,
					'measureId',
					$emailcontent,
					$this->sendMandrillEmail(
						'Let’s check your watch accuracy! ⌚',
						$emailcontent,
						$user->name.' '.$user->firstname,
						$user->email,
						'check_accuracy_email',
						$this->sendAtString($time)
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
	private function checkAccuracyOneWeek($time, &$queuedEmail) {
		$measureWithoutAccuracy = $this
			->measure
			->select('measure.id as measureId, watch.*, user.userId,
			measure.*, watch.name as watchName, user.name, user.firstname, email')
			->join('watch', 'watch.watchId = measure.watchId')
			->join('user', 'watch.userId = user.userId')
			->where('statusId', 1)
			->where('measureReferenceTime <=', $time-($this->day*7))
			->where($this->whereNotAlreadySentMeasure($this->CHECK_ACCURACY_1_WEEK), 0, false)
			->as_array()
			->find_all();

		if ($measureWithoutAccuracy !== FALSE) {

			$this->__->groupBy($measureWithoutAccuracy, 'email');

			foreach ($measureWithoutAccuracy as $user) {

				$user = (object) $user;

				$emailcontent = $this->load->view('email/generic',
					oneWeekAccuracyContent($user->firstname, $user), true);

				$this->addEmailToQueue(
					$queuedEmail,
					$user->measureId,
					$this->CHECK_ACCURACY_1_WEEK,
					$time,
					'measureId',
					$emailcontent,
					$this->sendMandrillEmail(
						'Let’s check your watch accuracy! ⌚',
						$emailcontent,
						$user->name.' '.$user->firstname,
						$user->email,
						'check_accuracy_email',
						$this->sendAtString($time)
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
	private function startANewMeasure($time, &$queuedEmail) {
		$userWithWatchWithoutMeasure = $this
			->measure
			->select('watch.watchId, watch.name as watchName, watch.brand,
			user.userId, user.name, user.firstname, email, measure.*')
			->join('watch', 'watch.watchId = measure.watchId')
			->join('user', 'watch.userId = user.userId')
			->where('statusId', 2)
			->where('accuracyReferenceTime <=', $time-($this->day*30))
			->where($this->whereNotAlreadySentWatch($this->START_NEW_MEASURE), 0, false)
			->as_array()
			->find_all();

		if ($userWithWatchWithoutMeasure !== FALSE) {

			$this->__->groupBy($userWithWatchWithoutMeasure, 'email');

			foreach ($userWithWatchWithoutMeasure as $user) {

				$user = (object) $user;

				$emailcontent = 	$this->load->view('email/generic',
						oneMonthAccuracyContent($user->firstname, $user), true);

				$this->sendMandrillEmail(
					'Let’s check your watch accuracy! ⌚',
					$emailcontent,
					$user->name.' '.$user->firstname,
					$user->email,
					'check_accuracy_email',
					$this->sendAtString($time)
				);

				$this->addEmailToQueue(
					$queuedEmail,
					$user->watchId,
					$this->START_NEW_MEASURE,
					$time,
					'watchId',
					$emailcontent,
					$this->sendMandrillEmail(
						'Let’s check your watch accuracy! ⌚',
						$emailcontent,
						$user->name.' '.$user->firstname,
						$user->email,
						'check_accuracy_email',
						$this->sendAtString($time)
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

		$this->mcapi->listSubscribe('7f94c4aa71', $user->email, '');

		return $this->sendMandrillEmail(
			'Welcome to Toolwatch! ⌚',
			$this->load->view('email/signup', '', true),
			$user->name.' '.$user->firstname,
			$user->email,
			'signup',
			$this->sendAtString(time())
		);
	}

	/**
	 * Send a password token for reset $user
	 *
	 * @param User $user
	 * @param String $token
	 */
	private function resetPassword($user, $token) {
		return $this->sendMandrillEmail(
			'Your Toolwatch password ⌚',
			$this->load->view('email/reset-password', array('resetToken'=>$token), true),
			$user->name.' '.$user->firstname,
			$user->email,
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

		$watch = $this->watch->find($measure->watchId);
		$data['watch'] = $watch;

		$user = $this->user->getUserFromWatchId($watch->watchId);
		$data['user']  = $user;

		$attachments = array();
		$description = "Check the accuracy of my ".$watch->brand.' '.$watch->name;
		$this->load->helper('ics');
		$in30days = time() + 30*24*60*60;

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
