<?php


class Longonya_conf extends CI_Driver
{
    public $headerData = array();

    public $options = array();

    public $paths = array();//ListOfAbsolutePaths
    /*
    public function __construct()
    {
        parent::__construct();
    }*/

    public function setHdata( $mapp )
    {
        $this->headerData = $mapp;
    }

    /**
    $options 배열에서 뭔가 가져오는데, 세팅안했을 경우 '' 을 반환.
    **/
    public function setOpt($strAttr, $strVal)
    {
        $this->options[$strAttr] = $strVal;
    }

    /**
    $options 배열에서 뭔가 가져오는데, 세팅안했을 경우 '' 을 반환.
    **/
    public function getOpt($strAttr)
    {
        if( isset($this->options[$strAttr]))
        {
            return $this->options[$strAttr];
        }
        return '';
    }

    /**
    $options 배열에서 뭔가 가져오는데, 세팅안했을 경우 0을반환.
    **/
    public function getOpt0($strAttr)
    {
        if( isset($this->options[$strAttr]))
        {
            return $this->options[$strAttr];
        }
        return 0;
    }

     /**
    $options 배열에서 뭔가 가져오는데, 세팅안했을 경우 주어진 기본값을반환.
    **/
    public function getOptA($strAttr, $strAltDefault)
    {
        if( isset($this->options[$strAttr]))
        {
            return $this->options[$strAttr];
        }
        return $strAltDefault;
    }

    ////////////////////////////////////////////////////경로설정
    public function setPath( $strConfItem, $strPath )
    {
        $this->paths[$strConfItem] = APPPATH.$strPath;
    }

    public function getPath( $strConfItem )
    {
        return $this->paths[$strConfItem];
    }


    ////////////////////////////////////////////////////타이틀
    public function setTitle( $strHtmlTitle )
    {
        $this->headerData['title'] = $strHtmlTitle;
    }

    public function getTitle()
    {
        return $this->headerData['title'];
    }

    ///////////////////////////////////////////////////내용
    public function setContent( $strHtmlBodyContent )
    {
        $this->headerData['content'] = $strHtmlBodyContent;
    }

    public function getContent( $strHtmlBodyContent )
    {
        return $this->headerData['content'];
    }


}

?>
