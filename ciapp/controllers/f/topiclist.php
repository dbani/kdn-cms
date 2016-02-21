<?php

class topiclist extends CI_Controller{

    public function _remap($method, $params = array())
    {
        //print( urldecode($method) );
        if( isset($method) && is_numeric($method) ){
            //포럼넘버로 가면 리스팅으로 가게 됩니다.
            $this->printListing( intval($method) );
        }
        else if( $method === 'index' || $method === '' )
        {
            if( isset($params[0]) && is_numeric($params[0]) )
            {
                $this->printListing( intval($params[0]) );
            }else{
                $this->printListing( 0 );
            }
        }
        else if(!method_exists( $this, $method ))
        {
            show_error('Mama, I just killed a man. Thats a lie.');
        }else{
            $this->$method($params);
        }
    }

    public function printListing( $forumNum, $intPage = 1 )
    {
        $this->load->driver('longonya');
        $this->longonya->conf->setTitle('KOTUL.NET - 토픽');
        $this->load->library('kdnAuth');

        $this->load->model('wktforum/forums');
        $this->load->model('wktforum/topics');

        $arrInput = array();

        $arrInput['foruminfo'] = $this->forums->getForumInfo( $forumNum );

        if( $forumNum > 0 )
        {
            $arrInput['topicLst'] = $this->topics->getTopics4ListByForumID($forumNum, $intPage);
        }
        else
        {
            $arrInput['topicLst'] = $this->topics->getTopics4List($intPage);
        }
        $arrInput['currentPage'] = $intPage;
        $arrInput['perPage'] = 30;

        if( !is_array($arrInput['foruminfo']) && $forumNum > 0)
        {
            show_error('유효한 포럼이 아닙니다!!');return;
        }
        $this->longonya->conf->setTitle('KOTUL.NET - 포럼:'.$arrInput['foruminfo']['forum_name']);
        $this->longonya->conf->setOpt('skinType','default');
        $this->longonya->conf->setOpt('skinTheme','kdn');

         echo $this->longonya->loadDocPaget(
             'kdnforum/topiclist', $arrInput
         );
    }

}
?>
