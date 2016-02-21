<?php

class posts extends CI_Model
{
    function __construct()
    {
        parent::__construct();
        $this->load->database();
    }
}

?>
