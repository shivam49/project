<?php

class Backend_GenresController extends Zend_Controller_Action {
    protected $_sub= '';
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
        $searchSess = new Zend_Session_Namespace('searchGenres');
        $searchSess->sub= $this->_sub;
    }
    
    public function loadGenresAction(){
        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->getHelper('layout')->disableLayout();
        $allParams = $this->_getAllParams();
        $genresObj= new Frontend_Model_Genres();
        $searchSess = new Zend_Session_Namespace('searchGenres');
        if(array_key_exists('type', $allParams)){
            if($allParams['type'] == 'reset'){
                unset($searchSess->sub);
                unset($searchSess->title);
            }
        }
        if(array_key_exists('submit', $allParams) or $searchSess->sub == 'submit'){
            $searchSess->sub= 'submit';
            if(array_key_exists('vTitle', $allParams)){
                $searchSess->title= $allParams['vTitle'];
            }
            $arrayInputSearch= array(
                'vTitle'=> $searchSess->title
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
            $resultSearch= $genresObj->doFilter($arrayInputSearch, $start);
            $listGenres= $resultSearch['data'];
            $totalRecorde= $resultSearch['totalCount'];
        } else {
            if($allParams['num']){
                $allParams['num']= $allParams['num']-1;
                if($allParams['num'] == 0){
                    $start= $allParams['num'];
                }  else {
                    $start= $allParams['num']*10;
                }
                settype($start, "string");
                $listGenres= $genresObj->getList('', $start, '10', 'iId', 'DESC');
            }  else {
                $listGenres= $genresObj->getList('', '0', '10', 'iId', 'DESC');
            }
            $totalRecorde= $genresObj->getTotalRecord();
        }
        print jsonResponse(array(
            'success'=> true,
            'data'=> $listGenres,
            'totalCount'=> $totalRecorde
        ));
    }
    
    public function editLineAction(){
        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->getHelper('layout')->disableLayout();
        $allParams = $this->_getAllParams();
        $genresObj= new Frontend_Model_Genres();
        $whereCondition= "iId={$allParams['pk']}";
        $arrayUpdate= array(
            $allParams['name']=> $allParams['value']
        );
        $upId= $genresObj->doUpdate($arrayUpdate, $whereCondition);
        print jsonResponse(array(
            'success'=> true
        ));
    }
    
    public function saveGenresAction(){
        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->getHelper('layout')->disableLayout();
        $allParams = $this->_getAllParams();
        $genresObj= new Frontend_Model_Genres();
        $arrayInsert= array(
            'vTitle'=> $allParams['vTitle']
        );
        $addId= $genresObj->doInsert($arrayInsert);
        print jsonResponse(array(
            'success'=> true
        ));
    }


    public function deleteGenresAction(){
        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->getHelper('layout')->disableLayout();
        $allParams = $this->_getAllParams();
        $genresObj= new Frontend_Model_Genres();
        $whereCondition= "iId={$allParams['id']}";
        $delete= $genresObj->doDeleteBack($whereCondition);
        print jsonResponse(array(
            'success'=> true
        ));
    }
}