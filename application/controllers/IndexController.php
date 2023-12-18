<?php
class IndexController extends Zend_Controller_Action {

    /* (non-PHPdoc)
     * @see Zend_Controller_Action::init()
     */
    public function init() {
    }

    public function indexAction() {
        Zend_Debug::dump('test');
    }

}