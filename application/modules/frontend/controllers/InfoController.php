<?php



class InfoController extends Zend_Controller_Action {

    

    public function init() {

        $this->view->currentPageDetails = $this->_helper->page->getDetails();

    	parent::init();

    }





    public function indexAction() {
		$this->view->loggedUser  = $this->_helper->app->getLoggedUser();
         if($this->view->loggedUser){
             $this->_helper->_layout->setLayout('layout-logout-not-homepage');
         }else{
            $this->_helper->_layout->setLayout('layout-not-homepage'); // show signUp & Login links
        }
    }



}

?>