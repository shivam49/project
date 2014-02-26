<?php

class Backend_BandsController extends Zend_Controller_Action {

    protected $_rowPerPage = 5;
    protected $_startPoint = 0;
    protected $_sortField = 'totalVote';
    protected $_sortType = 'DESC';
    protected $_searchValue = '';
    protected $_featureband = '';

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
        
        $searchSess = new Zend_Session_Namespace('searchBrowse');
        if ( !isset($searchSess->sortType) ) {
            $searchSess->sortType = $this->_sortType;
            $searchSess->sortField = $this->_sortField;
            $searchSess->searchValue = $this->_searchValue;
            $searchSess->featureband = $this->_featureband;
            
        }
        
        $viewArray['formInfo'] = $this->getFormEdit();
        $this->view->viewArray = $viewArray;
        
    }

    public function getFormEdit(){
        $marketObj = new Frontend_Model_Markets();
        $genresObj = new Frontend_Model_Genres();
        $stateObj = new Frontend_Model_State();
        $form_array = array();
        $marketList = $marketObj->getList();
        $form_array['marketList'] = $marketList;

        $genresList = $genresObj->getList();
        $form_array['genresList'] = $genresList;
        
        $whereConditionCountry = array("iCountry_id='?'",array(1));
        $stateList = $stateObj->getList($whereConditionCountry);
        $form_array['stateList']=$stateList;
        return $form_array;
        
        
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
                                b.vTitle as bandTitle,
                                b.vCity as city,
                                b.eFeatured as featured,
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
        }
        else {
            if($this->_featureband == 'Yes' or $this->_featureband == 'No'){
                $sqlStr = "select iId from bands WHERE(eFeatured = '{$this->_featureband}' AND Not isnull(iMarket_id))";
            }else{
                $sqlStr = "select iId from bands WHERE(Not isnull(iMarket_id))";
            }
            
            $stmt = $db->query( $sqlStr );
            $rows = $stmt->fetchAll();
            $totalBand = count($rows);
            $page = ceil($totalBand / $this->_rowPerPage);
            if($this->_featureband == 'Yes' or $this->_featureband == 'No'){
                $sqlStr =  "select 
                                b.iId as bandId,
                                b.vTitle as bandTitle,
                                b.vCity as city,
                                b.eFeatured as featured,
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
                        WHERE(b.eFeatured = '{$this->_featureband}' and Not isnull(b.iMarket_id))        
                        ORDER BY {$this->_sortField} {$this->_sortType}, bandTitle ASC, bandId DESC
                        LIMIT {$this->_startPoint}, {$this->_rowPerPage}
                       ";
        
            }else{
                $sqlStr =  "select 
                                b.iId as bandId,
                                b.vTitle as bandTitle,
                                b.vCity as city,
                                b.eFeatured as featured,
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
        
        if($allParams['featureband']){
            $this->_featureband = $allParams['featureband'];
            $searchSess->featureband = $this->_featureband;
        }else{
            $this->_featureband =$searchSess->featureband ;
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
        
//        if ( $returnData ) {
            print jsonResponse(
                            array(
                                'success' => TRUE,
                                'data' => $returnData['rows'],
                                'count' => $returnData['totalPages']
                            )
                  );
//        } else {
//            print jsonResponse(
//                            array(
//                                'success' => false
//                            )
//                  );
//        }   
    }
    
    public function featuredAction(){
        
        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->getHelper('layout')->disableLayout();

        $allParams = $this->_getAllParams();
        $array_featured = array();
        
        if($allParams['type'] == 'set'){
            $array_featured = array(
                'eFeatured'=>'Yes'
            );
        }else{
            $array_featured = array(
                'eFeatured'=>'No'
            );
        }
        
        $whereCondition = array("iId = '?'",array($allParams['id']));
        $bandObj = new Frontend_Model_Bands();
        $update  = $bandObj->doUpdate($array_featured, $whereCondition);
        if($update){
            print jsonResponse(
                                array(
                                    'success'=>true,
                                    'data'=>$update
                                )
            );
        }else{
            print jsonResponse(
                                array(
                                    'success'=>false
                                )
            );
        }
        
        
    }
    
    
    
    public function getUserInfoAction(){
        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->getHelper('layout')->disableLayout();
        $bandObj = new Frontend_Model_Bands();
        $memberObj = new Frontend_Model_Members();
        $allParams = $this->_getAllParams();
        $whereCondition1 = array("iId = '?'",array($allParams['id']));
        $bandList = $bandObj->getList($whereCondition1);
        $whereCondition2= array("iId ='?'",array($bandList[0]['iMember_id']));
        $memberList = $memberObj->getList($whereCondition2);
        $bandList[0]['vGenre_ids'] = explode(',', $bandList[0]['vGenre_ids']);
        $bandList[0]['vEmail'] = $memberList[0]['vEmail'];
        print jsonResponse(array(
            'success'=>true,
            'data'=>$bandList[0]
        ));
    }
    
    
    public function saveBandInfoAction(){
        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->getHelper('layout')->disableLayout();
        $allParams = $this->_getAllParams();
        $bandObj = new Frontend_Model_Bands();
        $memberObj = new Frontend_Model_Members();
        $whereCondition1 = array("iId = '?'",array($allParams['iId']));
        $whereCondition2 = array("iId ='?'",array($allParams['iMember_id']));
        $memberList = $memberObj->getList($whereCondition2);
        $memberForm = new Frontend_Form_SignUpBandsForm();
        if($memberList[0]['vEmail'] == $allParams['vEmail']){
            $memberForm->getElement('vEmail')->removeValidator('Zend_Validate_EmailAddress');
            $memberForm->getElement('vEmail')->removeValidator('Zend_Validate_Db_NoRecordExists');
        }
        $memberForm->getElement('vPassword')->clearValidators();
        $memberForm->getElement('vPassword')->setRequired(false);
        $memberForm->getElement('vTitle')->setRequired(false);

        $arrFrmErrors1 = array();
        $memberForm->isValid($allParams);
        $arrFrmErrors1 = $memberForm->getMessages();

        $arrFrmErrors2 = array();
        $bandForm = new Frontend_Form_StepSignupForm();
        $bandForm->isValid($allParams);
        $arrFrmErrors2 = $bandForm->getMessages();

        $arrFrmErrors3 = array_merge($arrFrmErrors1,$arrFrmErrors2);
        if(count($arrFrmErrors3)){
            print jsonResponse(array(
                'success' => false,
                'errorMessage' => $arrFrmErrors3
            ));
        }else{
            $array_member = array(
                'vEmail'=>$allParams['vEmail']
            );
            $array_band = array(
                'vTitle'=>$allParams['vTitle'],
                'iState_id'=>$allParams['iState_id'],
                'iMarket_id'=>$allParams['iMarket_id'],
                'vGenre_ids'=>implode(',',$allParams['vGenre_ids']),
                'vCity'=>$allParams['vCity'],

            );

            $upMember = $memberObj->doUpdate($array_member,$whereCondition2);
            $upBand = $bandObj->doUpdate($array_band,$whereCondition1);
            if($upMember && $upBand){
                print jsonResponse(array(
                    'success' =>true,

                ));
            }else{
                print jsonResponse(array(
                    'success'=>false
                ));
            }
        }
    }
    
     public function saveBandOtherInfoAction(){
        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->getHelper('layout')->disableLayout();
        $allParams = $this->_getAllParams();
        $bandObj = new Frontend_Model_Bands();
        $whereCondition1 = array("iId = '?'",array($allParams['iId']));
        $bandArray = array(
            'vWebsite'=>$allParams['vWebsite'],
            'vFacebook'=>$allParams['vFacebook'],
            'vTwitter'=>$allParams['vTwitter'],
            'vInstagram'=>$allParams['vInstagram'],
            'vSpotify'=>$allParams['vSpotify'],
            'vSoundcloud'=>$allParams['vSoundcloud']
        );
        $update = $bandObj->doUpdate($bandArray, $whereCondition1);
        if($update){
            print jsonResponse(array(
                'success'=>true,

            ));
        }else{
            print jsonResponse(array(
                'success'=>false,

            ));
        }
        
    }
    
    
    
    public function deleteBandAction(){
        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->getHelper('layout')->disableLayout();
        $allParams = $this->_getAllParams();
        $memberObj = new Frontend_Model_Members();
        $bandObj = new Frontend_Model_Bands();
        $videoObj = new Frontend_Model_Videos();
        $audioObj = new Frontend_Model_Tracks();
        $voteObj = new Frontend_Model_Votes();
        $imagesObj = new Frontend_Model_Images();
        $bandList = $bandObj->getRecordById($allParams['id']);
        $whereCondition1 = array("iMember_id= '?' ",array($bandList['iMember_id']));
        $whereCondition2 = array("iBand_id= '?' ",array($bandList['iId']));
        $videoList = $videoObj->getList($whereCondition1);
        if(!empty($videoList)){
            foreach ($videoList as $keyVi => $valueVi) {
                $whereConditionVideo = array("iId = '?' ",array($valueVi['iId']));
                $delVideo = $videoObj->doDelete($whereConditionVideo);
                
            }
        }
        
        $voteList = $voteObj->getList($whereCondition2);
        if(!empty($voteList)){
            foreach ($voteList as $keyVo => $valueVo) {
                $whereConditionVote = array("iId = '?'",array($valueVo['iId']));
                $delVote = $voteObj->doDelete($whereConditionVote);
                
            }
        }
        
        $trackList = $audioObj->getList($whereCondition1);
        if(!empty($trackList)){
            foreach ($trackList as $keyTr => $valueTr) {
                $whereConditionTrack = array("iId = '?'",array($valueTr['iId']));
                $delTrack = $audioObj->doDelete($whereConditionTrack);
            }
            
        }
        
        $imagesList = $imagesObj->getList($whereCondition1);
        if(!empty($imagesList)){
            foreach ($imagesList as $keyIm => $valueIm) {
                $whereConditionImage = array("iId = '?'",array($valueIm['iId']));
                $delImage = $imagesObj->doDelete($whereConditionImage);
                if($delImage){
                    @unlink($_SERVER['DOCUMENT_ROOT'].$this->view->currentPageDetails['projectPath']."public/uploaded_resource/frontend/band/image/".$valueIm['vImg']);
                }
            }
            
        }
        
       if(!empty($bandList)){
           $bandObj->setBand($bandList['iId']);
           $path = $bandObj->getPath();
           @unlink($_SERVER['DOCUMENT_ROOT'].$this->view->currentPageDetails['projectPath'].$path);
           $whereConditionBand = array("iId= '?' ",array($bandList['iId']));
           $delBand = $bandObj->doDelete($whereConditionBand);
       } 
       $whereConditionMember = array("iId= '?' ",array($bandList['iMember_id']));
       $delmember = $memberObj->doDelete($whereConditionMember);
        print jsonResponse(
                            array(
                                'success'=>true
                            )
        );
        
    }
    
    
    
    
    public function bandInfoAction(){
        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->getHelper('layout')->disableLayout();
        $allParams = $this->_getAllParams();
//        $memberObj = new Frontend_Model_Members();
//        $bandObj = new Frontend_Model_Bands();
        
//        $whereCondition = array("iId = '?'",array($allParams['id']));
//        $bandList
        $db = Zend_Registry::get('db');
        $sqlStr = "select iId from bands WHERE(iId ='{$allParams['id']}')";
        $sqlStr =  "select 
                                b.iId as bandId,
                                b.vTitle as bandTitle,
                                b.vCity as city,
                                b.eFeatured as featured,
                                s.vName_little as stateLittle,
                                ( select m.vEmail from members as m where (b.iMember_id = m.iId and m.eType = 'Band' )) as memberEmail,
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
                        WHERE(b.iId ='{$allParams['id']}')        

                       ";
//            }
            
                        
//        }
        $stmt = $db->query( $sqlStr );
        $rows = $stmt->fetchAll();
        if(!empty($rows)){
            print jsonResponse(
                            array(
                                'success'=>true,
                                'data'=>$rows[0]
                            )
            );
        }else{
            print jsonResponse(
                            array(
                                'success'=>false,
                            )
            );
        }
    }
    
    
    public function memberBandAction(){
        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->getHelper('layout')->disableLayout();
        $allParams = $this->_getAllParams();
        $bandMemberObj =  new Frontend_Model_BandsMembers();
        $whereCondition = array("iBand_id = '?'",array($allParams['id']));
        $bandMembersList = $bandMemberObj->getList($whereCondition);
        
        if(!empty($bandMembersList)){
            print jsonResponse(
                    array(
                        'success'=>true,
                        'data'=>$bandMembersList
                    )
            );
        }
        
        
    }
    
        
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
    }
    
    public function saveBandMemberAction(){
        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->getHelper('layout')->disableLayout();
        $bandsMemberObj= new Frontend_Model_BandsMembers();
        $allParams= $this->_getAllParams();
        $insertId = $bandsMemberObj->doInsert($allParams);
        if($insertId){
            $allParams['iId'] = $insertId;
            print jsonResponse(
                        array(
                            'success'=>true,
                            'data'=>$allParams
                        )
            );
            
        }
    }
    
    
    public function deleteBandMemberAction(){
        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->getHelper('layout')->disableLayout();
        $bandsMemberObj= new Frontend_Model_BandsMembers();
        $allParams= $this->_getAllParams();
        $whereCondtion = array("iId = '?' ",array($allParams['id']));
        $del = $bandsMemberObj->doDelete($whereCondtion);
        print jsonResponse(
                        array(
                            'success'=>true
                        )
        );
    }

}

?>