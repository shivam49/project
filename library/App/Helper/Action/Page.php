<?php

class App_Helper_Action_Page extends Zend_Controller_Action_Helper_Abstract
{

    protected $_currentPageDetails = '';

    public function init() {
        //$prjPath = '/tadbir-kavir/opm/project/ver1-extjs/';
        $prjPath = APPLICATION_BASEURL.'/';
        $this->_currentPageDetails = array(
            // it should upgrade to read from configuration of application ini file
            'projectPath' => $prjPath ,
            'viewPath' => '../application/modules/' . $this->getRequest()->getModuleName() . '/views/scripts/' . $this->getRequest()->getControllerName() . '/',

            'pathPrefix' => $this->getResourcePrefix(),
            'module' => $this->getRequest()->getModuleName(),
            'controller' => $this->getRequest()->getControllerName(),
            'action' => $this->getRequest()->getActionName(),
            'currentPageFilename' => $this->getRequest()->getModuleName() . '-' .
                                     $this->getRequest()->getControllerName() . '-' .
                                     $this->getRequest()->getActionName()

        );
    }

    public function getDetails() {
        return $this->_currentPageDetails;
    }

    protected function getResourcePrefix() {
        $uri = $_SERVER['REQUEST_URI'];
        $decrease = false;
        if ( substr($uri, strlen($uri)-1, 1)!='/' ) {
            //$uri .= '/';
            //print 'decrease: YES';
            $decrease = true;
        }
        //print '<br>uri:'.$uri;
        $path = str_replace(Zend_Controller_Front::getInstance()->getBaseUrl(), '', $uri);
        //print '<br>path:'.$path;
        $path = rtrim($path, '/');
        $path = ltrim($path, '/');
        //print '<br>path2:'.$path;
        if ( $path!='' ) {
            $pathParts = explode('/', $path);
            //var_dump(count($pathParts));
            if ( is_array($pathParts) ) {
                $counter = count($pathParts);
                if ( $decrease ) {
                    $counter--;
                }
                $path = './'. str_repeat('../', $counter);
                //print 'here';
            } else {
                $path = '';
            }
            //$path = rtrim($path, '/');
            //print '<br>path end:'.$path;
            return $path;
        } else {
            return '';
        }
            
    }
    
}
?>