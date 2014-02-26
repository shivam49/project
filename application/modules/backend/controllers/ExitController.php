<?php

class Backend_ExitController extends Zend_Controller_Action {
  
    public function indexAction() {
        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->getHelper('layout')->disableLayout();
        
        Zend_Auth::getInstance()->clearIdentity();
        Zend_Session::destroy();
        
        $this->_redirect('/backend/authentication');    
    }

}