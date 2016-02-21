<?php

class redirect extends CI_Controller{

    public function add(){
        $intEntryID = $this->input->get_post("entryID");
        $strRedirectName = $this->input->get_post("red_name");
        $strRedirectAnchor = $this->input->get_post("red_anchor");

        $strSQLF = " insert ignore into `kotul_doc_redirect`(`doc_idx`, `title`, `anchor`) VALUES (%d, '%s', '%s') ";

        $this->load->database();
        $this->db->query(
            sprintf($strSQLF, $intEntryID, $strRedirectName, $strRedirectAnchor)
        );
    }

    public function mod()
    {
        $intRedirectID = $this->input->get_post("red_ID");
        $strRedirectName = $this->input->get_post("red_name");
        $strRedirectAnchor = $this->input->get_post("red_anchor");

        $this->load->model('document/redirects');
        $this->redirects->modify($intRedirectID, $strRedirectName, $strRedirectAnchor);

    }
    public function remove()
    {
        $intRedirectID = $this->input->get_post("red_ID");
        $this->load->model('document/redirects');
        $this->redirects->remove($intRedirectID);
    }

    public function listJSON()
    {
        $intEntryID = $this->input->get_post("entryID");

        $this->load->model('document/redirects');

        $lst = $this->redirects->getListByID( $intEntryID );

        print(json_encode($lst));
    }
}

?>
