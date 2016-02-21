<?php

class filebx extends CI_Controller
{

    public function upload()
    {

        $pathToFiles_base = FCPATH."files/uploads/";

        $this->load->model('fileindex');

        $fileItemm = $_FILES['file'];
        //print_r($fileItemm);
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

        $fileItemm['actual_path'] = str_replace(FCPATH,DIRECTORY_SEPARATOR,$pathToFiles);
        $fileItemm['actual_path'] = base64_encode($fileItemm['actual_path']);

        $_splitMIME = explode('/', $fileItemm['type'] );

        $fileItemm['filetype'] = $_splitMIME[0];

        //$fileItemm['tmp_name'] = base64_encode($fileItemm['tmp_name']);
        unset($fileItemm['tmp_name']);

        echo json_encode( $fileItemm );
    }

    /**
    기존 파일 리스트
    **/
    public function getList()
    {
        $intCpID = $this->input->get_post('cpid');
        $strCpType = $this->input->get_post('cptype');

        $pathToFiles_base = FCPATH."files/uploads/";

        $this->load->model('fileindex');

        $listAttaList = $this->fileindex->getAttachedList( $strCpType, $intCpID );

        $list4Printin = array();
        foreach( $listAttaList as $itemm)
        {
            $_halfMIME = explode('/',  $itemm['at_mime']);

            $item = array(
                'findex_num'=> $itemm['findex_num'],
                'actual_path' => base64_encode( $itemm['actualPath'] ),
                'filetype'=>$_halfMIME[0],
                'type'=>$itemm['at_mime'],
                'size'=>$itemm['at_size'],
                'name'=>$itemm['filename']
            );
            $list4Printin[] = $item;
        }

        echo json_encode( $list4Printin );
    }
}

?>
