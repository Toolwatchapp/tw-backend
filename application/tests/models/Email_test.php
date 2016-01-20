<?php
/**
 * user,action,time
 nestor,signup,01 octobre 2015 10:00:00
 nestor,lost password,02 octobre 2015 11:00:00
 ernest,signup, 03 octobre 2015 11:30:00
 anatole,signup, 04 octobre 2015 04:32:00
 phillibert,signup, 05 octobre 2015 03:11:00
 phillibert,add watch, 05 octobre 2015 03:14:00
 phillibert,add m1, 05 octobre 2015 03:16:00
 phillibert,add m2, 07 octobre 2015 02:12:00
 hippolyte,signup, 08 octobre 2015 01:09:00
 hippolyte,add watch, 08 octobre 2015 01:14:00
 hippolyte,add m1, 08 octobre 2015 01:18:00
 hippolyte,add m2, 10 octobre 2015 03:12:00
 raymond,signup, 09 octobre 2015 02:09:00
 raymond,add watch, 09 octobre 2015 02:14:00
 raymond,add m1, 09 octobre 2015 02:18:00
 raymond,add m2, 17 octobre 2015 04:12:00

 Emails envoy�s (user,email,time):
 nestor,signup,01 octobre 2015 10:01:00
 nestor,reset-password, 02 octobre 2015 11:01:00
 ernest,comeback_100d, 12 janvier 2016 11:45:00
 anatole,add_first_watch, 05 octobre 2015 08:40:00
 phillibert,check_accuracy, 06 octobre 2015 09:20:00
 phillibert,result_email, 07 octobre 2015 04:15:00
 phillibert,start_new_measure, 08 novembre 2015 03:03:00
 hippolyte,add_another_watch, 13 octobre 2015 02:08:00
 raymond,check_accuracy, 10 octobre 2015 10:20:00
 raymond,check_accuracy, 17 octobre 2015 03:18:00
 */
class Email_test extends TestCase {

	public static $users;
	public static $measure;
	public static $watch;
	public static $watch2Id;
	public static $baseMeasureId;
	public static $watchId;

	public static function setUpBeforeClass() {
		$CI = &get_instance();
		$CI->load->model('Email');
		$CI->load->model('Watch');
		$CI->load->model('User');
		$CI->load->model('measure');


		$CI->emailWatch   = new MY_Model('email_watch');
		$CI->emailMeasure = new MY_Model('email_measure');
		$CI->emailUser   = new MY_Model('email_user');

		$CI->emailUser->delete_where(array("id >=" => "0"));
		$CI->emailWatch->delete_where(array("id >=" => "0"));
		$CI->emailMeasure->delete_where(array("id >=" => "0"));

		$CI->measure->delete_where(array("id >="      => "0"));
		$CI->Watch->delete_where(array("watchId >="   => "0"));
		$CI->User->delete_where(array("userId >="     => "0"));

		$data = array(
			'email'        => 'nestor@nestor.com',
			'password'     => hash('sha256', 'azerty'),
			'name'         => 'math',
			'firstname'    => 'nay',
			'timezone'     => -5,
			'country'      => 'Canada',
			'registerDate' => time(),
			'lastLogin'    => time()
		);
		$CI->User->insert($data);

		$data['email'] = 'ernest@ernest.com';
		$CI->User->insert($data);

		$data['email'] = 'anatole@anatole.com';
		$CI->User->insert($data);

		$data['email'] = 'phillibert@phillibert.com';
		$CI->User->insert($data);

		$data['email'] = 'hippolyte@hippolyte.com';
		$CI->User->insert($data);

		$data['email'] = 'raymond@raymond.com';
		$CI->User->insert($data);

		$tmp = $CI->User->select()->find_all();


		self::$users['nestor'] = $tmp[0];
		self::$users['ernest'] = $tmp[1];
		self::$users['anatole'] = $tmp[2];
		self::$users['phillibert'] = $tmp[3];
		self::$users['hippolyte'] = $tmp[4];
		self::$users['raymond'] = $tmp[5];
	}

