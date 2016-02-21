<?php

class Redirects extends CI_Model
{
    public function getList()
    {
        $this->load->database();
        return $this->db->query('SELECT * FROM `kotul_doc_redirect`')->result_array();
    }

    public function getListByID( $intNum )
    {
        $this->load->database();
        return $this->db->query("SELECT * FROM `kotul_doc_redirect` where `doc_idx` = {$intNum}")->result_array();
    }

    public function getEnchantedList()
    {
        $this->load->database();
        return $this->db->query(
            "SELECT `r`.`red_num`, `doc_idx`, `r`.`title`, `i`.`title` as `redirect2` FROM `kotul_doc_redirect` as `r` left join  `kotul_doc_index` as `i` on ( `r`.`doc_idx` = `i`.`num` )"
        )->result_array();
    }

    public function modify($intRedirectNum, $strName, $strAnchor)
    {
        $this->load->database();
        $strSQLF = " update `kotul_doc_redirect` SET `title` = '%s', `anchor` = '%s' WHERE `red_num` = %d ";
        $this->db->query( sprintf($strSQLF, $strName, $strAnchor, $intRedirectNum ));

    }

    public function remove($intRedirectNum)
    {
        $this->load->database();
        $strSQLF = " delete from `kotul_doc_redirect` WHERE `red_num` = %d ";
        $this->db->query( sprintf($strSQLF, $intRedirectNum ));
    }
}
?>
