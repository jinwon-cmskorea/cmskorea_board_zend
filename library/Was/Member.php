<?php

class Was_Member {
    protected $_memberTable;
    protected $_identityTable;
    
    public function __construct() {
        $this->_memberTable = new Was_Member_Table_Member();
        $this->_identityTable = new Was_Auth_Table_AuthIdentity();
    }
    
    public function registMember(array $datas) {
        //유효성 검사
        $validate = new Zend_Validate_Regex(array('pattern' => '/[~!@#$%^&*()_+|<>?:{}]/'));
        if (!($validate->isValid($datas['pw']))){
            throw new Exception("데이터 전달에 실패했습니다. 비밀번호는 특수문자 1개 필수입니다. 다시 작성해주세요.");
        }
        $insertData = array(
                'id' => $datas['id'],
                'pw' => md5($datas['pw']),
                'name' => $datas['name'],
                'insertTime' => new Zend_Db_Expr('NOW()')
        );
        $this->_memberTable->insert($insertData);
        
        $insertData = array(
                'id' => $datas['id'],
                'name' => $datas['name'],
                'telNumber' => str_replace('-', '', $datas['telNumber']),
                'email' => $datas['email'],
                'position' => '5',
                'insertTime' => new Zend_Db_Expr('NOW()'),
                'updateTime' => new Zend_Db_Expr('NOW()')
        );
        $this->_identityTable->insert($insertData);
    }
    
    public function getMember($id) {
        $select = $this->_memberTable->select()->where('id = ?', $id);
        return $this->_memberTable->fetchAll($select);
    }
    
    public function authenticate($id, $pw) {
        $select = $this->_identityTable->select()->where('id = ?', $id)
                                                 ->where('pw = ?', $pw);
        $result = $this->_identityTable->fetchAll($select);
        return count($result) > 0 ? '' : "아이디 또는 비밀번호가 일치하지 않습니다.";
    }
}