	public function setUp() {
		$CI = &get_instance();

		$mcapi = $this->getMockBuilder('Mandrill_Messages')
														->disableOriginalConstructor()
														->getMock();

		$mcapi->method('listSubscribe')->willReturn(true);

		$mandrillMessage = $this->getMockBuilder('Mandrill_Messages')
		                        ->disableOriginalConstructor()
		                        ->getMock();

		$returnSend = Array
		(
			Array
			(
				'email'         => 'recipient.email@example.com',
				'status'        => 'sent',
				'reject_reason' => 'hard-bounce',
				'_id'           => 'abc123abc123abc123abc123abc123',
			)

		);

		$mandrillMessage->method('send')->willReturn($returnSend);

		$returnReschedule = Array
		(
			'_id'        => 'I_dtFt2ZNPW5QD9-FaDU1A',
			'created_at' => '2013-01-20 12:13:01',
			'send_at'    => '2021-01-05 12:42:01',
			'from_email' => 'sender@example.com',
			'to'         => 'test.recipient@example.com',
			'subject'    => 'This is a scheduled email',
		);

		$mandrillMessage->method('reschedule')->willReturn($returnReschedule);
		$mandrillMessage->method('cancelScheduled')->willReturn($returnSend);

		$CI->load->model('Email');
		$CI->load->model('Watch');
		$CI->load->model('Measure');

		$CI->Email->mandrill->messages = $mandrillMessage;

		$this->email      = $CI->Email;
		$this->watchModel = $CI->Watch;
		$this->measureModel = $CI->Measure;
	}

	public function test_mock() {

		$result = $this->email->mandrill->messages->send(null);

		$this->assertEquals(
			'abc123abc123abc123abc123abc123',
			$result[0]['_id']
		);
	}

	/**
	 * Nestor SIGN_UP. Mocked id should be resturned
	 * @return pass|fail
	 */
	public function test_signup() {

		$this->assertEquals(
			'abc123abc123abc123abc123abc123',
			$this->email->updateObserver(
				'TEST',
				SIGN_UP,
				self::$users['nestor'])[0]['_id']
		);
	}

	/**
	 * Nestor password reset. Mocked id should be resturned
	 * @return pass|fail
	 */
	public function test_lostPassword(){

		$this->assertEquals(
			'abc123abc123abc123abc123abc123',
			$this->email->updateObserver(
				'TEST',
				RESET_PASSWORD,
				array('user' => self::$users['nestor'],
				'token' => 'plop' ))[0]['_id']
		);
	}

  /**
   * Nestor adds a watch and a baseMeasure.
   * 24h later, he have reminder for the accuracyMeasure.
   * ernest, anatole, phillibert, hippolyte and raymond have a reminder
   * to add a watch.
   * @return pass|fail
   */
 public function test_AccuracyAndAddFirstWatch(){

 	self::$watchId = $this->watchModel->addWatch(
 		self::$users['nestor']->userId,
 		'rolex',
 		'marolex',
 		'2000',
 		'0000-0000',
 		'caliber'
 	);

 	self::$baseMeasureId = $this->measureModel->addBaseMesure(self::$watchId, time(), time());

 	//1 day later
 	// Should have 5 add first and 1 check
 	$emails = $this->email->cronCheck(24*60*60);

	$addWatchContent = file_get_contents("emails/add_watch.html",
		FILE_USE_INCLUDE_PATH);
	$checkAccuracyContent = file_get_contents("emails/check_accuracy.html",
		FILE_USE_INCLUDE_PATH);

 	$this->assertEquals(sizeof($emails['users']), 5);

 	$this->assertEquals($emails['users'][0]['userId'], self::$users['ernest']->userId);
 	$this->assertEquals($emails['users'][0]['emailType'], $this->email->ADD_FIRST_WATCH);
	$this->assertEquals($emails['users'][0]['content'], $addWatchContent);

 	$this->assertEquals($emails['users'][1]['userId'], self::$users['anatole']->userId);
 	$this->assertEquals($emails['users'][1]['emailType'], $this->email->ADD_FIRST_WATCH);
	$this->assertEquals($emails['users'][1]['content'], $addWatchContent);

 	$this->assertEquals($emails['users'][2]['userId'], self::$users['phillibert']->userId);
 	$this->assertEquals($emails['users'][2]['emailType'], $this->email->ADD_FIRST_WATCH);
	$this->assertEquals($emails['users'][2]['content'], $addWatchContent);

 	$this->assertEquals($emails['users'][3]['userId'], self::$users['hippolyte']->userId);
 	$this->assertEquals($emails['users'][3]['emailType'], $this->email->ADD_FIRST_WATCH);
	$this->assertEquals($emails['users'][3]['content'], $addWatchContent);

 	$this->assertEquals($emails['users'][4]['userId'], self::$users['raymond']->userId);
 	$this->assertEquals($emails['users'][4]['emailType'], $this->email->ADD_FIRST_WATCH);
	$this->assertEquals($emails['users'][4]['content'], $addWatchContent);

 	$this->assertEquals(sizeof($emails['watches']), 0);

 	$this->assertEquals(sizeof($emails['measures']), 1);
 	$this->assertEquals($emails['measures'][0]['measureId'], self::$baseMeasureId);
 	$this->assertEquals($emails['measures'][0]['emailType'], $this->email->CHECK_ACCURACY);
	$this->assertEquals($emails['measures'][0]['content'], $checkAccuracyContent);

 }

