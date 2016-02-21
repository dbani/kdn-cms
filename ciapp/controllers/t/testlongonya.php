<?php

class testlongonya extends CI_Controller
{

    public function index()
    {
        $this->load->driver('Longonya');

        //echo $this->longonya->faak('faak')."<br />";
        //echo $this->longonya->parser->faak('faak')."<br />";

        echo $this->longonya->setting->getTitle();
    }

}

?>
