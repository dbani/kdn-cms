<?php

class category extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getListOnly()
    {
        $strQuery = " SELECT * FROM `kotul_doc_category` ";

        $this->load->database();

        return $this->db->query( $strQuery )->result_array();
    }

    public function add( $strName, $strRoot = 0 )
    {
        $strQueryF = "INSERT IGNORE INTO `kotul_doc_category` SET `cat_name` = '%s', `root_cat_num` = %d ";
        $this->load->database();

        $this->db->query( sprintf($strQueryF, $strName, $strRoot) );
    }


    /////

    /**
    위키 엔트리 아이디 별로 관계 목록을 봅니다.
    */
    public function getRelListByEntryID( $intEntryID )
    {
        $strSqlf = "SELECT `a`.`idx_dc_num`, `a`.`doc_num`, `a`.`cat_num`, `b`.`cat_name`
FROM `kotul_doc_cat` as `a`
left join `kotul_doc_category` as `b`
on ( `a`.`cat_num` = `b`.`cat_num` )
WHERE `a`.`doc_num` = %d";

        $this->load->database();

        return $this->db->query( sprintf($strSqlf, $intEntryID) )->result_array();
    }

    public function addRel( $doc_num, $cat_num )
    {
        $strSQLf = "INSERT INTO `kotul_doc_cat`( `doc_num`, `cat_num`) VALUES ( %d, %d)";

        $this->load->database();

        $this->db->query( sprintf($strSQLf, $doc_num, $cat_num ) );
    }

    public function deleteRel ( $relNum )
    {
        $strSQLf = "DELETE FROM `kotul_doc_cat` WHERE `idx_dc_num` = %d ";

        $this->load->database();

        $this->db->query( sprintf($strSQLf, $relNum ) );
    }

}
?>
