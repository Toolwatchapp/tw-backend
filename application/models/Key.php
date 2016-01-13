<?php if (!defined('BASEPATH')) {exit('No direct script access allowed');
}

class Key extends ObservableModel {

  /**
	 * Default constructeur
	 */
	function __construct() {
		parent::__construct();
		$this->table_name = "keys";
    $this->load->config("rest");
	}

  public function has_valide_key($userId, $key){
    return $this->select("count(*)")
    ->where("user_id", $userId)
    ->find_by("key", $key) > 0;
  }

  public function generate_key($user)
  {
      do
      {
          // Generate a random salt
          $salt = base_convert(bin2hex($this->security->get_random_bytes(64)), 16, 36);

          // If an error occurred, then fall back to the previous method
          if ($salt === FALSE)
          {
              $salt = hash('sha256', time() . mt_rand());
          }

          $new_key = substr($salt, 0, config_item('rest_key_length'));
      }
      while ($this->key_exists($key));

      $data[config_item('rest_key_column')] = $key;
      $data['date_created'] = function_exists('now') ? now() : time();
      $data['user_id'] = $user->userId;

      if(($this->update_where("user_id", $user->userId, $data) === true
        && $this->affected_rows() === 1)
        ||
        ($this->insert($data) !== false
        && $this->affected_rows() === 1)){

          return $key;
      }

      return false;
  }

  private function key_exists($key)
  {
      return $this->count_by(config_item('rest_key_column'), $key) > 0;
  }

}
