<?php error_reporting(E_ALL & ~E_NOTICE);

// define basic variable of software
$___projectRootPath = realpath(dirname(__FILE__));
$___applicationPath = $___projectRootPath . '/application';
$___libraryPath = $___projectRootPath . '/library';
$___defaultModuleOfApplication = 'frontend';
$___applicationEnvironment = (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production');
$___applicationPasswordSalt = 'APZProjectX';

$___applicationBaseUrl = '';

//------------------------------------------------------------------------------
// Define path to ROOT project directory
defined('PROJECT_ROOT_PATH')
    || define('PROJECT_ROOT_PATH', $___projectRootPath );

// Define path to application directory
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', $___applicationPath);

defined('APPLICATION_BASEURL')
|| define('APPLICATION_BASEURL',  $___applicationBaseUrl);

// Define path to DEFAULT module directory
defined('DEFAULT_MODULE')
    || define('DEFAULT_MODULE',  $___defaultModuleOfApplication);
    
// Define application environment
defined('APPLICATION_ENV')
    || define('APPLICATION_ENV', $___applicationEnvironment);

define('PASSWORD_SALT', $___applicationPasswordSalt);
/*
$password = 'apz';
$salt = sha1(PASSWORD_SALT);        
$hash = base64_encode(sha1($password . $salt, true) . $salt);
print $hash;
*/

// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
    $___libraryPath,
    get_include_path(),
)));


require_once 'Zend/Application.php';  

// Create application, bootstrap, and run
$application = new Zend_Application(
    APPLICATION_ENV, 
    APPLICATION_PATH . '/configs/application.ini'
);

$application->bootstrap()
            ->run();
?>
