<?php

class SignoutController extends Zend_Controller_Action {
 public function init() {
//        Zend_Auth::getInstance()->clearIdentity();
        $this->view->currentPageDetails = $this->_helper->page->getDetails();
    	parent::init();
    }
    public function indexAction() {
      $this->_helper->_layout->setLayout('layout');
        Zend_Auth::getInstance()->clearIdentity();
        Zend_Session::destroy();
        
        $this->_redirect('/'); 
    }
    
}

?>
