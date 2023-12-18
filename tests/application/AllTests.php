<?php
/**
 * CmskoreaERP AllTests file
 */
/**
 * require bootstrap
 */
require_once __DIR__ . '/bootstrap.php';
/**
 * Was_Application_AllTests
 */
class Was_Application_AllTests extends PHPUnit_Framework_TestSuite {

    public static function suite() {
        $suite = new PHPUnit_Framework_TestSuite('Was Application All Tests');

        $suite->addTestFile(__DIR__ . '/BootstrapTest.php');

        return $suite;
    }
}