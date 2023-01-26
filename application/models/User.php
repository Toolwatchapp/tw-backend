<?php if (!defined('BASEPATH')) {exit('No direct script access allowed');
}

include_once('ObservableModel.php');

/**
 * User model.
 *
 * Handles everything related to the user account;
 */
class User extends ObservableModel {

	/**
	 * Default constructor
	 */
	function __construct() {
		parent::__construct();
		$this->table_name = "user";
		$this->key = "userId";
		$this->users_session = new MY_Model("users_sessions");
		$this->session_model = new MY_Model('ci_sessions');
		$this->api_keys = new MY_Model('keys');
	}

	/**
	 * Login. Tries to log an user with $email and $password
	 *
	 * @param  String $email    The email password
	 * @param  String $password The passwod
	 * @return User           	The user
	 */
	function login($email, $password, $event = LOGIN_EMAIL) {

		$user = $this->select(
					'userId, 
					lower(email) as email,
					name, 
					firstname,
					timezone, 
					country, 
					registerDate')
				->find_by(
					$this->construct_user_for_login($email, $password)
				);

		if ($user) {
			
			$this->notify($event, $user);
			$this->set_userdata($user);

		} else {
			$this->notify($event.'_FAIL', $user);
		}

		return $user;
	}

	/*
	* Convenient method that can be overriden when specializing users;

	* @param  String $email
	* @param  String $password
	* @return array  An array for database selection 
	*/
	protected function construct_user_for_login($email, $password){

		return array(
			"lower(email)" => strtolower($email),
			"password" => hash('sha256', $password),
			"facebook" => 0
		);
	}

	/**
	* @param User user
	* 
	* Set userdata for logged in users.
	**/
	protected function set_userdata($user){
		$this->session->set_userdata('userId', $user->userId);
		$this->session->set_userdata('email', $user->email);
		$this->session->set_userdata('name', $user->name);
		$this->session->set_userdata('firstname', $user->firstname);
		$this->session->set_userdata('country', $user->country);
		$this->session->set_userdata('registerDate', $user->registerDate);

		$this->update_where('userId', $user->userId, array('lastLogin' => time()));

		if($this->session->session_id){
			$this->users_session->insert(
				array(
					'user_id' => $user->userId, 
					'session_id'=> $this->session->session_id
				)
			);
		}

		if(
			//not set
			$user->country == null || $user->country == "" || $user->country == "none"
			//Not provided or Tor
			|| $user->country == "XX" || $user->country == "T1"
		){
			
			$user->country = $_SERVER["HTTP_CF_IPCOUNTRY"]; 
			$this->session->set_userdata('country', $user->country);

			$this->update($user->userId, array(
				'country' => $user->country
			));
		}
	}

	/**
	 * Checks if the user is logged in according to its session
	 * @return boolean
	 */
	function isLoggedIn() {

		return !empty($this->session->userdata('userId'));
	}

	/**
	 * Logout an user
	 */
	function logout() {

		$sessionId = $this->session->session_id;

		$this->users_session->delete_where(
			array(
				'user_id' => $this->session->userdata('userId'),
				'session_id' => $sessionId
			)
		);

		$this->session_model->delete($sessionId);
		
		// if(session_status() == PHP_SESSION_ACTIVE)
		// {
		// 	$this->session->sess_destroy();
		// 	session_destroy();
		// 	session_unset();
		// }

		$this->notify(LOGOUT, array());

		return true;
	}

	/**
	 * Soft delete users
	 * @param  int $userId
	 * @return boolean
	 */
	function delete($id = NULL){

		return $this->update($id,
			[
				'email' => 'deleted@user.com',
				'password' => 'deleted user',
				'firstname' => 'deleted user',
				'name' => 'deleted user',
				'timezone' => 'deleted user',
				'country' => 'deleted user',
				'isActive' => 0,
			]
		) && $this->affected_rows() === 1;
	}

	/**
	 * Checks $email is linked with an account on tw
	 * @param  String $email The email to check against the db
	 * @return boolean wether or not the email is already taken
	 */
	function checkUserEmail($email) {
		$res = false;

		if ($this->find_by('lower(email)', strtolower($email))) {
			$res = true;
		}

		return $res;
	}

	/**
	 * Retrieve an user by its id
	 * @param  int $userId the user id
	 * @return boolean|User False in case of faillure
	 */
	function getUser($userId) {

		return $this->find_by('userId', $userId);
	}

	/**
	 * Signup (register) a new user.
	 *
	 * @param  String $email
	 * @param  String $password
	 * @param  String $name
	 * @param  String $firstname
	 * @param  String $timezone
	 * @param  String $country
	 * @return boolean   false an faillure
	 */
	function signup($email, $password, $name, $firstname, $country, $event = SIGN_UP) {

		$res  = false;
		$data = $this->contruct_user_for_insert($email, $password, $name, $firstname, $country);
		
		if ($this->insert($data)) {

			//So we don't have to fetch it.
			$user         = arrayToObject($data);
			//Get the inseserted id to complete the object
			$user->userId = $this->inserted_id();

			$this->notify($event, $user);

			$this->load->model("emailPreferences");
			$this->emailPreferences->newUser($user->userId);
			$this->set_userdata($user);
			unset($user->password);

			$res = $user;
		}

		return $res;
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
		return array(
			'email'        => strtolower($email),
			'password'     => hash('sha256', $password),
			'name'         => $name,
			'firstname'    => $firstname,
			'country'      => $country,
			'registerDate' => time(),
			'lastLogin'    => time()
		);
	}

	/**
	 * Get a reset token for $email
	 * @param String $email
	 *
	 * @return boolean|String the reset token or false on faillure
	 */
	function askResetPassword($email) {

		$resetToken = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 10);

		if(
			$this->checkUserEmail($email) === true &&
			$this->update_where('lower(email)', strtolower($email), array('resetToken' => $resetToken, 'facebook' => 0))
			&& $this->affected_rows() === 1){
			$this->notify(RESET_PASSWORD,  array('email' => $email, 'token'=>$resetToken));
			$this->deleteActiveSessions($email);
			return $resetToken;
		}

		return false;
	}

