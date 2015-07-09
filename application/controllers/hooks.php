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

			if($text === "Jack nbusers"){
				
				$result["text"] = $this->user->count_all() . ". " . $quote;

			}else if($text === "Jack nbmeasures"){

				$result["text"] = $this->measure->count_all() . ". " . $quote;

			}else if($text === "Jack nbwatches"){

				$result["text"] = $this->watch->count_all() . ". " . $quote;

			}else if($text === "Jack help"){

                $result["text"] = "Jack nbusers; Jack nbmeasures; Jack nbwatches" . ". " . $quote;

            }

			echo json_encode($result);
		}

		
	} 

}