<?php

class BrowseController extends Zend_Controller_Action {

    protected $_rowPerPage = 5;
    protected $_startPoint = 0;
    protected $_sortField = 'totalVote';
    protected $_sortType = 'DESC';
    protected $_searchValue = '';

    public function init() {

        $this->view->currentPageDetails = $this->_helper->page->getDetails();
        parent::init();
    }

    public function indexAction() {

        $this->view->loggedUser = $this->_helper->app->getLoggedUser();
        if ($this->view->loggedUser) {
            $this->_helper->_layout->setLayout('layout-logout-not-homepage'); // show account info
        } else {
            $this->_helper->_layout->setLayout('layout-not-homepage'); // show signUp & Login links
        }
        
        $searchSess = new Zend_Session_Namespace('searchBrowse');
        if ( !isset($searchSess->sortType) ) {
            $searchSess->sortType = $this->_sortType;
            $searchSess->sortField = $this->_sortField;
            $searchSess->searchValue = $this->_searchValue;
        }
        
    }

    
    public function getList() {
        $db = Zend_Registry::get('db');
        
        if ( $this->_searchValue != '' ) {
            $searchVal = $db->quote($this->_searchValue);
            $searchVal = substr($searchVal, 1, strlen($searchVal)-2);
            $sqlStr = "select iId from bands where( vTitle like '%{$searchVal}%' and Not isnull(iMarket_id) )";
            $stmt = $db->query( $sqlStr );
            $rows = $stmt->fetchAll();
            $totalBand = count($rows);
            $page = ceil($totalBand / $this->_rowPerPage);
            
            $sqlStr =  "select 
                                b.iId as bandId,
                                b.iMember_id as memberId,
                                b.vTitle as bandTitle,
                                b.vCity as city,
                                s.vName_little as stateLittle,
                                ( select i.vImg from images as i where (b.iMember_id = i.iMember_id and i.eProfile_default='Yes') limit 1 ) as bandImage,
                                ( select count(v.iId) from votes as v where(b.iId = v.iBand_id) ) as totalVote,
                                ( select sum(t.iId) from tracks as t where(b.iId = t.iBand_id) ) as totalTrackPlay,
                                ( select count( DISTINCT v2.iBand_id) from votes as v2 where(b.iId = v2.iBand_id) ) as totalFan
                        from 
                                bands as b
                        left join
                                state as s 
                        ON 
                                b.iState_id = s.iId
                        WHERE( b.vTitle like '%{$searchVal}%' and Not isnull(b.iMarket_id))        
                        ORDER BY {$this->_sortField} {$this->_sortType}
                        LIMIT {$this->_startPoint}, {$this->_rowPerPage}
                       ";
        } else {
            
            $sqlStr = "select iId from bands WHERE(Not isnull(iMarket_id))";
            $stmt = $db->query( $sqlStr );
            $rows = $stmt->fetchAll();
            $totalBand = count($rows);
            $page = ceil($totalBand / $this->_rowPerPage);
            
            $sqlStr =  "select 
                                b.iId as bandId,
                                b.iMember_id as memberId,
                                b.vTitle as bandTitle,
                                b.vCity as city,
                                s.vName_little as stateLittle,
                                ( select i.vImg from images as i where (b.iMember_id = i.iMember_id and i.eProfile_default='Yes') limit 1 ) as bandImage,
                                ( select count(v.iId) from votes as v where(b.iId = v.iBand_id) ) as totalVote,
                                ( select sum(t.iId) from tracks as t where(b.iId = t.iBand_id) ) as totalTrackPlay,
                                ( select count( DISTINCT v2.iBand_id) from votes as v2 where(b.iId = v2.iBand_id) ) as totalFan
                        from 
                                bands as b
                        left join
                                state as s 
                        ON 
                                b.iState_id = s.iId
                        WHERE(Not isnull(b.iMarket_id))        
                        ORDER BY {$this->_sortField} {$this->_sortType}, bandTitle ASC, bandId DESC
                        LIMIT {$this->_startPoint}, {$this->_rowPerPage}
                       ";
                        
        }
        $stmt = $db->query( $sqlStr );
        $rows = $stmt->fetchAll();
        
        if ( $totalBand ) {
            return array(
                'rows' => $rows,
                'totalPages' => $page
            );
        } else {
            return false;
        }
        
    }
    
