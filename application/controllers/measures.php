<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Measures extends MY_Controller 
{
    public function __construct()
	{
        $this->_needLoggedIn = true;
		parent::__construct();
        $this->load->model('watch');
        $this->load->model('measure');
	}

    public function removeDuplicate($key){
        if($key === "TMGfrXeb6WvCgNAjeKd4"){
            $this->measure->removeDuplicate();
        }else{
            echo "Unauthorized";
        }
    }
	
    public function index()
    {       
        if($this->input->post('addWatch'))
        {
            $brand = $this->input->post('brand');
            $name = $this->input->post('name');
            $yearOfBuy = $this->input->post('yearOfBuy');
            $serial = $this->input->post('serial');
            $caliber = $this->input->post('caliber');
            
            if($this->watch->addWatch($this->session->userdata('userId'), $brand, $name, $yearOfBuy, $serial, $caliber))
            {
                $this->_bodyData['success'] = 'Watch successfully added!';
            }
            else
            {
               $this->_bodyData['error'] = 'An error occured while adding your watch.';
            }
        }
        else if($this->input->post('deleteMeasures'))
        {
            $measureId = $this->input->post('deleteMeasures');
            
            if($this->measure->deleteMesure($measureId))
            {
                $this->_bodyData['success'] = 'Measures successfully deleted!';
            }
            else
            {
               $this->_bodyData['error'] = 'An error occured while deleting your measures.';
            }
            
        }
        else if($this->input->post('deleteWatch'))
        {
            $watchId = $this->input->post('deleteWatch');
            
            if($this->watch->deleteWatch($watchId))
            {
                $this->_bodyData['success'] = 'Watch successfully deleted!';
            }
            else
            {
               $this->_bodyData['error'] = 'An error occured while deleting your watch.';
            }
        }
        
        $this->_headerData['headerClass'] = 'blue';
        $this->load->view('header', $this->_headerData);
        
        $this->_bodyData['watches'] = $this->watch->getWatches($this->session->userdata('userId'));
        $this->_bodyData['allMeasure'] = $this->measure->getMeasuresByUser($this->session->userdata('userId'), 
            $this->_bodyData['watches']);
        
        $this->load->view('measure/all', $this->_bodyData);    
        
        $this->load->view('footer');  
    }
    
    public function new_watch()
    {    
        $this->_headerData['headerClass'] = 'blue';
        $this->load->view('header', $this->_headerData);
        
        $this->load->view('measure/new-watch', $this->_bodyData);    
        
        $this->load->view('footer');  
    }
    
    public function new_measure()
    {

        $this->_headerData['headerClass'] = 'blue';
        $this->load->view('header', $this->_headerData);
        
        $this->_bodyData['watches'] = $this->watch->getWatches($this->session->userdata('userId'));
        $this->load->view('measure/new-measure', $this->_bodyData);    
        
        $this->load->view('footer');  
    }  
    
    public function get_accuracy()
    {
        if($this->input->post('measureId') && $this->input->post('watchId'))
        {

            $this->_headerData['headerClass'] = 'blue';
            $this->load->view('header', $this->_headerData);
        
            $this->_bodyData['selectedWatch'] = $this->watch->getWatch($this->input->post('watchId'));
            $this->_bodyData['measureId'] = $this->input->post('measureId');
            $this->load->view('measure/get-accuracy', $this->_bodyData);    
        
            $this->load->view('footer');  
                            
        }
        else
        {
            redirect('/measures/');
        }
    }
}