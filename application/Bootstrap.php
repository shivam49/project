<?php class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
	
    protected function _initSession() {
        if( isset($_GET['APZSESSID']) and isset($_GET['SECUREAPZ']) and md5($_GET['APZSESSID'].PASSWORD_SALT)==$_GET['SECUREAPZ'] ) {
            Zend_Session::setId($_GET['APZSESSID']);
        }
        Zend_Session::start();
   }

    protected function _initAutoload()
    {
        /* Add autoloader empty namespace */
        $autoLoader = Zend_Loader_Autoloader::getInstance();
        $resourceLoader = new Zend_Loader_Autoloader_Resource(array(
                'basePath' => APPLICATION_PATH . '/modules/frontend' ,
                'namespace' => 'Frontend_',
                'resourceTypes' => array(
                        'form' => array(
                                'path' => 'forms/',
                                'namespace' => 'Form_',
                        ),
                        'model' => array(
                                'path' => 'models/',
                                'namespace' => 'Model_'
                        )
                )
        ));
        

        $resourceLoader = new Zend_Loader_Autoloader_Resource(array(
                'basePath' => APPLICATION_PATH . '/modules/backend',
                'namespace' => 'Backend_',
                'resourceTypes' => array(
                        'form' => array(
                                'path' => 'forms/',
                                'namespace' => 'Form_',
                        ),
                        'model' => array(
                                'path' => 'models/',
                                'namespace' => 'Model_'
                        )
                )
        ));	
        
        /* Return it so that it can be stored by the bootstrap */
        return $autoLoader;
    }

    protected function _initView()
    {
            
            $deviceTemplate = 'default'; // pc browser
        
            // Initialize view
            $view = new Zend_View();

            $view->setHelperPath(realpath(APPLICATION_PATH . '/../library/App/Helper/View/'), 'App_Helper_View');                
            //$view->setHelperPath(realpath(APPLICATION_PATH . '/../library/App/Helper/View/Module/Site/'), 'App_Helper_View_Module_Site');                
            //$view->setHelperPath(realpath(APPLICATION_PATH . '/../library/App/Helper/View/Module/Frontend/'), 'App_Helper_View_Module_Frontend');                
            //$view->setHelperPath(realpath(APPLICATION_PATH . '/../library/App/Helper/View/Module/Backend/'), 'App_Helper_View_Module_Backend');                

            //$view->doctype('XHTML1_STRICT');
            
            $view->headTitle('Project X Ver 1.0');
            $view->headTitle()->setSeparator(' : ');
            
            $view->frontendSkin = "frontend/{$deviceTemplate}";
            $view->backendSkin = "backend/{$deviceTemplate}";
            
            // Add it to the ViewRenderer
            $viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper(
                    'ViewRenderer'
            );
            $viewRenderer->setView($view);
            // Return it, so that it can be stored by the bootstrap
            return $view;
	}


    protected function _initTranslate() {
        $registry = Zend_Registry::getInstance();
		
        date_default_timezone_set('USA/LosAngles');
        Zend_Date::setOptions(array('format_type' => 'php'));

//        $local_name = 'fa';
//        $local_country = 'IR';
//        $locale = new Zend_Locale("{$local_name}_{$local_country}");    
//        
//        /*
//        $module = 'frontend';
//        $controller = 'bug';
//        $action = 'submit';
//        */
//        $translate = new Zend_Translate(
//                array(
//                        'adapter' => 'array',
//                        //'content' => APPLICATION_PATH."/languages/{$local_name}/{$module}/{$controller}/{$action}.php",
//                        'content' => APPLICATION_PATH."/languages/{$local_name}/default.php",
//                        'locale'  => $local_name
//                )
//        );		
//        
//
//        $registry->set('Zend_Locale', $locale);
//        
//        $registry->set('Zend_Translate', $translate);
         
        return $registry;
    }

    protected function _initActionHelper() {
        Zend_Controller_Action_HelperBroker::addPrefix('App_Helper_Action');
        
        /*
       
       // aded by sharifi for thumb libraray
       $autoLoader = Zend_Loader_Autoloader::getInstance();
       $resourceLoader = new Zend_Loader_Autoloader_Resource(array(
                    'basePath' => APPLICATION_PATH,
                    'namespace' => '',
                ));
        $resourceLoader->addResourceType('loader', 'loaders/', 'My_Loader_');
        $autoLoader->pushAutoloader($resourceLoader);
        $autoLoader->pushAutoloader(new My_Loader_Autoloader_PhpThumb());
        
        */
    }

    protected function _initLogger() {    
        $logger = new Zend_Log();
        $writer = new Zend_Log_Writer_Firebug();
        $logger->addWriter($writer);
        Zend_Registry::set('logger', $logger);
        //$logger->log('testtttt php!', 1);
        
        $profiler = new Zend_Db_Profiler_Firebug('All DB Queries');
        $profiler->setEnabled(true);
    
        //$front = Zend_Controller_Front::getInstance();
        //$bootstrap = $front->getParam('bootstrap');
        //$resource = $bootstrap->getPluginResource('db');
        $resource = $this->getPluginResource('db');
        $db = $resource->getDbAdapter();
        Zend_Registry::set('db', $db);
        $db->setProfiler( $profiler );        

        // In production version must active these line of codes!!!
        //$writer->setEnabled(false);
        //$profiler->setEnabled(false);

    }


}


