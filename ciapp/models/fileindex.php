<?php

class fileindex extends CI_Model
{
    public function register( $charsDocType , $intDocIdx, $strFileName, $intSize, $intMIME, $strActualPath )
    {
        $this->load->database();
        $sqlf = "insert into `kotul_file_idxs`(`doc_idx`, `at_type`, `filename`, `at_size`, `at_mime`, `actualPath`) value (%d ,'%s' , '%s', %d, '%s', '%s')";

        $this->db->query(
            sprintf($sqlf, $intDocIdx, $charsDocType,  $strFileName,$intSize, addslashes($intMIME), addslashes($strActualPath) )
        );
    }

    public function find( $charsDocType , $intDocIdx, $strActualPath )
    {
        $this->load->database();

        $sqlf = "SELECT * FROM  `kotul_file_idxs` WHERE `at_type` = '%s' and `doc_idx` = %d and `actualPath` = '%s' ";


        return $this->db->query(
            sprintf($sqlf, $charsDocType, $intDocIdx, $strActualPath )
        )->result_array();
    }

     public function register_or_update( $charsDocType ,$intDocIdx, $strFileName ,$intSize, $intMIME, $strActualPath )
     {
         $listAlready = $this->find($charsDocType ,$intDocIdx, $strActualPath);
         if( count($listAlready) > 0 )
         {
             //업데이트
             return;
         }

         $this->register( $charsDocType , $intDocIdx, $strFileName,$intSize, $intMIME, $strActualPath );
     }


    public function getAttachedList( $charsDocType ,$intDocIdx )
    {
        $this->load->database();
        $sqlF = "SELECT * FROM `kotul_file_idxs` WHERE `at_type` = '%s' and `doc_idx` = %d ;";

        return $this->db->query(
            sprintf($sqlF, $charsDocType, $intDocIdx )
        )->result_array();
    }

    public function remove( $findexNum )
    {
        $this->load->database();

        $sqlF0 = "SELECT * FROM  `kotul_file_idxs` WHERE `findex_num` = %d ;";

        $listCurrentFilee = $this->db->query(
            sprintf($sqlF0, $findexNum )
        )->result_array();

        if( count( $listCurrentFilee ) == 0 )
        {
            return false;
        }

        $strActualPath = $listCurrentFilee[0]['actualPath'];

        $sqlF = "Delete FROM `kotul_file_idxs` WHERE `findex_num` = %d ;";
        $this->db->query(
            sprintf($sqlF, $findexNum )
        );

        if( file_exists( $strActualPath ) )
        {
            unlink( $strActualPath );
        }

        return true;

    }


}

?>
