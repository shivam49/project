<?php

class App_Model_Template {

    protected $_view = null;
    protected $_templatePath = '';
    
    public function __construct( $path='' ) {
        $this->_view = new Zend_View();
        $this->setTemplatePath( $path );
    }
 
    public function setTemplatePath( $path='' ){
        $front = Zend_Controller_Front::getInstance();
        $frontReq = $front->getRequest();
        $moduleName = $frontReq->module;
        if ( $path!='' ) {
            $this->_templatePath = $path;
        } else {
            $this->_templatePath = "application/modules/{$moduleName}/views/templates";
        }
    }
    
    public function setSuffixTemplatePath( $path ){
        $this->_templatePath .= '/'.$path;
    }

    public function renderTemplate($name, $vars=array()) {
        $this->_view->setScriptPath( $this->_templatePath );
        if ( count($vars) ) {
            $this->_view->assign($vars);
        }
        return $this->_view->render($name);
    }
    
}

?>