	/**
	 * Deletes all sessions associated with $email
	 * https://hackerone.com/reports/162128
	 * @param String $email
	 */
	protected function deleteActiveSessions($email){

		$sessionsIdsForUser = $this->users_session->select('session_id, userId')
							->join('user', 'user.userId = users_sessions.user_id')
							->find_all_by('lower(email)', strtolower($email));

		if(is_array($sessionsIdsForUser) && sizeof($sessionsIdsForUser) !== 0){

			$userId = $sessionsIdsForUser[0]->userId;

			$this->users_session->delete_where(
				array(
					'user_id' => $userId
				)
			);
			
			$this->api_keys->delete_where(array('user_id' => $userId));

			foreach ($sessionsIdsForUser as $session) {

				$this->session_model->delete($session->session_id);
			}
		}

	}

	/**
	 * Change an account's password given a $resetToken and a new $password
	 * @param String $resetToken The reset token
	 * @param String $password   The new password
	 */
	function resetPassword($resetToken, $password) {
	
		/**
		 * TODO:The update is based on the reset token generated in the askResetPassword
		 * method. While being highly unlikely, it is possible for two accounts
		 * to have the same token. In such a case, the wrong account can be
		 * updated...
		 *
		 * Note that reset token are blanked after reset, so the generator would
		 * have to generate the same reset token for two different peoples and
		 * one of them have to leave it this way (not using the token) for
		 * problems to happen.
		 */
		if(($user = $this->find_by('resetToken', $resetToken)) && 
			$this->update_where('resetToken', $resetToken,
			array('resetToken' => '', 'password' => hash('sha256', $password)))
			&& $this->affected_rows() === 1){

			$this->notify(RESET_PASSWORD_USE, array('email' => $user->email));
			$this->deleteActiveSessions($user->email);
			return true;
		}
			
		return false;
	}

	/**
	 * Get an user base on a $watchId
	 * @param  int $watchId The watchId to seatch
	 * @return boolean|User  The user associated to $watchId or false
	 */
	function getUserFromWatchId($watchId) {

		return $this->select('user.*')
			->join('watch', '`user`.`userId`=`watch`.`userId`')
			->find_by('watchId', $watchId);
	}
}
