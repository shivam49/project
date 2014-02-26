<?php

class SignupController extends Zend_Controller_Action {
 public function init() {
//        Zend_Auth::getInstance()->clearIdentity();
        $this->view->currentPageDetails = $this->_helper->page->getDetails();
    	parent::init();
    }
    public function indexAction() {
        $this->view->loggedUser  = $this->_helper->app->getLoggedUser();
        if( $this->view->loggedUser ){
                $this->_redirect('/');
                $this->_helper->_layout->setLayout('layout-logout'); // show account info
        }else{
            $this->_helper->_layout->setLayout('layout-not-homepage'); // show signUp & Login links
        } 

    }
    
    public function validateFormAction(){
        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->getHelper('layout')->disableLayout();
        $allParams = $this->_getAllParams();
        $signupForm = new Frontend_Form_SignUpBandsForm();
        $signupForm->isValid($allParams);
        $errMessage = $signupForm->getMessages();
        echo Zend_Json::encode($errMessage);
    }
    
        
    public function validateFormFansAction(){
        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->getHelper('layout')->disableLayout();
        $allParams = $this->_getAllParams();
        $signupForm = new Frontend_Form_SignUpFansForm();
//        $validator = $signupForm->getElement('vEmailConfirm')->getValidator('identical');
//        $validator->setToken($allParams['vEmail']);
        $signupForm->isValid($allParams);
        $errMessage = $signupForm->getMessages();
        echo Zend_Json::encode($errMessage);
    }
    public function fansAction(){
        $allParams = $this->_getAllParams();
        $this->view->loggedUser  = $this->_helper->app->getLoggedUser();
        if( $this->view->loggedUser ){
                $this->_redirect('/');
                $this->_helper->_layout->setLayout('layout-logout'); // show account info
        }else{
            $this->_helper->_layout->setLayout('layout-not-homepage'); // show signUp & Login links
        } 
         $this->view->form= new Frontend_Form_SignUpFansForm();
         //$validator = $this->view->form ->getElement('vEmailConfirm')->getValidator('identical');
         //$validator->setToken($allParams['vEmail']);
            if($this->getRequest()->isPost('save_changes') && $this->view->form->isValid($allParams)){
                //if($allParams['vRef']==1){
                    $fansObj = new Frontend_Model_Fans();
                    $memberObj = new Frontend_Model_Members();
                    $emailNotfi = new Frontend_Model_EmailNotifier();
                    $formValues = $this->view->form->getValidValues( $allParams );
                    $userPass = $formValues['vPassword'];
                    if ( isset($formValues['vPassword']) ) {
                       $formValues['vPassword'] = $this->hashPassword($formValues['vPassword'], PASSWORD_SALT);
                       $dateTime = new DateTime;
                       $formValues['dUser_signup_date']=$dateTime->format('Y-m-d H:m:s');
                       if(  isset( $allParams['facebookid'] ) &&  (strlen($allParams['facebookid']) > 1)  && $this->fbloginchecker()) {
                                $Facebook_login = new Zend_Session_Namespace('Facebook_login');
                                // gen a random password for now
                                $formValues['vPassword']=  $memberObj->hashPasswordDB( md5($allParams['password'].rand(1000000000, 9999999999)) );

                                // double check to make sure it's the same email and facebook id that we received
                                // Otherwise we do not use the facebook id.
                                if( $Facebook_login->email == $allParams['vEmail'] && $Facebook_login->id == $allParams['facebookid']) {
                                    $formValues['vFacebook_id'] = $allParams['facebookid'];
                                }
                                else {
                                        print jsonResponse(array(
                                        $this->view->errorMessage => 'Facebook ID error.'
                                    ));
                                }
                        }
                        $formValues['eStatus'] = 'Active';
                       $result = $memberObj->doInsert($formValues);
                       if(is_numeric($result)){
                           $fansArray = array(
                               'iMember_id'=>$result,
                               'vName' => $formValues['vName'],
                               'vLastname'=>$formValues['vLastname']
                           );
                           $fansResult = $fansObj->doInsert($fansArray);
                           if(is_numeric($fansResult)){
                                $emailNotfi->user_add($result, $userPass);
                                $this->view->form->reset();
                                $this->view->SuccessMessage = '<div>Thank you for registering! username and password has been sent to '.$formValues['vEmail'].'</div>';
                                $authResult = $this->getAuth($formValues['vEmail'], $formValues['vPassword'],'members');

                                $this->view->userId = $result;
                           }else{
                               $this->view->errorMessage = 'Your registration fails';
                           }


                       }else{
                           $this->view->errorMessage = 'Your registration fails';
                       }
                   }  else {
                       $this->view->errorMessage = 'password is wrong';
                   }
               //}else{
                   // $this->view->errorMessage = 'You may not be able to register. Please read the register rules and confirm.';
                //}
            }
            
        
    }
    public function bandsAction(){
        $this->view->loggedUser  = $this->_helper->app->getLoggedUser();
        if( $this->view->loggedUser ){
                $this->_redirect('/');
                $this->_helper->_layout->setLayout('layout-logout'); // show account info
        }else{
            $this->_helper->_layout->setLayout('layout-not-homepage'); // show signUp & Login links
        } 
        $allParams = $this->_getAllParams();
        $this->view->form = new Frontend_Form_SignUpBandsForm();
        if($this->getRequest()->isPost('save_changes') && $this->view->form->isValid($allParams)){
            $bandsObj = new Frontend_Model_Bands();
            $membersObj = new Frontend_Model_Members();
//                    $emailNotfi = new Frontend_Model_EmailNotifier();
            $formValues = $this->view->form->getValidValues( $allParams );
            $userPass = $formValues['vPassword'];
            if ( isset($formValues['vPassword']) ) {
               $formValues['vPassword'] = $this->hashPassword($formValues['vPassword'], PASSWORD_SALT);
               $dateTime = new DateTime;
               $formValues['dUser_signup_date']=$dateTime->format('Y-m-d H:m:s');
               $formValues['eType'] = 'Band';
               $formValues['eStatus'] = 'Active';
               if(  isset( $allParams['facebookid'] ) &&  (strlen($allParams['facebookid']) > 1)  && $this->fbloginchecker()) {
                        $Facebook_login = new Zend_Session_Namespace('Facebook_login');
                        // gen a random password for now
                        $formValues['vPassword']=  $membersObj->hashPasswordDB( md5($allParams['password'].rand(1000000000, 9999999999)) );

                        // double check to make sure it's the same email and facebook id that we received
                        // Otherwise we do not use the facebook id.
                        if( $Facebook_login->email == $allParams['vEmail'] && $Facebook_login->id == $allParams['facebookid']) {
                            $formValues['vFacebook_id'] = $allParams['facebookid'];
                        }
                        else {
                                print jsonResponse(array(
                                $this->view->errorMessage => 'Facebook ID error.'
                            ));
                        }
                }
               $result = $membersObj->doInsert($formValues);
               if(is_numeric($result)){
                   $bandArray = array(
                       'iMember_id' => $result,
                       'vTitle'=> $allParams['vTitle']
                    );
                   $bandResult = $bandsObj->doInsert($bandArray);
                   if(is_numeric($bandResult)){
                       $register_part1 = new Zend_Session_Namespace('part1');
                       $register_part1->result = $result;
                       $register_part1->userpass = $userPass;
                       $register_part1->email = $formValues['vEmail'];
                       $register_part1->pass = $formValues['vPassword'];
                       $this->_redirect('signup/bands-part2');
                   }else{
                       $this->view->errorMessage = 'Your registration fails';
                   }
               }else{
                   $this->view->errorMessage = 'Your registration fails';
               }
           }  else {
               $this->view->errorMessage = 'password is wrong';
           }
        }
    }
    
