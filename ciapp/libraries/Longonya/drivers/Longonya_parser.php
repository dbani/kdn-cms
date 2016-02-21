<?php

/**

**/
class longonya_parser extends CI_Driver
{


    /**
    주어진 페이지에서 변수부분"{v{변수명}}"을 파싱합니다.

    입력 :
    $strPage - 해석이 필요한 HTML 페이지
    $mapVariables -변수의 값이 들어있는 배열
    $clearRestof - 이거 하고 나머지 해석안된 부분 다 지울까요?
    $parentVariableName - 배열 파싱할 때, 부모가 되는 variable 값.
    **/
    public function parseVariable($strPage, $mapVariables, $parentVariableName = '')
    {
        if(!is_array($mapVariables))
        {
            //두번째 파라미터 값이 배열이 아닙니다!
            return $strPage;
        }
        foreach($mapVariables as $Vname => $Vval)
        {
            //제대로 들어왔습니다. 작업 시작합니다.
            $$Vname = $Vval;
            if(is_array($Vval))
            {
                //배열은 표시할수 없습니다.
                $strPage = str_replace("{v{".$parentVariableName.$Vname."}}", "ARRAY", $strPage );
                $strPage = $this->parseVariable($strPage, $Vval, $parentVariableName.$Vname.'/');
                continue;
            }
            if(is_object($Vval))
            {
                //stdObject는 표시할 수 없습니다.
                $strPage = str_replace("{v{".$parentVariableName.$Vname."}}", "STD_OBJECT", $strPage );
                continue;
            }

            $strPage = str_replace("{v{".$parentVariableName.$Vname."}}", $Vval, $strPage );
        }


        return $strPage;
    }


    /**
    남아있는 템플릿언어를 지워 없앱니다.
    **/
    public function removeVariableTemplate( $strPage )
    {
        return preg_replace("/{v{(.*?)}}/u", "" , $strPage);
    }


    /*=========================================================
     S C R I P T 태그 관련 시작
    ==========================================================*/

    /**
    스크립트 태그를 쏙 빼서 그 태그 리스트를 반환합니다.
    $boolRemoveMatchedTag - 매치되는 태그가 있으면 원본 HTML에서 통채로 지울까요? (기본값 : 예)
    **/
    public function extractScriptRefFromHTML(&$refHTML)
    {
        $list_return = array();

        $listMatchs = array();
        $intNumMatches = preg_match_all("{<(\\s|)script[^>]+src=\\\"([^\"]*|)\\\"([^>]*|)>(.*?|)</script>}s", $refHTML ,$listMatchs, PREG_PATTERN_ORDER);

        //print("<h2>Extraction of JS Script From HTML </h2><pre>".print_r($listMatchs, true)."</pre>");

        for($idx = 0; $idx < $intNumMatches; $idx++)
        {
            $list_return[] = trim($listMatchs[2][$idx]);
        }
        //print("<h2>Extraction : </h2><pre>".print_r($list_return, true)."</pre>");
        return $list_return;
    }

    /**
    스크립트 태그 내용을 빼서 그 리스트를 반환합니다.
    **/
    public function extractScriptContentFromHTML(&$refHTML)
    {
        $list_return = array();

        $listMatchs = array();
        $intNumMatches = preg_match_all("{<(\\s|)script([^>]*|)>(.*?)</script>}xs", $refHTML ,$listMatchs, PREG_PATTERN_ORDER);

        //print("<h2>Extraction of JS Script From HTML </h2><pre>".print_r($listMatchs, true)."</pre>");

        for($idx = 0; $idx < $intNumMatches; $idx++)
        {
            $strNY = $listMatchs[3][$idx];
            if( trim($strNY) == '' )
            {
                //print("<!--EmptyJS 텅텅-->\n");
                continue;
            }
            //print("<!--FULLJS 꽉꽉-->\n");
            $list_return[] = $strNY;
        }

        return $list_return;
    }

    public function extractAndRemoveAllScriptTags( &$strDOM , $boolRemoveit = true )
    {
        $listResult = array();

        $listMatchs = array();
        $intMatches = preg_match_all("{<(\\s|)script([^>]*|)>(.*?)</script>}xs", $strDOM ,$listMatchs, PREG_PATTERN_ORDER);

        //print("<h2>extractAndRemoveAllScriptTags </h2><pre>".print_r($listMatchs, true)."</pre>");


        if( isset($listMatchs[0]) && count( $listMatchs[0] ) > 0 )
        {
            $listResult = $listMatchs[0];

            //print("<h2>extractAndRemoveAllScriptTags </h2><pre>".print_r($listResult, true)."</pre>");
            if( $boolRemoveit )
            {
                $strDOM = str_replace( $listResult, '' ,$strDOM );
            }
        }

        return $listResult;
    }


    /**
    script 태그란 태그를 전부 벗깁니다.
    **/
    public function stripScriptTags( &$refHTML )
    {
       $refHTML = preg_replace ("{<(\\s|)script([^>]+|)>(.*?|)</script>}xs", "", $refHTML);
    }

    /*=========================================================
     S C R I P T 태그 관련 끝
    ==========================================================*/
    /*=========================================================
     C S S 태그 관련 시작
    ==========================================================*/
    /**
    주어진 HTML 페이지에서 연결되는 CSS파일들의 목록을 추출합니다.
    **/
    public function extractCSSListFromHTML($strHTML)
    {
        $listMatchs = array();
        $intNumMatches = preg_match_all("#<\s*?link[^>]*href=([\"'](.*?)[\"'])[^>]*>#s", $strHTML ,$listMatchs, PREG_PATTERN_ORDER);

        //print("<pre>");print_r($listMatchs);print("</pre>");

        if(!isset($listMatchs[2]))
        {
            return array();
        }
        else
        {
            return $listMatchs[2];
        }
    }

    public function extractCSSContentFromHTML($strHTML)
    {
        $listMatchs = array();
        $intNumMatches = preg_match_all("#<\s*?style\b[^>]*>(.*?)</style\b[^>]*>#s", $strHTML ,$listMatchs, PREG_PATTERN_ORDER);

        //print("<pre>");print_r($listMatchs);print("</pre>");

        if(!isset($listMatchs[1]))
        {
            return array();
        }
        else
        {
            return $listMatchs[1];
        }
    }

    public function stripStyleTag( &$refHTML )
    {
        $refHTML = preg_replace ("#<\s*?style\b[^>]*>(.*?)</style\b[^>]*>#s", "", $refHTML);
        $refHTML = preg_replace ("#<\s*?link[^>]*href=([\"'](.*?)[\"'])[^>]*>#s", "", $refHTML);
    }

    /*=========================================================
     C S S 태그 관련 끝
    ==========================================================*/
}

?>
