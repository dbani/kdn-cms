<?php

/* 포럼 자체의 리스트를 보여줍니다. */
class forumlist extends CI_Controller
{
    public function index()
    {
        $this->load->driver('longonya');
        $this->longonya->conf->setTitle('KOTUL.NET - 포럼들');
        $this->load->library('kdnAuth');

        $this->load->model('wktforum/forums');

        $arrInput = array();
        $arrInput['forumLst'] = $this->forums->getForums4Listing();

        $this->longonya->conf->setOpt('skinType','default');
        $this->longonya->conf->setOpt('skinTheme','kdn');

         echo $this->longonya->loadDocPaget(
             'kdnforum/forumlist', $arrInput
         );
    }

}
?>
