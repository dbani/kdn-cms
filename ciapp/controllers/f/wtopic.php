<?php
/**
토픽(글)을 쓰는 역할.
**/
class wtopic extends CI_Controller
{
    public function index()
    {
        $intTopicNum = $this->input->get_post('topic');


        $this->load->driver('longonya');
        $this->longonya->conf->setTitle('KOTUL.NET - 글 편집');
        $this->load->library('kdnAuth');

        $this->load->model('wktforum/forums');
        $this->load->model('wktforum/topics');

        $userInfo = $this->kdnauth->getCurrentUserInfo();

        if(!isset($userInfo['id']) || $userInfo['id'] == "")
        {
            show_error('로그인한 사람들만 글 쓸 수 있습니다');
            return;
        }



        $arrInput = array();
        $arrInput['userInfo'] = $userInfo;
        $arrInput['listforums'] = $this->forums->getForums();
        $arrInput['fo_num'] = $this->input->get_post('forum');
        $arrInput['tp_num'] = $intTopicNum;
        $arrInput['editing_mode'] = 'topic';

        //토픽 편집부에 대한 블럭
        if( $intTopicNum > 0 )
        {
            $topicData = $this->topics->getSingleTopic( $intTopicNum );

            if( is_array($topicData) )
            {
                if( $userInfo['id'] != $topicData['user_id'] )
                {
                    show_error('토픽을 올린 자신만 글을 수정할 수 있습니다.');
                    return;
                }

                $arrInput['topicdata'] = $topicData;
                $arrInput['docTitle'] = $topicData['cp_title'];
                $arrInput['docContent'] = $topicData['cp_text'];
                $arrInput['fo_num'] = $topicData['root_mo_num'];
            }
        }

        $this->longonya->conf->setOpt('skinType','default');
        $this->longonya->conf->setOpt('skinTheme','kdn');
        echo $this->longonya->loadDocPaget(
             'kdnforum/write_writing', $arrInput
         );
    }

    /*
    토픽 새로 작성 Create
    **/
    public function create()
    {
        $strTitle = $this->input->get_post('title');
        $intForum = $this->input->get_post('forum');
        $strContent = $this->input->get_post('content');
        $listFiles = $this->input->get_post('files');
        $boolIsSticky = ($this->input->get_post('isSticky') == 'y');

        $this->load->library('kdnAuth');
        $this->load->model('wktforum/topics');
        $userInfo = $this->kdnauth->getCurrentUserInfo();

        if( $intForum == false || $intForum == 0 )
        {
            $this->output->set_status_header('400');
            echo "포럼을 지정하지 않았습니다.";
            return;
        }

        $this->topics->addNewTopic(
            $intForum, $userInfo['id'], $strTitle, $strContent, $boolIsSticky
        );
    }

    /**
    토픽 업데이트
    **/
    public function update()
    {
        $intTpNum = $this->input->get_post('topic');
        $strTitle = $this->input->get_post('title');
        $intForum = $this->input->get_post('forum');
        $strContent = $this->input->get_post('content');
        $listFiles = $this->input->get_post('files');
        $boolIsSticky = ($this->input->get_post('isSticky') == 'y');

        $this->load->library('kdnAuth');
        $this->load->model('wktforum/topics');

        $this->topics->updateTopic($intTpNum, $intForum, $strTitle, $strContent, $boolIsSticky );

    }


}

?>
