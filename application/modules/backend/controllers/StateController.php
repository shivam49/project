<?php

class Backend_StateController extends Zend_Controller_Action {
  
    protected $_sub= '';


    public function init() {

        $this->view->currentPageDetails = $this->_helper->page->getDetails();
        parent::init();
    }
    
    public function indexAction() {
        $this->view->loggedUser = $this->_helper->app->getLoggedUser();
        if ($this->view->loggedUser) {
            $this->_helper->_layout->setLayout('layout'); // show account info
//            $stateObj= new Frontend_Model_State();
//            $count= $stateObj->getTotalRecord();
//            $this->view->count= $count;
            
        } else {
            $this->_helper->_layout->setLayout('layout-login'); // show signUp & Login links
            $this->_redirect('/backend/authentication');
        }
        
        $searchSess = new Zend_Session_Namespace('searchState');
        $searchSess->sub= $this->_sub;
    }
    
//    public function searchStateAction(){
//        $this->_helper->viewRenderer->setNoRender();
//        $this->_helper->getHelper('layout')->disableLayout();
//        $allParams = $this->_getAllParams();
//        fb($allParams);
//        $searchSess = new Zend_Session_Namespace('searchState');
//        $stateObj= new Frontend_Model_State();
//        if(array_key_exists('submit', $allParams) or $searchSess->sub == 'submit'){
//            
//            $searchSess->sub= 'submit';
//            
//            $arrayInputSearch= array(
//                'vName'=> $allParams['vName'],
//                'vName_little'=> $allParams['vName_little'],
//                'eStatus'=> $allParams['eStatus']
//            );
//            if(array_key_exists('num', $allParams)){
//                $allParams['num']= $allParams['num']-1;
//                if($allParams['num'] == 0){
//                    $start= $allParams['num'];
//                }  else {
//                    $start= $allParams['num']*10;
//                }
//                settype($start, "string");
//            }  else {
//                $start= '0';
//            }
////            fb($allParams);
////            fb($searchSess->sub);
//            $resultSearch= $stateObj->doFilter($arrayInputSearch, $start);
//            $listState= $resultSearch['data'];
//            $totalRecorde= $resultSearch['totalCount'];
////            fb($resultSearch);
//        }
//    }
    

    public function saveCountryAction(){
        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->getHelper('layout')->disableLayout();
        $allParams = $this->_getAllParams();
        $stateObj= new Frontend_Model_State();
        $arrayInsert= array(
            'vName'=> $allParams['vName'],
            'vName_little'=> $allParams['vName_little'],
            'eStatus'=> $allParams['eStatus'],
            'iCountry_id'=> 1
        );
        $addId= $stateObj->doInsert($arrayInsert);
        print jsonResponse(array(
            'success'=> true
        ));
    }
    
    public function loadStateAction(){
        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->getHelper('layout')->disableLayout();
        $allParams = $this->_getAllParams();
        $stateObj= new Frontend_Model_State();
        $searchSess = new Zend_Session_Namespace('searchState');
        if(array_key_exists('type', $allParams)){
            if($allParams['type'] == 'reset'){
                unset($searchSess->sub);
                unset($searchSess->name);
                unset($searchSess->littleName);
                unset($searchSess->eStatus);
            }
        }
        if(array_key_exists('submit', $allParams) or $searchSess->sub == 'submit'){
            
            $searchSess->sub= 'submit';
            if(array_key_exists('vName', $allParams)){
                $searchSess->name= $allParams['vName'];
            }
            if(array_key_exists('vName_little', $allParams)){
                $searchSess->littleName= $allParams['vName_little'];
            }
            if(array_key_exists('eStatus', $allParams)){
                $searchSess->eStatus= $allParams['eStatus'];
            }
            
            $arrayInputSearch= array(
                'vName'=> $searchSess->name,
                'vName_little'=> $searchSess->littleName,
                'eStatus'=> $searchSess->eStatus
            );
            if(array_key_exists('num', $allParams)){
                $allParams['num']= $allParams['num']-1;
                if($allParams['num'] == 0){
                    $start= $allParams['num'];
                }  else {
                    $start= $allParams['num']*10;
                }
                settype($start, "string");
            }  else {
                $start= '0';
            }
            $resultSearch= $stateObj->doFilter($arrayInputSearch, $start);
            $listState= $resultSearch['data'];
            $totalRecorde= $resultSearch['totalCount'];
        }  else {
            if($allParams['num']){
                $allParams['num']= $allParams['num']-1;
                if($allParams['num'] == 0){
                    $start= $allParams['num'];
                }  else {
                    $start= $allParams['num']*10;
                }
                settype($start, "string");
                $listState= $stateObj->getList('', $start, '10', 'iId', 'DESC');
            }  else {
                $listState= $stateObj->getList('', '0', '10', 'iId', 'DESC');
            }
            $totalRecorde= $stateObj->getTotalRecord();
        }
        print jsonResponse(array(
            'success'=> true,
            'data'=> $listState,
            'totalCount'=> $totalRecorde
        ));
    }
   
    public function deleteStateAction(){
        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->getHelper('layout')->disableLayout();
        $allParams = $this->_getAllParams();
        $stateObj= new Frontend_Model_State();
        $whereCondition= "iId={$allParams['id']}";
        $countww= $stateObj->getTotalRecord();
        $delete= $stateObj->doDelete($whereCondition);
        $count= $stateObj->getTotalRecord();
        print jsonResponse(array(
            'success'=> true,
            'count'=> $count
        ));
    }
    
    public function editLineAction(){
        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->getHelper('layout')->disableLayout();
        $allParams = $this->_getAllParams();
        $stateObj= new Frontend_Model_State();
        if($allParams['name'] == 'eStatus'){
            if($allParams['value'] == 2){
                $allParams['value']= 'Inactive';
            }  else {
                $allParams['value']= 'Active';
            }
        }
        $whereCondition= "iId={$allParams['pk']}";
        $arrayUpdate= array(
            $allParams['name']=> $allParams['value']
        );
        $upId= $stateObj->doUpdate($arrayUpdate, $whereCondition);
        print jsonResponse(array(
            'success'=> true
        ));
    }

}