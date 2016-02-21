<?php

class write extends CI_Controller
{
    /*
    public function _remap($method, $params = array())
    {
        //print( urldecode($method) );
        if( $method === 'index' )
        {
            $this->v( $params[0] );
        }
        else if(!method_exists( $this, $method ))
        {
            $this->v( $method );
        }
    }*/

    /**
    일부 코드 참고 :
    https://www.raptor-editor.com/documentation/tutorials/basic-saving
    **/
    public function updateWdoc()
    {
        $intEntryNum = $this->input->post('frmIdxNum');
        $docuContent = $this->input->post('docu_content');
        $intDocNum = $this->input->post('docID');
        $strDocTitle = $this->input->post('docTitle');

        $objContents = json_decode($docuContent, true);

        $this->load->library('kdnAuth');
        $userInfo = $this->kdnauth->getCurrentUserInfo();
        $this->load->database();
        //print_r( $userInfo );
        if( !isset($userInfo['id']) )
        {
            $this->output->set_status_header('401');
            echo "이 문서를 갱신할 수 있는 권한이 없습니다.";
            return;
        }

        $documentIDXINFO = $this->db->query("SELECT `num`, `type` from `kotul_doc_index` where `typeval` = '{$intDocNum}' ")->result_array();


        if( count( $documentIDXINFO) ==0 )
        {
            //헐 없어요! 없다구요!!
            /*
            echo json_encode(array(
                'status' => 'not-sufficient',
                'todo' => 'setNewDocument'
            ));
            return;*/

            $intNuveonNum = $this->_createNewOne( $strDocTitle, $docuContent );

            if( $intNuveonNum > 0)
            {
                echo json_encode(array(
                    'status' => 'ok',
                    'newDocID' => $intNuveonNum
                ));
            }else
            {
                echo json_encode(array(
                    'status' => 'failed'
                ));
            }

            echo json_encode(array(
                'status' => 'ok',
                'operation'=>'new_one'

            ));

            return;
        }


        if( $documentIDXINFO[0]['type'] =='docdb' )
        {
            $this->applyDocDB( $intDocNum, $docuContent );
        }
        else if( $documentIDXINFO[0]['type'] =='docufile' )
        {
            $this->applyDocFile( $intDocNum , $docuContent );
        }


        echo json_encode(array(
            'status' => 'ok'
        ));

    }

    /**
    안물어보면 디비입니다.
    **/
    protected function _createNewOne( $strTitle, $strContent, $intParentNum = 0 )
    {
        $this->load->model('document/Lgndocument', 'lgndoc');

        $intNewDocuNum = $this->lgndoc->createDocDB(
                0, $strTitle, $strContent, 1
            );

        $comp = $this->lgndoc->createIndex($strTitle, 'docdb', $intParentNum, $intNewDocuNum );

        $documentIDXINFO = $this->db->query("SELECT `num`, `type` from `kotul_doc_index` where `typeval` = '{$intNewDocuNum}' ")->result_array();

        if( count( $documentIDXINFO ) > 0 )
        {
            return $intNewDocuNum;
        }else{
            return 0;
        }
    }

    public function applyDocDB( $intDocID, $strNewContent )
    {
        $this->load->database();

        $strNewContent = addslashes($strNewContent);

        $this->db->query("UPDATE `kotul_documents` set `doc_text` = '{$strNewContent}' where `num` = {$intDocID} ");
    }

    public function applyDocFile( $strDocPath, $strNewContent )
    {
        $strDocPath = str_replace(
            array('[paget]'),
            array(APPPATH.'paget'),
            $strDocPath
        );

        //$strDocPath = str_replace();

        file_put_contents( $strDocPath, $strNewContent );
    }

    /////////
    public function updateTitle()
    {
        $intDocNumber = $this->input->get_post('docID');
        $intWikiEntryNumber = $this->input->get_post('entryID');
        $strNewName = $this->input->get_post('value');

        $this->load->database();

        $this->db->query(
            "UPDATE `kotul_doc_index` SET `title` = '{$strNewName}' where `num` = {$intWikiEntryNumber} "
        );
    }

    /////////
    public function updatePath()
    {
        $intDocNumber = $this->input->get_post('docID');
        $intWikiEntryNumber = $this->input->get_post('entryID');
        $strNewName = $this->input->get_post('value');

        $this->load->database();

        $this->db->query(
            "UPDATE `kotul_doc_index` SET `typeval` = '{$strNewName}' where `type` = 'docufile' and `num` = {$intWikiEntryNumber} "
        );
    }

    ///////////////////////////////////////////////
    // 새로운 문서를 만들 링크
    ////////////////////////////////////////////////
    public function newOne( $strEntryName )
    {
        $strNewName = rawurldecode( $strEntryName );
        $strNewName = html_entity_decode( $strNewName );

        //echo $strNewName;

        if( $strNewName == '0' || $strNewName === false )
        {
            show_error('이름없는 문서는 없습니다!.');
            return;
        }

        $this->load->library('kdnAuth');
        $userInfo = $this->kdnauth->getCurrentUserInfo();
        if( !isset($userInfo['id']) || $userInfo['id'] == '' )
        {
            show_error('편집창이라도 보려면 로그인이 필요합니다.');
            return;
        }

        $this->_createNewOne( $strNewName, '내용임. 근데 냉무.' );

        //$this->load->helper('jsredirect');
        //jsredirect('/d/edit/' + urlencode($strEntryName) );

        $this->load->driver('longonya');
        $this->longonya->conf->setTitle('KOTUL.NET');

        $this->longonya->conf->setOpt('skinType','default');
        $this->longonya->conf->setOpt('skinTheme','kdn');

        $arrInput = array(
            'doc_title' => $strNewName,
            'doc_title_url' => str_replace('+','%20',urlencode($strNewName))
        );

        echo $this->longonya->loadDocPaget(
             'lgnUI/doc_created', $arrInput
         );
    }


}
