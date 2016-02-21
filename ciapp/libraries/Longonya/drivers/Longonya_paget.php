<?php

class Longonya_paget extends CI_Driver
{
    protected $pagetBasePath = "";
    protected $currentPagetID = ''; //현재 파싱중인 페이젯 아이디는?

    public function setPagetBasePath ( $strNewPath )
    {
        $this->pagetBasePath = $strNewPath;
    }
    //현재 페이젯 아이디를 설정합니다.
    public function setPagetID( $strNewPagetID )
    {
        $this->currentPagetID = $strNewPagetID;
    }

    //현재 선택한 페이젯의 경로를 확인합니다.
    public function getCurrentPagetPath()
    {
        return $this->pagetBasePath.$this->currentPagetID."/";
    }

    //파일의 존재여부를 파악합니다. 있으면 해당파일 절대경로, 없으면 빈 문자열.
    public function pagetIsExists( $strSubPageName = 'index' )
    {
        $strPP = $this->getCurrentPagetPath();
        $strFileWutINeed = $strPP.$strSubPageName;

        if(file_exists( $strFileWutINeed.".php" )  == true)
        {
            return $strFileWutINeed.".php";
        }
        else if(file_exists( $strFileWutINeed.".htm" ) == true)
        {
            return $strFileWutINeed.".htm";
        }
        else if(file_exists( $strFileWutINeed.".html" ) == true)
        {
            return $strFileWutINeed.".html";
        }
        else if(file_exists( $strFileWutINeed.".md" ) == true)
        {
            return $strFileWutINeed.".md";
        }
        else
        {
            return '';
        }
    }

    /**
    페이젯을 로드합니다. 기타 다른 처리는 하지 않습니다.
    **/
    public function getPaget( $strSubPageName = 'index')
    {
        $strPP = $this->getCurrentPagetPath();

        $strFileWutINeed = $strPP.$strSubPageName;

        $strPreProcessor = '';

        if(file_exists( $strFileWutINeed.".php" )  == true)
        {
            $strFileWutINeed = $strFileWutINeed.".php";
        }
        else if(file_exists( $strFileWutINeed.".htm" ) == true)
        {
            $strFileWutINeed = $strFileWutINeed.".htm";
        }
        else if(file_exists( $strFileWutINeed.".html" ) == true)
        {
            $strFileWutINeed = $strFileWutINeed.".html";
        }
        else if(file_exists( $strFileWutINeed.".md" ) == true)
        {
            $strFileWutINeed = $strFileWutINeed.".md";
            $strPreProcessor = 'md';
        }
        else
        {
            show_error('Paget ('.$this->currentPagetID.'/'.$strSubPageName.') is not exists!!');
        }

        $strDoc = file_get_contents( $strFileWutINeed );

        if( $strPreProcessor == 'md' )
        {
            $datCI =& get_instance();
            $datCI->load->library("parsedown");
            $strDoc = $datCI->parsedown->text($strDoc);
        }

        $this->extractAndMoveJSFromHTML($strDoc);
        $this->extractAndMoveCSSFromHTML($strDoc);
        $this->extractNreplaceIMGFromHTML($strDoc);

        return $strDoc;
    }

    /**

    */
    public function extractNreplaceIMGFromHTML(&$refHTML)
    {
        //echo $this->parent->conf->getPath('paget')."<br />";



        $list_return = array();

        $listMatchs = array();
        $intNumMatches = preg_match_all("/<(\\s|)img[^>]+src=\"([^\"]*)\"([^>]+|)>/si", $refHTML ,$listMatchs, PREG_PATTERN_ORDER);

        //print("<h2>Extraction of IMG </h2><pre>".print_r($listMatchs, true)."</pre>");

        for($idx = 0; $idx < $intNumMatches; $idx++)
        {
            //$list_return[] = $listMatchs[2][$idx];
            $original_full_tag = $listMatchs[0][$idx]; //Full 태그

            $original_image_src = $listMatchs[2][$idx];
            if( substr( $original_image_src , 0, 2) == "./" )
            {
                //로컬이미지당.
                $new_full_tag = str_replace(
                    $original_image_src,
                    $this->_changeImagePathToWant($original_image_src), $original_full_tag);
                //$this->_changeImagePathToWant($original_image_src);

                $refHTML = str_replace($original_full_tag, $new_full_tag, $refHTML);
            }
            else if( substr( $original_image_src , 0, 3) == "../" )
            {
                //로컬이미지당.
                $new_full_tag = str_replace(
                    $original_image_src,
                    $this->_changeImagePathToWant($original_image_src), $original_full_tag);
                //$this->_changeImagePathToWant($original_image_src);

                $refHTML = str_replace($original_full_tag, $new_full_tag, $refHTML);
            }
        }
    }