 /**
  * 1 week after the baseMeasure, nestor have a CHECK_ACCURACY_1_WEEK
  * reminder
  * @return pass|fail
  */
 public function test_AccuracyOneWeek(){

 	//1 week later
 	// Should have 1 CHECK_ACCURACY_1_WEEK
 	$emails = $this->email->cronCheck(24*8*60*60);

	$checkAccuracy1wContent = file_get_contents("emails/check_accuracy_1w.html",
		FILE_USE_INCLUDE_PATH);

 	$this->assertEquals(sizeof($emails['users']), 0);
 	$this->assertEquals(sizeof($emails['watches']), 0);
 	$this->assertEquals(sizeof($emails['measures']), 1);

 	$this->assertEquals($emails['measures'][0]['measureId'], self::$baseMeasureId);
 	$this->assertEquals($emails['measures'][0]['emailType'],
 		$this->email->CHECK_ACCURACY_1_WEEK);
	$this->assertEquals($emails['measures'][0]['content'], $checkAccuracy1wContent);
 }

 /**
  * Nestor do the accuracy measure.
  * No emails to be sent
  * @return pass|fail
  */
 public function test_accuracyEmpty(){

 	self::$baseMeasureId = $this->measureModel->addAccuracyMesure(self::$baseMeasureId,
 		time()+(24*8*60*60), time()+(24*8*60*60));

 	//1 day after
 	// Should be empty
 	$emails = $this->email->cronCheck(24*9*60*60);
 	$this->assertEquals(sizeof($emails['users']), 0);
 	$this->assertEquals(sizeof($emails['watches']), 0);
 	$this->assertEquals(sizeof($emails['measures']), 0);

 }

 	/**
 	 * Nestor should be reminded once to add a second watch
 	 * two day after his first watch has been accuratly measured
 	 * @return pass|fail
 	 */
 public function test_addSecondWatch(){

 	//The accuracy measure was at time()+(24*8*60*60)
 	//2 days later
 	$emails = $this->email->cronCheck(24*10*61*60);

	$addSecondWatch = file_get_contents("emails/add_second_watch.html",
		FILE_USE_INCLUDE_PATH);

 	$this->assertEquals(sizeof($emails['users']), 1);

 	$this->assertEquals($emails['users'][0]['userId'],
 		self::$users['nestor']->userId);

 	$this->assertEquals($emails['users'][0]['emailType'],
 		$this->email->ADD_SECOND_WATCH);

	$this->assertEquals($emails['users'][0]['content'], $addSecondWatch);

 	$this->assertEquals(sizeof($emails['watches']), 0);
 	$this->assertEquals(sizeof($emails['measures']), 0);

 	//Check that the email is sent only once
 	$emails = $this->email->cronCheck(24*10*62*60);

 	$this->assertEquals(sizeof($emails['users']), 0);
 	$this->assertEquals(sizeof($emails['watches']), 0);
 	$this->assertEquals(sizeof($emails['measures']), 0);

 }

 /**
  * 30 days after the last completed measure, Nestor should have an
  * email to start a new measure.
  * @return pass|fail
  */
 public function test_startANewMeasure(){

 	//The accuracy measure was at time()+(24*8*60*60)
 	$emails = $this->email->cronCheck(24*39*60*60);

	$startNewMeasureContent = file_get_contents("emails/start_new_measure.html",
		FILE_USE_INCLUDE_PATH);

 	$this->assertEquals(sizeof($emails['watches']), 1);

 	$this->assertEquals($emails['watches'][0]['watchId'],
 		self::$watchId);

 	$this->assertEquals($emails['watches'][0]['emailType'],
 		$this->email->START_NEW_MEASURE);

	$this->assertEquals($emails['watches'][0]['content'],
		$startNewMeasureContent);

 	$this->assertEquals(sizeof($emails['users']), 0);
 	$this->assertEquals(sizeof($emails['measures']), 0);

 	//Check that the email is sent only once
 	$emails = $this->email->cronCheck(24*39*61*60);

 	$this->assertEquals(sizeof($emails['users']), 0);
 	$this->assertEquals(sizeof($emails['watches']), 0);
 	$this->assertEquals(sizeof($emails['measures']), 0);

 }

