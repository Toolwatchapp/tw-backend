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

		$user = $this->select('*')
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

		} else {
			$this->notify($event.'_FAIL', $data);
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

		//Workaround for automated tests
		session_unset();

		$this->notify(LOGOUT, array());
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
	function signup($email, $password, $name, $firstname, $timezone, $country) {

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
			'timezone'     => $timezone,
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
		} else {

			$this->notify($event.'_FAIL', $user);
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
			$this->notify(RESET_PASSWORD,  array('email' => $email));
			return $resetToken;
		}

		return false;
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
		if($this->update_where('resetToken', $resetToken,
			array('resetToken' => '', 'password' => hash('sha256', $password)))
			&& $this->affected_rows() === 1){
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
