<?php

class BandsMemberController extends Zend_Controller_Action {
    
    public function init() {
        $this->view->currentPageDetails = $this->_helper->page->getDetails();
    	parent::init();
    }
    
    public function indexAction() {
        $this->view->loggedUser  = $this->_helper->app->getLoggedUser();
         if($this->view->loggedUser){
             $this->_helper->_layout->setLayout('layout-logout-not-homepage');
         }else{
            $this->_helper->_layout->setLayout('layout'); // show signUp & Login links
        }
        
    }
    
//    public function autoJeditableDescAction(){
//        $this->view->loggedUser  = $this->_helper->app->getLoggedUser();
//        $this->_helper->viewRenderer->setNoRender();
//        $this->_helper->getHelper('layout')->disableLayout();
//        $allParams= $this->_getAllParams();
//        $bandsMember= new Frontend_Model_BandsMembers();
//        $whereCondition="iId={$allParams['iId']}";
////        $arrayUp= array('')
//        fb($allParams);
////        print '';
//    }
    
    
    public function editLineAction(){
        $this->view->loggedUser  = $this->_helper->app->getLoggedUser();
        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->getHelper('layout')->disableLayout();
        $bandsMemberObj= new Frontend_Model_BandsMembers();
        $allParams= $this->_getAllParams();
        $arrayUp= array(
            $allParams['name'] => $allParams['value']
        );
        $whereCondition= "iId={$allParams['pk']}";
        $bandsMemberObj->doUpdate($arrayUp, $whereCondition);
//        fb($allParams);
    }


    public function saveMemberAction(){
        $this->view->loggedUser  = $this->_helper->app->getLoggedUser();
        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->getHelper('layout')->disableLayout();
        $allParams= $this->_getAllParams();
//        fb($allParams);
        $bandsMemberObj= new Frontend_Model_BandsMembers();
        $bandObj= new Frontend_Model_Bands();
        if($this->view->loggedUser->eType == 'Band') {
            if($allParams['submit'] == 'add'){
                $whereCondition= "iMember_id={$this->view->loggedUser->iId}";
                $listBand= $bandObj->getList($whereCondition, '', '', '', '', array('iId'));
                $name= $allParams['name'];
                $arrayInsert= array(
                    'vName_lastname'=> $name,
                    'vInstrument'=> $allParams['instrument'],
                    'iBand_id'=> $listBand[0]['iId']
                );
                $insertId= $bandsMemberObj->doInsert($arrayInsert);
                print jsonResponse(array(
                    'success'=> TRUE,
                    'data'=> $arrayInsert,
                    'id'=> $insertId,
                    'mode'=> 'add'
                ));
            } 
//            else{
//                $whereCondition= "iId={$allParams['submit']}";
//                $arrayUp= array(
//                    'vName_lastname'=> $allParams['name'],
//                    'vInstrument'=> $allParams['instrument']
//                );
//                $bandsMemberObj->doUpdate($arrayUp, $whereCondition);
//                print jsonResponse(array(
//                    'success'=> TRUE,
//                    'data'=> $arrayUp,
//                    'id'=> $allParams['submit'],
//                    'mode'=> 'edit'
//                ));
//            }
        }
    }
  
    public function loadMemberAction(){
        $this->view->loggedUser  = $this->_helper->app->getLoggedUser();
        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->getHelper('layout')->disableLayout();
        $bandsMemberObj= new Frontend_Model_BandsMembers();
        $bandsObj= new Frontend_Model_Bands();
        $allParams= $this->_getAllParams();
        if(!empty($this->view->loggedUser->iId)){
            $whereConditionBands= "iMember_id={$this->view->loggedUser->iId}";
            $listbands= $bandsObj->getList($whereConditionBands);
            $whereConditionMember= "iBand_id={$listbands[0]['iId']}";
            $list= $bandsMemberObj->getList($whereConditionMember);
        }  else {
            $list= array();
        }
        
        print jsonResponse(array(
            'success'=> TRUE,
            'data'=> $list
        ));
    }
    
    public function deleteMemberAction(){
        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->getHelper('layout')->disableLayout();
        $allParams= $this->_getAllParams();
        $bandsMemberObj= new Frontend_Model_BandsMembers();
//        fb($allParams);
        $whereCondition= "iId={$allParams['id']}";
        $bandsMemberObj->doDelete($whereCondition);
        print jsonResponse(array(
            'success'=> true,
            'id'=> $allParams['id']
        ));
    }

}
?>