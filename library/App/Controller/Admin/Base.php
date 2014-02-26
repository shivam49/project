<?php
abstract class App_Controller_Admin_Base extends Zend_Controller_Action {

    protected $_loggedUser;
    protected $_currentPageDetails;

    
    public function init() {
        $this->_currentPageDetails = $this->_helper->page->getDetails();
        $this->view->currentPageDetails = $this->_currentPageDetails;
        $this->view->currentSessionId = session_id();
        $this->view->currentSessionIdSecurity = md5(session_id() . PASSWORD_SALT);
        $this->_loggedUser = $this->_helper->app->getLoggedUser();
        $this->view->loggedUser = $this->_loggedUser; 
        parent::init();
    }

    
    public function preDispatch() {
        
        if ( Zend_Auth::getInstance()->hasIdentity() ) {
            
            $userTypeId = $this->_loggedUser->iType_id;
            $currentPosition = $this->_currentPageDetails;
            if ( userHasPermission($userTypeId, $currentPosition, 'backend') ) {
                fb('YES permission');
                parent::preDispatch();
            } else {
                // AJAX check
                if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                    fb('NO AJAX permission');
                    print jsonResponse(array(
                        'success' => false,
                        'error'=>'NotHavePermission'
                    ));
                    die();    
                } else {
                    $this->_redirect('/backend/authentication/login');
                }
                fb('NO permission');
            }
            
        } else {
            fb('Need authentication');
            $this->_redirect('/backend/authentication/login');
        }
        
    }
  
}
?>