<?php

class listing extends CI_Controller
{
    public function index()
    {
        $this->load->driver('longonya');
        $this->longonya->conf->setTitle('KOTUL.NET - 갤러리들');
        $this->load->library('kdnAuth');

        $this->load->model('wktgall/galls');

        $arrInput = array();
        $arrInput['gallLst'] = $this->galls->gets();

        $this->longonya->conf->setOpt('skinType','default');
        $this->longonya->conf->setOpt('skinTheme','kdn');

         echo $this->longonya->loadDocPaget(
             'kdngall/galllist', $arrInput
         );
    }
}

?>
