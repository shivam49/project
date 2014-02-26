<?php

class Backend_MarketsController extends Zend_Controller_Action {
  
    
    
    public function init() {

        $this->view->currentPageDetails = $this->_helper->page->getDetails();
        parent::init();
    }
    
    public function indexAction() {
        $this->view->loggedUser = $this->_helper->app->getLoggedUser();
        if ($this->view->loggedUser) {
            $this->_helper->_layout->setLayout('layout'); // show account info
            
        } else {
            $this->_helper->_layout->setLayout('layout-login'); // show signUp & Login links
            $this->_redirect('/backend/authentication');
        }
    }
    
    
    public function marketListAction(){
        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->getHelper('layout')->disableLayout();
        $marketObj = new Frontend_Model_Markets();
        $marketList = $marketObj->getList();
        print jsonResponse(
                        array(
                            'success'=>true,
                            'data'=>$marketList
                        )
        );
    }
    
    
//    public function saveMarketAction(){
//        $this->_helper->viewRenderer->setNoRender();
//        $this->_helper->getHelper('layout')->disableLayout();
//        $marketObj = new Frontend_Model_Markets();
//        $allParams = $this->_getAllParams();
//        $result = $marketObj->doInsert($allParams);
//        if($result){
//            $allParams['iId'] = $result;
//            print jsonResponse(
//                        array(
//                            'success'=>true,
//                            'data'=>$allParams
//                        )
//            );
//        }
//    }
        public function saveMarketAction(){
        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->getHelper('layout')->disableLayout();
        $marketObj = new Frontend_Model_Markets();
        $allParams = $this->_getAllParams();
        $whereCondition = array("vTitle like '?'",array($allParams['vTitle']));
        $market = $marketObj->getList($whereCondition);
        if(empty($market)){
            $result = $marketObj->doInsert($allParams);
            if($result){
                $allParams['iId'] = $result;
                print jsonResponse(
                            array(
                                'success'=>true,
                                'data'=>$allParams
                            )
                );
            }
        }else{
            print jsonResponse(
                            array(
                                'success'=>false,
                            )
                );
        }

    }
    
    
    public function editMarketAction(){
        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->getHelper('layout')->disableLayout();
        $marketObj = new Frontend_Model_Markets();
        $allParams = $this->_getAllParams();
        $whereCondition = array("iId = '?'",array($allParams['pk']));
        $up = $marketObj->doUpdate(array('vTitle'=>$allParams['value']), $whereCondition);
    }
    
    
    
    public function deleteMarketAction(){
        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->getHelper('layout')->disableLayout();
        $marketObj = new Frontend_Model_Markets();
        $allParams = $this->_getAllParams();
        $whereCondition = array("iId = '?'",array($allParams['id']));
        $delete = $marketObj->doDelete($whereCondition);
        if($delete){
            print jsonResponse(
                        array(
                            'success'=>true,
                        )
            );
        }
        
    }
    

}