<?php if (!defined('BASEPATH')) {exit('No direct script access allowed');
}

/**
 * Hooks controller.
 *
 * The Hooks controllers handle different hooks. As of now:
 *
 * - default hook (on index) Jack slack bot
 * - email hook to compute and send automated email.
 */
class Hooks extends CI_Controller {

	/**
	 * Captain Jack (slack bot) quotes.
	 *
	 * TODO: This could go to a dedicated config file or even a csv.
	 *
	 * @var array
	 */
	private $quotes = array(
		"Did no one come to save me just because they missed me?",
		"I can let you drown",
		"The world’s still the same. There’s just less in it",
		"Did everyone see that? Because I will not be doing it again",
		"Why is all the rum gone?",
		"Close your eyes and pretend all a bad dream. That’s how I get by",
		"Better to not know which moment may be your last alive to be mystery of it all",
		"I regret nothing, ever",
		"My tremendous intuitive sense of the female creature informs me that you are in trouble",
		"What a man can do and what a man can’t do",
		"The problem is not the problem. The problem is your attitude about the problem. Do you understand?",
		"The seas may be rough, but I am the Captain! No matter how difficult I will always prevail",
		"This is the day you will always remember as the day you almost caught Captain Jack Sparrow",
		"Wherever we want to go, we go",
		"Why fight when you can negotiate?",
		"If you choose to lock your heart away, you’ll lose it for certain",
		"You’ve stolen me and I’m here to take myself back",
		"Not all treasure is silver and gold mate",
		"My spirit will live on"
	);

	function __construct() {
		parent::__construct();
		$this->load->model('watch');
		$this->load->model('measure');
	}

	/**
	 * Default hook used by the Cpt Jack slackbot
	 *
	 * Supported commands are:
	 *
	 * - Jack nbusers
	 * - Jack nbmeasures
	 * - Jack nbwatches
	 * - Jack whois `email`
	 * - Jack help
	 *
	 * Each command results will be followed by a quote.
	 *
	 * @return String command response in a json format as per slack
	 * specifications.
	 */
	function index() {

		//FIXME: The token has to be env value
		if ($this->input->post('token') === "bPiAi9XNEa3p9FF1lQnZfuUY") {

			$text           = $this->input->post('text');
			$quote          = $this->quotes[rand(0, 18)];
			$result["text"] = $quote;

			log_message("info", print_r($text, true));

			if (startsWith($text, "Jack nbusers")) {

				$result["text"] = $this->user->count_all().". ".$quote;

			} else if (startsWith($text, "Jack nbmeasures")) {

				$result["text"] = $this->measure->count_all().". ".$quote;

			} else if (startsWith($text, "Jack nbwatches")) {

				$result["text"] = $this->watch->count_all().". ".$quote;

			} else if (startsWith($text, "Jack whois")) {

				if(strpos($text, "<mailto:") !== false){

					log_message("info", "Incoming from slack");

					$text = substr($text, strpos($text, "|")+1);
					$text = str_replace(">", "", $text);
				}

				$text = str_replace("Jack whois ", "", $text);

				log_message("info", "Computed email " . print_r($text, true));
				$user = $this->user->select(" user.userId, user.name, firstname,
                    DATE_FORMAT(FROM_UNIXTIME(`registerDate`), '%e %b %Y') AS 'register',
                    DATE_FORMAT(FROM_UNIXTIME(`lastLogin`), '%e %b %Y') AS 'lastLogin'", false)
				->find_by('email', $text);

				log_message("info", print_r($this->db->last_query(), true));
				log_message("info", print_r($user, true));

				if (is_object($user)) {

					$measures = $this->measure->getMeasuresByUser($user->userId);

					$result["text"] = "ID;Name;Firstname;LastLogin;register \r\n".
					"```". $user->userId . ";" . $user->name . ";" . $user->firstname .
					 ";" . $user->register . ";" . $user->lastLogin . "```\r\n"
					 . "Dashboard\r\n"
					 . "ID;WatchName;WatchBrand;Measure 1 (UTC);Measure 2 (UTC);Accuracy;status\r\n";

						if($measures){
							foreach ($measures as $measure) {
							 $result["text"] .= '```'.$measure->id . ";" . $measure->name
							 . ";" . $measure->brand
							 . ";" . $measure->measureReferenceTime
							 . ";" . $measure->accuracyReferenceTime;

							 if($measure->statusId == 1.5){
								 $result["text"] .=  ";" . "In ".$measure->accuracy." hours";
								 $result["text"] .=  ";" . "Waiting accuracy";
							 }else if($measure->statusId == 1){
								 $result["text"] .=  ";" . "TBD";
								 $result["text"] .=  ";" . "Waiting accuracy";
							 }else if($measure->statusId == 2){
								 $result["text"] .=  ";" .$measure->accuracy;
								 $result["text"] .=  ";" . "Done";
							 }else{
								 $result["text"] .=  ";" . "TBD";
								 $result["text"] .=  ";" . "Never measured";
							 }
							 $result["text"] .= "```\r\n";
						 }

						}
				} else {
					$result["text"] = "User not found. ".$quote;
				}

			} else if (startsWith($text, "Jack help")) {

				$result["text"] = "`Jack nbusers` ; `Jack nbmeasures` ; `Jack nbwatches`; `Jack whois email`.";

			}

			echo json_encode($result);
		}

	}

	/**
	 * Email hook.
	 *
	 * Compute and send automated email at $time
	 *
	 * @param  String $key  authorization key
	 * @param  int $time 	hours from now to compute the emails. Only used
	 * for testing. Compute the email in the future.
	 */
	public function email($key, $time = 0){

		//FIXME: The token has to be env value
		if ($key === "bPiAi9XNEa3p9FF1lQnZfuUY") {

			$this->load->library("auto_email");
			$this->auto_email->cronCheck(60*60*$time);
		}
	}

	public function reset_email($key){

		//FIXME: The token has to be env value
		if ($key === "bPiAi9XNEa3p9FF1lQnZfuUY") {

			$emailBatch = new MY_MODEL("email_batch");
			$emailBatch->truncate();
			$emailBatch->insert(array("time"=>time(), "amount"=>0));


			$date = new DateTime("@".time());

			echo "<h1> Reset success. New last batch at " . $date->format('Y-m-d H:i:s') . "</h1>";

		}
	}
}
