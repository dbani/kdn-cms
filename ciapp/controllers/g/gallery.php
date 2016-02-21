<?php

class gallery extends CI_Controller
{
    public function _remap($method, $params = array())
    {
        if( isset($method) && is_numeric($method) ){
            //갤러리 리스팅
            $this->_gallListing( intval($method) );
        }
        else if( $method === 'index' || $method === '' )
        {
            if( isset($params[0]) && is_numeric($params[0]) )
            {
                $this->postListing( intval($params[0]) );
            }else{
                show_error('예선호 이쁘지?');
            }
        }
        else if(!method_exists( $this, $method ))
        {
            show_error('Mama, I just killed a man. Thats a lie.');
        }else{
            $this->$method($params);
        }
    }

    protected function _gallListing( $gallNumber )
    {
        $this->load->driver('longonya');
        $this->load->library('kdnAuth');

        $this->load->model('wktgall/galls');
        $this->load->model('composition','composition');
        //$this->load->model('comment/comments','dcomm');

        $userInfo = $this->kdnauth->getCurrentUserInfo();

        $arrInput = array();
        $arrInput['userInfo'] = $userInfo;
        if( is_numeric( $gallNumber ) && $gallNumber > 0 ){
            $arrInput['gallInfo'] = $this->galls->getOne( $gallNumber );
        }
        else
        {
            $arrInput['gallInfo'] = array();
        }

        if( !is_array($arrInput['gallInfo']))
        {
            show_error('이 갤러리는 아직 만들어지지 않았습니니다.');return;
        }

        //$arrInput['comments'] = $this->dcomm->getCommentsEnhanced('forum', $topicNum );

        $arrInput['pictures'] = $this->composition->gets('gall', $gallNumber, 1, 30 );

        $this->longonya->conf->setOpt('skinType','default');
        $this->longonya->conf->setOpt('skinTheme','kdn');

        echo $this->longonya->loadDocPaget(
             'kdngall/piclist', $arrInput
         );
    }
}

?>
