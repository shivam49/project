<?php

class IndexController extends Zend_Controller_Action {
    
    public function init() {
        $this->view->currentPageDetails = $this->_helper->page->getDetails();
    	parent::init();
    }


    public function indexAction() {
        $this->view->loggedUser  = $this->_helper->app->getLoggedUser();
        if( $this->view->loggedUser ){
            $this->_helper->_layout->setLayout('layout-logout'); // show account info
        }else{
            $this->_helper->_layout->setLayout('layout'); // show signUp & Login links
        }
        
        $bandsObj= new Frontend_Model_Bands();
        $imageObj= new Frontend_Model_Images();
        $memberObj= new Frontend_Model_Members();
        
        $arrayFinalHome['featuredBands']= $this->featured();
        
        $arrayFinalHome['plays']= $this->plays();
        
        $arrayFinalHome['newlyBands']= $this->newlyBnds();
        
        $arrayFinalHome['mostVotes']= $this->mostVotes();
//        fb($arrayFinalHome['mostVotes']);
        
        $this->view->arrayFinalHomeView= $arrayFinalHome;
    }
    
    public function location($idState, $city){
        $stateObj= new Frontend_Model_State();
        if($idState>0){
            $whereConditionState= "iId={$idState}";
            $listState= $stateObj->getList($whereConditionState, '', '', '', '', array('vName_little'));
            if($city){
                $vCity= $city.', ';
            }  else {
                $vCity= '';
            }
            if(!empty($listState)){
                $state= $listState[0]['vName_little'];
            }  else {
                $state= '';
            }
            $location= $vCity.$state;
        }  else {
            $location= '';
        }
        return $location;
    }


    public function mostVotes(){
        $bandsObj= new Frontend_Model_Bands();
        $stateObj= new Frontend_Model_State();
        $imageObj= new Frontend_Model_Images();
        $voteObj= new Frontend_Model_Votes();
        $listBands= $bandsObj->getList('', '', '', '', '', array('iId', 'iMember_id', 'vTitle', 'iState_id', 'vCity', 'vWebsite'));
        if(!empty($listBands)){
            foreach ($listBands as $key=>$value){
                $whereConditionVote= "iBand_id={$listBands[$key]['iId']}";
                $listVote= $voteObj->getList($whereConditionVote, '', '', '', '', array('iId'));
                
                $listBands[$key]['vote']= count($listVote);

                $listBands[$key]['location']= $this->location($listBands[$key]['iState_id'], $listBands[$key]['vCity']);
//                fb($listBands);
                $whereConditionImg= array(
                    "iMember_id= '?' and eProfile_default= '?'",
                    array(
                        $listBands[$key]['iMember_id'],
                        'Yes'
                    )
                );
                $listImg= $imageObj->getList($whereConditionImg, '', '', '', '', array('vImg'));
                if(empty($listImg)){
                    $listBands[$key]['img']= 'images-defaults-profile.jpg';
                }  else {
                    $listBands[$key]['img']= $listImg[0]['vImg'];
                }
            }
//            fb($listBands);
            $arraySortBands= $this->array_sort($listBands, 'vote', SORT_DESC);
            $arrayMostVoteBands= array_slice($arraySortBands, 0, 4);
        }  else {
            $arrayMostVoteBands= 'noBands';
        }
        
        return $arrayMostVoteBands;
    }


    public function newlyBnds(){
        $bandsObj= new Frontend_Model_Bands();
        $stateObj= new Frontend_Model_State();
        $imageObj= new Frontend_Model_Images();
        $listBands= $bandsObj->getList('', '', '', '', '', array('iId', 'iState_id', 'vCity', 'iMember_id', 'vTitle'));
        if(!empty($listBands)){
            $arraySortBands= $this->array_sort($listBands, 'iId', SORT_DESC);
            $arrayNewlyFoureBands= array_slice($arraySortBands, 0, 4);
            
            foreach ($arrayNewlyFoureBands as $key=>$value){
                
                $arrayNewlyFoureBands[$key]['location']= $this->location($arrayNewlyFoureBands[$key]['iState_id'], $arrayNewlyFoureBands[$key]['vCity']);
                
                $whereConditionImg= array(
                    "iMember_id= '?' and eProfile_default= '?'",
                    array(
                        $arrayNewlyFoureBands[$key]['iMember_id'],
                        'Yes'
                    )
                );
                $listImg= $imageObj->getList($whereConditionImg, '', '', '', '', array('vImg'));
//                fb($listImg);
                if(empty($listImg)){
                    $arrayNewlyFoureBands[$key]['img']= 'images-defaults-profile.jpg';
                }  else {
                    $arrayNewlyFoureBands[$key]['img']= $listImg[0]['vImg'];
                }
            }
        }  else {
            $arrayNewlyFoureBands= 'noBands';
        }
        return $arrayNewlyFoureBands;
    }


