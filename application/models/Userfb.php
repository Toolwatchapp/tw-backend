<?php if (!defined('BASEPATH')) {exit('No direct script access allowed');
}

class Userfb extends User {

    /**
	 * Default constructor
	 */
	function __construct() {
		parent::__construct();
	}
	
	/**
	 * Login. Tries to log a facebook user
	 *
	 * @param  String $email    The email password
	 * @param  String $token    The facebooktokenid
	 * @return User           	The user
	 */
	function login($email, $token, $event = LOGIN_FB){

		//Valid Token
		if($this->facebook->is_token_valid($token)){
			return parent::login($email, "0", $event);
		}
		else {
			return false;
		}
	}

	/**
	 * Login. Tries to log a facebook user
	 *
	 * @param  String $email    The email
	 * @param  String $password    The password
	 * @return User           	The user
	 */
	function deprecated_login($email, $password){

		$user = false;

		if(!$user){
			$user = parent::login($email, "0", LOGIN_FB);
		}
		
		if(!$user){
			
			$user = parent::login($email, getenv("FB_PW").$password, LOGIN_FB);		
		}

		if($user){	
			$this->update_legacy_facebook($email);
		}

		return $user;
	}

    /**
	 * Signup (register) a new user.
	 *
	 * @param  String $email
	 * @param  String $token
	 * @param  String $name
	 * @param  String $firstname
	 * @param  String $timezone
	 * @return boolean   false an faillure
	 */
	function signup($email, $name, $firstname, $token, $country="none", $event = SIGN_UP_FB) {

        if($this->facebook->is_token_valid($token)){
			
		    return parent::signup($email, "0", $name, $firstname, $country, $event);
        }else{
            return false;
        }
	}

    /*
	* Convenient method that can be overriden when specializing users;

	* @param  String $email
	* @param  String $password
	* @return array  An array for database selection 
	*/
	protected function construct_user_for_login($email, $password = "0"){

		$user = parent::construct_user_for_login($email, $password);
        $user['facebook'] = "1";
        return $user;
	}

    /*
	* Convenient method that can be overriden when specializing users;

	* @param  String $email
	* @param  String $password
	* @param  String $name
	* @param  String $firstname
	* @param  String $timezone
	* @param  String $country
	* @return array  An array for database insertion 
	*/
	protected function contruct_user_for_insert($email, $password, $name, $firstname, $country){
		$data = parent::contruct_user_for_insert($email, $password, $name, $firstname, $country);
        $data['facebook'] = 1;
        return $data;
	}


    /**
	* Transform facebook accounts created before 1.0.3 to new, more
	* secure schema 
	*/
	private function update_legacy_facebook($email){
		$this->update_where(
			'email', 
			$email, 
			array(
				'password' => hash('sha256', "0"),
				'facebook' => 1
			)
		);
	}

}