<?php

defined('BASEPATH') || exit('No direct script access allowed');

class MY_Router extends CI_Router {

    function _set_request ($seg = array())
    {
        // The str_replace() below goes through all our segments
        // and replaces the hyphens with underscores making it
        // possible to use hyphens in controllers, folder names and
        // function names
        parent::_set_request(str_replace('-', '_', $seg));
    }

}