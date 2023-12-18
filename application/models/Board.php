<?php
class Application_Model_Board
{
    protected $_pk;
    protected $_memberPk;
    protected $_title;
    protected $_writer;
    protected $_content;
    protected $_views;
    protected $_insertTime;
    protected $_updateTime;
    
    public function setPk($pk) {
        $this->_pk = $pk;
    }
    public function getPk() {
        return $this->_pk;
    }
    
    public function setMemberPk($memberPk) {
        $this->_memberPk = $memberPk;
    }
    public function getMemberPk() {
        return $this->_memberPk;
    }
    
    public function setTitle($title) {
        $this->_title = $title;
    }
    public function getTitle() {
        return $this->_title;
    }
}