<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Hooks extends CI_Controller 
{

	private $quotes = array("Did no one come to save me just because they missed me?",
    	"I can let you drown...",
    	"The world’s still the same. There’s just less in it.",
    	"Did everyone see that? Because I will not be doing it again.",
    	"Why is all the rum gone?",
    	"Close your eyes and pretend all a bad dream. That’s how I get by.",
    	"Better to not know which moment may be your last alive to be mystery of it all.",
    	"I regret nothing, ever",
    	"My tremendous intuitive sense of the female creature informs me that you are in trouble.",
    	"What a man can do and what a man can’t do.",
    	"The problem is not the problem. The problem is your attitude about the problem. Do you understand?",
    	"The seas may be rough, but I am the Captain! No matter how difficult I will always prevail.",
    	"This is the day you will always remember as the day you almost caught Captain Jack Sparrow",
    	"Wherever we want to go, we go...",
    	"Why fight when you can negotiate?",
    	"If you choose to lock your heart away, you’ll lose it for certain",
    	"You’ve stolen me and I’m here to take myself back",
    	"Not all treasure is silver and gold mate",
    	"My spirit will live on");

    function __construct()
    {
        parent::__construct();   
        $this->load->model('watch');
        $this->load->model('measure');
    }

    function index()
	{

		if($this->input->post('token') === "bPiAi9XNEa3p9FF1lQnZfuUY"){

			$text = $this->input->post('text');
			$quote = $this->quotes[rand ( 0 , 18 )];
			$result["text"] = $quote;

			if($this->startsWith($text,"Jack nbusers")){

                if($text === "Jack nbusers"){
                   
                    $result["text"] = $this->user->count_all() . ". " . $quote;
                
                }else{

                    $time = str_replace("Jack nbusers ", "", $text);

                    $periodBefore = $this->user->select("userId")
                        ->where("registerDate >=",  (time() - $time * 2 * 60 * 1000))
                        ->where("registerDate <=", time() - $time * 60 * 1000)
                        ->find_all();

                    $period = $this->user->select("userId")
                        ->where("registerDate >=",  (time() - $time * 60 * 1000))
                        ->find_all();

                    $result["text"] = sizeof($period) . " (" . (\
                        sizeof($period) - sizeof($periodBefore)) . "). " . $quote;

                }

			}else if($this->startsWith($text,"Jack nbmeasures")){

                if($text === "Jack nbmeasures"){
                   
				    $result["text"] = $this->measure->count_all() . ". " . $quote;

                }else{

                    $time = str_replace("Jack nbmeasures ", "", $text);

                    $periodBefore = $this->measure->select("id")
                        ->where("accuracyReferenceTime >=",  (time() - $time * 2 * 60 * 1000))
                        ->where("accuracyReferenceTime <=", time() - $time * 60 * 1000)
                        ->find_all();


                    $period = $this->measure->select("id")
                        ->where("accuracyReferenceTime >=",  (time() - $time * 60 * 1000))
                        ->find_all();

                    $result["text"] = sizeof($period) . " (" . (\
                        sizeof($period) - sizeof($periodBefore)) . "). " . $quote;

                }

			}else if($this->startsWith($text,"Jack nbwatches")){

				$result["text"] = $this->watch->count_all() . ". " . $quote;

            }else if($this->startsWith($text,"Jack whois")){

                $user = $this->user->select(" user.userId, user.name, firstname, 
                    DATE_FORMAT(FROM_UNIXTIME(`registerDate`), '%e %b %Y') AS 'register',
                    DATE_FORMAT(FROM_UNIXTIME(`lastLogin`), '%e %b %Y') AS 'lastLogin'", false)
                    ->find_by('email', str_replace("Jack whois ", "", $text));

                if($user){
                    $watches = $this->watch->getWatches($user->userId);
                    $measures = $this->measure->getMeasuresByUser($user->userId, $watches);

                    $result["text"] = "Id " . $user->userId . ", Name " . $user->name . 
                        " ,Firstname " . $user->firstname . " ,Register " . $user->register .
                        " ,LastLogin " . $user->lastLogin . " ,Watches " . sizeof($watches) . 
                        " ,Measures " . sizeof($measures);

                }else{
                    $result["text"] = "User not found. " . $this->db->last_query();
                }

			}else if ($this->startsWith($text,"Jack help")){

                $result["text"] = "Jack nbusers (time); Jack nbmeasures (time); Jack nbwatches; Jack whois email" . ". ";

            }

			echo json_encode($result);
		}

		
	} 

    private function startsWith($haystack, $needle) {
        // search backwards starting from haystack length characters from the end
        return $needle === "" || strrpos($haystack, $needle, -strlen($haystack)) !== FALSE;
    }

    private function endsWith($haystack, $needle) {
        // search forward starting from end minus needle length characters
        return $needle === "" || (($temp = strlen($haystack) - strlen($needle)) >= 0 && strpos($haystack, $needle, $temp) !== FALSE);
    }

}