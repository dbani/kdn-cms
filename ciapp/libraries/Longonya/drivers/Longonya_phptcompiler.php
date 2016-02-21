<?php

/**
@class longonya_phptparser
@author Patrick Seo (munutts@gmail.com)
Template Compiler (with XpressEngine Compatible)
**/
/**

2015-10-05 Include 명령어 추가

**/
 /**


레이아웃제어 템플릿명령어
<!--{php{[php 스크립트문장하나. 초과해선 안됨.]}}-->//최내측 괄호밖은 모두 붙여씁니다.
<!--{if{[조건문 php문]}}-->
<!--{foreach{[리스트 변수명]:[인덱스 변수명]:[아이템 변수명]}}-->
//상기 문장들은 하나라도 변수가 !isset 이면 실행안할겁니다.
<!--{else{}}--> //else
<!--{endif{}}-->, <!--{endforeach{}}-->
//원하는 변수 가져오라고 예약
<!--{want_var{[변수 아이디]}}-->
//Include (2015-10-05 추가)
<!--{include{[인클루드(paget패스 기준)]}}-->

<block></block> 태그

{itv{[key]}}반복문 내 코드 (단, 더 깊은 Depth의 경우는 직접 코드로 하셔야 합니다. 쌰아!)

=============================================
XE 템플릿언어 호환용
<!--@if()--><!--@else--><!--@end-->


**/

class Longonya_phptcompiler extends CI_Driver
{

    /**
    컴파일 수행
    **/
    public function compile( $strHTML, $mapVars = array() )
    {
        //배리어블
        foreach( $mapVars as $k => $v )
        {
            $$k = $v;
        }

        $this->stripNsip($strHTML); //nsip 스트립

        $strHTML = $this->parseLgnTemplate($strHTML);//롱고냐 템플릿
        $strHTML = $this->parseEchoVar($strHTML);//롱고냐 템플릿

        $listFrom = array('<block>','</block>','<code>','</code>');
        $listTo = array('<?php ', '?>','<?php ', '?>');
        $strHTML = str_replace($listFrom, $listTo, $strHTML );
        $strHTML = preg_replace("/<!--{php{([^}]*)}}-->/u",'<?php $1; ?>', $strHTML);

        ob_start();
        eval( "\n?>\n".$strHTML."\n<?php \n" );
        $resultHTML = ob_get_contents();
        ob_end_clean();

        return $resultHTML;
    }

    /**
    <!--{nsip{}}-->태그라는 php연산시 숨기는 코드영역을 표기하는 문법
    **/
    public function stripNsip( &$refHTML )
    {
         $refHTML = preg_replace(
             "/<!--{nsip{}}-->.*?<!--{\/nsip{}}-->/su",
             "<!--THIS PART IS REPLACED BY AAA-->",
             $refHTML
         );
    }

    /**
    include 템플릿을 먼저 해석하여 미리미리 포함시킵니다.
    **/
    public function parseIncludeTemplate ( $strHTML )
    {
        $listMatches = array();
        $intMatches = preg_match_all("/<!--\{(include|require)\{([^}]*|)\}\}-->/u", $strHTML, $listMatches);

        $listFrom = array();
        $listTo = array();

        for($idxMatch = 0; $idxMatch < $intMatches; $idxMatch++ )
        {
            $strwholeComment = $listMatches[0][$idxMatch];
            //$strCommand = $listMatches[1][$idxMatch];
            $strParam = $listMatches[2][$idxMatch];

            //$strHTML = str_replace( $strwholeComment, $strToReplacing, $strHTML );

            $strPath = ($this->_getPagetFullPath()).$strParam;
            $strINCNY = '';
            if( file_exists($strPath) )
            {
                //파일 있음
                $strINCNY = file_get_contents($strPath);
            }
            else
            {
                $strINCNY = '';
            }

            $listFrom[] = $strwholeComment;
            $listTo[] = $strINCNY;
        }

        $strHTML = str_replace( $listFrom, $listTo, $strHTML );
        return $strHTML;
    }

    public function parseLgnTemplate( $strHTML )
    {
        $listMatches = array();
        $intMatches = preg_match_all("/<!--\{([^}]*)\{([^}]*|)\}\}-->/u", $strHTML, $listMatches);

        $listFrom = array();
        $listTo = array();

        for($idxMatch = 0; $idxMatch < $intMatches; $idxMatch++ )
        {
            $strwholeComment = $listMatches[0][$idxMatch];
            $strCommand = $listMatches[1][$idxMatch];
            $strNYNY = $listMatches[2][$idxMatch];

            $strToReplacing = "";
            switch( $strCommand )
            {
                case 'foreach':
                    $listVals = explode(":", $strNYNY);
                    $strToReplacing = sprintf("<?php foreach($%s as $%s => $%s ): ?>",$listVals[0],$listVals[1],$listVals[2]);
                    break;

                case 'endforeach':
                    $strToReplacing = "<?php endforeach; ?>";
                    break;

                case 'if':
                    $strToReplacing = "<?php if(".$strNYNY."): ?>";
                    break;
                case 'elseif':
                    $strToReplacing = "<?php elseif(".$strNYNY."): ?>";
                    break;
                case 'else':
                    $strToReplacing = "<?php else: ?>";
                    break;
                case 'endif':
                    $strToReplacing = "<?php endif; ?>";
                    break;
                default:
                    $strToReplacing = $strwholeComment;
                    break;
            }

            $listFrom[] = $strwholeComment;
            $listTo[] = $strToReplacing;

            //$strHTML = str_replace( $strwholeComment, $strToReplacing, $strHTML );
        }

        $strHTML = str_replace( $listFrom, $listTo, $strHTML );
        return $strHTML;
    }


    public function parseEchoVar( $strHTML )
    {
        return preg_replace("/{p{([^\}]*)}}/u",'<?php print($1); ?>', $strHTML);
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
