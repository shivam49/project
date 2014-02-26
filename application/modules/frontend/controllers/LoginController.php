<?php



class LoginController extends Zend_Controller_Action {

 public function init() {

//        Zend_Auth::getInstance()->clearIdentity();

        $this->view->currentPageDetails = $this->_helper->page->getDetails();

    	parent::init();

    }

    public function indexAction() {

         $this->view->loggedUser  = $this->_helper->app->getLoggedUser();

        if( $this->view->loggedUser ){

                $this->_redirect('/');

                $this->_helper->_layout->setLayout('layout-logout-not-homepage'); // show account info

                

                

        }else{

            $this->_helper->_layout->setLayout('layout-not-homepage'); // show signUp & Login links

            

        } 

    }

        public function loginAction() {

            $this->_helper->viewRenderer->setNoRender();

            $this->_helper->getHelper('layout')->disableLayout();

            $allParams = $this->_getAllParams();

            if ( $this->getRequest()->isPost('login-form')  ) {

            $user_email = mysql_escape_string( $this->_getParam('vEmail') );

            $password = mysql_escape_string( $this->_getParam('vPassword') );

            $loginForm = new Frontend_Form_LoginForm();

            $loginForm->isValid( $allParams );

            $arrFrmErrors = $loginForm->getMessages();

            if ( count($arrFrmErrors) ) {

                print jsonResponse(array(

                'errorMessage' =>$arrFrmErrors

            ));

                

            }else{

                if ( !empty($user_email) and !empty($password) ) { 

                    $whereCondition1 = array("vEmail = '?'",array($user_email));

                    $memberObj = new Frontend_Model_Members();

                    $bandObj = new Frontend_Model_Bands();

                    $memberList = $memberObj->getList($whereCondition1);

                    $whereCondition2 = array("iMember_id ='?'",array($memberList[0]['iId']));

                    $listBand = $bandObj->getList($whereCondition2);

                    $hashPassword = mysql_escape_string( $this->hashPassword($password, PASSWORD_SALT) );
                    if($hashPassword == $memberList[0]['vPassword']){
                        if($listBand[0]['vGenre_ids'] == '' or $listBand[0]['iMarket_id'] == ''  or $listBand[0]['iMarket_id'] == 0 or $listBand[0]['iState_id'] == '' or $listBand[0]['iState_id'] == 0 or $listBand[0]['vCity'] == ''){

                            $register_part1 = new Zend_Session_Namespace('part1');

                            $register_part1->result = $memberList[0]['iId'];

                            $register_part1->userpass = $password;

                            $register_part1->email = $user_email;

                            $register_part1->pass = $hashPassword;

                            print jsonResponse(array(

                                    'redirect' => 'redirect'

                                )) ;

    //                        $this->view->redirect = 'true';

    //                        $this->_redirect('signup/bands-part2');

                        }

                        else{

                            $authResult = $this->getAuth($user_email, $hashPassword, 'members');

                            if ( $authResult===true ) {

                                print jsonResponse(array(

                                    'SuccessMessage' => 'Login successfully'

                                )) ;

                            } else {
                                print jsonResponse(array(

                                    'errorMessage' => 'username or password is wrong'

                                )) ;

                            }

                        }
                    }else {
                        print jsonResponse(array(

                            'errorMessage' => 'username or password is wrong'

                        )) ;

                    }

                    

                } else {
                    print jsonResponse(array(

                         'errorMessage' => 'username or password is wrong'

                     )) ;

                }

            }

        }

    }

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

    

    

    

    public function fbloginchecker() {

	

	require_once APPLICATION_PATH . '/../library/facebook/facebook.php';



	// read the facebook appid and secret from application.ini

	$config = Zend_Controller_Front::getInstance()->getParam('bootstrap');

	$fbcfg = $config->getOption('facebook');



	// Create our application instance

	$facebook = new Facebook(array(

    		'appId' => $fbcfg['appId'],

    		'secret' => $fbcfg['secret']

	));



	// Get User ID

	$fbuser = $facebook->getUser();

	

	// Get the details of this user if logged in

	if ($fbuser) {    

	    try {

	        $fbuser_profile = $facebook->api('/me');

	        if (isset($fbuser_profile['email']) && strlen($fbuser_profile['email']) > 2) {

	            if (isset($fbuser_profile['id']) && isset($fbuser_profile['first_name']) && isset($fbuser_profile['last_name']) && isset($fbuser_profile['email'])) {

		            $Facebook_login = new Zend_Session_Namespace('Facebook_login');

		            $Facebook_login->id = $fbuser_profile['id'];

		            $Facebook_login->email = $fbuser_profile['email'];

			    return true;

	            }

	        }

	    }

	    catch (FacebookApiException $e) {

	    		$err = $e;

	    }

	}	

	return false;	

    }

    

        public function getAuthFacebook() {



        $auth = Zend_Auth::getInstance();

        $dbAdapter = Zend_Db_Table::getDefaultAdapter();

        $authAdapter = new Zend_Auth_Adapter_DbTable($dbAdapter);

        $authAdapter->setTableName('members')

                    ->setIdentityColumn('vEmail')

                    ->setCredentialColumn('vFacebook_id');



	if( !$this->fbloginchecker() )

		return false;

	$Facebook_login = new Zend_Session_Namespace('Facebook_login');	

	

        $authAdapter->setIdentity($Facebook_login->email);

        $authAdapter->setCredential($Facebook_login->id);

	

        $result = $auth->authenticate($authAdapter);

        if( $result->isValid() ){

            $loggedUserData = $authAdapter->getResultRowObject(null, 'vPassword');

            $auth->getStorage()->write( $loggedUserData );

            return true;

        } else {

            return false;

        }

    } 

    

    

        public function fbloginAction() {

        $this->_helper->viewRenderer->setNoRender();

        $this->_helper->getHelper('layout')->disableLayout();

        

	if( $this->fbloginchecker() ) {

		if( $this->getAuthFacebook() == true) 

			    print jsonResponse(array(

		                'success' => false,

	                        'error' => 'Account already created.',

	                        'redirectUrl' => APPLICATION_BASEURL.'/wellcome'

			    ));	

		else

			    print jsonResponse(array(

			    	'success' => true

			    ));	

	}else {

	        print jsonResponse(array(

	                'success' => false,

	                'error'=> 'No Facebook login found.',

	        ));

	}

	return true;	

    }

}



?>

