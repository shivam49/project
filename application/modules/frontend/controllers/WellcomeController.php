<?php



class WellcomeController extends Zend_Controller_Action {

    

    public function init() {

        $this->view->currentPageDetails = $this->_helper->page->getDetails();

    	parent::init();

    }





    public function indexAction() {

        $this->view->loggedUser  = $this->_helper->app->getLoggedUser();

        

//         $this->view->position = 'CelebrityFans';

        if( $this->view->loggedUser ){

		$lisrUser = $this->_helper->app->getLoggedUser();

                $whereCondition = array("iMember_id ='?'",array($lisrUser->iId));

                if($lisrUser->eType == 'Fan'){

                    $userModel = new Frontend_Model_Fans();

                }

                if($lisrUser->eType == 'Band'){

                    $userModel = new Frontend_Model_Bands();

                }

                $listInfo = $userModel->getList($whereCondition);

                $lisrUser->listInfo = $listInfo[0];

                $this->view->loggedUser = $lisrUser;

                $this->_helper->_layout->setLayout('layout-logout-not-homepage'); // show account info

//                $this->view->set_arb = 'no';

                

        }else{

            $this->_helper->_layout->setLayout('layout-not-homepage'); // show signUp & Login links

            $this->_redirect('/'); 

        } 

    }

    

    public function wellcomeAction(){

        $this->_helper->viewRenderer->setNoRender();

        $this->_helper->getHelper('layout')->disableLayout();

        

    }



}

?>