    public function bandsPart2Action(){
        $this->view->loggedUser  = $this->_helper->app->getLoggedUser();
        if( $this->view->loggedUser ){
                $this->_redirect('/');
                $this->_helper->_layout->setLayout('layout-logout'); // show account info
        }else{
            $this->_helper->_layout->setLayout('layout-not-homepage'); // show signUp & Login links
        } 
        $marketObj = new Frontend_Model_Markets();
        $genresObj = new Frontend_Model_Genres();
        $marketList = $marketObj->getList();
        $this->view->market = $marketList;
        $genresList = $genresObj->getList();
        $this->view->genres = $genresList;
        $stateObj =  new Frontend_Model_State();
        $whereCondition = array("iCountry_id = '?'", array(1));
        $stateList = $stateObj->getList($whereCondition);
        $this->view->state = $stateList;
    }
    
    public function registerBandsAction(){
        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->getHelper('layout')->disableLayout();
        $allParams = $this->_getAllParams();
        //fb($allParams);
        $stepSignupForm = new Frontend_Form_StepSignupForm();
        $emailNotfi = new Frontend_Model_EmailNotifier();
        $stepSignupForm->isValid($allParams);
        $arrFrmErrors = $stepSignupForm->getMessages();
        if ( count($arrFrmErrors) ) {
                print jsonResponse(array(
                'errorMessage' =>$arrFrmErrors
            ));
        }else{
            $bandObj = new Frontend_Model_Bands();
            $arrayUp = array(
                'vGenre_ids' =>  implode(',',$allParams['vGenre_ids']),
                'iMarket_id'=>$allParams['iMarket_id'],
                'iState_id'=>$allParams['iState_id'],
                'vCity'=>$allParams['vCity']
            );
            $register_part1 = new Zend_Session_Namespace('part1');
            $whereCondition = array("iMember_id = '?'",array($register_part1->result));
            $bandResult = $bandObj->doUpdate($arrayUp, $whereCondition);
            if($bandResult){
//                $bandObj->setBand($bandResult);
//                $getPath = $bandObj->getPath();
//                if(!is_dir($getPath)){
//                    mkdir($getPath,0777);
//                    chmod($getPath, 0777);
//
//                    $oldumask = umask(0);    
//                        mkdir($getPath.'/audio',0777);
//                    umask($oldumask);
//
//
//                }
//                if (!is_dir($getPath.'/audio')) {
//                    $oldumask = umask(0);    
//                        mkdir($getPath.'/audio',0777);
//                    umask($oldumask);
//                }
                //$emailNotfi->user_add($register_part1->result, $register_part1->userpass);
                $success = '<div>Thank you for registering...</div>';
                $authResult = $this->getAuth($register_part1->email, $register_part1->pass,'members');
                unset($register_part1);
                print jsonResponse(array(
                   'SuccessMessage' =>$success
                ));
            }else{
                print jsonResponse(array(
                   'errorMessage' =>'error'
               ));
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
