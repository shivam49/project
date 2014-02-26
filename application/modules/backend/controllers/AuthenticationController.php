<?php

class Backend_AuthenticationController extends Zend_Controller_Action {
  
    public function init() {
        Zend_Auth::getInstance()->clearIdentity();
        $this->view->currentPageDetails = $this->_helper->page->getDetails();
    	parent::init();
    }

    public function indexAction() {
        $this->_forward('login');
    }


    public function loginAction() {
        $this->_helper->_layout->setLayout('layout-login');
	if ( $this->getRequest()->isPost() ) {
            
            $user_email = mysql_escape_string( $this->_getParam('txt-email') );
            $password = mysql_escape_string( $this->_getParam('txt-password') );
            if ( !empty($user_email) and !empty($password) ) {
                
                $hashPassword = mysql_escape_string( $this->hashPassword($password, PASSWORD_SALT) );

                // Authenticate User
                $authResult = $this->getAuth($user_email, $hashPassword);
                if ( $authResult===true ) {
                    
                    // Access details of logged User
                    //$loggedUser = $this->_helper->app->getLoggedUser();
                    //fb($loggedUser);
                    //$this->_helper->debugger->alert( $loggedUser );

                    // load pannel configuration and page!
                    //$pannelProperties = $this->_helper->app->setupPannel( $loggedUser->iId );
                    
                    // Access details of logged user Pannel
                    //$this->_helper->debugger->alert( $this->_helper->app->getPannel() );
                    
                    // ACL
                    // acceess to all for now!!!

                    // REDIRECT to pannel homepage
                    $this->_redirect('/backend/bands');

                } else {
                    $this->view->errorMessage = 'Please input correct information for login.';
                }

            } else {
                $this->view->errorMessage = 'Please fill all fields for login.';
            }
            
        }
        
    }
    // - [END] Protected function ---------------------------------------------
    
    
    public function hashPassword($password, $salt) {
        $salt = sha1($salt);
        $hash = base64_encode( sha1($password . $salt, true) . $salt );
        return $hash;
    }     

    public function getAuth($email, $pass) {
        $auth = Zend_Auth::getInstance();
        
        $dbAdapter = Zend_Db_Table::getDefaultAdapter();
        $authAdapter = new Zend_Auth_Adapter_DbTable($dbAdapter);
        $authAdapter->setTableName('user_admin')
                    ->setIdentityColumn('vEmail')
                    ->setCredentialColumn('vPassword')
                    ->setCredentialTreatment("? and eStatus='Active'");   

        $authAdapter->setIdentity($email);
        $authAdapter->setCredential($pass);

        $result = $auth->authenticate($authAdapter);
        if( $result->isValid() ){
            $loggedUserData = $authAdapter->getResultRowObject(null, 'vPassword');
            $auth->getStorage()->write( $loggedUserData );
            return true;
        } else {
            return false;
        }
    } 
    
    
}