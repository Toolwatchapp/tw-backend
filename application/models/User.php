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
	}

	/**
	 * Login. Tries to log an user with $email and $password
	 *
	 * @param  String $email    The email password
	 * @param  String $password The passwod
	 * @return User           	The user
	 */
	function login($email, $password) {
		$res = false;

		$user = $this->select('userId, email, name, firstname,
		 	timezone, country, registerDate')
		     ->where('email', $email)
		     ->where('password', hash('sha256', $password))
				 ->find_all();

		$event = LOGIN_EMAIL;

		if (strrpos($password, 'FB_') === 0) {
			$event = LOGIN_FB;
		}


		if (is_array($user)
		//That's not a mistake, the tranformation from array
		//to a single user is made here.
		//Not super intuitive nor conform to coding rules
		&& $user = $user[0]) {

			$this->session->set_userdata('userId', $user->userId);
			$this->session->set_userdata('email', $user->email);
			$this->session->set_userdata('name', $user->name);
			$this->session->set_userdata('firstname', $user->firstname);
			$this->session->set_userdata('timezone', $user->timezone);
			$this->session->set_userdata('country', $user->country);
			$this->session->set_userdata('registerDate', $user->registerDate);

			$this->update_where('userId', $user->userId, array('lastLogin' => time()));

			$this->notify($event, $user);

			$this->users_session->insert(
				array(
					'user_id' => $user->userId, 
					'session_id'=> $this->session->session_id
				)
			);

		} else {
			$this->notify($event.'_FAIL', $user);
		}

		return $user;
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
		
		// Workaround for automated tests
		session_unset();

		session_destroy();
		$this->session->sess_destroy();



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

		if ($this->find_by('email', $email)) {
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
	function signup($email, $password, $name, $firstname, $country) {

		$event = SIGN_UP;

		if (strrpos($password, 'FB_') === 0) {
			$event = SIGN_UP_FB;
		}

		$res  = false;
		$data = array(
			'email'        => $email,
			'password'     => hash('sha256', $password),
			'name'         => $name,
			'firstname'    => $firstname,
			'country'      => $country,
			'registerDate' => time(),
			'lastLogin'    => time()
		);


		if ($this->insert($data)) {

			//So we don't have to fetch it.
			$user         = arrayToObject($data);
			//Get the inseserted id to complete the object
			$user->userId = $this->inserted_id();

			$this->notify($event, $user);

			$res = true;
		}

		return $res;
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
			$this->update_where('email', $email, array('resetToken' => $resetToken))
			&& $this->affected_rows() === 1){
			$this->notify(RESET_PASSWORD,  array('email' => $email, 'token'=>$resetToken));
			$this->deleteActiveSessions($email);
			return $resetToken;
		}

		return false;
	}

	/**
	 * Deletes all sessions associated with $email
	 * @param String $email
	 */
	private function deleteActiveSessions($email){

		$sessionsIdsForUser = $this->users_session->select('session_id, userId')
							->join('user', 'user.userId = users_sessions.user_id')
							->find_all_by('email', $email);

		if(is_array($sessionsIdsForUser) && sizeof($sessionsIdsForUser) !== 0){

			$userId = $sessionsIdsForUser[0]->userId;

			$this->users_session->delete_where(
				array(
					'user_id' => $userId
				)
			);
			

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
