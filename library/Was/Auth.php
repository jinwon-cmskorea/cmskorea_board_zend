<?php

class Was_Auth extends Zend_Auth {
    protected $_member;
    
    const SESSION_NAMESPACE = 'cmskoreaMember';
    
    public function __construct() {
        $this->_member = new Was_Member();
    }
    
    public function authenticate($id, $pw) {
        // Cmskorea_Board_Member 로 위임
        $authResult = $this->_member->authenticate($id, $pw);
        // 로그인 성공 시 세션에 회원정보를 저장한다.
        if (!$authResult) {
            $memberInfo = $this->_member->getMember($id);
            $_SESSION[self::SESSION_NAMESPACE] = $memberInfo;
            
            return $authResult;
        } else {
            return $authResult;
        }
    }
    
    public function getMember() {
        if (!isset($_SESSION[self::SESSION_NAMESPACE]) || empty($_SESSION[self::SESSION_NAMESPACE])) {
            throw new Exception('오류 : 회원 정보가 설정되지 않았습니다.');
        }
        
        return $_SESSION[self::SESSION_NAMESPACE];
    }
    
    public function isLogin() {
        try {
            $memberInfo = $this->getMember();
        } catch (Exception $e) {
            return false;
        }
        
        return !empty($memberInfo) ? true : false;
    }
    
    public function logout() {
        unset($_SESSION[self::SESSION_NAMESPACE]);
        
        return true;
    }
}