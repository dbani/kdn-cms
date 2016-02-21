<?php

class forums extends CI_Model
{
    protected $strForumTable = 'kotul_forums';
    protected $strTopicTable = 'kotul_composition';

    function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    public function getForums()
    {
        //
        $this->load->database();
        $strSQL0 = "SELECT `fo_num`, `parent_fo_num`, `forum_name`, `forum_desc` FROM `{$this->strForumTable}` where `is_deleted` = 0";

        return $this->db->query( $strSQL0 )->result_array();
    }

    public function getForums4Listing()
    {
        $listThat = $this->getForums();

        foreach( $listThat as $idx => &$ThatItem )
        {
            $sqlf =  "SELECT  `cp_title`, `date_created`, `date_updated` FROM `{$this->strTopicTable}` WHERE `root_mo_num` = %d order by `date_updated` desc limit 0,1";

            $recentGul = $this->db->query( sprintf($sqlf, $ThatItem['fo_num']) )->result_array();

            if( count($recentGul) > 0 )
            {
                $ThatItem['recentPost'] = $recentGul[0];
            }
        }

        return $listThat;
    }

    public function newForum( $intRoot, $strName, $strDesc = '예선호는 포럼이 아니긔!' )
    {
        $this->load->database();
        $strSQL0 = "INSERT INTO `{$this->strForumTable}` SET
        `parent_fo_num` = %d, `forum_name` = '%s',
        `forum_desc` = '%s' ";

        $this->db->query( sprintf($strSQL0, $intRoot, $strName, $strDesc ) );
    }

    public function modForum()
    {

    }

    public function getForumInfo( $intFoNum )
    {
        $sqlf =  "SELECT  * FROM `kotul_forums` WHERE `fo_num` = %d ";

        $lstFo = $this->db->query(
            sprintf( $sqlf, $intFoNum )
        )->result_array();

        if( count($lstFo) > 0 )
        {
            return $lstFo[0];
        }else{
            return null;
        }
    }
}

?>
