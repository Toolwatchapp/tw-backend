<?php if (!defined('BASEPATH')) {exit('No direct script access allowed');}

/**
 * Ajax controller.
 *
 * This controller is in charge of every ajax call.
 *
 * TODO: Having an Ajax controller doesn't make much sense to me.
 * In my opignion, methods containes here should be distributed in
 * related controller and properly documented as method expecting
 * Ajax behaviour.
 */
class Ajax extends MY_Controller {

	/**
	 * Default constructor that invokes CI_Controller
	 * constructor.
	 */
	function __construct() {
		parent::__construct();

		/**
		 * All measure are took relative to the Paris timezone.
		 */
		date_default_timezone_set('Europe/Paris');
	}

	/**
	 * Email password login method
	 *
	 * @param POST String Email
	 * @param POST String password (encoded)
	 * @return Boolean Login status (JSON)
	 */
	function login() {

		if ($this->expectsPost(array('email', 'password'))) {

			$result = array();

			$email    = $this->input->post('email');
			$password = $this->input->post('password');

			/**
			 * We disallow login with password beginning by FB_
			 * as this are fb user that must use the fb login button
			 * @see facebookSignup
			 */
			if(startsWith($password, "FB_")){

				$result['success'] = false;

			//Login atttempt
			}else if ($this->user->login($email, $password)) {

				$result['success'] = true;

			//If the login attempt was infructuous
			} else {
				$result['success'] = false;
			}

			echo json_encode($result);
		}else{
			echo "POST FAIL";
		}

	}

	/**
	 * Checks if a given email already exists in the db
	 *
	 * //TODO: This doesn't seams to be called from the client side
	 * but only used as helper method.
	 * If so, it should go private or relocated elsewhere.
	 *
	 * @param POST String email
	 *
	 * @return boolean JSON
	 */
	function checkEmail() {

		if ($this->expectsPost(array('email'))) {
			$result = array();

			if (!$this->user->checkUserEmail($this->input->post('email'))) {
				$result['success'] = true;
			} else {
				$result['success'] = false;
			}

			echo json_encode($result);
		}
	}

	/**
	 * Facebook signup
	 *
	 * Handle the signup and the signin for facebook signin button.
	 * If the user never signin with facebook on tw, we create an account
	 * for him. Otherwise, we just log him in his previously created account.
	 *
	 * This method will return 'email' if the user tries to signup/signin with
	 * a facebook that habe an associated email we already have in our db.
	 *
	 * @param POST String email
	 * @param POST String id
	 * @param POST String last_name
	 * @param POST String firstname
	 * @param POST String timezone
	 * @param POST String country
	 *
	 * TODO: Is success a good name for a variable that isn't boolean ?
	 *
	 * @return JSON success == signup. An new account has been created
	 * @return JSON success == signin. We logged the user in his account
	 * @return JSON success == email. The email associated with the Facebook
	 * account is already used. Most likely, the user forgot that he has an email
	 * account.
	 *
	 */
	function facebookSignup() {
		$result['success'] = false;

		if ($this->expectsPost(array('email', 'id', 'last_name',
			'firstname', 'country'))) {

			/**
			 * Getting all the posts
			 */
			$email     = $this->input->post('email');
			$name      = $this->input->post('last_name');
			$firstname = $this->input->post('firstname');
			$country   = $this->input->post('country');
			/**
			 * For fb user, we don't have their fb password (obviously).
			 * Yet, having a password is mandatory in tw and I don't feel
			 * like having a specialized type of user for facebook users.
			 * So, we use as password FB_ concatenated with the FB id of
			 * the user.
			 *
			 * Email + password login are forbidden
			 * @see login
			 */
			$password  = "FB_"+$this->input->post('id');

			// If the email doesn't exists yet
			if (!$this->user->checkUserEmail($email)) {

				/**
				 * Signup attempt
				 * TODO: Can this fail ? If so, under which circonstances ? If not,
				 * remove the if, if yes, provide a else with a dedicated response
				 * code.
				 */
				if ($this->user->signup($email, $password, $name, $firstname, $country)) {

					$result['success'] = "signup";
					$this->user->login($email, $password);

				}

			// The email was already in the db, so we try to log the user
			// using a potentially existing account
			} else if ($this->user->login($email, $password)) {

				$result['success'] = "signin";

			// The email is already taken by a classical account
			} else {

				$result['success'] = "email";
			}
		}

		echo json_encode($result);
	}

