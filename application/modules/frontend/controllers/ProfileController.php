<?php

class ProfileController extends Zend_Controller_Action {
    
    public function init() {
        $this->view->currentPageDetails = $this->_helper->page->getDetails();
    	parent::init();
    }

//
//    public function indexAction() {
//        $this->view->loggedUser  = $this->_helper->app->getLoggedUser();
//        if($this->view->loggedUser){
////            //fb($this->view->loggedUser);
//             $this->_helper->_layout->setLayout('layout-logout'); // show account info
//             if($this->view->loggedUser->eType == 'Band'){
//                 $this->_redirect('/profile/band');
//             }elseif ($this->view->loggedUser->eType == 'Fan') {
//                 $this->_redirect('/profile/fan');
//            }
//             
//        }else{
//            $this->_helper->_layout->setLayout('layout'); // show signUp & Login links
//        } 
//    }
    ////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////BAND PROFILE//////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////

    public function bandAction(){
        $this->view->loggedUser  = $this->_helper->app->getLoggedUser();
//        fb($this->view->loggedUser);
         if($this->view->loggedUser){
            $this->_helper->_layout->setLayout('layout-logout-not-homepage');
         } else {
            $this->_helper->_layout->setLayout('layout-not-homepage'); // show signUp & Login links
         }
        $allParams= $this->_getAllParams();
        $bandObj= new Frontend_Model_Bands();
        $imagesObj= new Frontend_Model_Images();
        
        $id= $allParams['id'];
        $profile_band = new Zend_Session_Namespace('band');
        $profile_band->iId_band = $id;
        
        if($id == $this->view->loggedUser->iId){
            $allowEditProfile = 'Yes';
        }else{
            $allowEditProfile = 'No';
        }
        if($id == NULL){
            $this->_redirect('');
        }else{
            $listBand= $bandObj->getList('', '', '', '', '', array('iMember_id'));
            foreach ($listBand as $key=>$value){
                $arrayId[]= $listBand[$key]['iMember_id'];
            }
            $validateIdBand= in_array($id, $arrayId);
            if($validateIdBand == TRUE){
                $whereConditionBand= "iMember_id={$id}";
                //chnage by majid
                $listBandName= $bandObj->getList($whereConditionBand, '', '', '', '', array('iId','vTitle','iMember_id'));
                $arrayFinalView['bandName']= $listBandName[0]['vTitle'];
                $arrayFinalView['member_id']= $listBandName[0]['iMember_id'];
                ///////////////////////////////

                $whereConditionPicDefault= array(
                    "iMember_id='?' and eProfile_default='?'",
                    array(
                        $id,
                        'Yes'
                    )
                );
                $listPicDef= $imagesObj->getList($whereConditionPicDefault, '', '', '', '', array('vImg'));
                $arrayFinalView['picProfile']= $listPicDef[0]['vImg'];
                if($arrayFinalView['picProfile'] == NULL){
                    $arrayFinalView['picProfile']= 'images-defaults-profile.jpg';
                }
                $arrayFinalView['eightPhoto']= $this->eightLatestPhoto($whereConditionBand);

                $arrayFinalView['eightVideo']= $this->eightLatestVideos($whereConditionBand);

                $arrayFinalView['overallVotes']= $this->overallVotes($whereConditionBand);

                $arrayFinalView['fanOfBand']= $this->fanOfBand($id);

                $arrayFinalView['track']= $this->track($id, $whereConditionBand);

                $arrayFinalView['location']= $this->location($id, $whereConditionBand);

                $arrayFinalView['market']= $this->market($whereConditionBand);

                $arrayFinalView['genres']= $this->genres($whereConditionBand);

                $arrayFinalView['members']= $this->members($whereConditionBand);

                $arrayFinalView['tracks'] = $this->getTrack();
                
                $arrayFinalView['formInfo'] = $this->getFormEdit();
                    
                $arrayFinalView['aloweEditProfile'] = $allowEditProfile;
//                    fb($arrayFinalView);
                $this->view->arrayFinalView= $arrayFinalView;
                if($this->view->loggedUser == FALSE){
                    $this->view->login= 'notLogin';
//                    $whereConditionBand= "iMember_id={$id}";
//                    $listBandName= $bandObj->getList($whereConditionBand, '', '', '', '', array('vTitle'));
//                    $arrayFinalView['bandName']= $listBandName[0]['vTitle'];
//                    
//                    $whereConditionPicDefault= array(
//                        "iMember_id='?' and eProfile_default='?'",
//                        array(
//                            $id,
//                            'Yes'
//                        )
//                    );
//                    $listPicDef= $imagesObj->getList($whereConditionPicDefault, '', '', '', '', array('vImg'));
//                    $arrayFinalView['picProfile']= $listPicDef[0]['vImg'];
//                    if($arrayFinalView['picProfile'] == NULL){
//                        $arrayFinalView['picProfile']= 'images-defaults-profile.jpg';
//                    }
//                    $this->view->arrayFinalView= $arrayFinalView;
                    
                }  else {
                    $this->view->login= 'login';
                    if($id == $this->view->loggedUser->iId){
                        $this->view->idProfile= 'notVote';
                    }else{
                        $this->view->idProfile= $allParams['id'];
                    }
                    
//                    fb($id);
                    
                    
                }
            } else {
                $this->_redirect('');
            }
//            if($validateIdBand == TRUE and $id != $this->view->loggedUser->iId){
//                $this->view->idProfile= $allParams['id'];
//            }elseif($id == $this->view->loggedUser->iId){
//                $this->view->idProfile= 'notVote';
//            }elseif($validateIdBand == FALSE) {
//                $this->_redirect('');
//            }
        }
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
    public function getTrack(){
        $trackObj = new Frontend_Model_Tracks();
        $profile_band = new Zend_Session_Namespace('band');
        $whereCondition = array("iMember_id = '?' AND eShow_in_profile_playlist='?'", array($profile_band->iId_band, 'Yes'));
        $trackList = $trackObj->getList($whereCondition);
        $sort = array();
        foreach ($trackList as $key => $row)
        {
            $sort[$key] = $row['iId'];
        }
        array_multisort($sort, SORT_DESC, $trackList); //SORT_ASC or SORT_DESC
//        $trackList= array_slice($trackList, 0, 5);
        foreach ($trackList as $key => $value) {
            $trackList[$key]['iClick'] = number_format($value['iClick'], 0, '.', ',');
        }
        return $trackList;
    }

        public function members($whereConditionBand){
        $bandObj= new Frontend_Model_Bands();
        $bandMemberObj= new Frontend_Model_BandsMembers();
        $listBand= $bandObj->getList($whereConditionBand, '', '', '', '', array('iId'));
        $whereConditionBandMember= "iBand_id={$listBand[0]['iId']}";
        $listBandMember= $bandMemberObj->getList($whereConditionBandMember, '', '', '', '', array('vName_lastname'));
        if(!empty($listBandMember)){
            foreach ($listBandMember as $key=>$value){
                $bandMemberArray[]= $listBandMember[$key]['vName_lastname'];
            }
            $bandMemberStr= implode(', ', $bandMemberArray);
            return $bandMemberStr;
        }
    }


    public function genres($whereConditionBand){
        $bandObj= new Frontend_Model_Bands();
        $genresObj= new Frontend_Model_Genres();
        $listBand= $bandObj->getList($whereConditionBand, '', '', '', '', array('vGenre_ids'));
        $idGenres= explode(',', $listBand[0]['vGenre_ids']);
        if($listBand[0]['vGenre_ids'] != ''){
            foreach ($idGenres as $key=>$value){
                $whereConditionGenre="iId={$idGenres[$key]}";
                $listGenres= $genresObj->getList($whereConditionGenre, '', '', '', '', array('vTitle'));
                $genreTitle[]= $listGenres[0]['vTitle'];
            }
            $title= implode(', ', $genreTitle);
            return $title;
        }
    }


    public function market($whereConditionBand){
        $bandObj= new Frontend_Model_Bands();
        $marketObj= new Frontend_Model_Markets();
        $listBand= $bandObj->getList($whereConditionBand, '', '', '', '', array('iMarket_id'));
        $whereConditionMarket= "iId={$listBand[0]['iMarket_id']}";
        $listMarket= $marketObj->getList($whereConditionMarket, '', '', '', '', array('vTitle'));
        return $listMarket[0]['vTitle'];
    }


    public function location($id, $whereConditionBand) {
        $bandObj= new Frontend_Model_Bands();
        $stateObj= new Frontend_Model_State();
        $listBand= $bandObj->getList($whereConditionBand, '', '', '', '', array('vCity', 'iState_id'));
        $city= $listBand[0]['vCity'];
        $idState= $listBand[0]['iState_id'];
        if($idState != ''){
            $whereConditionStat= "iId={$idState}";
            $listState= $stateObj->getList($whereConditionStat, '', '', '', '', array('vName_little'));
        }
        $state= $listState[0]['vName_little'];
        $location= $city.', '.$state;
        return $location;
    }


    public function track($id, $whereConditionBand){
        $trackObj= new Frontend_Model_Tracks();
        $bandObj= new Frontend_Model_Bands();
        $listBand= $bandObj->getList($whereConditionBand, '', '' ,'', '', array('iId'));
        $whereConditionFanOfBand= array(
            "iBand_id='?' and iMember_id='?'",
            array(
                $listBand[0]['iId'], $id
            )
        );
        $listTrack= $trackObj->getList($whereConditionBand, '', '', '', '', array('iId'));
        $count= count($listTrack);
        return $count;
    }


//    public function fanOfBand($id, $whereConditionBand){
//        $fanOfBandObj= new Frontend_Model_Fansofbands();
//        $bandObj= new Frontend_Model_Bands();
//        $listBand= $bandObj->getList($whereConditionBand, '', '' ,'', '', array('iId'));
//        $whereConditionFanOfBand= array(
//            "iBand_id='?' and iMember_id='?'",
//            array(
//                $listBand[0]['iId'], $id
//            )
//        );
//        $listFanOfBand= $fanOfBandObj->getList($whereConditionFanOfBand, '', '', '', '', array('iid'));
//        $count= count($listFanOfBand);
//        return $count;
//    }
    public function fanOfBand($id){
        $voteObj = new Frontend_Model_Votes();
        $whereConditionFanOfBand= array("iBand_id='?'",array($id));
        $count= $voteObj->getCountFansVote($whereConditionFanOfBand);
        return $count;
    }


    public function overallVotes($whereConditionBand){
        $votesObj= new Frontend_Model_Votes();
        $listVote= $votesObj->getList($whereConditionBand, '', '', '', '', array('iId'));
        $count= count($listVote);
        return $count;
    }


  
    public function eightLatestPhoto($whereConditionBand){
        $imagesObj= new Frontend_Model_Images();
        $listEightPic= $imagesObj->getList($whereConditionBand, '', '', '', '', array('iId', 'vImg', 'tDesc', 'dDate_create'));
        if(!empty($listEightPic)){
        $idEghtPic= array();
        foreach ($listEightPic as $key=>$value){
            $idEghtPic[]= $listEightPic[$key]['iId'];
        }
        
        arsort($idEghtPic);
        $countImg = count($idEghtPic);
        $eighLatest= array_slice($idEghtPic, 0, 10);
        foreach ($listEightPic as $key=>$value){
            foreach ($eighLatest as $key1=>$value1){
                if($eighLatest[$key1] == $listEightPic[$key]['iId']) {
                    $resultEightPhoto[]= $listEightPic[$key];
                }
            }
        }
        if($resultEightPhoto == NULL){
            $resultEightPhoto= '';
        }
        
        foreach ($resultEightPhoto as $key2=>$value2){
            list($width, $height)= getimagesize($_SERVER['DOCUMENT_ROOT'].$this->view->currentPageDetails['projectPath']."public/uploaded_resource/frontend/band/image/".$resultEightPhoto[$key2]['vImg']);
            $resultEightPhoto[$key2]['width']= $width;
            $resultEightPhoto[$key2]['height']= $height;
        }
//        fb($resultEightPhoto);
        return array($resultEightPhoto,$countImg);
        }  else {
            $resultEightPhoto= '';
            $countImg= 0;
        }
    }

    public function eightLatestVideos($whereConditionBand){
        $videosObj= new Frontend_Model_Videos();
        $listEightVideo= $videosObj->getList($whereConditionBand, '', '', '', '', array('iId', 'tVideo_embed_code', 'tVideo_image', 'vVideo_title'));
//        fb($listEightVideo);
        $countVideo = count($listEightVideo);
        $idEightVideo= array();
        foreach ($listEightVideo as $key=>$value){
            $idEightVideo[]= $listEightVideo[$key]['iId'];
        }
        arsort($idEightVideo);
        $eightLatestVideo= array_slice($idEightVideo, 0, 9);
        foreach ($listEightVideo as $key=>$value){
            foreach ($eightLatestVideo as $key1=>$value1){
                if($eightLatestVideo[$key1] == $listEightVideo[$key]['iId']) {
                    $resultEightVideo[]= $listEightVideo[$key];
                }
            }
        }
        
        if($resultEightVideo == NULL){
            $resultEightVideo= '';
        }
        return array($resultEightVideo,$countVideo);
    }
    
    public function loadVideoProfileAction(){
        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->getHelper('layout')->disableLayout();
//        $this->view->loggedUser  = $this->_helper->app->getLoggedUser();
        $allParams= $this->_getAllParams();
//        fb($allParams);
        $videoObj= new Frontend_Model_Videos();
        $whereCondition= "iId={$allParams['id']}";
        $listVideo= $videoObj->getList($whereCondition, '', '', '', '', array('tVideo_embed_code'));
//        fb($listVideo[0]);
        $listVideo[0]['tVideo_embed_code']= str_replace("\\", "", $listVideo[0]['tVideo_embed_code']);
//        str_replace("\\", "", $list[0]['tVideo_embed_code']);
        print jsonResponse(array(
            'data'=> $listVideo[0]
        ));
    }



    
        public function bandSaveVoteAction(){
        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->getHelper('layout')->disableLayout();
        $this->view->loggedUser  = $this->_helper->app->getLoggedUser();
        $allParams= $this->_getAllParams();
        $bandsOb= new Frontend_Model_Bands();
        $votesOb= new Frontend_Model_Votes();
        if($allParams['vote']){
            if($allParams['vote'] == 'add'){
                $idMemberComp= $this->view->loggedUser->iId;
                $whereConditionComp= "iMember_id={$idMemberComp}";
                $listVoteComp= $votesOb->getList($whereConditionComp);
                if(!empty($listVoteComp)){
                    foreach ($listVoteComp as $key=>$value) {
                        $listIdSort[]= $listVoteComp[$key]['iId'];
                    }
                    arsort($listIdSort);
                    $firstElement= array_slice($listIdSort, 0, 1);
                    $idComp= $firstElement[0];
                    foreach ($listVoteComp as $key1=>$value1){
                        if($listVoteComp[$key]['iId'] == $idComp) {
                            $dateCompV= $listVoteComp[$key]['dDate'];
                        }
                    }
                    $dateComp= strtotime(date('Y-m-d H:i:s'));
                    $dateCompVotes= strtotime($dateCompV);
                    $comp= $dateComp-$dateCompVotes;
                    $compDay= floor($comp/(3600*24));
                } else {
                    $compDay= 1;
                }
                if($compDay == 0){
                    print jsonResponse(array(
                        'success'=> FALSE
                    ));
                }else{
                    $date= date('Y-m-d H:i:s');
                    $whereCondition= "iMember_id={$allParams['idProfile']}";
                    $listBands= $bandsOb->getList($whereCondition, '', '', '', '', array('iId'));
                    $insertArr= array(
                        "dDate"=> $date,
                        "iMember_id"=> $this->view->loggedUser->iId,
                        "iBand_id"=>$listBands[0]['iId']
                    );
                    $add= $votesOb->doInsert($insertArr);
                    if($add){
                        print jsonResponse(array(
                            'success'=> true
                        ));
                    } else {
                        print jsonResponse(array(
                            'success'=> 'faild'
                        ));
                    }
                }
            }
        }
    }


    
//    public function bandLoadVoteAction(){
//        $this->_helper->viewRenderer->setNoRender();
//        $this->_helper->getHelper('layout')->disableLayout();
//        $this->view->loggedUser  = $this->_helper->app->getLoggedUser();
//        $allParams= $this->_getAllParams();
//        $bandsOb= new Frontend_Model_Bands();
//        $votesOb= new Frontend_Model_Votes();
//        if($allParams['vote']){
//            if($allParams['vote'] == 'add'){
//                $idMemberComp= $this->view->loggedUser->iId;
//                $whereConditionComp= "iMember_id={$idMemberComp}";
//                $listBnadComp= $bandsOb->getList($whereConditionComp, '', '', '', '', array('iId'));
//                $whereConditionVoteComp= array(
//                    "iMember_id='?' and iBand_id='?'",
//                    array(
//                        $idMemberComp,
//                        $listBnadComp[0]['iId']
//                    ));
//                $listVoteComp= $votesOb->getList($whereConditionVoteComp);
//                $dateComp= strtotime(date('m/d/Y'));
//                $comp= $dateComp-$listVoteComp[0]['dDate'];
//                $compDay= date('d', $comp);
//                if($compDay == 01){
//                    print jsonResponse(array(
//                        'success'=> FALSE
//                    ));
//                }
//                else{
//                    $date= date('m/d/Y');
//                    $whereCondition= "iMember_id={$this->view->loggedUser->iId}";
//                    $listBands= $bandsOb->getList($whereCondition, '', '', '', '', array('iId'));
//                    $dateAdd= strtotime($date);
//                    $insertArr= array(
//                        "dDate"=> $dateAdd,
//                        "iMember_id"=> $this->view->loggedUser->iId,
//                        "iBand_id"=>$listBands[0]['iId']
//                    );
////                    $add= $votesOb->doInsert($insertArr);
////                    if($add){
//                        print jsonResponse(array(
//                            'success'=> true
//                        ));
////                    } else {
////                        print jsonResponse(array(
////                            'success'=> 'faild'
////                        ));
////                    }
//                    
//                }
//            }
//        }
//    }


    public function addVoteAction(){
        $this->view->loggedUser  = $this->_helper->app->getLoggedUser();
         if($this->view->loggedUser){
             $this->_helper->_layout->setLayout('layout-logout');
         }else{
            $this->_helper->_layout->setLayout('layout'); // show signUp & Login links
        }
//        $this->_helper->_layout->setLayout('layout-logout');
        $this->_helper->viewRenderer->setNoRender();
//        $this->_helper->getHelper('layout')->disableLayout();
        $allParams= $this->_getAllParams();
//        //fb($allParams);
    }



///////////////////////////////////////////////////////////////////////////////////////////////
    //////////////////////////////////////////////////FAN PROFILE////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////
    public function fanAction(){
        $this->view->loggedUser  = $this->_helper->app->getLoggedUser();
//        fb($this->view->loggedUser);
         if($this->view->loggedUser){
            $this->_helper->_layout->setLayout('layout-logout-not-homepage');
         } else {
            $this->_helper->_layout->setLayout('layout-not-homepage'); // show signUp & Login links
         }
         
         $allParams = $this->_getAllParams();
         $profile_fan = new Zend_Session_Namespace('fan');
         $profile_fan->iMember_id = $allParams['id'];
         if($allParams['id'] == $this->view->loggedUser->iId){
             $allowEdit = 'Yes';
         }else{
             $allowEdit = 'No';
         }
         $whereCondition = array("iId = '?'",array($profile_fan->iMember_id));
         
         $arrayFinalFanView['getFanInfo'] = $this->getInfoFanPage($whereCondition);
         $arrayFinalFanView['aloweEditProfile'] = $allowEdit;
        $this->view->arrayFinalFanView= $arrayFinalFanView;
    }
    
    
       public function getInfoFanPage($whereCondition){
        $memberObj = new Frontend_Model_Members();
        $fanObj = new Frontend_Model_Fans();
        $memberList = $memberObj->getList($whereCondition,'','','','',array('iId','vEmail'));
        $whereCondition1 = array("iMember_id = '?'",array($memberList[0]['iId']));
        $fanList = $fanObj->getList($whereCondition1);
        $fanList[0]['vEmail'] = $memberList[0]['vEmail'];
        return $fanList[0];
        
        
    }
    
    
    //////////////////////////////////////create by majid for edit fan and band profile////////////////////////////////
    
    //-------------------------all action for fans edit
    public function fanEditAction(){
        $this->view->loggedUser  = $this->_helper->app->getLoggedUser();
        if( $this->view->loggedUser ){
                $this->_helper->_layout->setLayout('layout-logout-not-homepage'); // show account info
                if($this->view->loggedUser->eType == 'Band'){
                    $this->_redirect('/profile/band-edit');
                }
        }else{
            $this->_helper->_layout->setLayout('layout'); // show signUp & Login links
            $this->_redirect('/'); 
        } 
    }
     
//    public function getInfoAction(){
//        $this->_helper->viewRenderer->setNoRender();
//        $this->_helper->getHelper('layout')->disableLayout();
//        $params = $this->_helper->app->getLoggedUser();
//        $memObj = new Frontend_Model_Members();
//        $infoListArray = array();
//        $where = array("iId = '?'",array($params->iId));
//        $list = $memObj->getList($where);
//        $wherecO = array("iMember_id = '?'", array($list[0]['iId']));
//        if($list[0]['eType']=='Fan'){
//            $fanObj = new Frontend_Model_Fans();
//            $infoList = $fanObj->getList($wherecO);
//            unset($infoList[0]['iId']);
//            $countryObj = new Frontend_Model_Country();
//            if(is_numeric($infoList[0]['vCountry'])){
//                $countryList = $countryObj->getRecordById($infoList[0]['vCountry']);
//                $country = $countryList['vName'];
//            }else{
//                $country=$infoList[0]['vCountry'];
//            }
//            if(is_numeric($infoList[0]['vShip_country'])){
//                $countryShipList = $countryObj->getRecordById($infoList[0]['vShip_country']);
//                $countryShip = $countryShipList['vName'];
//                
//            }  else {
//                $countryShip=$infoList[0]['vShip_country'];
//            }
//            
//            
//            $infoList[0]['vCountry'] = $country;
//            $infoList[0]['vShip_country']=$countryShip;
//            $dateArr = explode('-', $infoList[0]['dBirth_date']);
//            $infoList[0]['birthYear'] = $dateArr[0];
//            $infoList[0]['birthMonth'] = $dateArr[1];
//            if(substr($dateArr[2], 0, 1)== 0){
//                $infoList[0]['birthDay'] = substr($dateArr[2], 1, 1);
//            }else{
//                $infoList[0]['birthDay'] = $dateArr[2];
//            }
//            
//            $infoListArray = $infoList[0];
//            
//        }
//        if($list[0]['eType']=='Band'){
//            $bandObj = new Frontend_Model_Bands();
//            $infoList = $bandObj->getList($wherecO);
//            unset($infoList[0]['iId']);
//            $infoList[0]['vGenre_ids'] = explode(',', $infoList[0]['vGenre_ids']);
//            if($infoList[0]['iState_id'] != ''){
//                $stateObj = new Frontend_Model_State();
//                $stateN = $stateObj->getRecordById($infoList[0]['iState_id']);
//                $infoList[0]['state_name'] = $stateN['vName'];
//            }
//            
//            if($infoList[0]['iMarket_id'] != ''){
//                $marketObj = new Frontend_Model_Markets();
//                $marketT = $marketObj->getRecordById($infoList[0]['iMarket_id']);
//                $infoList[0]['market_title'] = $marketT['vTitle'];
//            }
//            fb($infoList);
//            if($infoList[0]['vGenre_ids']){
//                $genresObj = new Frontend_Model_Genres();
//                $nameG = array();
//                foreach ($infoList[0]['vGenre_ids'] as $keyG => $valueG) {
//                    $genresT = $genresObj->getRecordById($valueG);
//                    $nameG[] = $genresT['vTitle'];
//
//                }
//                $infoList[0]['genres_title'] = implode(' , ', $nameG);
//            }
//            
//            
//            $infoListArray = $infoList[0];
//            //fb($infoListArray);
//            
//        }
//        $listAll = array_merge($list[0],$infoListArray);
//        print jsonResponse(array(
//            'data' => $listAll
//        ));
//    }
 public function getInfoAction(){
        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->getHelper('layout')->disableLayout();
        $params = $this->_helper->app->getLoggedUser();
        $memObj = new Frontend_Model_Members();
        $infoListArray = array();
        $where = array("iId = '?'",array($params->iId));
        $list = $memObj->getList($where);
        $wherecO = array("iMember_id = '?'", array($list[0]['iId']));
        if($list[0]['eType']=='Fan'){
            $fanObj = new Frontend_Model_Fans();
            $infoList = $fanObj->getList($wherecO);
            unset($infoList[0]['iId']);
            $countryObj = new Frontend_Model_Country();
            if(is_numeric($infoList[0]['vCountry'])){
                $countryList = $countryObj->getRecordById($infoList[0]['vCountry']);
                $country = $countryList['vName'];
            }else{
                $country=$infoList[0]['vCountry'];
            }
            if(is_numeric($infoList[0]['vShip_country'])){
                $countryShipList = $countryObj->getRecordById($infoList[0]['vShip_country']);
                $countryShip = $countryShipList['vName'];
                
            }  else {
                $countryShip=$infoList[0]['vShip_country'];
            }
            
            
            $infoList[0]['vCountry'] = $country;
            $infoList[0]['vShip_country']=$countryShip;
            $dateArr = explode('-', $infoList[0]['dBirth_date']);
            $infoList[0]['birthYear'] = $dateArr[0];
            $infoList[0]['birthMonth'] = $dateArr[1];
            if(substr($dateArr[2], 0, 1)== 0){
                $infoList[0]['birthDay'] = substr($dateArr[2], 1, 1);
            }else{
                $infoList[0]['birthDay'] = $dateArr[2];
            }
            
            $infoListArray = $infoList[0];
            
        }
        if($list[0]['eType']=='Band'){
            $bandObj = new Frontend_Model_Bands();
            $imageObj = new Frontend_Model_Images();
            $infoList = $bandObj->getList($wherecO);
            $whereConditionImage = array("iMember_id = '?' AND eProfile_default='?' ",array($list[0]['iId'] , 'Yes'));
            $listImage = $imageObj->getList($whereConditionImage);
            if(!empty($listImage)){
                $infoList[0]['image_band'] = $listImage[0]['vImg'];
            }
            unset($infoList[0]['iId']);
            $infoList[0]['vGenre_ids'] = explode(',', $infoList[0]['vGenre_ids']);
            if($infoList[0]['iState_id'] != ''){
                $stateObj = new Frontend_Model_State();
                $stateN = $stateObj->getRecordById($infoList[0]['iState_id']);
                $infoList[0]['state_name'] = $stateN['vName'];
            }
            
            if($infoList[0]['iMarket_id'] != ''){
                $marketObj = new Frontend_Model_Markets();
                $marketT = $marketObj->getRecordById($infoList[0]['iMarket_id']);
                $infoList[0]['market_title'] = $marketT['vTitle'];
            }
            if($infoList[0]['vGenre_ids'][0] != NULL){
                $genresObj = new Frontend_Model_Genres();
                $nameG = array();
                foreach ($infoList[0]['vGenre_ids'] as $keyG => $valueG) {
                    $genresT = $genresObj->getRecordById($valueG);
                    $nameG[] = $genresT['vTitle'];

                }
                $infoList[0]['genres_title'] = implode(' , ', $nameG);
            }
            
            $infoListArray = $infoList[0];
            //fb($infoListArray);
            
        }
//        fb($infoList);
        $listAll = array_merge($list[0],$infoListArray);
        print jsonResponse(array(
            'data' => $listAll
        ));
    }

     public function saveFanBasicInfoAction(){
        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->getHelper('layout')->disableLayout();
        $params = $this->_helper->app->getLoggedUser();
        $memberObj = new Frontend_Model_Members();
        $fansObj = new Frontend_Model_Fans();
        $profile_fan = new Zend_Session_Namespace('fan');
        $listBefor = $memberObj->getRecordById($profile_fan->iMember_id);
        $fansForm = new Frontend_Form_SignUpFansForm();
        $allParams = $this->_getAllParams();
        if($listBefor['vEmail'] == $allParams['vEmail']){
            $fansForm->getElement('vEmail')->removeValidator('Zend_Validate_EmailAddress');
            $fansForm->getElement('vEmail')->removeValidator('Zend_Validate_Db_NoRecordExists');
        }
        $fansForm->getElement('vPassword')->setRequired(false);
        $fansForm->isValid( $allParams );
        $arrFrmErrors = $fansForm->getMessages();
        if ( count($arrFrmErrors) ) {
            print jsonResponse(array(
                'errorMessage' =>$arrFrmErrors
            ));
        }else{
            $whereCondition = array("iId = '?'",array($params->iId));
            $array_member = array(
                'vEmail'=>$allParams['vEmail']
            );
            $up = $memberObj->doUpdate($array_member, $whereCondition);
            if(is_numeric($up)){
               $whereConditionFan = array("iMember_id = '?'",array($params->iId));
               $array_fan = array(
                    'vName'=>$allParams['vName'],
                    'vLastname'=>$allParams['vLastname'],
                    'vName_title'=>$allParams['vName_title'],
                    'vWebsite'=>$allParams['vWebsite']
               );
               if(!empty($array_fan)){
                   $upF = $fansObj->doUpdate($array_fan, $whereConditionFan);
               }
                
                print jsonResponse(array(
                    'SuccessMessage' =>'success'
                ));
                 
            }else{
                print jsonResponse(array(
                    'errorMessage' =>'faild'
                ));
            }
            
        }
    }

    //i chnage it
       public function saveFanMoreInfoAction(){
        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->getHelper('layout')->disableLayout();
        $params = $this->_helper->app->getLoggedUser();
        $fansObj = new Frontend_Model_Fans();
        $profile_fan = new Zend_Session_Namespace('fan');
     
        $countryObj = new Frontend_Model_Country();
        
        $allParams = $this->_getAllParams();
        $whereConditionCountry = array("vName = '?'",array($allParams['vCountry']));
        $vCountryList = $countryObj->getList($whereConditionCountry);
        if(!empty($vCountryList)){
            $country = $vCountryList[0]['iId'];
        }else{
            $country = $allParams['vCountry'];
        }
            $array_more = array(
            'vCompany'=>$allParams['vCompany'],
            'vAddress'=>$allParams['vAddress'],
            'vAddress2'=>$allParams['vAddress2'],
            'vCity'=>$allParams['vCity'],
            'vCountry'=>$country,
            'vState'=>$allParams['vState'],
            'vZipcode'=>$allParams['vZipcode'],
            'vPhone'=>$allParams['vPhone'],
            'vFax'=>$allParams['vFax'],
            'dBirth_date'=>$allParams['dBirth_date']
        );
            $whereConditionFan = array("iMember_id = '?'",array($profile_fan->iMember_id));
            $upF = $fansObj->doUpdate($array_more, $whereConditionFan);
            if(is_numeric($upF)){
                print jsonResponse(array(
                    'SuccessMessage' =>'success'
                ));
            }  else {
                print jsonResponse(array(
                    'errorMessage' =>'faild'
                ));
            }
    } 
    
    //i change it
      public function saveFanShipInfoAction(){
        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->getHelper('layout')->disableLayout();
        $params = $this->_helper->app->getLoggedUser();
        $fansObj = new Frontend_Model_Fans();
        $countryObj = new Frontend_Model_Country();
        $profile_fan = new Zend_Session_Namespace('fan');
        $allParams = $this->_getAllParams();
        $whereConditionCountry = array("vName = '?'",array($allParams['vCountry']));
        $vCountryList = $countryObj->getList($whereConditionCountry);
        if(!empty($vCountryList)){
            $country = $vCountryList[0]['iId'];
        }else{
            $country = $allParams['vShip_country'];
        }
        $array_ship = array(
            'vShip_name'=>$allParams['vShip_name'],
            'vShip_lastname'=>$allParams['vShip_lastname'],
            'vShip_address'=>$allParams['vShip_address'],
            'vShip_address2'=>$allParams['vShip_address2'],
            'vShip_city'=>$allParams['vShip_city'],
            'vShip_country'=>$country,
            'vShip_state'=>$allParams['vShip_state'],
            'vShip_zipcode'=>$allParams['vShip_zipcode']
        );
        $whereConditionFan = array("iMember_id = '?'",array($profile_fan->iMember_id));
         $upF = $fansObj->doUpdate($array_ship, $whereConditionFan);
         if(is_numeric($upF)){
             print jsonResponse(array(
                 'SuccessMessage' =>'success'
             ));
         }  else {
             print jsonResponse(array(
                 'errorMessage' =>'faild'
             ));
         }

    }

    public function changePasswordAction(){
        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->getHelper('layout')->disableLayout();
        $params = $this->_helper->app->getLoggedUser();
        $allParams = $this->_getAllParams();
        if(is_numeric($params->iId)){
            $memberObj = new Frontend_Model_Members();
            $listBefor = $memberObj->getRecordById($params->iId);
            $forgrtPassForm = new Frontend_Form_ChangePasswordForm();
            $forgrtPassForm->isValid($allParams);
            $arrFrmErrors = $forgrtPassForm->getMessages();
            if($listBefor['vPassword'] == $memberObj->hashPasswordDB($allParams['CuPassword'])){
                if ( count($arrFrmErrors) ) {
                    print jsonResponse(array(
                            'errorMessage' =>$arrFrmErrors
                    ));
                }else{
                    $whereCondition = array("iId ='?'", array($params->iId));
                    $new_pass_array = array(
                        'vPassword'=>$memberObj->hashPasswordDB($allParams['vPassword'])
                    );
                    $up = $memberObj->doUpdate($new_pass_array, $whereCondition);
                    if(is_numeric($up)){
//                        Zend_Auth::getInstance()->clearIdentity();
                        print jsonResponse(array(
                            'SuccessMessage' =>'Password changed successfully'
                        ));
                    }else{
                        print jsonResponse(array(
                            'errorMessage' =>'faild'
                        ));
                    }
                    //ssssssssssssssssssssss
                    
                }
            }else{
                if($allParams['CuPassword'] == null){
                    $arrFrmErrors['CuPassword']['isEmpty'] = 'Value is required and can`t be empty';
                }  else {
                    $arrFrmErrors['CuPassword']['noMatch'] = 'current password is worng';
                }
                
                print jsonResponse(array(
                            'errorMessage' =>$arrFrmErrors
                ));
            }
            
        }else{
            print jsonResponse(array(
                 'errorMessage' =>'faild1'
             ));
        }
    }
    
 public function bandEditAction(){
        $this->view->loggedUser  = $this->_helper->app->getLoggedUser();
        if( $this->view->loggedUser ){
                $this->_helper->_layout->setLayout('layout-logout-not-homepage'); // show account info
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
                if($this->view->loggedUser->eType == 'Fan'){
                    $this->_redirect('/profile/fan-edit');
                }
        }else{
            $this->_helper->_layout->setLayout('layout'); // show signUp & Login links
            $this->_redirect('/'); 
        } 
    }
    
    
      public function saveBandBasicInfoAction(){
        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->getHelper('layout')->disableLayout();
        $params = $this->_helper->app->getLoggedUser();
        $memberObj = new Frontend_Model_Members();
        $bandsObj = new Frontend_Model_Bands();
        $listBefor = $memberObj->getRecordById($params->iId);
        $bandsForm = new Frontend_Form_SignUpBandsForm();
        $allParams = $this->_getAllParams();
        if($listBefor['vEmail'] == $allParams['vEmail']){
            $bandsForm->getElement('vEmail')->removeValidator('Zend_Validate_EmailAddress');
            $bandsForm->getElement('vEmail')->removeValidator('Zend_Validate_Db_NoRecordExists');
        }
//        $bandsForm->getElement('vEmailConfirm')->setRequired(false);
        $bandsForm->getElement('vPassword')->setRequired(false);
//        $bandsForm->getElement('vPasswordConfirm')->setRequired(false);
        $bandsForm->isValid( $allParams );
        $arrFrmErrors = $bandsForm->getMessages();
        if ( count($arrFrmErrors) ) {
            print jsonResponse(array(
                'errorMessage' =>$arrFrmErrors
            ));
        }else{
            $whereCondition = array("iId = '?'",array($params->iId));
            $array_member = array(
                'vEmail'=>$allParams['vEmail']
            );
            $up = $memberObj->doUpdate($array_member, $whereCondition);
            if(is_numeric($up)){
               $whereConditionFan = array("iMember_id = '?'",array($params->iId));
               $array_ban = array(
                     'vTitle'=>$allParams['vTitle'],
                    'vWebsite'=>$allParams['vWebsite'],
                   'vFacebook'=>$allParams['vFacebook'],
                   'vTwitter'=>$allParams['vTwitter'],
                   'vInstagram'=>$allParams['vInstagram'],
                   'vSpotify'=>$allParams['vSpotify'],
                   'vSoundcloud'=>$allParams['vSoundcloud']
               );
               if(!empty($array_ban)){
                   $upF = $bandsObj->doUpdate($array_ban, $whereConditionFan);
               }
                
                print jsonResponse(array(
                    'SuccessMessage' =>'success'
                ));
                 
            }else{
                print jsonResponse(array(
                    'errorMessage' =>'faild'
                ));
            }
            
        }
    }
    
    
    public function saveBandMoreInfoAction(){
        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->getHelper('layout')->disableLayout();
        $allParams = $this->_getAllParams();
        $params = $this->_helper->app->getLoggedUser();
        $bandObj = new Frontend_Model_Bands();
        $stepSignupForm = new Frontend_Form_StepSignupForm();
        $stepSignupForm->isValid($allParams);
        
        $arrFrmErrors = $stepSignupForm->getMessages();
//        fb($arrFrmErrors);
        if ( count($arrFrmErrors) ) {
                print jsonResponse(array(
                'errorMessage' =>$arrFrmErrors
            ));
        }else{
             $allParams['vGenre_ids'] = implode(',', $allParams['vGenre_ids']);
                $array_more = array(
                'iState_id'=>$allParams['iState_id'],
                'iMarket_id'=>$allParams['iMarket_id'],
                'vGenre_ids'=>$allParams['vGenre_ids'],
                'vCity'=>$allParams['vCity'],
            );
                $whereConditionFan = array("iMember_id = '?'",array($params->iId));
                $upF = $bandObj->doUpdate($array_more, $whereConditionFan);
                if(is_numeric($upF)){
                    print jsonResponse(array(
                        'SuccessMessage' =>'success'
                    ));
                }  else {
                    print jsonResponse(array(
                        'errorMessage' =>'faild'
                    ));
                }
        }
       
    }

        public function getCountryAction(){
        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->getHelper('layout')->disableLayout();
        $allParams = $this->_getAllParams();
        $countryObj =  new Frontend_Model_Country();
        $whereCondition = array("vName like '%?%'", array($allParams['vName']));
        $list = $countryObj->getList($whereCondition);
        print jsonResponse(array(
            'data' => $list
        ));
    }
    
    public function sessionCountryAction(){
        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->getHelper('layout')->disableLayout();
        $allParams = $this->_getAllParams();
        $countryObj = new Frontend_Model_Country();
        $whereCondition = array("vName ='?'",array($allParams['vName']));
        $listCountry = $countryObj->getList($whereCondition);
        if(!empty($listCountry)){
            $countryList = new Zend_Session_Namespace('Country_list');	
            $countryList->country =  $listCountry[0];
        }
        print jsonResponse(array(
            'success'=>true
        ));
        
    }

        public function getStateAction(){
        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->getHelper('layout')->disableLayout();
        $allParams = $this->_getAllParams();
        $countryList = new Zend_Session_Namespace('Country_list');	
        $stateObj =  new Frontend_Model_State();
        $whereCondition = array("iCountry_id = '?' AND vName_little like '%?%'", array($countryList->country['iId'],$allParams['vName']));
        $list = $stateObj->getList($whereCondition);
        print jsonResponse(array(
            'data' => $list
        ));
    }
    
    
    
     public function clickUpdateAction(){
        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->getHelper('layout')->disableLayout();
        $this->view->loggedUser  = $this->_helper->app->getLoggedUser();
        $allParams = $this->_getAllParams();
        $trackObj = new Frontend_Model_Tracks();
        $whereCondition = array("iId = '?'",array($allParams['id']));
        $listTrack = $trackObj->getList($whereCondition,'','','','',array('iClick'));
        $sumClick = $listTrack[0]['iClick']+1;
        $arrayTrack = array(
            'iClick'=>$sumClick
        );
        $update = $trackObj->doUpdate($arrayTrack, $whereCondition);
         
        if(is_numeric($update)){
            print jsonResponse(array(
                'success'=>true,
                'count' =>number_format($sumClick, 0, '.', ',')

            ));
        }
    }
    
        
    public function showMovieAction(){
        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->getHelper('layout')->disableLayout();
        $allParams = $this->_getAllParams();
        $profile_band = new Zend_Session_Namespace('band');
        $whereCondiotion = array("iId = '?' AND iMember_id='?'",array($allParams['id_movie'],$profile_band->iId_band));
        $movieObj = new Frontend_Model_Videos();
        $movieList = $movieObj->getList($whereCondiotion);
        if(!empty($movieList)){
            print jsonResponse(array(
                'success'=>true,
                'data'=>$movieList[0]
            ));
        }
        
        
    }
    
    
    
      public function getUserInfoAction(){
        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->getHelper('layout')->disableLayout();
        $this->view->loggedUser  = $this->_helper->app->getLoggedUser();
        $bandObj = new Frontend_Model_Bands();
        $memberObj = new Frontend_Model_Members();
        $profile_band = new Zend_Session_Namespace('band');
        $whereCondition1 = array("iMember_id = '?'",array($profile_band->iId_band));
        $bandList = $bandObj->getList($whereCondition1);
        if($bandList[0]['iMember_id'] == $this->view->loggedUser->iId){
            $whereCondition2= array("iId ='?'",array($bandList[0]['iMember_id']));
            $memberList = $memberObj->getList($whereCondition2);
            $bandList[0]['vGenre_ids'] = explode(',', $bandList[0]['vGenre_ids']);
            $bandList[0]['vEmail'] = $memberList[0]['vEmail'];
            print jsonResponse(array(
                'success'=>true,
                'data'=>$bandList[0]
            ));
            
        }else{
            print jsonResponse(array(
                'success'=>FALSE
            ));
        }
    }
    
    public function saveBandInfoAction(){
        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->getHelper('layout')->disableLayout();
        $this->view->loggedUser  = $this->_helper->app->getLoggedUser();
        $profile_band = new Zend_Session_Namespace('band');
        $allParams = $this->_getAllParams();
        $bandObj = new Frontend_Model_Bands();
        $memberObj = new Frontend_Model_Members();
        $whereCondition1 = array("iMember_id = '?'",array($profile_band->iId_band));
        $bandList = $bandObj->getList($whereCondition1);
        
        if($bandList[0]['iMember_id'] == $this->view->loggedUser->iId){
            $whereCondition2 = array("iId ='?'",array($this->view->loggedUser->iId));
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
        
    }
    
     public function saveBandOtherInfoAction(){
        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->getHelper('layout')->disableLayout();
        $this->view->loggedUser  = $this->_helper->app->getLoggedUser();
        $profile_band = new Zend_Session_Namespace('band');
        $allParams = $this->_getAllParams();
        $bandObj = new Frontend_Model_Bands();
        $whereCondition1 = array("iMember_id = '?'",array($profile_band->iId_band));
        $bandList = $bandObj->getList($whereCondition1);
        
        if($bandList[0]['iMember_id'] == $this->view->loggedUser->iId){
            $update = $bandObj->doUpdate($allParams, $whereCondition1);
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
        
    }


}
?>