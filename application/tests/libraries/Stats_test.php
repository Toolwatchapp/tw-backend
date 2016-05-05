<?php

class Stats_test extends TestCase {

  private static $stats;
  private static $brands;
  private static $dumnyCache;
  private static $CI;
  private static $userId;

  public static function setUpBeforeClass() {

		$CI = &get_instance();
		$CI->load->model('Watch');
		$CI->load->model('User');
		$CI->load->model('measure');

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

    self::$userId = $CI->User->insert($data);

    self::$brands = ['rolex', 'omega', 'seiko'];
    self::$dumnyCache = array();

    self::$dumnyCache['@tw'.'_neg_'.'count'] = 0.0;
    self::$dumnyCache['@tw'.'_neg_'.'sum'] = 0.0;
    self::$dumnyCache['@tw'.'_pos_'.'sum'] = 0.0;      self::$dumnyCache['@tw'.'_pos_'.'count'] = 0.0;

    self::$CI = $CI;

    self::populate('insertDirectlyInBdd');

    $CI->load->driver('cache', array('adapter' => 'apc'));
    $CI->cache->clean();

	}

  private static function insertWithModel($id){
    $measureId = self::$CI->measure->addBaseMesure($id, time() - rand(-10, 10), time() - rand(-10, 10));

    return self::$CI->measure->addAccuracyMesure($measureId, time() + 12*60*60 - rand(-10, 10), time() + 12*60*60 - rand(-10, 10))
    ->unroundedAccuracy;
  }

  private static function insertDirectlyInBdd($id){
    $data = array(
      'watchId'              => $id,
      'measureReferenceTime' => time() - rand(-10, 10),
      'measureUserTime'      => time() - rand(-10, 10),
      'accuracyReferenceTime' => time() + 12*60*60 - rand(-10, 10),
      'accuracyUserTime'      => time() + 12*60*60 - rand(-10, 10),
      'statusId'             => 2);

    return self::$CI->measure->find(
                  self::$CI->measure->insert($data)
                )->unroundedAccuracy;
  }

  private static function populate($insertFunction){
    foreach (self::$brands as $brand) {
      $watchesIds = array();

      if(!array_key_exists($brand.'_neg_'.'count', self::$dumnyCache)){
        self::$dumnyCache[$brand.'_neg_'.'count'] = 0.0;
        self::$dumnyCache[$brand.'_neg_'.'sum'] = 0.0;
        self::$dumnyCache[$brand.'_pos_'.'sum'] = 0.0;      self::$dumnyCache[$brand.'_pos_'.'count'] = 0.0;
      }

      for ($i=0; $i < rand(3, 6); $i++) {

        array_push($watchesIds,
            self::$CI->watch->addWatch(
            self::$userId,
            $brand,
            'marolex',
            '2000',
            '0000-0000',
            'caliber'
          ));
      }

      foreach ($watchesIds as $id) {

        for ($i=0; $i < rand(3, 10); $i++) {
          $accuracy = self::{$insertFunction}($id);

          if(abs($accuracy) < 300){
            $pre = ($accuracy < 0) ? '_neg_' : '_pos_';

            self::$dumnyCache[$brand.$pre.'count']++;
            self::$dumnyCache[$brand.$pre.'sum'] += $accuracy;
            self::$dumnyCache["@tw".$pre.'count']++;
            self::$dumnyCache["@tw".$pre.'sum'] += $accuracy;
          }
        }
      }
    }
  }

  public function test_posAverage() {

    self::$CI->load->library('stats');
    self::$stats = self::$CI->stats;

    $this->assertEquals(
      self::$dumnyCache['@tw_pos_sum'] / self::$dumnyCache['@tw_pos_count'],
      self::$stats->getPosAverage('@tw'),
      'should match at 0.1',
      0.1
    );

    foreach (self::$brands as $brand) {
      $this->assertEquals(
        self::$dumnyCache[$brand.'_pos_sum'] / self::$dumnyCache[$brand.'_pos_count'],
        self::$stats->getPosAverage($brand),
        'should match at 0.1',
        0.1
      );
    }
  }

  public function test_negAverage(){
    $this->assertEquals(
      self::$dumnyCache['@tw_neg_sum'] / self::$dumnyCache['@tw_neg_count'],
      self::$stats->getNegAverage('@tw'),
      'should match at 0.1',
      0.1
    );

    foreach (self::$brands as $brand) {
      $this->assertEquals(
        self::$dumnyCache[$brand.'_neg_sum'] / self::$dumnyCache[$brand.'_neg_count'],
        self::$stats->getNegAverage($brand),
        'should match at 0.1',
        0.1
      );
    }
  }

  public function test_update(){
    self::populate('insertWithModel');
    $this->assertEquals(
      self::$dumnyCache['@tw_pos_sum'] / self::$dumnyCache['@tw_pos_count'],
      self::$stats->getPosAverage('@tw'),
      'should match at 0.1',
      0.1
    );
  }

  // public function test

  // public static function tearDownAfterClass() {
  //  $CI = &get_instance();
  //  $CI->load->model('User');
  //  $CI->load->model('Watch');
  //  $CI->load->model('Measure');
  //  $CI->watch->delete_where(array("watchId >=" => "0"));
  //  $CI->User->delete_where(array("userId >=" => "0"));
  //  $CI->Measure->delete_where(array("id >=" => "0"));
  // }


}

?>
