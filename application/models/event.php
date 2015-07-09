<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Event extends MY_Model 
{

	public  $CTA_MEASURES = 1;
	public  $CTA_MEASURE_NOW = 2;
	public  $CTA_GET_STARTED = 3;
	public  $CTA_FEATURES = 4;
	public  $LOGIN_EMAIL = 5;
	public  $LOGIN_FAIL = 6;
	public  $LOGIN_FB = 7;
	public  $LOGIN_FB_FAIL = 8;
	public  $RESET_PASSWORD = 9;
	public  $RESET_PASSWORD_USE = 10;
	public  $LOGOUT = 11;
	public  $SIGN_UP = 12;
	public  $SIGN_UP_FAIL = 13;
	public  $SIGN_UP_FB = 14;
	public  $ADD_WATCH = 15;
	public  $DELETE_WATCH = 16;
	public  $NEW_MEASURE = 17;
	public  $DELETE_MEASURE = 18;
	public  $DELETE_ALL_MEASURES = 19;
	public  $BOARD_LOAD = 20;
	public  $NEW_ACCURACY = 21;
	public  $MEASURE_LOAD = 22;
	public  $ACCURACY_LOAD = 23;
	public  $ACCURACY_WARNING_POPUP = 24;
	public  $ACCURACY_SOMETHING_WRONG = 25;
	public  $LOGIN_POPUP = 26;
	public  $SIGN_UP_POPUP = 27;
	public  $EVENT_STRING;

	protected $nbEvent = 27;
	protected $masks;

    function __construct()
    {
        parent::__construct();
        $this->table_name = "event";
        $this->masks = array(
			'CTR_MEASURE'  		 => array($this->NEW_MEASURE, $this->MEASURE_LOAD),
			'CTR_ACCURACY' 		 => array($this->NEW_ACCURACY, $this->ACCURACY_LOAD),
			'CTR_PASSWORD' 		 => array($this->RESET_PASSWORD, $this->RESET_PASSWORD_USE),
			'CTR_SIGNUP'   		 => array($this->SIGN_UP, $this->SIGN_UP_POPUP),
			'CTR_SIGNUP_FB'		 => array($this->SIGN_UP, $this->SIGN_UP_FB),
			'CTR_LOGIN'    		 => array($this->LOGIN_EMAIL, $this->LOGIN_POPUP),
			'CTR_LOGIN_FB' 		 => array($this->LOGIN_FB, $this->LOGIN_POPUP),
			'MEASURE_COMPLETION' => array($this->NEW_ACCURACY, $this->NEW_MEASURE)
		);

		$this->EVENT_STRING = "'DATE',";
		$vars = get_object_vars($this);
    	$index = 0;
    	foreach ($vars as $key => $value) {
    		if(!is_array($value) && !is_array($key) && $index < $this->nbEvent){
    			$this->EVENT_STRING = $this->EVENT_STRING . "'" . $key . "',";
    			$index++;
    		}
    	}

    	$this->EVENT_STRING = rtrim($this->EVENT_STRING, ",");
    }

    function add($event){

    	$data = array(
            'ip' => $this->input->ip_address(),
            'user_id' => $this->session->userdata('userId'), 
            'mobile' => $this->agent->is_mobile(),
            'browser' => $this->agent->browser(),
            'platform' => $this->agent->platform(),
            'event' => $event);
        
        if($this->insert($data) === false){
        	echo $this->db->last_query();
        }
    }

    function mobileEvents(){
    	return $this->select("
    		(select count(1) from event where mobile = 1) / 
    		count(1) as percent", false)
    		->find();
    }

    function getPlatforms(){
    	return $this->select("platform, count(1) as nb")
    	->group_by('platform')
    	->find_all();
    }

    function getBrowsers(){
    	return $this->select("browser, count(1) as nb")
    	->group_by('browser')
    	->find_all();
    }

    function getEvents(){

    	return array_merge(
    		$this->activeUser(), 
    		$this->activeBrowser(),
    		$this->computeMask(),
    		$this->events()
    	);
    }

    public function getAllEvents(){

    	$selectString = 'CONCAT(DAY(timestamp), "/", MONTH(timestamp),"/",
	    		YEAR(timestamp)) as date,';

    	$vars = get_object_vars($this);

    	$index = 0;
    	foreach ($vars as $key => $value) {
    		if(!is_array($value) && !is_array($key) && $index < $this->nbEvent){
    			$selectString = $selectString . 
    				$this->constructSubMask($value) .
    				' as ' . $key . ',';
    			$index++;
    		}
    		
    	}

    	return $this->select(rtrim($selectString, ","), false)
    		->group_by('date')
    		->find_all();
    }

    private function computeMask(){

    	$computedMasks = array();

    	foreach ($this->masks as $mask => $values) {

    		$maskResult = $this->select('CONCAT(DAY(timestamp), "/", MONTH(timestamp),"/",
	    		YEAR(timestamp)) as date, ("'. $mask .'") as name,'. 
				$this->constructSubMask($values[0]) . '/' . 
				$this->constructSubMask($values[1]) . ' as cnt', false)
				->where('event.event', $values[0])
				->or_where('event.event', $values[1])
				->group_by('date')
				->find_all();

			if(is_array($maskResult)){
				$computedMasks = array_merge($computedMasks, $maskResult);
			}
    	}

    	return $computedMasks;

    }

    private function constructSubMask($value){
    	return '
    		(Select count(id) from event where CONCAT(DAY(timestamp), "/", 
    		MONTH(timestamp), "/", YEAR(timestamp)) = 
			date and event.event = '.$value.')';
    }

    private function activeUser(){
    	return $this->select('count(distinct(user_id)) as cnt, 
    		CONCAT(DAY(timestamp), "/", MONTH(timestamp),"/",
    		YEAR(timestamp)) as date, ("ACTIVE_REGISTERED_USER") as name', false)
    		->where("user_id !=", 0)
    		->group_by("date")
    		->find_all();
    }

    private function activeBrowser(){
    	return $this->select('count(distinct(ip)) as cnt, 
    		CONCAT(DAY(timestamp), "/", MONTH(timestamp),"/",
    		YEAR(timestamp)) as date, ("ACTIVE_UNREGISTERED_USER") as name', false)
    		->where("user_id =", 0)
    		->group_by("date")
    		->find_all();
    }

    private function events(){
    	$data = $this->select('count(event) as cnt, event,
    		CONCAT(DAY(timestamp), "/", MONTH(timestamp),"/",
    		YEAR(timestamp)) as date, name', false)
    		->join("event_name", "event.event = event_name.id")
    		->group_by("event")
    		->group_by("date")
    		->order_by("event")
    		->find_all();

    	return $data;
    }



 }