    public function plays(){
        $trackObj= new Frontend_Model_Tracks();
        $bandsObj= new Frontend_Model_Bands();
        $stateObj= new Frontend_Model_State();
        
        $listTrack= $trackObj->getList('', '', '', '', '', array('iId', 'vTitle', 'iClick', 'iBand_id','vFile_mp3','vFile_ogg','iMember_id'));
        foreach ($listTrack as $keyLi => $valueLi) {
            $listTrack[$keyLi]['vFile_mp3'] = str_replace("\\", "", $listTrack[$keyLi]['vFile_mp3']);
            $listTrack[$keyLi]['vFile_ogg'] = str_replace("\\", "", $listTrack[$keyLi]['vFile_ogg']);
            $listTrack[$keyLi]['vTitle'] = str_replace("\\", "", $listTrack[$keyLi]['vTitle']);
        }
        
        $arraySortTrack= $this->array_sort($listTrack, 'iClick', SORT_DESC);
        $arrayFiveTrack= array_slice($arraySortTrack, 0, 5);
        foreach ($arrayFiveTrack as $key=>$value){
            $whereConditionBands= "iId={$arrayFiveTrack[$key]['iBand_id']}";
            $listBand= $bandsObj->getList($whereConditionBands);
            
            $arrayFiveTrack[$key]['titleBand']= $listBand[0]['vTitle'];
            
            $arrayFiveTrack[$key]['location']= $this->location($listBand[0]['iState_id'], $listBand[0]['vCity']);
            
        }
        return $arrayFiveTrack;
    }

    public function array_sort($array, $on, $order){
//        fb($array);
    $new_array = array();
    $sortable_array = array();

    if (count($array) > 0) {
        foreach ($array as $k => $v) {
            if (is_array($v)) {
                foreach ($v as $k2 => $v2) {
                    if ($k2 == $on) {
                        $sortable_array[$k] = $v2;
                    }
                }
            } else {
                $sortable_array[$k] = $v;
            }
        }

        switch ($order) {
            case SORT_ASC:
                asort($sortable_array);
            break;
            case SORT_DESC:
                arsort($sortable_array);
            break;
        }

        foreach ($sortable_array as $k => $v) {
            $new_array[$k] = $array[$k];
        }
    }
//fb($new_array);
    return $new_array;
    
} 

//    public function featured(){
//        $bandsObj= new Frontend_Model_Bands();
//        $imageObj= new Frontend_Model_Images();
//        $memberObj= new Frontend_Model_Members();
//        $trackObj= new Frontend_Model_Tracks();
//        $stateObj= new Frontend_Model_State();
//        $whereConditionBands= array(
//            "eFeatured= '?'",
//            array(
//                'Yes'
//            )
//        );
//        $listBand= $bandsObj->getList($whereConditionBands, '', '', '', '', array('iMember_id', 'vTitle', 'vCity', 'iState_id'));
//        if(!empty($listBand)){
//            foreach ($listBand as $key=>$value){
//                
//                $whereConditionTrack= "iMember_id={$listBand[$key]['iMember_id']}";
//                $listTrack= $trackObj->getList($whereConditionTrack, '', '', '', '', array('iId', 'vTitle', 'iMember_id'));                
//                if(!empty($listTrack)){
//                    $idTrack= array();
//                    foreach ($listTrack as $key1=>$value1){
//                        $idTrack[]= $listTrack[$key1]['iId'];
//                    }
//                    arsort($idTrack);
//                    $lastTrack= array_slice($idTrack, 0, 1);
//                    foreach ($listTrack as $key2=>$value2){
//                        if($listTrack[$key2]['iId'] == $lastTrack[0]){
//                            $listLastTrack= $listTrack[$key];
//                        }
//                    }
//                }
//                
//                $location= $this->location($listBand[$key]['iState_id'], $listBand[$key]['vCity']);
//                
//                $whereConditionImg= array(
//                    "iMember_id= '?' and eProfile_default= '?'",
//                    array(
//                        $listBand[$key]['iMember_id'],
//                        'Yes'
//                    )
//                );
//                $listImg= $imageObj->getList($whereConditionImg, '', '', '', '', array('vImg'));
//                if(empty($listImg)){
//                    $imgFeature[]= array(
//                        'vImg'=> 'images-defaults-profile.jpg',
//                        'iMember_id'=>$listBand[$key]['iMember_id'],
//                        'vTitle'=> $listBand[$key]['vTitle'],
//                        'location'=> $location,
//                        'songTitle'=> $listLastTrack['vTitle']
//                        );
//                }  else {
//                    $imgFeature[]= array(
//                        'vImg'=> $listImg[0]['vImg'],
//                        'iMember_id'=>$listBand[$key]['iMember_id'],
//                        'vTitle'=> $listBand[$key]['vTitle'],
//                        'location'=> $location,
//                        'songTitle'=> $listLastTrack['vTitle']
//                    );
//                }
//            }
//            
//            
//        }  else {
//            $imgFeature= 'noFeatured';
//        }
//        return $imgFeature;
////        fb($imgFeature);
//    }

