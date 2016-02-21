<?php

class Longonya_wikiparser  extends CI_Driver
{
    protected $listComments = array();

    public function compile( $strHTML, $strcurrentDocName , $mapVals = array() )
    {
        $resultHTML = $strHTML;

        // include here
        $resultHTML = $this->compileWikiInc(  $resultHTML, $strcurrentDocName );
        // include here

        $resultHTML = $this->compileWikiLink(  $resultHTML, $strcurrentDocName );

        $resultHTML = $this->compileWikiTable( $resultHTML, $strcurrentDocName );

        $this->listComments = $this->compileWikiComment(
            $resultHTML, $strcurrentDocName
        );
        return $resultHTML;
    }

    /*
    [include()]
    */

    public function compileWikiInc( $strHTML, $strcurrentDocName )
    {
        $listMatches = array();
        $intMatches = preg_match_all("/\[include\(([^\\)]*|)\)\]/ux", $strHTML, $listMatches);

        //print_r($listMatches);

        $arrayFrom = array();$arrayTo = array();

        $this->parent->CI->load->model('document/Lgndocument', 'lgndocument');


        for($idxMatch = 0; $idxMatch < $intMatches; $idxMatch++ )
        {
            $strLnkFROM = $listMatches[0][$idxMatch];
            $strLnkNY = $listMatches[1][$idxMatch];

            $mapaa = $this->parent->CI->lgndocument->getbyName($strLnkNY);

            $strDocDoc = '';
            if( is_array($mapaa) )
            {
                $strDocDoc = $mapaa['contentText'];
            }else{
                $strDocDoc = 'ERROR : '.$strLnkNY.' is not exists.';
            }

            $arrayFrom[] = $strLnkFROM;
            $arrayTo[] = $strDocDoc;
        }

        $strHTML = str_replace( $arrayFrom, $arrayTo , $strHTML );

        return $strHTML;
    }

    /*
    [[링크]], [[링크|라벨]]
    */
    public function compileWikiLink( $strHTML, $strcurrentDocName )
    {
        $listMatches = array();
        $intMatches = preg_match_all("/\[\[([^\\]]*|)\]\]/ux", $strHTML, $listMatches);

        //print_r($listMatches);

        $arrayFrom = array();$arrayTo = array();

        for($idxMatch = 0; $idxMatch < $intMatches; $idxMatch++ )
        {
            $strLnkFROM = $listMatches[0][$idxMatch];
            $strLnkNY = $listMatches[1][$idxMatch];

            $strLnkNYSplitt = explode('|', $strLnkNY);
            //$strLnkNYSplitt[0] = str_replace( $strcurrentDocName, '<strong>'.$strcurrentDocName.'</strong>', $strLnkNYSplitt[0] );


            $strLnkTo = "<a href=\"";
            //$strLnkTo .= ( substr(urldecode($strLnkNYSplitt[0]) , 0, 7) == 'http://' )?'':'/d/view/';
            //$strLnkTo .= str_replace('+','_', urlencode( $strLnkNYSplitt[0] ));

            if( substr(urldecode($strLnkNYSplitt[0]) , 0, 7) == 'http://' )
            {
                $strLnkTo .= $strLnkNYSplitt[0];
            }else
            {
                $strLnkTo .= '/d/view/'.str_replace('+','%20', urlencode( $strLnkNYSplitt[0] ));
            }

            $strLnkTo .= "\" class=\"link lgnwiki-link\" >";
            if( count($strLnkNYSplitt) == 1)
            {
                $strLnkTo .= str_replace( $strcurrentDocName, '<strong class="lgnwiki-U-R-here">'.$strcurrentDocName.'</strong>', $strLnkNYSplitt[0] );
            }else{
                $strLnkTo .= str_replace( $strcurrentDocName, '<strong class="lgnwiki-U-R-here">'.$strcurrentDocName.'</strong>', $strLnkNYSplitt[1]);
            }
            $strLnkTo .= "</a>";

            $arrayFrom[] = $strLnkFROM;
            $arrayTo[] = $strLnkTo;
        }

        $strHTML = str_replace( $arrayFrom, $arrayTo , $strHTML );

        return $strHTML;
    }

    /*
    [[링크]], [[링크|라벨]]
    */
    public function compileWikiComment( &$strHTML, $strcurrentDocName )
    {
        $listMatches = array();
        $intMatches = preg_match_all("/\[\*([^\\]]*|)\]/ux", $strHTML, $listMatches);

        //print_r($listMatches);
        $arrayReturl = array();
        $arrayFrom = array();$arrayTo = array();

        for($idxMatch = 0; $idxMatch < $intMatches; $idxMatch++ )
        {
            $intCommentNum = ($idxMatch+1);
            $strLnkFROM = $listMatches[0][$idxMatch];
            $strLnkNY = $listMatches[1][$idxMatch];

            $strLnkTo = "<sup><a href=\"#commT-{$intCommentNum}\" ";
            $strLnkTo .= " name=\"commS-{$intCommentNum}\" ";
            $strLnkTo .= " class=\"lgnwiki-comment\" data-toggle=\"tooltip\" data-placement=\"bottom\" ";
            $strLnkTo .= " title=\"".strip_tags($strLnkNY)."\" >";
            $strLnkTo .= "[{$intCommentNum}]";
            $strLnkTo .= "</a></sup>";

            $arrayFrom[] = $strLnkFROM;
            $arrayTo[] = $strLnkTo;

            $arrayReturl[] = $strLnkNY;
        }

        $strHTML = str_replace( $arrayFrom, $arrayTo , $strHTML );

        return $arrayReturl;
    }

    /**
    {{table[]
    [head]
    [h;colspan:4;rowspan:1] 내용 |[]
    [body]

    [foot]

    /table}}
    **/
    public function compileWikiTable( $strHTML, $strcurrentDocName )
    {
        $listMatches = array();
        $intMatches = preg_match_all("/\{\{table\[([^\\]]*|)\](.*|)\/table\}\}/ux", $strHTML, $listMatches);

        //print_r($listMatches);

        $arrayFrom = array();$arrayTo = array();

        for($idxMatch = 0; $idxMatch < $intMatches; $idxMatch++ )
        {
            $strTableFROM = $listMatches[0][$idxMatch];
            $strTableConf = $listMatches[1][$idxMatch];
            $strTableNY = $listMatches[2][$idxMatch];

            $strTableTo = '<table class="kdnwiki-table">';


            $strTableTo .= '</table>';

            $arrayFrom[] = $strTableFROM;
            $arrayTo[] = $strTableTo;
        }

        $strHTML = str_replace( $arrayFrom, $arrayTo , $strHTML );

        return $strHTML;
    }


    public function getFootnotes()
    {
        return $this->listComments;
    }
}

?>
