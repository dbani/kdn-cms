<?php
/**
Relocate ( and cacheing ) DOM to its optimal position.
**/
class Longonya_dom_relocator extends CI_Driver
{
    protected $myparent;

    function __construct()
    {
        //parent::__construct();

    }

    /**
    relocate to optimal position.
    **/
    public function relocateDOM( $strDOM )
    {
        $strDOM = $this->relocateScriptTag( $strDOM );
        $strDOM = $this->relocateCSSTag( $strDOM );
        return $strDOM;
    }

    /**
    relocate Script Tag
    **/
    public function relocateScriptTag( $strDOM )
    {
        $parentt = $this->getParent();
        $listScriptRefs = $parentt->parser->extractScriptRefFromHTML($strDOM);
        $listScriptContents = $parentt->parser->extractScriptContentFromHTML($strDOM);

        $listScriptRefs = array_unique($listScriptRefs, SORT_STRING); //레퍼런스의 중복제거. 중복된 것중 첫번째로 나온 요소가 사용됨.

        //echo "<!--알아서 재정렬도 안되는 잉여 : ".print_r($listScriptRefs, true)."-->";

        $strNewScript = "";
        foreach( $listScriptRefs as $strReffJS)
        {
            $strNewScript .= sprintf( "<script type=\"text/javascript\" src=\"%s\" data-moved-by=\"longonya\" > </script>\n" , $strReffJS);
        }
        foreach( $listScriptContents as $strContentJS)
        {
            $strNewScript .= sprintf( "<script type=\"text/javascript\" data-moved-by=\"longonya\" >\n%s\n</script>\n" , $strContentJS);
        }

        //echo "<pre>".$strNewScript."</pre>\n";

        $parentt->parser->stripScriptTags( $strDOM );

        $intWhereToPos = strlen($strDOM);
        $intWhereToPos = min($intWhereToPos, strpos($strDOM, '</body>'));
        //$intWhereToPos = min($intWhereToPos, strpos($strDOM, '</footer>'));
        //$intWhereToPos = max($intWhereToPos, strpos($strDOM, '</html>'));

        //$strDOM = substr_replace($strDOM,$strNewScript, $intWhereToPos, 0 );
        $strDOM = $this->stringInsert($strDOM, $strNewScript, $intWhereToPos );

        return $strDOM;
    }

    /**
    relocate link and style Tag
    **/
    public function relocateCSSTag( $strDOM )
    {
        $parentt = $this->getParent();
        $listCSSRefs = $parentt->parser->extractCSSListFromHTML($strDOM);
        $listCSSContents = $parentt->parser->extractCSSContentFromHTML($strDOM);

        $listCSSRefs = array_unique($listCSSRefs, SORT_STRING); //레퍼런스의 중복제거. 중복된 것중 첫번째로 나온 요소가 사용됨.

        //print("<pre>");print_r($listCSSRefs);print("</pre>");

        $strNewCSS = "";
        foreach( $listCSSRefs as $strReffCSS )
        {
            if( strpos($strReffCSS,'?') == false )
            {
                $strReffCSS = $strReffCSS.'?time='.time();
            }
            else
            {
                $strReffCSS = $strReffCSS.'&time='.time();
            }
            $strNewCSS .= sprintf( "<link rel=\"stylesheet\" type=\"text/css\" href=\"%s\" data-moved-by=\"longonya\" />\n" , $strReffCSS);
        }
        foreach( $listCSSContents as $strContentCSS)
        {
            $strNewCSS .= sprintf( "<style>\n%s\n</style>\n" , $strContentCSS);
        }

        $parentt->parser->stripStyleTag( $strDOM );

        $intWhereToPos = 0;
        $intWhereToPos = max($intWhereToPos, strpos($strDOM, '</head>'));

        //$strDOM = substr_replace($strDOM,$intWhereToPos, $intWhereToPos, 0 );
        $strDOM = $this->stringInsert($strDOM,$strNewCSS,$intWhereToPos );
        return $strDOM;
    }

    protected function getParent()
    {
        if( is_object($this->myparent) )
        {
            return $this->myparent;
        }

        if( isset($this->parent) == true )
        {
            //CI 2.x
            $this->myparent = &$this->parent;
        }
        else if( isset($this->_parent) == true )
        {
            //CI 3.x
            $this->myparent = &$this->_parent;
        }

        return $this->myparent;
    }


    protected function stringInsert($str,$insertstr,$pos)
    {
        $str = substr($str, 0, $pos) . $insertstr . substr($str, $pos);
        return $str;
    }


}

?>