    public function getBandsAction() {

        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->getHelper('layout')->disableLayout();
        
        $allParams = $this->_getAllParams();
        
        if ($allParams['page'] > 1) {
            $this->_startPoint = ($allParams['page'] - 1) * $this->_rowPerPage;
        } else {
            $this->_startPoint = 0;
        }

        $returnData = $this->getList();
        
        if ( $returnData ) {
            print jsonResponse(
                            array(
                                'success' => TRUE,
                                'data' => $returnData['rows'],
                                'count' => $returnData['totalPages']
                            )
                  );
        } else {
            print jsonResponse(
                            array(
                                'success' => false
                            )
                  );
        }   

    }

    
    
    
    public function sortListAction() {

        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->getHelper('layout')->disableLayout();

        $allParams = $this->_getAllParams();
        fb($allParams);
        if ($allParams['page'] > 1) {
            $this->_startPoint = ($allParams['page'] - 1) * $this->_rowPerPage;
        } else {
            $this->_startPoint = 0;
        }

        $searchSess = new Zend_Session_Namespace('searchBrowse');
        if ( $allParams['data-sort'] ) {
            if ( $allParams['data-sort'] == 'SORT_DESC' ) {
                $this->_sortType = 'DESC';
            } else {
                $this->_sortType = 'ASC';
            }
        $searchSess->sortType = $this->_sortType;
        } else {
            $this->_sortType = $searchSess->sortType;
        }

        if ( $this->_sortType == 'ASC') {
            $toggleSort = 'SORT_DESC';
        } else {
            $toggleSort = 'SORT_ASC';
        }
        
//        if ( $allParams['sortBySearch'] ) {
//            $this->_searchValue = $allParams['sortBySearch'];
//            $searchSess->searchValue = $this->_searchValue;
//        } else {
//            $this->_searchValue = $searchSess->searchValue;
//        }

        if ( $allParams['sortType'] ) {
            switch ($allParams['sortType']) {

                case 'sort_by_name_band':
                    $this->_sortField = 'bandTitle';
                    break;

                case 'sort_by_top_songs':
                    $this->_sortField = 'totalTrackPlay';
                    break;

                case 'sort_by_most_fans':
                    $this->_sortField = 'totalFan';
                    break;

                case 'sort_by_most_vote':
                default:
                    $this->_sortField = 'totalVote';
            }
            $searchSess->sortField = $this->_sortField;
        } else {
            $this->_sortField = $searchSess->sortField;
        }

        $returnData = $this->getList();
        
        if ( $returnData ) {
            print jsonResponse(
                            array(
                                'success' => TRUE,
                                'data' => $returnData['rows'],
                                'sort_new' => $toggleSort,
                                'count' => $returnData['totalPages']
                            )
                  );
        } else {
            print jsonResponse(
                            array(
                                'success' => false
                            )
                  );
        }   
        
        
    }


    public function searchAction() {

        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->getHelper('layout')->disableLayout();

        $allParams = $this->_getAllParams();
        if ($allParams['page'] > 1) {
            $this->_startPoint = ($allParams['page'] - 1) * $this->_rowPerPage;
        } else {
            $this->_startPoint = 0;
        }

        $searchSess = new Zend_Session_Namespace('searchBrowse');
        if ( $allParams['search'] ) {
            $this->_searchValue = $allParams['search'];
            $searchSess->searchValue = $this->_searchValue;
        } else {
            $this->_searchValue = $searchSess->searchValue;
        }

        $returnData = $this->getList();
        
        if ( $returnData ) {
            print jsonResponse(
                            array(
                                'success' => TRUE,
                                'data' => $returnData['rows'],
                                'count' => $returnData['totalPages']
                            )
                  );
        } else {
            print jsonResponse(
                            array(
                                'success' => false
                            )
                  );
        }   
    }

}

?>