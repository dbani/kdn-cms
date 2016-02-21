<?php

class edit extends CI_Controller
{
    public function _remap($method, $params = array())
    {
        //print( urldecode($method) );
        if( $method === 'index' )
        {
            $this->e( $params[0] );
        }
        else if(!method_exists( $this, $method ))
        {
            $this->e( $method );
        }
        else
        {
            $this->$method($params);
        }
    }

     /*******
    *
    * 문서 편집 및 새항목 작성
    *
    **/
    protected function e( $strDocName = '' )
    {
        $strEntryName = rawurldecode($strDocName);

        $this->load->driver('longonya');
        $this->longonya->conf->setTitle('KOTUL.NET - '.$strEntryName." - 편집하기");
        $this->load->library('kdnAuth');

        $userInfo = $this->kdnauth->getCurrentUserInfo();

        $this->longonya->conf->setOpt('skinTheme','kdn');

        $this->load->model('document/Lgndocument','lgndoc');

        $strDocInfo = $this->lgndoc->getbyName($strEntryName);

        if( !isset( $userInfo['id'] ) ||$userInfo['id'] == '' )
        {
            //echo $this->longonya->loadDocPaget('lgnUI/doc_editor'
            show_error('You have no permission to do that.');
            return;
        }


        echo $this->longonya->loadDocPaget('lgnUI/doc_editor', array('docTitle'=>$strEntryName,'docContent'=>$strDocInfo['contentText'],'docNum'=>$strDocInfo['typeval'], 'entry_num'=> $strDocInfo['entry_num'], 'userInfo'=>$userInfo )
                                          );

    }

}

?>
