<?php

/**
 Thread, 게시판의 '글'과 같은 개념.
**/
class topics extends CI_Model
{
    protected $strTableName = 'kotul_composition';

    function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    public function getTopicsByForumID( $intFnum, $intPage = 1, $intPerPage = 30 )
    {
        $this->load->database();
        $strSQLF = "SELECT * FROM `{$this->strTableName}` where `root_mo_num` = %d limit %d, %d";

        $intStartItem = ( ($intPage - 1) * $intPerPage );

        return $this->db->query(
            sprintf($strSQLF, $intFnum, $intStartItem, $intPerPage )
        )->result_array();
    }

    public function getTopics4ListByForumID( $intFnum, $intPage = 1, $intPerPage = 30 )
    {
        $this->load->database();
        $strSQLF = "SELECT `a`.*, `b`.`name`, `b`.`photo` FROM
        `{$this->strTableName}` as `a` left join `kotul_users` as `b` on(`b`.`id` = `a`.`user_id`)
        where `a`.`root_mo_num` = %d limit %d, %d";

        $intStartItem = ( ($intPage - 1) * $intPerPage );

        return $this->db->query(
            sprintf($strSQLF, $intFnum, $intStartItem, $intPerPage )
        )->result_array();
    }

    public function getTopics4List(  $intPage = 1, $intPerPage = 30 )
    {
        $this->load->database();
        $strSQLF = "SELECT `a`.*, `b`.`name`, `b`.`photo`, `c`.`forum_name` FROM
        `{$this->strTableName}` as `a` left join `kotul_users` as `b` on(`b`.`id` = `a`.`user_id`) left join `kotul_forums` as `c` on (`a`.`root_mo_num` = `c`.`fo_num`) WHERE `root_mo` = 'forum' limit %d, %d";

        $intStartItem = ( ($intPage - 1) * $intPerPage );

        return $this->db->query(
            sprintf($strSQLF,  $intStartItem, $intPerPage )
        )->result_array();
    }

    public function getSingleTopic( $topicNum )
    {
        $this->load->database();
        $strSQLF = "SELECT * FROM `{$this->strTableName}` where `tp_num` = %d ";

        $topics =  $this->db->query(
            sprintf($strSQLF, $topicNum )
        )->result_array();

        if( count($topics) > 0 )
        {
            return $topics[0];
        }else{
            return null;
        }
    }

    public function addNewTopic( $intForum , $userID ,$strTitle, $content, $isSticky = false )
    {
        $this->load->database();
        $strSQLF = " INSERT INTO `{$this->strTableName}`( `root_mo`,`root_mo_num`, `user_id`, `cp_title`, `cp_text`, `is_sticky`, `date_created`, `date_updated`) VALUES ('forum', %d ,'%s', '%s', '%s', %d , '%s', '%s' ) ";


        $this->db->query(
            sprintf($strSQLF, $intForum, $userID, addslashes($strTitle), addslashes($content), ($isSticky)?'1':'0', date('Y-m-d h:i:s'), date('Y-m-d h:i:s') )
        );
    }

    public function updateTopic( $intTPNUM ,$intForum ,$strTitle, $content, $isSticky = false )
    {
         $this->load->database();
        $strSQLF = "
        UPDATE `{$this->strTableName}` SET
        `root_mo_num` = %d, `cp_title` = '%s', `cp_text` = '%s',
        `is_sticky` = %d, `date_updated` = '%s'
        WHERE `tp_num` = %d
        ";

         $this->db->query(
            sprintf($strSQLF, $intForum, addslashes($strTitle), addslashes($content), ($isSticky)?'1':'0', date('Y-m-d h:i:s'), $intTPNUM )
        );
    }
}

?>
