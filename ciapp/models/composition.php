<?php

/**
포럼 스레드/토픽, 갤러리 설명문, 뉴스 기사 등의 근간을 이루는 글 입니다.
**/
class composition extends CI_Model
{
    protected $strTableName = 'kotul_composition';
    protected $intRecentRowsCount = 0;
    protected $arrUpdatableFields = array();

    function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    /**
    모듈 아이디로만 찾기 (페이지네이션)
    **/
    public function gets( $strMoType, $intMoNum, $intPage = 1, $intPerPage = 30 )
    {

        $intStartItem = ( ($intPage - 1) * $intPerPage );


        $this->load->database();
        $this->db->select('SQL_CALC_FOUND_ROWS *', FALSE);
        $this->db->from( $this->strTableName );
        $this->db->join('kotul_users', "kotul_users.id = {$this->strTableName}.user_id", 'left');
        $this->db->where('root_mo',$strMoType);

        if( $intMoNum > 0 ){
            $this->db->where('root_mo_num',$intMoNum);
        }
        if( $intPerPage  > 0 ){
            $this->db->limit( $intPerPage, $intStartItem );
        }

        $listresult = $this->db->get()->result_array();
        $this->_getCounting();

        foreach( $listresult as &$lstItem )
        {
            $xval = @json_decode( $lstItem['cp_extraval'], true );

            if( is_array($xval) ){
                $lstItem['extra_vals'] = $xval;
            }
            else{
                $lstItem['extra_vals'] = array();
            }
        }

        return $listresult;
    }

    public function getSingle( $intCPNum )
    {
        $this->db->select(' * ');
        $this->db->where('tp_num',$intCPNum);
        $this->db->from( $this->strTableName );
        $listresult = $this->db->get()->result_array();

        if( count( $listresult ) > 0 )
        {
            return $listresult[0];
        }
        else
        {
            return null;
        }
    }

    /**
    컴포지션 문서 추가하기
    */
    public function addNew( $userID, $strMoType, $intMoNum, $strTitle, $strContent, $mapaExtraval, $isSticky = false )
    {
         $this->load->database();
        $this->db->insert( $this->strTableName, array(
            'root_mo' => $strMoType,
            'root_mo_num' => $intMoNum,
            'user_id' => $userID,
            'cp_title' => $strTitle,
            'cp_text'=> $strContent,
            'is_sticky' => ($isSticky==true)?1:0,
            'cp_extraval' => json_encode($mapaExtraval),
            'date_created'=>date('Y-m-d h:i:s'),
            'date_updated' => date('Y-m-d h:i:s')
        ) );
    }

    /**
    컴포지션의 포괄적인 갱신
    **/
    public function update( $intCPNum, $mapBonnie )
    {
        if( count( $this->arrUpdatableFields ) == 0 )
        {
            $this->_getFieldInfo();
        }
        //$this->
        $mapToUpdates = array();
        foreach( $mapBonnie as $strField => $mxVal )
        {
            if( in_array( $strField, $this->arrUpdatableFields ) )
            {

                if( $strField == 'cp_extraval' && is_array( $mxVal ) )
                {
                    $mxVal = json_encode( $mxVal );
                }

                $mapToUpdates[$strField] = $mxVal;
            }
        }
        $this->db->where('tp_num', $intCPNum );
        $this->db->update( $this->strTableName, $mapToUpdates );
    }

    /**
    컴포지션 삭제처리
    **/
    public function remove( $intCPNum )
    {
        $this->load->database();
        $this->db->delete( $this->strTableName, array( 'tp_num' => $intCPNum ) );
    }

    /**
    SQL_CALC_FOUND_ROWS 가 포함된 쿼리 직후 숫자를 셉니다.
    **/
    protected function _getCounting()
    {
        $qqqq = $this->db->query('SELECT FOUND_ROWS() AS `Count`');
        $this->intRecentRowsCount = $qqqq->row()->Count;
    }

    protected function _getFieldInfo()
    {
        $this->load->database();
        $fieldss = $this->db->query( "DESCRIBE `{$this->strTableName}`" )->result_array();
        //$arrUpdatableFields
        $this->arrUpdatableFields = array();
        foreach( $fieldss as $FieldItem )
        {
            if( $FieldItem['Key'] == 'PRI' )
            {
                continue;
            }
            $this->arrUpdatableFields[] = $FieldItem['Field'];
        }
    }
}
?>
