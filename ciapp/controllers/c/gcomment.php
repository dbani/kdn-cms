<?php


class gcomment extends CI_Controller
{
    public function addNew()
    {
        $strContent = $this->input->get_post('content');
        $strType = $this->input->get_post('type');
        $intTGNumber = $this->input->get_post('target');

        $this->load->library('kdnAuth');
        $userInfo = $this->kdnauth->getCurrentUserInfo();

        //권한에 따라 거르기
        if( !isset($userInfo['id']) || $userInfo['id'] =='' )
        {
            $this->output->set_status_header('401');
            echo "비로그인 사용자는 댓글을 달 수 없습니다.";
            return;
        }

        if( strlen(trim($strContent)) == 0 )
        {
            $this->output->set_status_header('400');
            echo "내용이 비어있는 댓글을 쓰실 수는 없습니다.";
            return;
        }

        $this->load->model('comment/comments', 'dcomm');

        $this->dcomm->writeNewComment($strType,$intTGNumber,0,$userInfo['id'],$strContent);
    }
}

?>
