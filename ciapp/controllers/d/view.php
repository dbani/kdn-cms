<?php

class view extends CI_Controller
{
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
        }else{
            $this->$method($params);
        }
    }

    /**
    view Document.
    **/
    public function v($strDocID = '')
    {

        $strEntryName = rawurldecode($strDocID);

        $this->load->driver('longonya');
        $this->longonya->conf->setTitle('KOTUL.NET - '.$strEntryName);
        $this->load->library('kdnAuth');

        $userInfo = $this->kdnauth->getCurrentUserInfo();
        //print_r($userInfo);

        $mapInput = array();
        $mapInput['userInfo'] = $userInfo;
        $mapInput['doc_title'] = $strEntryName;
        $mapInput['doc_title_url']= str_replace('+','%20',urlencode($strEntryName));


        if( $strDocID == '' )
        {
            echo $this->longonya->loadDocPaget('ysh/frontpage', $mapInput);
            return;
        }

        $this->load->model('document/category', 'cats');
        $this->load->model('document/Lgndocument','lgndoc');

        $strDocInfo = $this->lgndoc->getbyName($strEntryName);


        if( is_array( $strDocInfo ) == false )
        {
            $listFromRedirect = $this->lgndoc->getbyRedirectName( $strEntryName );//print_r( $listFromRedirect );

            $this->longonya->conf->setOpt('skinType','default');
        $this->longonya->conf->setOpt('skinTheme','kdn');

            if( is_array($listFromRedirect) == true &&
               count($listFromRedirect) > 0 )
            {

                //리다이렉트 ㄱㄱ
                $this->load->helper('jsredirect');
                jsredirect('/d/view/'.$listFromRedirect['title'].'?from='.$listFromRedirect['from']);
                return;
            }
            else
            {
                //문서가 없어요! 만들래요?
                //print('<'.$strEntryName.'>문서가 없습니다.');
                echo $this->longonya->loadDocPaget('lgnUI/doc_nonexist', $mapInput);
                return;
            }

        }
        /////////////////////////////////////////////

        $intEntryNum = $strDocInfo['entry_num'];

        $mapaInput = array();
        $mapaInput['isAdmin'] = (isset($userInfo['group_id']) && $userInfo['group_id'] == 'admin');
        $mapaInput['allowEdit'] = false;
        $mapaInput['strFrom'] = $this->input->get_post('from');
        $mapaInput['doc_title'] = $strEntryName;

        $strHTML =  $this->longonya->loadDocString($strDocInfo['contentText'], $mapInput);
        $strHTML = $this->longonya->wikiparser->compile($strHTML, $strEntryName);
        $mapaInput['wikiDocContent'] = $strHTML;
        $mapaInput['footnotes'] = $this->longonya->wikiparser->getFootnotes();
        $mapaInput['strUrlEdit'] = "/d/edit/".urlencode($strEntryName);
        $mapaInput['listCategory'] = $this->cats->getRelListByEntryID($intEntryNum);


        $this->longonya->conf->setOpt('skinType','');
        $this->longonya->conf->setOpt('skinTheme','');

        $strHTMLCompleted = $this->longonya->loadDocPaget('lgnUI/doc_nonexist', $mapInput);

        $this->longonya->conf->setOpt('skinType','');
        $this->longonya->conf->setOpt('skinTheme','');

        $strHTMLFinal = $this->longonya->loadDocPaget('lgnUI/wikidoc_frame', $mapaInput);

        $this->longonya->conf->setOpt('skinType','default');
        $this->longonya->conf->setOpt('skinTheme','kdn');

        print( $this->longonya->coatSkin(
            $strHTMLFinal, 'kdn', 'default', $mapInput
        ) );



    }

}

?>
