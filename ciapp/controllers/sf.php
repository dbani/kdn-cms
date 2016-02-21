<?php

/**
사이드파일스.
**/
class sf extends CI_Controller
{
    public function upload()
    {
        //$this->load->library('JqueryUpload/uploadhandler');

        //$this->uploadhandler->

        //print_r($_FILES);
        /*
        Array ( [file] => Array ( [name] => SIZED_IMG_6734.jpg [type] => image/jpeg [tmp_name] => C:\wamp\tmp\php403C.tmp [error] => 0 [size] => 38809 ) )
        */

        $intDocID = $this->input->post('docid');
        $styDocType = $this->input->post('doctype');

        if( $intDocID === false)
        {
            $this->output->set_status_header('400');
            print('해당 파일에 붙일 문서가 지정되지 않았습니다.');
            return;
        }

        if( $styDocType === false)
        {
            $styDocType = 'docu';
            //나머지 타입은 post, commen 등
        }

        $pathToFiles_base = FCPATH."files/uploads/";

        $this->load->model('fileindex');

        //foreach($_FILES['file'] as $fileItemm )
        //{

        $fileItemm = $_FILES['file'];
            print_r($fileItemm);
            $pathToFiles = str_replace("/", DIRECTORY_SEPARATOR,
                $pathToFiles_base.date('Ymd')."/".$fileItemm['name']
                                      );

            $pathTo = str_replace("/", DIRECTORY_SEPARATOR,
                $pathToFiles_base.date('Ymd')
                                      );

            if( file_exists( $pathToFiles ) == true )
            {
                //중복파일 있음.
                //아 몰라 그냥 덮어써!!!!!
            }else if( is_dir($pathTo) == false ){
                mkdir($pathTo,0777, true );
            }
            $boolCompletedUploaded = move_uploaded_file( $fileItemm['tmp_name'], $pathToFiles );

            if($boolCompletedUploaded == false )
            {
                $this->output->set_status_header('400');
                print('파일 '.$fileItemm['name'].'가 정상적으로 업로드되지 못했습니다.');
                continue;
            }

            $this->fileindex->register_or_update( $styDocType, $intDocID, $fileItemm['name'], $fileItemm['size'],$fileItemm['type'], $pathToFiles );
        //}
    }


    function getList()
    {
        $intDocID = $this->input->get_post('docid');
        $strDocType = $this->input->get_post('doctype');

        if( $intDocID === false)
        {
            $this->output->set_status_header('400');
            print("첨부화일을 볼 문서의 아이디가 지정되어있지 않습니다.");
            return;
        }

        if( $strDocType === false)
        {
            $this->output->set_status_header('400');
            print("첨부화일을 볼 문서의 형태가 지정되어있지 않습니다.");
            return;
        }

        $this->load->model('fileindex');

        $listAttachedFile = $this->fileindex->getAttachedList( $strDocType, $intDocID );

        foreach( $listAttachedFile as &$fileItem )
        {
            $strPathh = str_replace( FCPATH , "/" ,  $fileItem['actualPath']);
            $fileItem['src_url'] = str_replace(
                DIRECTORY_SEPARATOR, '/', $strPathh);
        }

        print(json_encode( $listAttachedFile ));

    }

    public function remove()
    {
        $intFindex = $this->input->get_post('findexid');


        if( $intFindex === false)
        {
            $this->output->set_status_header('400');
            print("첨부화일 아이디가 지정되어있지 않습니다.");
            return;
        }

        $this->load->model('fileindex');
       $this->fileindex->remove( $intFindex );
    }
}

?>
