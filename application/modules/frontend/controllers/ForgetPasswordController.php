<?php



class ForgetPasswordController extends Zend_Controller_Action {

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

    

    public function sendPasswordAction(){

        $this->_helper->viewRenderer->setNoRender();

        $this->_helper->getHelper('layout')->disableLayout();

        $allParams = $this->_getAllParams();

        $forgetPasswordForm = new Frontend_Form_ForgetPasswordForm();

        $forgetPasswordForm->isValid( $allParams );

        $arrFrmErrors = $forgetPasswordForm->getMessages();

        if ( count($arrFrmErrors) ) {

            print jsonResponse(array(

                'errorMessage' =>$arrFrmErrors

            ));

        }else{

            $memeberObj = new Frontend_Model_Members();

            $emailNotif = new Frontend_Model_EmailNotifier();

            $whereCondition = array("vEmail='?'", array($allParams['vEmail']));

            $getUserInfo = $memeberObj->getList($whereCondition);

            $NewPassword = $memeberObj->randString( 10 );

            $newPasswordHash = $memeberObj->hashPassword( $NewPassword );

            $security = md5("{$getUserInfo[0]['iId']}-{$newPasswordHash}-{$getUserInfo[0]['vEmail']}");

//            $arrayUP = array(

//                'vPassword' => $newPasswordHash

//            );

//            $result = $memeberObj->doUpdate($arrayUP, $whereCondition);

//            if(is_numeric($result)){

            

            $emailNotif->forget_password_user($getUserInfo[0]['iId'] , $NewPassword, $newPasswordHash, $security);

            print jsonResponse(array(

                'SuccessMessage' => 'Send Reset Password With Mail.Please Check Mail.'

            ));

                

//            }

        }

    }

    

    

    public function confirmAction() {

//        $this->_helper->_layout->setLayout('layout-login');

        

        $allParams = $this->_getAllParams();

        $whereCondition = array("iId = '?'",array($allParams['user']));

        $memnerObj = new Frontend_Model_Members();

        $memberLi = $memnerObj->getList($whereCondition);

        if ( $allParams['act'] == 'c' and $allParams['security'] == md5("{$allParams['user']}-{$allParams['key']}-{$memberLi[0]['vEmail']}") ){ // means confirm

             $this->view->memId = $allParams['user'];

       } else {

            $this->view->passwordInvalidLink = 'ERROR : Invalid password confirmation link.';

       }

         

         

    }

    

    

    public function changePasswordAction(){

        $this->_helper->viewRenderer->setNoRender();

        $this->_helper->getHelper('layout')->disableLayout();

        $allParams = $this->_getAllParams();

        $changePasswordForm = new Frontend_Form_ChangePasswordForm();

        $changePasswordForm->isValid($allParams);

        $arrFrmErrors = $changePasswordForm->getMessages();

        if ( count($arrFrmErrors) ) {

            print jsonResponse(array(

                'errorMessage' =>$arrFrmErrors

            ));

        }else{

             $memberObj = new Frontend_Model_Members();

             $newPasswordHash = $memberObj->hashPasswordDB( $allParams['vPassword'] );

             $arr_data = array('vPassword'=> $newPasswordHash);

             $whereCondition  =  array("iId = '?'", array($allParams['memId']));

             $updateResult = $memberObj->doUpdate($arr_data, $whereCondition);

             if($updateResult){

                 $emailnotif = new Frontend_Model_EmailNotifier();

                 $emailnotif->send_new_password($allParams['memId'], $allParams['vPassword']);

                 print jsonResponse(array(

                    'SuccessMessage' => 'password changed success'

                ));

            } else {

                print jsonResponse(array(

                    'errorMessage' =>'other'

                ));

                            

            }

        }

    }

    

}



?>

