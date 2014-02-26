<?php

class AuthenticationController extends Zend_Controller_Action {
  
    public function init() {
        Zend_Auth::getInstance()->clearIdentity();
        $this->view->currentPageDetails = $this->_helper->page->getDetails();
    	parent::init();
    }

      
    
    

    public function indexAction() {
    }



    public function loginAction() {
            $this->_helper->viewRenderer->setNoRender();
            $this->_helper->getHelper('layout')->disableLayout();
            if ( $this->getRequest()->isPost() ) {
            $user_email = mysql_escape_string( $this->_getParam('txt-email') );
            $password = mysql_escape_string( $this->_getParam('txt-password') );
            if ( !empty($user_email) and !empty($password) ) {               
                $hashPassword = mysql_escape_string( $this->hashPassword($password, PASSWORD_SALT) );
                $authResult = $this->getAuth($user_email, $hashPassword, 'members');
                if ( $authResult===true ) {
                        $this->_redirect('/');
                } else {
                        $this->_redirect('/login');
                }

            } else {
                    $this->_redirect('/login');
            }
            
        }
    }
    public function forgotPasswordAction() {
        $this->_helper->_layout->setLayout('layout-login');
	if ( $this->getRequest()->isPost() ) {
            
            if ( $this->_getParam('frm-forgot-password') ) {
                $user_email = mysql_escape_string( $this->_getParam('txt-forgot-pass-mail') );
                if ( !empty($user_email) ) {

                    // @todo : action must be here to complete it

                    $this->view->SuccessMessage = 'کلمه عبور جدید به آدرس ایمیل ' . $user_email . ' ارسال شد.';
                } else {
                    $this->view->errorMessage = 'آدرس ایمیل وارد شده معتبر نیست.';
                }
            }
            
        }
        
    }

    
    public function registerAction() {
        $this->_helper->_layout->setLayout('layout-register');
    }
        
    public function loadRegisterFormAction() {
        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->getHelper('layout')->disableLayout();      

        print $this->getRegisterFormContent();
    }

    
    public function saveRegisterAction() {
        $allParams = $this->_getAllParams();
        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->getHelper('layout')->disableLayout();      

        $pannelOrderForm = new Frontend_Form_RegisterOrderForm();
        $pannelOrderForm->isValid( $allParams );
        $json_result = $pannelOrderForm->getMessages();

        if ( $json_result ) {
            
            print $this->jsonResult( 'error', $json_result );

        } else {
            
            $frmValues = $pannelOrderForm->getValidValues( $allParams );
            $frmValues['vPassword'] = $this->hashPassword($frmValues['vPassword'], PASSWORD_SALT); 
                        
            $frmValues['vImg'] = strtolower($frmValues['eSex']) . '.jpg';
            
            $userlModel = new Frontend_Model_User();
            $resultUserId = $userlModel->doInsert($frmValues);
            $frmValues['iOwner_id'] = $resultUserId; 
            
            if( $resultUserId ) {
                
                $pannelModel = new Frontend_Model_Pannel();
                $resultPannelId = $pannelModel->doInsert( $frmValues );
                
                if( $resultPannelId ) {
                    print $this->jsonResult( 'success', $frmValues );
                } else {
                    print $this->jsonResult( 'error', 'در حال حاضر موقتا قادر به انجام درخواست شما نیستیم' );
                }
                
            } else {
                print $this->jsonResult( 'error', 'در حال حاضر موقتا قادر به انجام درخواست شما نیستیم' );
            }
            
        }               

    }
    // - [END] Public function ------------------------------------------------


    
    // - [START] Protected function -------------------------------------------
    protected function getRegisterFormContent() {
        $pannelOrderForm = new Frontend_Form_RegisterOrderForm();
        //$pannelOrderForm->setAction(Zend_Controller_Front::getInstance()->getBaseUrl() . "/{$this->_projectName}/{$this->_moduleName}/register");
        $pannelOrderForm->setMethod('post');
        //$pannelOrderForm->setAttrib('enctype', 'multipart/form-data');
        $pannelOrderForm->setAttrib('id', 'frmRegisterPannel');

        return  $pannelOrderForm;          
    }
    
    
    protected function jsonResult( $type, $data ) {
        header('Content-type: Application/json');
        return Zend_Json::encode( 
                array(
                    'result' => $type,
                    'error' => $data
                ) 
              );
    }
    // - [END] Protected function ---------------------------------------------
    
    
    public function hashPassword($password, $salt) {       
        $hash =md5(substr(md5($password),4,25));
        return $hash;
    }     

    public function getAuth($email, $pass, $table) {
        $auth = Zend_Auth::getInstance();
        
        $dbAdapter = Zend_Db_Table::getDefaultAdapter();
        $authAdapter = new Zend_Auth_Adapter_DbTable($dbAdapter);
        $authAdapter->setTableName($table)
                    ->setIdentityColumn('vEmail')
                    ->setCredentialColumn('vPAssword');
//                    ->setCredentialTreatment("? and eStatus='Active'");   

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