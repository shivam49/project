<?php

class Backend_IndexController extends Zend_Controller_Action {
    
    public function init() {
        $this->view->currentPageDetails = $this->_helper->page->getDetails();
    	parent::init();
    }

    public function indexAction() {
    }

}
?>