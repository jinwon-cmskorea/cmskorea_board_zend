<?php
/**
 * CmskoreaERP AllTests file
 */
/**
 * require bootstrap
 */
require_once __DIR__ . '/bootstrap.php';
/**
 * AllTests
 */
class AllTests extends PHPUnit_Framework_TestSuite {

    public static function suite() {
        $suite = new PHPUnit_Framework_TestSuite('All Tests');


        return $suite;
    }
}