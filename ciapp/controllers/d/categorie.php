<?php

class categorie extends CI_Controller
{
    public function add()
    {
        $strKatName = $this->input->get_post('name');
        $intRootNum = $this->input->get_post('rootnum');
        if( $intRootNum === false )
        {
            $intRootNum = 0;
        }

        if( $strKatName === false)
        {
            $this->output->set_status_header('400');
            echo "이름을 비운 채로 사용할 수 없습니다.";
            return;
        }

        $this->load->model('document/category', 'cats');
        $this->cats->add( $strKatName, $intRootNum );
    }

    public function listrelJSON()
    {
        $intEntryNum = $this->input->get_post('entryID');

        $this->load->model('document/category', 'cats');
        $lstRels = $this->cats->getRelListByEntryID($intEntryNum);

        print(json_encode($lstRels));
    }

    public function addrel()
    {
        $intEntryNum = $this->input->get_post('entryID');
        $intKategoryNum = $this->input->get_post('katID');

        $this->load->model('document/category', 'cats');
        $this->cats->addRel($intEntryNum, $intKategoryNum );
    }

    public function renmoverel()
    {
        $intRelNum = $this->input->get_post('relID');

        $this->load->model('document/category', 'cats');
        $this->cats->deleteRel( $intRelNum );
    }

}
?>
