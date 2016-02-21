<?php

/**
Shows One Topic Article, and its comments.
**/
class postlist extends CI_Controller{

    public function _remap($method, $params = array())
    {
        //print( urldecode($method) );
        if( isset($method) && is_numeric($method) ){
            //토픽 리스팅
            $this->postListing( intval($method) );
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

    public function postListing( $topicNum, $intPage = 1 )
    {
        $this->load->driver('longonya');
        $this->load->library('kdnAuth');

        $this->load->model('wktforum/topics');
        $this->load->model('comment/comments','dcomm');

        $userInfo = $this->kdnauth->getCurrentUserInfo();

        $arrInput = array();
        $arrInput['userInfo'] = $userInfo;
        $arrInput['topicinfo'] = $this->topics->getSingleTopic($topicNum);

        if( !is_array($arrInput['topicinfo']))
        {
            show_error('이 토픽 혹은 게시글은 아직 만들어지지 않았습니니다.');return;
        }

        $arrInput['comments'] = $this->dcomm->getCommentsEnhanced('forum', $topicNum );


        $this->longonya->conf->setOpt('skinType','default');
        $this->longonya->conf->setOpt('skinTheme','kdn');

        echo $this->longonya->loadDocPaget(
             'kdnforum/postlist', $arrInput
         );

    }
}
?>
