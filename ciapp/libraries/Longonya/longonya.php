<?php

class longonya extends CI_Driver_Library
{

    public $valid_drivers;

    public $CI;

    public $CIVER = ''; //사용중인 CI 버전

    function __construct()
    {


        $this->CI =& get_instance();
        $this->CI->config->load('longonya', TRUE);

        $this->CIVER = explode('.', CI_VERSION);

        $this->valid_drivers = $this->CI->config->item(
            'modules', 'longonya'
        );

        $_OIC = $this->CI->config->item(
            'modules', 'longonya'
        );

        if( $this->CIVER[0] >= 3 )
        {
            //CI가 3.x 입니다.
            foreach( $_OIC as $item)
            {
                $toput = str_replace('Longonya_','',$item);
                $this->valid_drivers[] = str_replace('longonya_','',$toput);
            }
        }

        //print_r( $this->valid_drivers );

        $this->conf->setHdata($this->CI->config->item(
            'lgnHeaderData', 'longonya'
        ));

        //lgnDefaultSkin
        $this->conf->setOpt('skinTheme', $this->CI->config->item('lgnDefaultSkin', 'longonya') );
        $this->conf->setOpt('skinType', $this->CI->config->item('lgnDefaultSkinType', 'longonya') );

        $this->conf->setPath('paget',
          $this->CI->config->item('lgnPagetPath', 'longonya')
                            );
        $this->conf->setPath('skinbase',
          $this->CI->config->item('lgnSkinPath', 'longonya')
                            );
        //페이젯 설정
        $this->paget->setPagetBasePath(
            $this->conf->getPath('paget')
        );

        //echo APPPATH."<br />";

    }

    /**
    주어진 정보로 페이지를 표시합니다.

    $strPagePath - paget 패스기준 html/php 패스

    **/
    public function loadDocPaget( $strPagePath, $mapVars=array() )
    {
        $mapVars['otherHostURL'] = $this->getOtherHostName();

        $lstPath = explode('/',$strPagePath,2);

        if( count( $lstPath ) < 2 || $lstPath[1] == '')
        {
            //show_error(' Cannot find any of paget ID.');
            $lstPath[1]= 'index';
        }

        $this->paget->setPagetID( $lstPath[0] );
        $strUnCompiledHTML = $this->paget->getPaget( $lstPath[1] );
        ///스킨 처리
        $strSkinTheme = $this->conf->getOptA('skinTheme','');
        $strSkinType = $this->conf->getOptA('skinType','');

        echo "";

        if( $strSkinTheme != '' && $strSkinType != '')
        {
            $strUnCompiledHTML = $this->coatSkin( $strUnCompiledHTML, $strSkinTheme, $strSkinType, $mapVars );
        }
        else
        {
            //Without Skin, Process is dead simple.
            $strUnCompiledHTML = $strUnCompiledHTML;
        }
        ///////
        return $this->loadDocString($strUnCompiledHTML, $mapVars);
    }

    public function loadDocPagetNoSkin( $strPagePath, $mapVars=array() )
    {
        $mapVars['otherHostURL'] = $this->getOtherHostName();

        $lstPath = explode('/',$strPagePath,2);

        if( count( $lstPath ) < 2 || $lstPath[1] == '')
        {
            //show_error(' Cannot find any of paget ID.');
            $lstPath[1]= 'index';
        }

        $this->paget->setPagetID( $lstPath[0] );
        $strUnCompiledHTML = $this->paget->getPaget( $lstPath[1] );
        return $this->loadDocString($strUnCompiledHTML, $mapVars);
    }

