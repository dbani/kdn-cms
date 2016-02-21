<?php
class wgall extends CI_Controller
{
    public function index()
    {
        $intCPNum = $this->input->get_post('cpnum');

        $this->load->driver('longonya');
        $this->load->library('kdnAuth');

        $this->load->model('wktgall/galls');
        $this->load->model('composition','composition');

        $userInfo = $this->kdnauth->getCurrentUserInfo();

        $arrInput = array();
        $arrInput['userInfo'] = $userInfo;
        $arrInput['gallsInfos'] = $this->galls->gets();

        $_targetDocs = $this->composition->getSingle( $intCPNum );
        if( is_array( $_targetDocs ) )
        {
            $arrInput['targetDoc'] = $_targetDocs;
        }

        $this->longonya->conf->setOpt('skinType','default');
        $this->longonya->conf->setOpt('skinTheme','kdn');

        echo $this->longonya->loadDocPaget(
             'kdngall/write_gall', $arrInput
         );
    }
}
?>
