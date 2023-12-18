<?php
/**
 * APPLICATION_PRODUCT
 * @see .htaccess
 * SetEnv APPLICATION_PRODUCT admin or client
 */
@set_time_limit(0);
defined('APPLICATION_PRODUCT')
|| define('APPLICATION_PRODUCT', getenv('APPLICATION_PRODUCT'));

/**
 * 운영 환경
 * APPLICATION_ENV
 */
defined('APPLICATION_ENV')
|| define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));

// Define path to application directory
define('INSTALL_PATH', realpath(dirname(__FILE__) . '/..'));

// Define path to application directory
defined('APPLICATION_PATH')
|| define('APPLICATION_PATH', realpath(INSTALL_PATH . '/application/' . APPLICATION_PRODUCT));

// Define data directory
define('APPLICATION_DATA', realpath(APPLICATION_PATH . '/data'));

// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
    realpath(INSTALL_PATH . '/library'),
    get_include_path(),
)));

/**
 * @see Zend_Controller_Front
 */
require_once 'Zend/Controller/Front.php';
$front = Zend_Controller_Front::getInstance();

/**
 * @see Zend_Application
 */
require_once 'Zend/Application.php';

// Create application, bootstrap, and run
$application = new Zend_Application(
    APPLICATION_ENV,
    APPLICATION_PATH . '/configs/application.ini'
    );

$application->bootstrap()
->run();