   public function featured(){
        $bandsObj= new Frontend_Model_Bands();
        $imageObj= new Frontend_Model_Images();
//        $memberObj= new Frontend_Model_Members();
        $trackObj= new Frontend_Model_Tracks();
//        $stateObj= new Frontend_Model_State();
        $whereConditionBands= array(
            "eFeatured= '?'",
            array(
                'Yes'
            )
        );
        $listBand= $bandsObj->getList($whereConditionBands, '', '', '', '', array('iMember_id', 'vTitle', 'vCity', 'iState_id'));
        if(!empty($listBand)){
            foreach ($listBand as $key=>$value){
//                fb($listBand[$key]['iMember_id']);
                $whereConditionTrack= "iMember_id={$listBand[$key]['iMember_id']}";
                $listTrack= $trackObj->getList($whereConditionTrack);
                if(!empty($listTrack)){
                    $release = array();
//                    fb($listTrack);
                    foreach ($listTrack as $key1=>$value1){
                        if($listTrack[$key1]['dDate_release'] != NULL){
                            
                            $listTrack[$key1]['dDate_release']= strtotime($listTrack[$key1]['dDate_release']);
                           
                            $release[]= $listTrack[$key1];
                            
                        }
                    }
                    if($release != NULL){
                        $arraySortRelease= $this->array_sort($release, 'dDate_release', SORT_DESC);
                    }
                    $trackFeture= array_slice($arraySortRelease, 0, 1);
                }
                $location= $this->location($listBand[$key]['iState_id'], $listBand[$key]['vCity']);
               
                $whereConditionImg= array(
                    "iMember_id= '?' and eProfile_default= '?'",
                    array(
                        $listBand[$key]['iMember_id'],
                        'Yes'
                    )
                );
                $listImg= $imageObj->getList($whereConditionImg, '', '', '', '', array('vImg'));
                
                if(empty($listImg)){
                    $imgFeature[]= array(
                        'vImg'=> 'images-defaults-profile.jpg',
                        'iMember_id'=>$listBand[$key]['iMember_id'],
                        'vTitle'=> $listBand[$key]['vTitle'],
                        'location'=> $location,
                        'songTitle'=> $listLastTrack['vTitle'],
                        'lastRelease'=> $trackFeture[0]
                        );
                }  else {
                    $imgFeature[]= array(
                        'vImg'=> $listImg[0]['vImg'],
                        'iMember_id'=>$listBand[$key]['iMember_id'],
                        'vTitle'=> $listBand[$key]['vTitle'],
                        'location'=> $location,
                        'songTitle'=> $listLastTrack['vTitle'],
                        'lastRelease'=> $trackFeture[0]
                    );
                }
            }
           
           
        }  else {
            $imgFeature= 'noFeatured';
        }
//        fb($imgFeature);
        return $imgFeature;
    }
    
    
    public function clickUpdateAction(){
        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->getHelper('layout')->disableLayout();
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

}
?>