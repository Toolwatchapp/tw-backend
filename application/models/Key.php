<?php if (!defined('BASEPATH')) {exit('No direct script access allowed');
}

/**
 * Manages API keys
 */
class Key extends MY_MODEL {

  /**
	 * Default constructeur
	 */
	function __construct() {
		parent::__construct();
		$this->table_name = "keys";
    $this->load->config("rest");
	}

	/**
	 * Generate a key for a given user
	 * @param User $user
	 * @return String generated key
	 */
  public function generate_key($user)
  {
			$finalKey = false;

      do
      {
          // Generate a random salt
          $salt = base_convert(bin2hex($this->security->get_random_bytes(64)), 16, 36);

          $new_key = substr($salt, 0, config_item('rest_key_length'));
      }
      while ($this->key_exists($new_key));

      $data[config_item('rest_key_column')] = $new_key;
      $data['date_created'] = function_exists('now') ? now() : time();
      $data['user_id'] = $user->userId;

      if(($this->update_where("user_id", $user->userId, $data) === true
        && $this->affected_rows() === 1) ||
        ($this->insert($data) !== false
        && $this->affected_rows() === 1)){

          $finalKey = $new_key;
      }

			return $finalKey;
  }

	/**
	 * Checks if a key already exists
	 * @param  String $key
	 * @return boolean
	 */
  private function key_exists($key)
  {
      return $this->count_by(config_item('rest_key_column'), $key) > 0;
  }

}
