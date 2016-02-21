<?php

class testdb extends CI_Controller
{
    public function t1()
    {
        $this->load->model('composition');

        print_r($this->composition->gets('forum',2, 1,30));
    }
}
