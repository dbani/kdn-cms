<?php

class gallery extends CI_Controller
{
    protected $strGallTable = 'kotul_galls';
    protected $strCompTable = 'kotul_composition';

    function __construct()
    {
        parent::__construct();
        $this->load->database();
    }


}
?>
