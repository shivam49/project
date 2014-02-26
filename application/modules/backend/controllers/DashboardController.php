<?php

class Backend_DashboardController extends App_Controller_Admin_Base {
    
    public function init() {
        $this->view->currentPageDetails = $this->_helper->page->getDetails();
    	parent::init();
    }

    public function indexAction() {
        $this->_helper->_layout->setLayout('layout');
    }

}