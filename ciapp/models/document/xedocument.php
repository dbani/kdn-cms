<?php

class Xedocument extends CI_Model
{
    function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    public function getOne( $intNum )
    {
        $sql = "SELECT `docs`.* from `kdn_documents` as `docs` where `docs`.`document_srl` = {$intNum} ";
        return $this->db->query($sql)->result_array();
    }

    /**
    module_srl이 같은 놈을 뽑잖니?
    **/
    public function getListByModule ( $intModuleSRL )
    {
        $sql = "SELECT `docs`.`document_srl`, `docs`.`title`, `docs`.`nick_name`, `docs`.`email_address` , `files`.`uploaded_filename`
        from `kdn_documents` as `docs` left join `kdn_files` as `files` on ( `files`.`upload_target_srl` = `docs`.`document_srl`) where `docs`.`module_srl` = {$intModuleSRL}  group by `docs`.`document_srl` order by `docs`.`document_srl` desc";

        return $this->db->query($sql)->result_array();
    }
}
?>
