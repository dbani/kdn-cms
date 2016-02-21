<?php

class galls extends CI_Model
{
    protected $strTableName = 'kotul_galls';

    function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    public function gets()
    {
        $this->db->select( ' * ' );
        $this->db->where('is_deleted', 0 );
        return $this->db->get($this->strTableName)->result_array();
    }

    public function getOne( $intGallNo )
    {
        $this->db->select( ' * ' );
        $this->db->where('is_deleted', 0 );
        $this->db->where('gall_num', $intGallNo );
        $lstt =  $this->db->get($this->strTableName)->result_array();
        if( count( $lstt ) > 0 )
        {
            return $lstt[0];
        }
        else
        {
            return null;
        }
    }
}

?>
