<?php if (!defined('BASEPATH')) {exit('No direct script access allowed');
}

require ('ObservableModel.php');

class User extends ObservableModel {
	function __construct() {
		parent::__construct();
		$this->table_name = "user";
	}

	function login($email, $password) {
		$res = false;

		$this->db->select('*')
		     ->from('user')
		     ->where('email', $email)
		     ->where('password', hash('sha256', $password));

		$event = LOGIN_EMAIL;

		if (strrpos($password, 'FB_') === 0) {
			$event = LOGIN_FB;
		}

		$query = $this->db->get();
		if ($query->num_rows() > 0) {
			$res  = true;
			$data = $query->row();
			$this->session->set_userdata('userId', $data->userId);
			$this->session->set_userdata('email', $data->email);
			$this->session->set_userdata('name', $data->name);
			$this->session->set_userdata('firstname', $data->firstname);
			$this->session->set_userdata('timezone', $data->timezone);
			$this->session->set_userdata('country', $data->country);
			$this->session->set_userdata('registerDate', $data->registerDate);

			$update = array('lastLogin' => time());

			$this->db->where('userId', $data->userId);
			$this->db->update('user', $update);

			$this->notify($event, $data);

		} else {
			$this->notify($event.'_FAIL', $data);
		}

		return $res;
	}

	function isLoggedIn() {
		$res = false;

		if ($this->session->userdata('userId')) {
			$res = true;
		}

		return $res;
	}

	function logout() {

		//Workaround for automated tests
		session_unset();

		$this->notify(LOGOUT);

		return true;
	}

	function checkUserEmail($email) {
		$res = false;
		$this->db->select('*')
		     ->from('user')
		     ->where('email', $email);

		$query = $this->db->get();
		if ($query->num_rows() > 0) {
			$res = true;
		}

		return $res;
	}

	function getUser($userId) {
		$data = array();
		$this->db->select('*')
		     ->from('user')
		     ->where('userId', $userId);

		$query = $this->db->get();
		if ($query->num_rows() > 0) {
			$data = $query->row();
		}

		return $data;
	}

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

		$this->db->insert('user', $data);

		if ($this->db->affected_rows() > 0) {
			$user         = arrayToObject($data);
			$user->userId = $this->db->insert_id();

			$this->notify($event, $user);

			$res = true;
		} else {
			$this->notify($event.'_FAIL', $user);
		}

		return $res;
	}

	function askResetPassword($email) {
		$resetToken = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 10);

		$update = array('resetToken' => $resetToken);

		$this->db->where('email', $email);
		$this->db->update('user', $update);

		$this->db->select('*')
		     ->from('user')
		     ->where('resetToken', $resetToken);

		$query = $this->db->get();
		if ($query->num_rows() <= 0) {
			$resetToken = '';
		} else {
			$data['user']            = new stdClass();
			$data['user']->name      = $this->session->userdata('name');
			$data['user']->firstname = $this->session->userdata('firstname');
			$data['user']->email     = $email;
			$data['resetToken']      = $resetToken;

			$this->notify(RESET_PASSWORD, $data);
		}

		return $resetToken;
	}

	function resetPassword($resetToken, $password) {
		$res = false;

		$update = array('resetToken' => '', 'password' => hash('sha256', $password));

		$this->db->where('resetToken', $resetToken);
		$this->db->update('user', $update);

		$this->db->select('*')
		     ->from('user')
		     ->where('password', hash('sha256', $password));

		$query = $this->db->get();
		if ($query->num_rows() > 0) {
			$res = true;
			$this->notify(RESET_PASSWORD_USE);
		}

		return $res;
	}

	function getUserFromWatchId($watchId) {
		$data = array();

		$this->db->select('*')
		     ->from('user, watch')
		     ->where('`user`.`userId`=`watch`.`userId`')
		     ->where('watchId', $watchId);

		$query = $this->db->get();
		if ($query->num_rows() > 0) {
			$data = $query->row();
		}

		return $data;
	}
}