// -----------------------------------------------------------------------------
// Public functions during application
// -----------------------------------------------------------------------------
function fb( $data ) {
    Zend_Registry::get('logger')->debug($data);
}

// '.'.'.'.'.'.'.'.'.'.'.'.'.'.'.'.'.'.'.'.'.'.'.'.'.'.'.'.'.'.'.'.'.'.'.'.'.'.'
/*
function jsonResponse( $successType, $extra=null ) {
    header('Content-type: Application/json');
    $export['success'] = $successType;
    if ( is_array($extra) ) {
        $export = array_merge($export, $extra);
    }
    return Zend_Json::encode( $export );       
}
*/
// '.'.'.'.'.'.'.'.'.'.'.'.'.'.'.'.'.'.'.'.'.'.'.'.'.'.'.'.'.'.'.'.'.'.'.'.'.'.'

function jsonResponse( $extra='' ) {
    //fb ( $extra );
    //header('Content-type: Application/json; charset=utf-8', true, 200);
    Zend_Json::$useBuiltinEncoderDecoder = true;
    $export = Zend_Json::encode( $extra );
    return $export;       
}

// '.'.'.'.'.'.'.'.'.'.'.'.'.'.'.'.'.'.'.'.'.'.'.'.'.'.'.'.'.'.'.'.'.'.'.'.'.'.'

function jsonResponseArray( $extra=null ) {
    //fb ( $extra );
    if ( is_array($extra) ) {
	Zend_Json::$useBuiltinEncoderDecoder = true;
        $contentResult = Zend_Json::encode( $extra );
        $contentResult = substr($contentResult, 1, strlen($contentResult)-2);
    } else {
        $contentResult = $extra;
    }
    return "[{$contentResult}]";       
}

// '.'.'.'.'.'.'.'.'.'.'.'.'.'.'.'.'.'.'.'.'.'.'.'.'.'.'.'.'.'.'.'.'.'.'.'.'.'.'

function prepareErrors( $errArray ) {
    foreach($errArray as $key => $err) {
        if ( is_array($err) ) {
            $errArray[$key] = implode('<br>', prepareErrors( $err ));
        }
    }
    return $errArray;
}

// '.'.'.'.'.'.'.'.'.'.'.'.'.'.'.'.'.'.'.'.'.'.'.'.'.'.'.'.'.'.'.'.'.'.'.'.'.'.'

function prepareCondition( $data ) {
    if ( is_array($data) ) {
        $strParts = explode('?', $data[0]);
        if ( is_array($strParts) ) {
            $export = '';
            $parts = count($strParts)-1;
            for($counter=0; $counter<$parts; $counter++) {
                $export .= $strParts[$counter] . mysql_escape_string( $data[1][$counter] );
            }
            $export .= $strParts[$counter];
            //fb('sql : ' . $export);
            return $export;
        }
        $data = $data[0];
    }
    return mysql_escape_string($data);
}

// '.'.'.'.'.'.'.'.'.'.'.'.'.'.'.'.'.'.'.'.'.'.'.'.'.'.'.'.'.'.'.'.'.'.'.'.'.'.'


// '.'.'.'.'.'.'.'.'.'.'.'.'.'.'.'.'.'.'.'.'.'.'.'.'.'.'.'.'.'.'.'.'.'.'.'.'.'.'

// ----------------------------------------------------------------------------- 

?>