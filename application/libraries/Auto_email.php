<?php

defined('BASEPATH') OR exit('No direct script access allowed');

// include manually module library - SendInBlue API
require_once (APPPATH . '../vendor/autoload.php');

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
	private $timeOffset 	= 0;
	private $time			= 0;
	private $lastBatchDate  = 0;
	private $emailBatchModel;

	// Send In Blue template ids
	private const SIB_BLUMSAFE = 1; // - sent - to be adjusted
	private const SIB_ADD_WATCH_SEIKO = 2; // -sent - ok
	private const SIB_ADD_WATCH_OMEGA = 3; // -sent - ok
	private const SIB_ADD_WATCH_ROLEX = 4; // -sent - ok
	private const SIB_ADD_WATCH_JLC = 5; // -sent - ok
	private const SIB_WATCH_RESULT = 7; // -sent - ok
	private const SIB_ADD_FIRST_WATCH = 14; // -sent - ok
	private const SIB_ADD_SECOND_WATCH = 13; // -sent - ok
	private const SIB_COMEBACK = 12; // -sent - ok
	private const SIB_SIGNUP = 9;
	private const SIB_RESET_PASSWORD = 15; // -sent - ok
	private const SIB_MAKE_FIST_MEASURE = 17; // -sent -ok
	private const SIB_CHECK_ACCURACY = 18; // -sent -ok
	private const SIB_ONE_WEEK_ACCURACY = 19; // -sent -ok
	private const SIB_ONE_MONTH_ACCURACY = 20; // -sent -ok
	private const SIB_RESET_PASSWORD_CONFIRMATION = 16; // -sent -ok

	/**
	 * Load model, library and helpers
	 */
	function __construct() {

		$this->CI =& get_instance();

		$this->CI->load->library("__");
		$this->CI->load->model("watch");
		$this->CI->load->model("measure");
		$this->CI->load->model("user");
		$this->CI->load->model("emailpreferences");
		$this->CI->load->helper("alphaid");
		$this->CI->config->load('config');

		$sendInBlueConfig = SendinBlue\Client\Configuration::getDefaultConfiguration()->setApiKey('api-key', getenv('SIB_API_KEY'));

		$this->sendInBlueEmailsAPI = new SendinBlue\Client\Api\TransactionalEmailsApi(
			new GuzzleHttp\Client(),
			$sendInBlueConfig
		);
		$this->sendInBlueContactsAPI = new SendinBlue\Client\Api\ContactsApi(
			new GuzzleHttp\Client(),
			$sendInBlueConfig
		);
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
			case "RESET_PASSWORD_USE":
				return $this->resetPasswordUse($data['email']);
				break;
			case "ADD_WATCH":
				return $this->newWatch($data);
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
		$this->activeUser = new MY_MODEL('active_user');

		$this->lastBatchDate = $this->findLastBatchDate();

		$emailBatchId = $this->emailBatchModel->insert(
			array("time"=>$this->time,
			"amount" => -1
			)
		);

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

		if((ENVIRONMENT === "development" || ENVIRONMENT === "testing"))
		{
			$date = new DateTime("@".$this->time);
			// @codeCoverageIgnoreStart
			if(defined('PHPUNIT_TESTSUITE') == false){
				echo "<h1> Emails sent at " . $date->format('Y-m-d H:i:s') . "</h1>";
			}
			// @codeCoverageIgnoreEnd

			$this->showSentEmails($emailsUserSent, "User emails");
			$this->showSentEmails($emailsWatchSent, "Watch emails");
			$this->showSentEmails($emailsMeasureSent, "Measure emails");
		}


		$this->emailBatchModel->update($emailBatchId,
			["amount"=>	sizeof($emailsMeasureSent)
					+ sizeof($emailsWatchSent)
					+ sizeof($emailsMeasureSent)
			]
		);

		return array(
			'users'    => $emailsUserSent,
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

		return (int) $this->emailBatchModel
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
	 * @codeCoverageIgnore
	 */
	private function showSentEmails($emails, $title){

		if(defined('PHPUNIT_TESTSUITE') == false){
			echo "<h2> ".$title." </h2>";
			foreach ($emails as $email) {

				if(isset($email['userId'])){
					echo 'TO ' . $this->CI->user->find_by('userId', $email['userId'])->email;
				}

				echo '<br/>'; var_dump($email['sib']); echo '<br/>';
			}
		}
	}

	/**
	 * Helper method to convert a timestamp in a Mandrill
	 * valid string
	 *
	 * @param  Long $scheduleTime timestamp
	 * @return String A Mandrill valide data as String
	 */
	private function sendAtString($scheduleTime) {

		$scheduleTime = $scheduleTime - $this->timeOffset;

		return date("c", strtotime(gmdate('r', $scheduleTime)));
	}

	/**
	 * Add a computed email to a $queue.
	 *
	 * @param Array $queue Received by reference and will be updated
	 * @param int $userId
	 * @param int $emailType
	 * @param long $time
	 * @param int $idTitle
	 * @param array $sibResponse
	 */
	private function addEmailToQueue(&$queue, $userId, $emailType, $time,
		$idTitle,  $sibResponse) {
		array_push($queue,
			array(
				$idTitle    => $userId,
				'sentTime'  => $time,
				'emailType' => $emailType,
				'sib'  => $sibResponse
			)
		);
	}

	/**
	 * Computes the upperBound for a given $timeCondition
	 *
	 * @param  Long $timeCondition seconds to go back
	 * @return Long an upper bound depending on $timeCondition and
	 * $this->time;
	 */
	private function getBatchUpperBound($timeCondition){

		$upperBound = $this->time-$timeCondition;
		$date = new DateTime("@" . $upperBound);

		log_message('info', 'Upper Bound (' .$upperBound. ') '
			. $date->format('Y-m-d H:i:s')
		);

		return $upperBound;
	}

	/**
	 * Computes the lower bound for a give $timeCondition
	 *
	 * @param  Long $timeCondition A time condition to go back to
	 * @return Long A lower bound depending on $timeCondition, $this->time
	 * and $this->lastBatchDate
	 */
	private function getBatchLowerBound($timeCondition){

		$lowerBound = $this->time-$timeCondition-($this->time-$this->lastBatchDate);
		$date = new DateTime("@" . $lowerBound);
		$last = new DateTime("@" . $this->lastBatchDate);

		log_message('info', 'Lower Bound (' .$lowerBound. ') '
			. $date->format('Y-m-d H:i:s') . ' ' . $last->format('Y-m-d H:i:s')
		);

		return $lowerBound;
	}

	/**
	 * Send email to incative user
	 *
	 * @param  long $time Time at which compute the rule
	 * @param  array $queuedEmail queue to store the computed email
	 */
	private function inactiveUser(&$queuedEmail) {

		log_message('info', 'inactiveUser');

		$inactiveUsers = $this->activeUser
			->select()
			->join('email_preference', 'active_user.userId = email_preference.userId AND email_preference.comeback = 1')
			->where('lastLogin <', $this->getBatchUpperBound($this->day*100))
			->where('lastLogin >', $this->getBatchLowerBound($this->day*100))
			->find_all();

		if ($inactiveUsers !== FALSE) {
			foreach ($inactiveUsers as $user) {

				$this->addEmailToQueue(
					$queuedEmail,
					$user->userId,
					$this->COMEBACK,
					$this->time,
					'userId',
					$this->sendSIBEmail(
						'We haven\'t seen you for a while ? ⌚',
						array(
							"email" =>$user->email,
							'unsub' => $this->unsub(alphaID($user->userId)),
							'firstname' => $user->firstname,
						),
						$user->name.' '.$user->firstname,
						$user->email,
						'comeback_100d',
						self::SIB_COMEBACK
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

		log_message('info', 'userWithoutWatch');


		$userWithoutWatch = $this->activeUser
			->select('active_user.userId, name, firstname, email, lastLogin')
			->join('email_preference', 'active_user.userId = email_preference.userId AND email_preference.firstWatch = 1')
			->where('watches', 0)
			->where('lastLogin <', $this->getBatchUpperBound($this->day))
			->where('lastLogin >', $this->getBatchLowerBound($this->day))
			->find_all();

		if ($userWithoutWatch !== FALSE) {
			foreach ($userWithoutWatch as $user) {

				$this->addEmailToQueue(
					$queuedEmail,
					$user->userId,
					$this->ADD_FIRST_WATCH,
					$this->time,
					'userId',
					$this->sendSIBEmail(
						'Let’s add a watch and start measuring! ⌚',
						array(
							"email" =>$user->email,
							'unsub' => $this->unsub(alphaID($user->userId)),
							'firstname' => $user->firstname,
						),
						$user->name.' '.$user->firstname,
						$user->email,
						'add_first_watch_email',
						self::SIB_ADD_FIRST_WATCH
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

		log_message('info', 'userWithWatchWithoutMeasure');

		$userWithWatchWithoutMeasure = $this->CI
			->watch
			->select('active_user.userId, watch.watchId, watch.brand, watch.name as watchName,
			active_user.name as lastname, firstname, email')
			->join('active_user', 'watch.userId = active_user.userId')
			->join('email_preference', 'active_user.userId = email_preference.userId AND email_preference.firstMeasure = 1')
			->where('(select count(1) from measure where watch.watchId = measure.watchId) = ', 0)
			->where('watch.status', 1)
			->where('creationDate < ', $this->getBatchUpperBound($this->day))
			->where('creationDate > ', $this->getBatchLowerBound($this->day))
			->as_array()
			->find_all();

		if ($userWithWatchWithoutMeasure !== FALSE) {

			$userWithWatchWithoutMeasure = $this->CI->__->groupBy($userWithWatchWithoutMeasure, 'email');

			foreach ($userWithWatchWithoutMeasure as $user) {

				$this->addEmailToQueue(
					$queuedEmail,
					$user[0]['watchId'],
					$this->START_FIRST_MEASURE,
					$this->time,
					'watchId',
					$this->sendSIBEmail(
						'Let’s start measuring! ⌚',
						array(
							"email" =>$user[0]['email'],
							'unsub' => $this->unsub(alphaID($user[0]["userId"])),
							'firstname' => $user[0]['firstname'],
							'watches' => $this->constructContentWatches($user),
						),
						$user[0]['lastname'].' '.$user[0]['firstname'],
						$user[0]['email'],
						'make_first_measure_email',
						self::SIB_MAKE_FIST_MEASURE
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

		log_message('info', 'userWithOneCompleteMeasureAndOneWatch');

		$userWithOneCompleteMeasureAndOneWatch = $this->activeUser
			->select('active_user.userId, active_user.name, firstname, email')
			->join('email_preference', 'active_user.userId = email_preference.userId AND email_preference.secondWatch = 1')
			->where('watches', 1)
			->where('(select count(1) from measure
					join watch on measure.watchId = watch.watchId
					where active_user.userId = watch.userId
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

				$this->addEmailToQueue(
					$queuedEmail,
					$user->userId,
					$this->ADD_SECOND_WATCH,
					$this->time,
					'userId',
					$this->sendSIBEmail(
						'Add another watch ? ⌚',
						array(
							"email" =>$user->email,
							'unsub' => $this->unsub(alphaID($user->userId)),
							'firstname' => $user->firstname,
							'firstwatch' => $watch->brand . " " . $watch->name
						),
						$user->name.' '.$user->firstname,
						$user->email,
						'add_another_watch_email',
						self::SIB_ADD_SECOND_WATCH
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

		log_message('info', 'checkAccuracy');

		$measureWithoutAccuracy = $this->CI
			->measure
			->select('measure.id as measureId, measure.*, watch.*,
								watch.name as watchName, active_user.userId, active_user.name as lastname,
								active_user.firstname, email')
			->join('watch', 'watch.watchId = measure.watchId')
			->join('active_user', 'watch.userId = active_user.userId')
			->join('email_preference', 'active_user.userId = email_preference.userId AND email_preference.dayAccuracy = 1')
			->where('watch.status', 1)
			->where('statusId', 1)
			->where('measureReferenceTime <', $this->getBatchUpperBound($this->day))
			->where('measureReferenceTime >', $this->getBatchLowerBound($this->day))
			->as_array()
			->find_all();

		if ($measureWithoutAccuracy !== FALSE) {

			$measureWithoutAccuracy = $this->CI->__->groupBy($measureWithoutAccuracy, 'email');

			foreach ($measureWithoutAccuracy as $user) {

				$this->addEmailToQueue(
					$queuedEmail,
					$user[0]['measureId'],
					$this->CHECK_ACCURACY,
					$this->time,
					'measureId',
					$this->sendSIBEmail(
						'Let’s check your watch accuracy! ⌚',
						array(
							"email" =>$user[0]['email'],
							'unsub' => $this->unsub(alphaID($user[0]["userId"])),
							'firstname' => $user[0]['firstname'],
							'watches' => $this->constructContentWatches($user),
						),
						$user[0]['lastname'].' '.$user[0]['firstname'],
						$user[0]['email'],
						'check_accuracy_email',
						self::SIB_CHECK_ACCURACY,
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

		log_message('info', 'checkAccuracyOneWeek');

		$measureWithoutAccuracy = $this->CI
			->measure
			->select('measure.id as measureId, watch.*, active_user.userId,
			measure.*, watch.name as watchName, active_user.name as lastname, active_user.firstname, email')
			->join('watch', 'watch.watchId = measure.watchId')
			->join('active_user', 'watch.userId = active_user.userId')
			->join('email_preference', 'active_user.userId = email_preference.userId AND email_preference.weekAccuracy = 1')
			->where('statusId', 1)
			->where('watch.status', 1)
			->where('measureReferenceTime <', $this->getBatchUpperBound($this->day*7))
			->where('measureReferenceTime >', $this->getBatchLowerBound($this->day*7))
			->as_array()
			->find_all();

		if ($measureWithoutAccuracy !== FALSE) {

			$measureWithoutAccuracy = $this->CI->__->groupBy($measureWithoutAccuracy, 'email');

			foreach ($measureWithoutAccuracy as $user) {

				$this->addEmailToQueue(
					$queuedEmail,
					$user[0]['measureId'],
					$this->CHECK_ACCURACY_1_WEEK,
					$this->time,
					'measureId',
					$this->sendSIBEmail(
						'Let’s check your watch accuracy! ⌚',
						array(
							"email" =>$user[0]['email'],
							'unsub' => $this->unsub(alphaID($user[0]["userId"])),
							'firstname' => $user[0]['firstname'],
							'watches' => $this->constructContentWatches($user),
						),
						$user[0]['lastname'].' '.$user[0]['firstname'],
						$user[0]['email'],
						'check_accuracy_email',
						self::SIB_ONE_WEEK_ACCURACY
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

		log_message('info', 'startANewMeasure');

		$watchesInNeedOfNewMeasure = $this->CI
			->measure
			->select('watch.watchId, watch.name as watchName, watch.brand,
			active_user.userId, active_user.name as lastname, active_user.firstname, email, measure.*')
			->join('watch', 'watch.watchId = measure.watchId')
			->join('active_user', 'watch.userId = active_user.userId')
			->join('email_preference', 'active_user.userId = email_preference.userId AND email_preference.newMeasure = 1')
			->where('watch.status', 1)
			->where('statusId', 2)
			->where('accuracyReferenceTime <', $this->getBatchUpperBound($this->day*30))
			->where('accuracyReferenceTime >', $this->getBatchLowerBound($this->day*30))
			->as_array()
			->find_all();

		if ($watchesInNeedOfNewMeasure !== FALSE) {

			$watchesInNeedOfNewMeasure =
				$this->CI->__->groupBy($watchesInNeedOfNewMeasure, 'email');

			foreach ($watchesInNeedOfNewMeasure as $user) {

				$this->addEmailToQueue(
					$queuedEmail,
					$user[0]['watchId'],
					$this->START_NEW_MEASURE,
					$this->time,
					'watchId',
					$this->sendSIBEmail(
						'Let’s start a new measure! ⌚',
						array(
							"email" =>$user[0]['email'],
							'unsub' => $this->unsub(alphaID($user[0]["userId"])),
							'firstname' => $user[0]['firstname'],
							'watches' => $this->constructContentWatches($user),
						),
						$user[0]['lastname'].' '.$user[0]['firstname'],
						$user[0]['email'],
						'start_new_measure_email',
						self::SIB_ONE_MONTH_ACCURACY
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

		$this->updateSIBContact($user);

		return $this->sendSIBEmail(
			'Welcome to Toolwatch! ⌚',
			array(
				"email" =>$user->email,
				'unsub' => $this->unsub(alphaID($user->userId)),
				'firstname' => $user->firstname
			),
			$user->name.' '.$user->firstname,
			$user->email,
			'signup',
			self::SIB_SIGNUP
		);
	}

	public function updateSIBContact($user, $subscribe = true) {
		$this->sendInBlueContactsAPI->createContact(new \SendinBlue\Client\Model\CreateContact([
			'email' => $user->email,
			'updateEnabled' => true,
			'attributes' => array(
				'FIRSTNAME' => $user->firstname, 'PRENOM' =>  $user->firstname,
				'LASTNAME' => $user->name, 'NOM' => $user->name,
				'DATE ADDED' => $this->sendAtString(time()),
				'SUBSCRIBED' => ($subscribe == '0') ? false : true,
				'BLOCKLISTED' => ($subscribe == '0') ? true : false,
				'PLATFORMNEWS' => ($subscribe == '0') ? false : true
			),
			'listIds' => [3]
	   	]));
	}

	/**
	 * Send a password token for reset $user
	 *
	 * @param $email $email
	 * @param String $token
	 */
	private function resetPassword($email, $token) {

		$result = $this->sendSIBEmail(
			'Your Toolwatch password ⌚',
			array(
				"reset" =>$token,
				"email" =>$email
			),
			"",
			$email,
			'reset_password',
			self::SIB_RESET_PASSWORD,
		);

		return $result;
	}

	private function sendSIBEmail($subject, $params, $recipientName,
	$recipientEmail, $tag, $templateId, $scheduledAt = null, $attachments = null) {

		if ($recipientName == "") {
			$recipientName = "watch friend";
		}

		$sendSmtpEmail = new \SendinBlue\Client\Model\SendSmtpEmail([
			'subject' => $subject,
			'sender' => ['name' => 'Toolwatch', 'email' => 'hello@toolwatch.io'],
			'replyTo' => ['name' => 'Toolwatch', 'email' => 'hello@toolwatch.io'],
			'to' => [[ 'name' => $recipientName, 'email' => $recipientEmail]],
			'params' => $params,
			'templateId' => $templateId
	   	]);

		if ($scheduledAt !== null) {
			$sendSmtpEmail->setScheduledAt($scheduledAt);
		}

		try {
			$result = $this->sendInBlueEmailsAPI->sendTransacEmail($sendSmtpEmail);

			error_log(json_encode([
				'result' => $result,
				'subject' => $subject,
				'sender' => ['name' => 'Toolwatch', 'email' => 'hello@toolwatch.io'],
				'replyTo' => ['name' => 'Toolwatch', 'email' => 'hello@toolwatch.io'],
				'to' => [[ 'name' => $recipientName, 'email' => $recipientEmail]],
				'params' => $params,
				'scheduledAt' => $scheduledAt,
				'templateId' => $templateId
		   ]));

		   return $result;

		} catch (Exception $e) {
			echo $e->getMessage(), print_r(
				[
					'subject' => $subject,
					'sender' => ['name' => 'Toolwatch', 'email' => 'hello@toolwatch.io'],
					'replyTo' => ['name' => 'Toolwatch', 'email' => 'hello@toolwatch.io'],
					'to' => [[ 'name' => $recipientName, 'email' => $recipientEmail]],
					'params' => $params,
					'scheduledAt' => $scheduledAt,
					'templateId' => $templateId
			   ]
			), PHP_EOL;
		}
		
	}

	/**
	 * Send a password changed confirmation
	 *
	 * @param $email $email
	 * @param String $token
	 */
	private function resetPasswordUse($email) {
		return $this->sendSIBEmail(
			'Your Toolwatch password has been changed ⌚',
			array(
				"email" => $email
			),
			'',
			$email,
			'reset_password_confirmation',
			self::SIB_RESET_PASSWORD_CONFIRMATION,
		);
	}

	
	private function newWatch($watch){

		$supportedBrands = array("omega", "rolex", "jaeger-lecoultre", "seiko");
		$supportedBrandsSubject = array(
			"omega" => array("add_watch_omega", "Omegafan too?", self::SIB_ADD_WATCH_OMEGA),
			"rolex" => array("add_watch_rolex", "My guess is that you are a Rolexophile too!", self::SIB_ADD_WATCH_ROLEX),
			"jaeger-lecoultre" => array("add_watch_jlc", "So you like the Grande Maison too?", self::SIB_ADD_WATCH_JLC),
			"seiko"=> array("add_watch_seiko", "Everyone loves Seiko!", self::SIB_ADD_WATCH_SEIKO)
		);

		$this->brand = strtolower($watch->brand);
		$user = $this->CI->user->getUser($watch->userId);
		$bloomSafe = false;

		if($this->CI->watch->count_by("watch.userId", $watch->userId) === 3
			&& $user !== false){
			
			$bloomSafe = $this->sendSIBEmail(
				'You are serious about watches ⌚',
				array(
					"firstname" => $user->firstname,
					'email' => $user->email,
					'unsub' => $this->unsub(alphaID($user->userId))
				),
				$user->firstname,
				$user->email,
				'blumsafe',
				self::SIB_BLUMSAFE,
			);
		}

		//The added watch is one of the watch that have a custom email
		if(in_array($this->brand, $supportedBrands)){

			//Get all the watches that match on of the brand in supportedBrands
			$watches = $this->CI->watch->select("watch.*, user.email, user.firstname, user.name")
			->join("user", "user.userId = watch.userId")
			->where("watch.userId", $watch->userId)
			->where("watch.watchId <>", $watch->watchId)
			->where_in("LOWER(watch.brand)", $supportedBrands)
			->order_by("creationDate", "desc")
			->as_array()
			->find_all();

			if(
				$watches === false
				|| 
				(
					//If the request went fine 
					is_array($watches) 
					&& 
					//This is the first time we have this brand
					$this->CI->__->find($watches, 
						function($watch){
							return strtolower($watch["brand"]) == $this->brand;
						}
					) == null
				)
			){

				if($watches === false){
					$watches = array(
						$this->CI->user->select()
							->as_array()
							->find($watch->userId)
						);
				}
				
				// Add 30 min
				$time = time() + 30*60;

				//A supported watch was created less than one hour ago,
				//schedule the mail to be sent later
				//or we sent a bloomsafe ad
				if(
					(is_array($watches) && 
					sizeof($watches) >= 1 && 
					array_key_exists("creationDate", $watches[0]) &&
					time() - $watches[0]["creationDate"] < 3600) || 
					$bloomSafe !== false)
				{
					$time = $time + 3600;
				}

				return $this->sendSIBEmail(
					$supportedBrandsSubject[$this->brand][1],
					array(
						"firstname" => $watches[0]["firstname"],
						'email' => $user->email,
						'unsub' => $this->unsub(alphaID($user->userId))
					),
					$watches[0]["firstname"],
					$watches[0]["email"],
					$supportedBrandsSubject[$this->brand][0],
					$supportedBrandsSubject[$this->brand][2],
					$this->sendAtString($time)
				);

			}
		}

		return false;
	}

	/**
	 * Send an email with the result of a measure
	 *
	 * @param  Measure $measure
	 */
	private function newResult($measure) {

		if($this->CI->emailpreferences->select('result')->find_by("userId", $measure->userId)->result == 1){
			
			$this->sendSIBEmail(
				'The result of your watch\'s accuracy ! ⌚',
				array(
					'fistname' => $measure->firstname,
					'watch' => $measure->brand . ' ' . $measure->model,
					'accuracy' => $measure->accuracy,
					'watches'=> $this->constructDashboardWatches($this->CI->measure->getMeasuresByUser($measure->userId)),
					'email' => $measure->email,
					'unsub' => $this->unsub(alphaID($measure->userId))
				),
				$measure->firstname,
				$measure->email,
				'result_email',
				self::SIB_WATCH_RESULT,
			);
		}
	}

	private function unsub($alphaId) {
		return base_url() . 'Unsubscribe/index/'.$alphaId;
	}

	private function constructDashboardWatches($watches){

		$emailWatches = array();
	  
		if($watches && is_array($watches)){
	  
		  foreach ($watches as $watch) {
			$watch = (object) $watch;
			
			if($watch->statusId === 1.5){
	  
			  array_push($emailWatches, (object) array(
				'name' => $watch->brand.' '.$watch->name,
				'action' => 'Check accuracy in '.$watch->accuracy.' hours',
				'link' => base_url().'/measures'
			  ));
	  
			}else if($watch->statusId == 1){
	  
			  array_push($emailWatches, (object) array(
				'name' => $watch->brand.' '.$watch->name,
				'action' => 'Check accuracy now',
				'link' => base_url().'/measures'
			  ));
	  
			}else if($watch->statusId == null){
	  
			  array_push($emailWatches, (object) array(
				'name' => $watch->brand.' '.$watch->name,
				'action' => 'Measure now',
				'link' => base_url().'/measures'
			  ));
	  
			}else{
	  
			  array_push($emailWatches, (object) array(
				'name' => $watch->brand.' '.$watch->name,
				'action' => 'Runs at ' . $watch->accuracy . ' spd (' . (($watch->accuracyAge == 0) ? 'today).' : $watch->accuracyAge  . ' day(s) ago).'),
				'link' => base_url().'/measures'
			  ));
			}
		  }
		}
	  
		return $emailWatches;
	  }
	  
	  private function constructContentWatches($watches){
	  
		$emailWatches = array();
	  
		foreach ($watches as $watch) {
			$watch = (object) $watch;
			array_push($emailWatches, (object) array(
				'name' => $watch->brand.' '.$watch->watchName,
			));
		}
		
		return $emailWatches;
	  }
}