    /**
    *지정된 이름의 스킨을 씌워줍니다.
    *@access public
    *@param
    * $strSkinTheme - 스킨 테마 이름
    * $strSkinType - 스킨 타입
    *@return - (String)스킨이 씌워진 HTML 문서 문자열
    */
    public function coatSkin( $strUnCompiledHTML, $strSkinTheme, $strSkinType, $mapVal = array()  )
    {
        //스킨이 존재함. ㅇㅇ
        $this->paget->setPagetID( 'skin_'.$strSkinTheme );
        $skv = $this->paget->pagetIsExists( $strSkinType );



        if( $skv != '' )
        {
            $menusFile = APPPATH.'config/menus.json';
            $menusFile = str_replace('/', DIRECTORY_SEPARATOR, $menusFile);
            if( file_exists($menusFile) )
            {
                $ddd = file_get_contents( $menusFile );
                $mapVal['menus'] = json_decode($ddd, true);
                //print_r($mapVal['menus']);
            }

            //echo "<!--아 왜 여긴 안오는데? 여기 똥이 있나?-->";
            $listAllDaScriptTags = $this->parser->extractAndRemoveAllScriptTags($strUnCompiledHTML);

            $strUnCompiledSKINHTML = $this->paget->getPaget( $strSkinType, $mapVal );
            $strUnCompiledHTML = str_replace(
                array('{v{content}}','{v{title}}' ),
                array($strUnCompiledHTML,$this->conf->getTitle() ) ,
                $strUnCompiledSKINHTML );

            $strUnCompiledHTML = $strUnCompiledHTML.implode("\n", $listAllDaScriptTags);

            $listScriptRefss = $this->parser->extractScriptRefFromHTML($strUnCompiledHTML);

            $strUnCompiledHTML = $this->phptcompiler->compile( $strUnCompiledHTML, $mapVal );

            $strUnCompiledHTML = $this->parser->parseVariable($strUnCompiledHTML, $mapVal);

        }
        else
        {
            echo "Skin is Gone";
        }

        //echo "<pre>".htmlspecialchars($strUnCompiledHTML)."</pre>";

        $strUnCompiledHTML = $this->parser->removeVariableTemplate( $strUnCompiledHTML );

        return $strUnCompiledHTML;
    }

    /**
    어딘가에서 가져온 인코딩안된 것을 인코딩합니다. 파싱의 마지막단계.
    **/
    public function loadDocString( $strUncompiledPageHTML , $mapVars=array() )
    {
        $strCompiledHTML = $strUncompiledPageHTML;
        $strCompiledHTML = $this->phptcompiler->parseIncludeTemplate($strCompiledHTML);
        $strCompiledHTML = $this->phptcompiler->compile($strCompiledHTML, $mapVars);
        $strCompiledHTML = $this->parser->parseVariable($strCompiledHTML, $mapVars);
        $strCompiledHTML = $this->dom_relocator->relocateDOM( $strCompiledHTML );

        //$strCompiledHTML = $this->wikiparser->compile($strCompiledHTML, '');

        return $strCompiledHTML;
    }

    /**
    지정된 컨피규어 가지고 페이지 생성

    **/
    public function generatePageWithConfigure()
    {}


    public function faak()
    {
        return $this->parser->faak();
    }

    public function getOtherHostName()
    {
        $strOtherHost = '';

        if( strpos( $_SERVER['HTTP_HOST'], 'academy' ) !== FALSE )
        {
            $strOtherHost = str_replace('academy','www',$_SERVER['HTTP_HOST']);
        }
        else if( strpos( $_SERVER['HTTP_HOST'], 'too-etc' ) !== FALSE )
        {
           $strOtherHost = str_replace('too-etc','too-etc-b',$_SERVER['HTTP_HOST']);
        }
        else if( strpos( $_SERVER['HTTP_HOST'], 'too-etc-b' )  !== FALSE )
        {
           $strOtherHost = str_replace('too-etc-b','too-etc',$_SERVER['HTTP_HOST']);
        }
        else
        {
            $strOtherHost = 'academy.'.str_replace('www.','', $_SERVER['HTTP_HOST'] );
        }
        $strOtherHost = 'http://'.$strOtherHost;

        return $strOtherHost;
    }
}

?>
