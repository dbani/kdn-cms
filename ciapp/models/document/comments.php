<?php
/**
코멘트'들'
**/
class comments extends CI_Model
{
    function __construct()
    {
        parent::__construct();
        $this->load->database();
    }


    public function getComments( $strType, $strNum )
    {
        //
        $this->load->database();
        $strSQL0 = "SELECT `cm_num`, `root_cm_num`, `md_type`, `md_num`, `user_id`, `cm_content`, `is_deleted`, `is_blinded`, `is_freezed`, `date_creation`, `date_modified`, `date_deleted` FROM `kotul_comments` WHERE `md_type` = '%s' and `md_num` = %d and `is_deleted` = 0";

        return $this->db->query(
            sprintf($strSQL0, $strType, $strNum )
        )->result_array();
    }

    public function writeNewComment( $strType, $strNum, $rootNum, $userID, $strContents )
    {
        $this->load->database();
        $strSQL0 = "INSERT INTO `kotul_comments` set
        `root_cm_num` = %d , `md_type` = '%s',
        `md_num` = %d, `user_id` = '%s',
        `cm_content` = '%s',
        `date_creation` = '%s', `date_modified` = '%s'
        ";

        $strFinalQuery = sprintf($strSQL0, $rootNum, $strType, $strNum, $userID, addslashes($strContents), date('Y-m-d h:i:s'), date('Y-m-d h:i:s'));

        //print( $strFinalQuery );

        return $this->db->query( $strFinalQuery );
    }

    public function readSingleComment( $intNum )
    {
        $this->load->database();
        $strSQL0 = "SELECT `cm_num`, `root_cm_num`, `md_type`, `md_num`, `user_id`, `cm_content`, `is_deleted`, `is_blinded`, `is_freezed`, `date_creation`, `date_modified`, `date_deleted` FROM `kotul_comments` WHERE `cm_num` = %d ";

        $ss =  $this->db->query(
            sprintf($strSQL0, $strNum )
        )->result_array();

        if( count($ss) > 0)
        {
            return $ss[0];
        }else{
            return null;
        }
    }

    public function updateComment( $intNum, $strNetContents )
    {
         $this->load->database();
        $strSQL0 = "UPDATE `kotul_comments` SET
        `cm_content` = '%s', `date_modified` = '%s' WHERE `cm_num` = %d
        ";

        return $this->db->query(
            sprintf($strSQL0, $strNetContents, date('Y-m-d h:i:s'), $intNum )
        );
    }

    public function deleteComment( $intNum )
    {
        $this->load->database();
        $strSQL0 = "UPDATE `kotul_comments` SET
        `is_deleted` = 1, `date_deleted` = '%s' WHERE `cm_num` = %d  ";

        return $this->db->query(
            sprintf($strSQL0, date('Y-m-d h:i:s'), $intNum )
        );
    }
}

?>
