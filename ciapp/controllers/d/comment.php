<?php

class comment extends CI_Controller
{
    public function _remap($method, $params = array())
    {
        //print( urldecode($method) );
        if( $method === 'index' )
        {
            ////$this->vc( $params[0] );
        }
        else if(!method_exists( $this, $method ))
        {
            $this->vc( $method );
        }else{
            $this->$method($params);
        }
    }

    protected function vc( $strEntryName )
    {
        $strEntryName = rawurldecode($strEntryName);

        $this->load->driver('longonya');
        $this->longonya->conf->setTitle('KOTUL.NET - '.$strEntryName.' - 댓글들');

        $this->load->library('kdnAuth');
        $this->load->model('comment/comments','dcomm');
        $this->load->model('document/Lgndocument','lgndoc');

        $userInfo = $this->kdnauth->getCurrentUserInfo();
        //$comments = $this->dcomm->getComments('wiki', );
        $mapDocInfo = $this->lgndoc->getbyName($strEntryName);

        $intDocNumber = $mapDocInfo['typeval'];
        $intwikiEntryNumber = $mapDocInfo['entry_num'];

        $comments = $this->dcomm->getComments('wiki', $intwikiEntryNumber );

        $mapInput = array();
        $mapInput['userInfo'] = $userInfo;
        $mapInput['doc_title'] = $strEntryName;
        $mapInput['doc_num'] = $intDocNumber;
        $mapInput['entry_num'] = $intwikiEntryNumber;
        $mapInput['comments'] = $comments;

        $this->longonya->conf->setOpt('skinTheme','kdn');
        echo $this->longonya->loadDocPaget('lgnUI/doc_comment', $mapInput);
    }

    public function add()
    {
        $strContent = $this->input->get_post('content');
        $intTGNumber = $this->input->get_post('docID');

        $this->load->library('kdnAuth');
        $userInfo = $this->kdnauth->getCurrentUserInfo();

        if( !isset($userInfo['id']) || $userInfo['id'] =='' )
        {
            $this->output->set_status_header('401');
            echo "비로그인 사용자는 댓글을 달 수 없습니다.";
            return;
        }

        $this->load->model('comment/comments', 'dcomm');

        $strUserID = $userInfo['id'];

        $this->dcomm->writeNewComment('wiki',$intTGNumber,0,$strUserID,$strContent);
    }
}
