<?php

/**
Document Indexes
**/
class Indexes extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    public function getList()
    {
        $this->load->database();

        $strSQLF = "SELECT * FROM `kotul_doc_index` ";

        return $this->db->query( $strSQLF )->result_array();
    }

    public function getItem( $idxNum)
    {
        $this->load->database();

        $strSQLF = "SELECT * FROM `kotul_doc_index` where `num` = {$idxNum}  ";

        return $this->db->query( $strSQLF )->result_array();
    }
}
?>
