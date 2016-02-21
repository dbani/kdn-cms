<?php

class Lgndocument extends CI_Model
{
    function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->driver('longonya');
    }

    public function getbyIndexNum( $intIdxNum )
    {
        $sql = "select `num`,`parent_num`,  `title`, `type`, `typeval` from `kotul_doc_index` where `num` = {$intIdxNum} ";

        $lstt = $this->db->query( $sql )->result_array();

        if( count( $lstt ) > 0 )
        {
            return $this->getDocumentContentFromInfo($lstt[0]);
        }
        else
        {
            return null;
        }
    }

    public function getbyName( $documentName, $language='korean' )
    {
        $documentName = html_entity_decode($documentName);
        $sql = "select `num`, `parent_num`, `title`, `type`, `typeval` from `kotul_doc_index` where `title` = '{$documentName}' ";

        //echo $sql;

        $lstt = $this->db->query( $sql )->result_array();

        if( count( $lstt ) > 0 )
        {
            $resultMap = $this->getDocumentContentFromInfo($lstt[0]);
            return $resultMap;
        }
        else
        {
            return null;
        }
    }

    public function getbyRedirectName( $strAlias, $language='korean' )
    {
        $sql = "select `i`.`num`, `i`.`title`,`r`.`title` as `from`, `type`, `typeval`
        from `kotul_doc_redirect` as `r` left join `kotul_doc_index` as `i` on(`r`.`doc_idx` = `i`.`num` ) where `r`.`title` = '{$strAlias}' ";

        $lstt = $this->db->query( $sql )->result_array();

        if( count( $lstt ) > 0 )
        {
            return $this->getDocumentContentFromInfo($lstt[0]);
        }
        else
        {
            return null;
        }
    }

    public function getDocumentContentFromInfo( $mapEntity )
    {
        if( $mapEntity['type'] == 'docdb' )
        {
            $intTVNum = intval( $mapEntity['typeval'] );
            $sql="SELECT `doc_text` FROM `kotul_documents` where `num` = {$intTVNum} ";
            //echo ($sql);
            $__sss = $this->db->query($sql)->result_array();

            $mapEntity['contentText'] = $__sss[0]['doc_text'];

        }
        else if( $mapEntity['type'] == 'docufile' )
        {
            $strAbsPathFile = $mapEntity['typeval'];

            if( strpos($strAbsPathFile, '[paget]/') !== false )
            {
                $thePath = explode('/', $strAbsPathFile );
                $this->longonya->paget->setPagetID( $thePath[1] );

                $strFname = end( $thePath );

                $lstXX = explode('.', $strFname );

                $mapEntity['contentText'] = $this->longonya->paget->getPaget( $lstXX[0] );
            }
            else
            {

                $strAbsPathFile = str_replace(
                array('[paget]/'),
                array(APPPATH.'paget/'),
                $strAbsPathFile);
                $mapEntity['contentText'] = file_get_contents($strAbsPathFile);
            }


        }

        $mapEntity['entry_num'] = $mapEntity['num'];

        return $mapEntity;
    }


    public function SetOrUpdate()
    {}

    /**
    @desc 인덱스 레코드를 생성합니다.
    **/
    public function createIndex( $strName, $strKind, $strParent , $strTypeVal )
    {
        if( $strKind != 'docdb' && $strKind != 'docufile' )
        {
            return false;
        }
        $sqlInsertIndexx = "INSERT INTO `kotul_doc_index` SET `parent_num` = '{$strParent}',
        `title` = '{$strName}',
        `type` = '{$strKind}', `typeval` = '{$strTypeVal}'
        ";

        $this->load->database();
        $this->db->query( $sqlInsertIndexx );

        return true;
    }

    /**
    데이터베이스를 생성하고 생성된 문서번호를 반환합니다.
    **/
    public function createDocDB( $module, $doc_title, $doc_text, $admin_only = 1 )
    {
        $strTitleHash = hash("sha256",$doc_title,false);

        $sqlInsertDocument = "INSERT INTO `kotul_documents` SET
        `module` = {$module}, `doc_title` = '{$doc_title}',
        `doc_title_hash` = '{$strTitleHash}', `doc_text` = '{$doc_text}', `doc_rev_num` = -1, `adminOnly` = {$admin_only} ";
        $this->load->database();
        $this->db->query( $sqlInsertDocument );

        $sqlFindNewDoc = sprintf(
            "SELECT `num` FROM `kotul_documents` where `doc_title_hash` = '%s' ",
            $strTitleHash
        );

        $newDoc = $this->db->query( $sqlFindNewDoc )->result_array();
        if( count( $newDoc) == 0)
        {
            return false;
        }

        return $newDoc[0]['num'];
    }

    public function create( $mapDocument )
    {
        $strTitleHash =  hash("sha256",$mapDocument['title'],false);
        $sqlInsertDocument = "INSERT INTO `kotul_documents` SET
        `module` = -1, `doc_title` = '{$mapDocument['title']}',
        `doc_title_hash` = '{$strTitleHash}', `doc_text` = '{{$mapDocument['text']}}', `doc_rev_num` = -1 ";

        $sqlInsertIndexx = "INSERT INTO `kotul_doc_index` SET
        `title` = '{$mapDocument['title']}',
        `type` = 'docdb', `typeval` = ''
        ";
    }


}

?>
