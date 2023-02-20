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

	function reportClientError(){
		if($this->expectsPost(array("error"))){

			log_message("error", $this->error . "\r\n" .
				"USER_ID:".($this->session->userdata('userId')?
				$this->session->userdata('userId'):0)
			);
		}
	}

	/**
	 * Email password login method
	 *
	 * @param POST String Email
	 * @param POST String password (encoded)
	 * @return Boolean Login status (JSON)
	 */
	function login() {

		if ($this->expectsPost(array('email', 'password')) && strlen($this->password) < 512) {

			$result = array();

			if ($this->user->login($this->email, $this->password)) {

				$result['success'] = true;

			//If the login attempt was infructuous
			} else {
				$result['success'] = false;
			}

			echo json_encode($result);
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

			if (!$this->user->checkUserEmail($this->email)) {
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

		if ($this->expectsPost(array('email', 'lastname',
			'firstname', 'token'))) {

			// If the email doesn't exists yet
			if (!$this->userfb->checkUserEmail($this->email)) {

				/**
				 * Signup attempt
				 * TODO: Can this fail ? If so, under which circonstances ? If not,
				 * remove the if, if yes, provide a else with a dedicated response
				 * code.
				 */
				if ($this->userfb->signup($this->email, $this->lastname, $this->firstname, $this->token)) {

					$result['success'] = "signup";
					$result['thanks'] = $this->load->view('modal/sign-up-success', null, true);
				}

			// The email was already in the db, so we try to log the user
			// using a potentially existing account
			} else if ($this->userfb->login($this->email, $this->token)) {

				$result['success'] = "signin";
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
			'country')) && strlen($this->password) < 512) {

			$result = array();

			//If the email isn't already in used
			if (!$this->user->checkUserEmail($this->email)) {

				$this->name = str_replace(".", " ", $this->name);
				$this->firstname = str_replace(".", " ", $this->firstname);

				// Create the account
				if ($this->user->signup(
						$this->email, $this->password, $this->name, $this->firstname,
						$this->country)) {

					$result['success'] = true;
					$result['thanks'] = $this->load->view('modal/sign-up-success', null, true);

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

			$result = array();

			//We don't send the token over the network, we just
			//make sure that a token has been generated.
			//The token will be transfered in an email.
			$resetToken = $this->user->askResetPassword($this->email);

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

		if ($this->expectsPost(array('resetToken', 'password')) && strlen($this->password) < 512) {

			$result = array();

			//Attempting to reset the password given the token and the
			//new password
			if ($this->user->resetPassword($this->resetToken, $this->password)) {

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

			// include manually module library - SendInBlue API
			require_once (APPPATH . '../vendor/autoload.php');

			$sendInBlueConfig = SendinBlue\Client\Configuration::getDefaultConfiguration()->setApiKey('api-key', getenv('SIB_API_KEY'));

			$this->sendInBlueEmailsAPI = new SendinBlue\Client\Api\TransactionalEmailsApi(
				new GuzzleHttp\Client(),
				$sendInBlueConfig
			);

			$result['success'] = false;

			try {
				$sibResponse = $this->sendInBlueEmailsAPI->sendTransacEmail(new \SendinBlue\Client\Model\SendSmtpEmail([
					'subject' => "Contact information",
					'sender' => ['name' => $this->name . ' [' . $this->email . ']', 'email' => 'contact@toolwatch.io'],
					'replyTo' => ['name' => $this->name, 'email' => $this->email],
					'to' => [[ 'name' => 'Marc Montagne', 'email' => 'marc@toolwatch.io']],
					'htmlContent' => $this->message
				 ]));
	
				log_message('info', 'SIB email: ' . print_r($sibResponse, true));
				
				$result['success'] = true;
				
			} catch (Exception $e) {
				error_log(json_encode(['subject' => "Contact information",
					'sender' => ['name' => $this->name . ' [' . $this->email . ']', 'email' => 'contact@toolwatch.io'],
					'replyTo' => ['name' => $this->name, 'email' => $this->email],
					'to' => [[ 'name' => 'Marc Montagne', 'email' => 'marc@toolwatch.io']],
					'htmlContent' => $this->message
				]));
			}
			
			echo json_encode($result);
		}
	}
}