    /**
    상대경로에 있는 것은 내용만을 불러오고, 절대경로에 있는 것은 가만히 놔둡니다.
    태그 재정렬은 여기서 안합니다.
    **/
    public function extractAndMoveCSSFromHTML(&$refHTML)
    {
        $listMatchs = array();
        $intNumMatches = preg_match_all("{<(\\s|)link(.*?) href=\"(.*?)\"[^>]+>}s", $refHTML ,$listMatchs, PREG_PATTERN_ORDER);

        $strOurPagetFullPath = $this->_getPagetFullPath();

        $listReplaceFrom = array();
        $listReplaceTo = array();

        for($idx = 0; $idx < $intNumMatches; $idx++)
        {
            $strLinkFullTag =  $listMatchs[0][$idx];
            $strOriginalCSSRef = $listMatchs[3][$idx]; //css Path

            $strNYCSS = '';
            if( substr( $strOriginalCSSRef , 0, 2) == "./" )
            {
                $__strNYCSS = file_get_contents( str_replace('./',$strOurPagetFullPath."{$this->currentPagetID}/", $strOriginalCSSRef) );
                $strNYCSS = "\n\n/* ===from {$strOriginalCSSRef} ====*/\n".$__strNYCSS;
            }
            else if( substr( $strOriginalCSSRef , 0, 3) == "../" )
            {
                $__strNYCSS = file_get_contents( str_replace('../',$strOurPagetFullPath, $strOriginalCSSRef) );
                $strNYCSS = "\n\n/* ===from {$strOriginalCSSRef} ====*/\n".$__strNYCSS;
            }
            else
            {
                continue;
            }

            $listReplaceFrom[] = $strLinkFullTag;
            $listReplaceTo[]="<style>\n".$strNYCSS."\n</style>\n";

        }

        $refHTML = str_replace($listReplaceFrom, $listReplaceTo, $refHTML );
    }

    /**
    상대경로에 있는 것은 내용만을 불러오고, 절대경로에 있는 것은 가만히 놔둡니다.
    태그 재정렬은 여기서 안합니다.
    **/
    public function extractAndMoveJSFromHTML(&$refHTML)
    {
        $listMatchs = array();
        $intNumMatches = preg_match_all("{<(\\s|)script[^>]+src=\\\"([^\"]*|)\\\"([^>]*|)>(.*?|)</script>}s", $refHTML ,$listMatchs, PREG_PATTERN_ORDER);

        $strOurPagetFullPath = $this->_getPagetFullPath();

        $listReplaceFrom = array();
        $listReplaceTo = array();

        for($idx = 0; $idx < $intNumMatches; $idx++)
        {
            $strLinkFullTag =  $listMatchs[0][$idx];
            $strOriginalJSRef = $listMatchs[2][$idx]; //js Path
            $__strNYJS = '';

            if( substr( $strOriginalJSRef , 0, 2) == "./" )
            {
                $strOpenPath = str_replace('./',$strOurPagetFullPath."{$this->currentPagetID}/", $strOriginalJSRef);
                $strOpenPath = str_replace('/', DIRECTORY_SEPARATOR, $strOpenPath );
                $__strNYJS = file_get_contents( $strOpenPath );
                //echo "<!--(./)\n{$__strNYJS}-->\n";

            }
            else if( substr( $strOriginalJSRef , 0, 3) == "../" )
            {
                $strOpenPath = str_replace('../',$strOurPagetFullPath, $strOriginalJSRef);
                $strOpenPath = str_replace('/', DIRECTORY_SEPARATOR, $strOpenPath );
                $__strNYJS = file_get_contents( $strOpenPath );
                //echo "<!--(../)\n{$__strNYJS}-->\n";
            }
            else
            {
                continue;
            }

            $strToTagg = "<script type=\"text/javascript\" moved-by=\"longonya-paget\">\n";
            $strToTagg .= "/* -- script from '{$strOriginalJSRef}' -- */\n";
            $strToTagg .= $__strNYJS;
            $strToTagg .="\n</script>";

            $listReplaceFrom[] = $strLinkFullTag;
            $listReplaceTo[] = $strToTagg;
            //echo "<!--(asdfasdfsadfadsfsdaf)\n{$strToTagg}-->\n";
        }

        $refHTML = str_replace($listReplaceFrom, $listReplaceTo, $refHTML );
        //echo "<!--(longonya-paget-refhtml)\n{$refHTML}-->\n";
    }



    protected function _changeImagePathToWant( $strIMGRelative )
    {
        //echo "<!--OUR IMAGE : {$strIMGRelative}-->\n";
        $strOurPagetPath = $this->_getPagetFullPath();

        $CI =& get_instance();
        $CI->load->helper('b64u');
        $strIMGRelative = str_replace( "../", $strOurPagetPath, $strIMGRelative);
        $strIMGRelative = str_replace( "./", $strOurPagetPath."{$this->currentPagetID}/", $strIMGRelative);

        $strIMGRelative = base64_url_encode($strIMGRelative);
        return '/imgg/'.$strIMGRelative;
    }

    protected function _getPagetFullPath()
    {
        if( isset($this->parent) == true )
        {
            //CI 2.x
            return $this->parent->conf->getPath('paget');
        }
        else if( isset($this->_parent) == true )
        {
            //CI 3.x
            return $this->_parent->conf->getPath('paget');
        }
    }

}

?>