 /**
  * 100 days after the last login, a comeback email should be sent
  * @return pass|fail
  */
 public function test_comback(){
 	//Last Login was at time()
 	$emails = $this->email->cronCheck(100*25*60*60);

	$comebackContent = file_get_contents("emails/comeback.html",
		FILE_USE_INCLUDE_PATH);

 	$this->assertEquals(sizeof($emails['users']), 6);
 	$this->assertEquals(sizeof($emails['watches']), 0);
 	$this->assertEquals(sizeof($emails['measures']), 0);

 	$this->assertEquals($emails['users'][0]['userId'], self::$users['nestor']->userId);
 	$this->assertEquals($emails['users'][0]['emailType'], $this->email->COMEBACK);
	$this->assertEquals($emails['users'][0]['content'], $comebackContent);

 	$this->assertEquals($emails['users'][1]['userId'], self::$users['ernest']->userId);
 	$this->assertEquals($emails['users'][1]['emailType'], $this->email->COMEBACK);
	$this->assertEquals($emails['users'][1]['content'], $comebackContent);


 	$this->assertEquals($emails['users'][2]['userId'], self::$users['anatole']->userId);
 	$this->assertEquals($emails['users'][2]['emailType'], $this->email->COMEBACK);
	$this->assertEquals($emails['users'][2]['content'], $comebackContent);

 	$this->assertEquals($emails['users'][3]['userId'], self::$users['phillibert']->userId);
 	$this->assertEquals($emails['users'][3]['emailType'], $this->email->COMEBACK);
	$this->assertEquals($emails['users'][3]['content'], $comebackContent);

 	$this->assertEquals($emails['users'][4]['userId'], self::$users['hippolyte']->userId);
 	$this->assertEquals($emails['users'][4]['emailType'], $this->email->COMEBACK);
	$this->assertEquals($emails['users'][4]['content'], $comebackContent);

 	$this->assertEquals($emails['users'][5]['userId'], self::$users['raymond']->userId);
 	$this->assertEquals($emails['users'][5]['emailType'], $this->email->COMEBACK);
	$this->assertEquals($emails['users'][5]['content'], $comebackContent);

 	//Check that the email is sent only once
 	$emails = $this->email->cronCheck(101*25*60*60);

 	$this->assertEquals(sizeof($emails['users']), 0);
 	$this->assertEquals(sizeof($emails['watches']), 0);
 	$this->assertEquals(sizeof($emails['measures']), 0);
 }

 /**
  * When user adds a watch. If s/he doesn't add a measure
  * in the first 24h hours -> email
  */
 public function test_addFirstMeasure(){

  self::$watchId = $this->watchModel->addWatch(
 	 self::$users['nestor']->userId,
 	 'rolex',
 	 'marolex',
 	 '2000',
 	 '0000-0000',
 	 'caliber'
  );

  //The watch is added at time
  $emails = $this->email->cronCheck(24*1*61*60);

	$addFirstMeasureContent = file_get_contents("emails/add_first_measure.html",
		FILE_USE_INCLUDE_PATH);

  $this->assertEquals(sizeof($emails['users']), 0);
  $this->assertEquals(sizeof($emails['watches']), 1);
  $this->assertEquals(sizeof($emails['measures']), 0);

  $this->assertEquals($emails['watches'][0]['watchId'],
 	 self::$watchId);

  $this->assertEquals($emails['watches'][0]['emailType'],
 		 $this->email->START_FIRST_MEASURE);

	$this->assertEquals($emails['watches'][0]['content'],
		$addFirstMeasureContent);

 		 //Check that the email is sent only once
 	 $emails = $this->email->cronCheck(24*1*62*60);

 	 $this->assertEquals(sizeof($emails['users']), 0);
 	 $this->assertEquals(sizeof($emails['watches']), 0);
 	 $this->assertEquals(sizeof($emails['measures']), 0);


 }

}
