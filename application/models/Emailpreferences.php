<?php if (!defined('BASEPATH')) {exit('No direct script access allowed');
}

include_once('ObservableModel.php');

/**
 * User model.
 *
 * Handles everything related to the user account;
 */
class Emailpreferences extends ObservableModel {

  function __construct() {
		parent::__construct();
		$this->table_name = "email_preference";
	}


  	/**
  	 * Get email preferences and email of a given userId
  	 * @param  int $userId
  	 * @return user|false in case the user doesn't exists
  	 */
  	function getPreferences($userId){
  		return $this->select()
  		->find_by("email_preference.userId", $userId);
  	}

    /**
     * Update email preferences of $userId
     *
     * @param  boolean   $dayAccuracy
     * @param  boolean   $weekAccuracy
     * @param  boolean   $result
     * @param  boolean   $newMeasure
     * @param  boolean   $firstMeasure
     * @param  boolean   $firstWatch
     * @param  boolean   $secondWatch
     * @param  boolean   $comeback
     * @param  int       $userId
     * @return true|false
     */
    function updateEmailPreferences($dayAccuracy, $weekAccuracy, $result, $newMeasure, $firstMeasure,  $firstWatch, $secondWatch, $comeback, $plaformAnnounces, $userId){

      $data = array(
        "dayAccuracy"  => $dayAccuracy,
        "weekAccuracy" => $weekAccuracy,
        "result"       => $result,
        "newMeasure"   => $newMeasure,
        "firstMeasure" => $firstMeasure,
        "firstWatch"   => $firstWatch,
        "secondWatch"  => $secondWatch,
        "comeback"     => $comeback,
        'platformAnnonces' => $plaformAnnounces,
      );

      return $this->update_where('userId', $userId, $data)
        && $this->affected_rows() == 1;
    }

    /**
     * Inserts a new line in the email_preference table
     * for $userId
     * @param  int $userId
     * @return true|false
     */
    function newUser($userId){
      return $this->insert(["userId"=>$userId]);
    }
}