	/**
	 * Signup method. Create an accound for a new user.
	 *
	 * @param POST String email
	 * @param POST String password
	 * @param POST String name
	 * @param POST String firstname
	 * @param POST String timezone
	 * @param POST String country
	 * @return boolean|mixed Produces an 'email' output is the email
	 * already exists
	 */
	function signup() {

		$result['success'] = false;

		if ($this->expectsPost(array('email','password','name','firstname',
			'country'))) {

			$result = array();

			$email       = $this->input->post('email');
			$password    = $this->input->post('password');
			$name        = $this->input->post('name');
			$firstname   = $this->input->post('firstname');
			$country     = $this->input->post('country');

			//If the email isn't already in used
			if (!$this->user->checkUserEmail($email)) {

				// Create the account
				if ($this->user->signup(
						$email, $password, $name, $firstname,
						$country)) {

					$result['success'] = true;

					//Log the user will create his session and so on
					$this->user->login($email, $password);

				} else {

					$result['success'] = false;
				}

			//The email is already in use
			} else {
				$result['success'] = 'email';
			}

			echo json_encode($result);
		}
	}

	/**
	 * Reset the password
	 *
	 * @param POST String email
	 * @return boolean success
	 */
	function askResetPassword() {
		$result['success'] = false;

		if ($this->expectsPost(array('email'))) {

			$email = $this->input->post('email');

			$result = array();

			//We don't send the token over the network, we just
			//make sure that a token has been generated.
			//The token will be transfered in an email.
			$resetToken = $this->user->askResetPassword($email);

			if ($resetToken) {

				$result['success'] = true;

			} else {
				$result['success'] = false;
			}

		}
		echo json_encode($result);
	}

	/**
	 * Reset the password of an user
	 *
	 * @param POST String $resetToken The reset token sent by email
	 * @param POST String $password		The new password for the usser
	 *
	 * @return boolean success
	 */
	function resetPassword() {

		if ($this->expectsPost(array('resetToken', 'password'))) {

			$result = array();

			$resetToken = $this->input->post('resetToken');
			$password   = $this->input->post('password');

			//Attempting to reset the password given the token and the
			//new password
			if ($this->user->resetPassword($resetToken, $password)) {

				$result['success'] = true;
			} else {
				$result['success'] = false;
			}

			echo json_encode($result);
		}
	}

	/**
	 * Send an email to tw team through the contact form
	 *
	 * TODO: Is this the right place for this ?
	 *
	 * @param POST String name
	 * @param POST String email
	 * @param POST String message
	 *
	 * @return boolean success json
	 */
	function contact() {

		if ($this->expectsPost(array('name', 'email', 'message'))) {
			$result = array();

			$name    = $this->input->post('name');
			$email   = $this->input->post('email');
			$message = $this->input->post('message');

			$this->load->library('mandrill');

			$messageMandrill = array(
				'html'       => $message,
				'subject'    => $subject,
				'from_email' => $email,
				'from_name'  => $name,
				'to'         => array(
					array(
						'email' => 'marc@toolwatch.io',
						'name'  => 'Marc',
						'type'  => 'to',
					)
				),
				'headers'   => array(
					'Reply-To' => $email,
				),
				'important'                 => false,
				'track_opens'               => true,
				'track_clicks'              => true,
				'tags'                      => array($tags),
				'google_analytics_campaign' => $tags,
				'google_analytics_domains'  => array('toolwatch.io'),
				'metadata'                  => array(
					'website'                  => 'toolwatch.io',
				)
			);

			$async   = false;
			$ip_pool = 'Main Pool';

			$scheduleTime = time();

			$send_at =  date('Y-', $scheduleTime).date('m-', $scheduleTime)
			.(date('d', $scheduleTime)).' '.(date('H', $scheduleTime)-1).':'
			.(date('i', $scheduleTime)).date(':s', $scheduleTime);

			$mandrillResponse =  $this->mandrill->messages->send($messageMandrill, $async, $ip_pool, $send_at);
			log_message('info', 'Mandrill email: ' . print_r($mandrillResponse, true));


			if ($mandrillResponse[0]['status'] === 'sent') {
				$result['success'] = true;
			} else {
				$result['success'] = false;
			}

			echo json_encode($result);
		}
	}
}
