<?php

class Email_test extends TestCase {

	public static $user;
	public static $measure;
	public static $watch;
	public static $watch2Id;

	public static function setUpBeforeClass() {
		$CI = &get_instance();
		$CI->load->model('Email');
		$CI->load->model('Watch');
		$CI->load->model('User');
		$CI->load->model('measure');

		$CI->emailMeasure = new MY_Model('email_measure');
		$CI->emailWatch   = new MY_Model('email_watch');

		$CI->Email->delete_where(array("id >="        => "0"));
		$CI->Watch->delete_where(array("watchId >="   => "0"));
		$CI->User->delete_where(array("userId >="     => "0"));
		$CI->emailMeasure->delete_where(array("id >=" => "0"));
		$CI->measure->delete_where(array("id >="      => "0"));

		$data = array(
			'email'        => 'mathieu@gmail.com',
			'password'     => hash('sha256', 'azerty'),
			'name'         => 'math',
			'firstname'    => 'nay',
			'timezone'     => -5,
			'country'      => 'Canada',
			'registerDate' => time(),
			'lastLogin'    => time()
		);

		$CI->User->insert($data);

		self::$user = $CI->User->select()->find_all()[0];

		$data = array(
			'userId'    => self::$user->userId,
			'brand'     => 'Rolex',
			'name'      => 'watch',
			'yearOfBuy' => 2015,
			'serial'    => 01,
			'caliber'   => 02);

		$CI->Watch->insert(
			$data
		);

		self::$watch = $CI->Watch->select()->find_all()[0];

		$data = array(
			'watchId'              => self::$watch->watchId,
			'measureReferenceTime' => 1438579820,
			'measureUserTime'      => 1438579880,
			'statusId'             => 1);

		$CI->measure->insert($data);

		self::$measure = $CI->measure->select()->find_all()[0];

	}

	public function setUp() {
		$CI = &get_instance();

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

		$CI->Email->mandrill->messages = $mandrillMessage;

		$this->email      = $CI->Email;
		$this->watchModel = $CI->Watch;

		$this->emailMeasure = new MY_Model('email_measure');
		$this->emailWatch   = new MY_Model('email_watch');

	}

	public function test_signup() {

		echo 'test_signup';

		$this->email->updateObserver('TEST', SIGN_UP, self::$user);

		$this->assertEquals(1, $this->email->select()->where('type', 1)->count_all());
		$this->assertEquals(1, $this->email->select()->where('type', 5)->count_all());

	}

	public function test_addWatch() {

		echo 'test_addWatch';

		$this->email->updateObserver('TEST', ADD_WATCH, self::$watch);

		$this->assertEquals(0, $this->email->select()->where('type', 1)->count_all());

	}

	public function test_login() {

		echo 'test_login';

		$comebackBefore = $this->email->select()->find_by('type', 5);

		$this->email->updateObserver('TEST', LOGIN_EMAIL, self::$user);

		$comebackAfter = $this->email->select()->find_by('type', 5);

		$this->assertNotEquals($comebackBefore->plannedAt, $comebackAfter->plannedAt);
		$this->assertEquals($comebackBefore->id, $comebackAfter->id);

	}

	public function test_newMeasure() {

		echo 'test_newMeasure';

		$data = array(
			'user'    => self::$user,
			'watch'   => self::$watch,
			'measure' => self::$measure,
		);

		$this->email->updateObserver('TEST', NEW_MEASURE, $data);

		$emails = $this->email->select()->where('type', 2)->find_all();

		$this->assertEquals(2, sizeof($emails));
		$this->assertNotEquals($emails[0]->plannedAt, $emails[1]->plannedAt);
	}

	public function test_newAccuracy() {

		echo 'test_newAccuracy';

		$data = array(
			'user'    => self::$user,
			'watch'   => self::$watch,
			'measure' => self::$measure,
		);

		$this->email->updateObserver('TEST', NEW_ACCURACY, $data);

		$this->assertEquals(1, $this->email->select()->where('type', 3)->count_all());
		$this->assertEquals(1, $this->email->select()->where('type', 4)->count_all());
	}

	public function test_addSecondWatch() {

		echo 'test_addSecondWatch';

		$this->watchModel->addWatch(
			self::$user->userId,
			'Rolex 2',
			'watch 2',
			2015,
			28,
			014
		);

		self::$watch2Id = $this->watchModel->inserted_id();

		$this->email->updateObserver('TEST', ADD_WATCH, self::$watch);

		$this->assertEquals(0, $this->email->select()->where('type', 3)->count_all());

	}

	public function test_bundle() {

		echo 'test_bundle';

		$data = array(
			'user'    => self::$user,
			'watch'   => self::$watch,
			'measure' => self::$measure,
		);

		$this->email->updateObserver('TEST', NEW_MEASURE, $data);

		$measure2          = self::$measure;
		$measure2->watchId = self::$watch2Id;
		$measure2->id      = 2;

		$watch2          = self::$watch;
		$watch2->watchId = self::$watch2Id;

		$data = array(
			'user'    => self::$user,
			'watch'   => $watch2,
			'measure' => $measure2,
		);

		$this->email->updateObserver('TEST', NEW_MEASURE, $data);

		$measure3          = self::$measure;
		$measure3->watchId = self::$watch2Id;
		$measure3->id      = 3;

		$data = array(
			'user'    => self::$user,
			'watch'   => $watch2,
			'measure' => $measure3,
		);

		$this->email->updateObserver('TEST', NEW_MEASURE, $data);

		$this->assertEquals(6, $this->emailMeasure->count_all());

		$this->assertEquals(2,
			$this->emailMeasure->select('count(distinct(emailId)) as cnt', false)
			->find_all()[0]->cnt);

	}

}