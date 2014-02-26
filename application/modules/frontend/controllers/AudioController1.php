<?php



class AudioController extends Zend_Controller_Action {

    

    public function init() {

           $this->view->currentPageDetails = $this->_helper->page->getDetails();

           parent::init();

    }

    

    //load index audio and create band directory if no exist

    public function indexAction() {

//        mkdir('public/uploaded_resource/frontend/bandddd',0777);
         $this->view->loggedUser  = $this->_helper->app->getLoggedUser();

        if( $this->view->loggedUser ){

            $bandsObj = new Frontend_Model_Bands();

            $bandsObj->setBand($this->view->loggedUser->iId);

            $getPath = $bandsObj->getPath();
//            fb($getPath);

            if(!is_dir($getPath)){



//                $oldumask = umask(0);

                    mkdir($getPath,0777);
//                umask($oldumask);
                    chmod($getPath, 0777);



                $oldumask = umask(0);    

                    mkdir($getPath.'/audio',0777);
                    

                umask($oldumask);



 

            }

            if (!is_dir($getPath.'/audio')) {

                $oldumask = umask(0);    

                    mkdir($getPath.'/audio',0777);

                umask($oldumask);

            }

                $this->_helper->_layout->setLayout('layout-logout-not-homepage'); // show account info

        }else{

            $this->_helper->_layout->setLayout('layout-not-homepage'); // show signUp & Login links

            $this->_redirect('/');

        } 

    }

    

    public function uploadAction(){

        $this->view->loggedUser  = $this->_helper->app->getLoggedUser();

        $this->_helper->viewRenderer->setNoRender();

        $this->_helper->getHelper('layout')->disableLayout();

        $allParams = $this->_getAllParams();

        if($_FILES['the_files']['error'] > 0){

            print jsonResponse(array(

                'success'=>FALSE

            ));

        }  else {

            $trackObj = new Frontend_Model_Tracks();

            $bandObj = new Frontend_Model_Bands();

            $whereCondition = array("iMember_id = '?'",array($allParams['iMember_id']));

            $bandList = $bandObj->getList($whereCondition,'','','','',array('iId','vTitle'));

            $dateTime = new DateTime;

            $dDate_create=$dateTime->format('Y-m-d H:m:s');

            $trackArray = array(

                'vTitle'=> str_replace('.mp3', '', $_FILES['the_files']['name']),

                'dDate_create'=>$dDate_create,

                'iMember_id'=>$allParams['iMember_id'],

                'iBand_id'=>$bandList[0]['iId']

            );

            $trackResult = $trackObj->doInsert($trackArray);

            if(is_numeric($trackResult)){

                $whereConditionTrack = array("iId = '?'",array($trackResult));

                $_FILES['the_files']['name'] = $trackResult.'-'.$_FILES['the_files']['name'];

                $updateTrack = array(

                    'vFile_mp3'=> $_FILES['the_files']['name']

                );

                $trackObj->doUpdate($updateTrack, $whereConditionTrack);

                $bandObj->setBand($allParams['iMember_id']);

                $getPath = $bandObj->getPath();

//                $papa = $_SERVER['DOCUMENT_ROOT'].$this->view->currentPageDetails['projectPath'].$getPath.'/'.$_FILES['the_files']['name'];

                move_uploaded_file($_FILES['the_files']['tmp_name'], $_SERVER['DOCUMENT_ROOT'].$this->view->currentPageDetails['projectPath'].$getPath.'/audio/'.$_FILES['the_files']['name']);

                       print jsonResponse(array(

                            'success'=>  true,

                            'trackId'=>$trackResult,
//                           'ss'=>$papa



                        ));

            }

            

        }

    }

    

    public function uploadOggAction(){

        $this->view->loggedUser  = $this->_helper->app->getLoggedUser();

        $this->_helper->viewRenderer->setNoRender();

        $this->_helper->getHelper('layout')->disableLayout();

        $allParams = $this->_getAllParams();

        if($_FILES['the_files']['error'] > 0){

            print jsonResponse(array(

                'success'=>FALSE

            ));

        }  else {

            $bandObj = new Frontend_Model_Bands();  

            $whereCondition = array("iMember_id = '?' AND iId = '?'",array($allParams['iMember_id'],$allParams['iTrack_id']));

            

            $trackObj = new Frontend_Model_Tracks();

            $trackList = $trackObj->getList($whereCondition);

            $bandObj->setBand($allParams['iMember_id']);

            $getPath = $bandObj->getPath();

            if($trackList[0]['vFile_ogg']!=''){

                $file =$_SERVER['DOCUMENT_ROOT'].$this->view->currentPageDetails['projectPath'].$getPath.'/audio/'.$trackList[0]['vFile_ogg'];

                if ( file_exists($file) ) {

                    @unlink($file);

                }

            }

            $_FILES['the_files']['name'] = $allParams['iTrack_id'].'-'.$_FILES['the_files']['name'];

            $updateTrack = array(

                'vFile_ogg'=> $_FILES['the_files']['name']

            );

            $trackResult =$trackObj->doUpdate($updateTrack, $whereCondition);

            if($trackResult){

                

                move_uploaded_file($_FILES['the_files']['tmp_name'], $_SERVER['DOCUMENT_ROOT'].$this->view->currentPageDetails['projectPath'].$getPath.'/audio/'.$_FILES['the_files']['name']);

                print jsonResponse(array(

                     'success'=>  true,

                     'trackId'=>$trackResult,

                     'trackName'=>$_FILES['the_files']['name'],

                    'band_id'=>$trackList[0]['iBand_id']



                 ));

            }else{

                print jsonResponse(array(

                     'success'=>  false,



                 ));

            }

            

            

        }

    }

    

     public function uploadMp3Action(){

        $this->view->loggedUser  = $this->_helper->app->getLoggedUser();

        $this->_helper->viewRenderer->setNoRender();

        $this->_helper->getHelper('layout')->disableLayout();

        $allParams = $this->_getAllParams();

        if($_FILES['the_files']['error'] > 0){

            print jsonResponse(array(

                'success'=>FALSE

            ));

        }  else {

            $bandObj = new Frontend_Model_Bands();  

            $whereCondition = array("iMember_id = '?' AND iId = '?'",array($allParams['iMember_id'],$allParams['iTrack_id']));

            

            $trackObj = new Frontend_Model_Tracks();

            $trackList = $trackObj->getList($whereCondition);

            $bandObj->setBand($allParams['iMember_id']);

            $getPath = $bandObj->getPath();

            if($trackList[0]['vFile_mp3']){

                $file =$_SERVER['DOCUMENT_ROOT'].$this->view->currentPageDetails['projectPath'].$getPath.'/audio/'.$trackList[0]['vFile_mp3'];

                if ( file_exists($file) ) {

                    @unlink($file);

                }

            }

            $_FILES['the_files']['name'] = $allParams['iTrack_id'].'-'.$_FILES['the_files']['name'];

            $updateTrack = array(

                'vFile_mp3'=> $_FILES['the_files']['name'],

            );

            $trackResult =$trackObj->doUpdate($updateTrack, $whereCondition);

            if(is_numeric($trackResult)){



                move_uploaded_file($_FILES['the_files']['tmp_name'], $_SERVER['DOCUMENT_ROOT'].$this->view->currentPageDetails['projectPath'].$getPath.'/audio/'.$_FILES['the_files']['name']);

                print jsonResponse(array(

                     'success'=>  true,

                     'trackId'=>$trackResult,

                     'trackName'=>$_FILES['the_files']['name'],

                    'band_id'=>$trackList[0]['iBand_id']



                 ));

            }else{

                print jsonResponse(array(

                     'success'=>  false,



                 ));

            }

        }

    }

    

    public function selectCoverAction(){

        $this->view->loggedUser  = $this->_helper->app->getLoggedUser();

        $this->_helper->viewRenderer->setNoRender();

        $this->_helper->getHelper('layout')->disableLayout();

        $albumObj = new Frontend_Model_Albums();

        $imageObj = new Frontend_Model_Images();

        $allParams = $this->_getAllParams();

        $whereCondition1 = array("iId = '?' AND iMember_id ='?'",array($allParams['id'],$this->view->loggedUser->iId));

        $imageList = $imageObj->getList($whereCondition1);

        $arrayCover = array(

            "vImg_cover"=>$imageList[0]['vImg']

        );

        $albumId= new Zend_Session_Namespace('album_id');

        $whereCondition2 = array("iId = '?' AND iMember_id = '?'",array( $albumId->iId ,$this->view->loggedUser->iId));

        $updateCover = $albumObj->doUpdate($arrayCover, $whereCondition2);

        if($updateCover){

            print jsonResponse(array(

                'success'=>true,

                'cover'=>$imageList[0]['vImg']

            ));

        }else{

            print jsonResponse(array(

                'success'=>FALSE

            ));

        }

        

    }



    public function uploadCoverAction(){

        $this->view->loggedUser  = $this->_helper->app->getLoggedUser();

        $this->_helper->viewRenderer->setNoRender();

        $this->_helper->getHelper('layout')->disableLayout();

        $allParams = $this->_getAllParams();

        if($_FILES['the_files']['error'] > 0){

            print jsonResponse(array(

                'success'=>FALSE

            ));

        }  else {

            $albumObj = new Frontend_Model_Albums();

            $whereCondition = array("iMember_id = '?' AND iId = '?'",array($allParams['iMember_id'],$allParams['id']));

            $name_cover = 'cover_'.$allParams['id'].'_'.$_FILES['the_files']['name'];

            $coverArray= array(

                'vImg_cover'=> $name_cover,

            );

            $albumResult = $albumObj->doUpdate($coverArray,$whereCondition);

            if(is_numeric($albumResult)){

                move_uploaded_file($_FILES['the_files']['tmp_name'], $_SERVER['DOCUMENT_ROOT'].$this->view->currentPageDetails['projectPath']."public/uploaded_resource/frontend/band/image/".$name_cover);

                       print jsonResponse(array(

                            'success'=>  true,

                            'albumId'=>$albumResult,

                           'cover' =>$name_cover

                        ));

            }

        }

    }

    public function loadImageAction(){

        $this->view->loggedUser  = $this->_helper->app->getLoggedUser();

        $this->_helper->viewRenderer->setNoRender();

        $this->_helper->getHelper('layout')->disableLayout();

        $whereCondition = array("iMember_id = '?'",array($this->view->loggedUser->iId));

        $imageObj = new Frontend_Model_Images();

        $imageList = $imageObj->getList($whereCondition);

        print jsonResponse(array(

            'success'=>true,

            'data'=>$imageList

        ));

    }



    public function loadMusicAction(){

        $this->view->loggedUser  = $this->_helper->app->getLoggedUser();

        $this->_helper->viewRenderer->setNoRender();

        $this->_helper->getHelper('layout')->disableLayout();

        $whereCondition = array("iMember_id = '?'",array($this->view->loggedUser->iId));

        $trackObj = new Frontend_Model_Tracks();

        $trackList = $trackObj->getList($whereCondition);

        print jsonResponse(array(

            'success'=>true,

            'data'=>$trackList

        ));

    }

    

    public function getListAlbumAction(){

        $this->view->loggedUser  = $this->_helper->app->getLoggedUser();

        $this->_helper->viewRenderer->setNoRender();

        $this->_helper->getHelper('layout')->disableLayout();

        $allParams = $this->_getAllParams();

        $albumObj = new Frontend_Model_Albums();

        $trackObj = new Frontend_Model_Tracks();

        if($allParams['id'] == -1){

            $whereConditionRoot = array("iMember_id = '?'",array($this->view->loggedUser->iId));

            $count1 = $albumObj->getTotalRecord($whereConditionRoot);

            $count2 = $trackObj->getTotalRecord($whereConditionRoot);

            if($count1>0 or $count2>0){

                $sta = "closed";

            }else{

                $sta = "";

            }

            $atttt[]= jsonResponse(array(

                        "attr"=> array(

                            "id"=>"node_0",

                            "rel"=>"folder"

                        ),

                        "data"=>"ALL ALBUMS",

                        "state"=>$sta

                    ));

        $nodeResultString = '[' . implode(',', $atttt). ']';

        print $nodeResultString;

        }

        if($allParams['id']==0){

                $whereConditionAlbum = array("iMember_id = '?'",array($this->view->loggedUser->iId));

                $listAlbum = $albumObj->getList($whereConditionAlbum,'','','dDate_release','ASC');

                $arrayIdTrack = array();

                foreach ($listAlbum as $key => $value) {

                    if($value['vTrack_ids'] == ''){

                        $state = "";

                    }else{

                        $arrayId = explode(',', $value['vTrack_ids']);

                        $arrayIdTrack = array_merge($arrayIdTrack,$arrayId);

                        $state = "closed";

                    }

                    $atttt[]= jsonResponse(array(

                        "attr"=> array(

                            "id"=>"node_".$value['iId'],

                            "rel"=>"folder"

                        ),

                        "data"=>$value['vTitle'],

                        "state"=>$state

                    ));

                }

                $whereConditionTrack = array("iMember_id ='?'",array($this->view->loggedUser->iId));

                $listIdT  = $trackObj->getList($whereConditionTrack);

                foreach ($listIdT as $keyL => $valueL) {

                    $in_array = in_array($valueL['iId'], $arrayIdTrack);

                    if($in_array==TRUE){

                        //

                    }  else {

                       $atttt[]= jsonResponse(array(

                            "attr"=> array(

                                "id"=>"node_".$valueL['iId'],

                                "rel"=>""

                            ),

                            "data"=>$valueL['vTitle'],

                            "state"=>""

                        )); 

                    }

                }
fb($atttt);
                 $nodeResultString = '[' . implode(',', $atttt). ']';

                 print $nodeResultString;

        } elseif ($allParams['id']!=0 && $allParams['id']!=-1) {

            $albomlist = $albumObj->getRecordById($allParams['id']);

            $arrayTrack = explode(',', $albomlist['vTrack_ids']);

            if(!empty($arrayTrack)){

                $listAlbum[$key]['trackList'] = array();

                foreach ($arrayTrack as $keyT => $valueT) {

                    $trackList = $trackObj->getRecordById($valueT);

                    $atttt[]= jsonResponse(array(

                        "attr"=> array(

                            "id"=>"node_".$trackList['iId'],

                            "rel"=>""

                        ),

                        "data"=>$trackList['vTitle'],

                        "state"=>""

                    ));

                }
fb($atttt);
            $nodeResultString = '[' . implode(',', $atttt). ']';

                print $nodeResultString;

            }

        }

    }

    

    public function moveMusicAction(){

        $this->view->loggedUser  = $this->_helper->app->getLoggedUser();

        $this->_helper->viewRenderer->setNoRender();

        $this->_helper->getHelper('layout')->disableLayout();

        $allParams = $this->_getAllParams();

        $albumObj = new Frontend_Model_Albums();

        if($allParams['id'] == $allParams['ref']){

            

        }else{

            $whereCondition1 = "(find_in_set({$allParams['id']},vTrack_ids))";

            $oldAlbum = $albumObj->getList($whereCondition1);

            if(!empty($oldAlbum)){

                $arrayOld = explode(',', $oldAlbum[0]['vTrack_ids']);

                foreach ($arrayOld as $key => $value) {

                    if($value == $allParams['id']){

                        unset($arrayOld[$key]);

                    }

                }

                $arrayNew = implode(',', $arrayOld);

                $arrayOldAlbum = array(

                    'vTrack_ids'=>$arrayNew

                );

                $whereCondition2 = array("iId = '?'",array($oldAlbum[0]['iId']));

                $upOldAlbum = $albumObj->doUpdate($arrayOldAlbum, $whereCondition2);

            }

            if($allParams['ref']!=0){

                $newAlbum = $albumObj->getRecordById($allParams['ref']);

                $arrayIdOld = explode(',', $newAlbum['vTrack_ids']);

                $arrayIdOld[] = $allParams['id'];

                if($arrayIdOld[0]==''){

                    unset($arrayIdOld[0]);

                }

                $impArr = implode(',', $arrayIdOld);

                $newAlbumArray = array(

                    'vTrack_ids'=>  $impArr

                );

                $whereCondition3 = array("iId = '?'",array($allParams['ref']));

                $upNewAlbum = $albumObj->doUpdate($newAlbumArray, $whereCondition3);

            }

            print jsonResponse(array(

                'status'=>1,

                'id'=>$allParams['id']

            ));

        }

    }

    

    

    public function createAlbumAction(){

        $this->view->loggedUser  = $this->_helper->app->getLoggedUser();

        $this->_helper->viewRenderer->setNoRender();

        $this->_helper->getHelper('layout')->disableLayout();

        $allParams = $this->_getAllParams();

        $bandObj = new Frontend_Model_Bands();

        $albumObj = new Frontend_Model_Albums();

        $whereCondition1 = array("iMember_id= '?'",array($this->view->loggedUser->iId));

        $bandId = $bandObj->getList($whereCondition1);

        $dateTime = new DateTime;

        $dDate_create=$dateTime->format('Y-m-d H:m:s');

        $arrayInsertAlbum = array(

            'iMember_id'=>$this->view->loggedUser->iId,

            'iBand_id'=>$bandId[0]['iId'],

            'vTitle'=>$allParams['title'],

            'dDate_create'=>$dDate_create

        );

        $result = $albumObj->doInsert($arrayInsertAlbum);

        print jsonResponse(array(

            'status'=>1,

            'id'=>$result

        ));

    }

    

    public function removeAction(){

        $this->view->loggedUser  = $this->_helper->app->getLoggedUser();

        $this->_helper->viewRenderer->setNoRender();

        $this->_helper->getHelper('layout')->disableLayout();

        $allParams = $this->_getAllParams();

        $trackObj = new Frontend_Model_Tracks();

        $albumObj = new Frontend_Model_Albums();

        $bandObj = new Frontend_Model_Bands();

        if($allParams['type']=='folder'){

            $whereCondition1 = array("iId = '?'",array($allParams['id']));

            $albumlist = $albumObj->getList($whereCondition1);

            $bandObj->setBand($albumlist[0]['iBand_id']);

            $path = $bandObj->getPath();

            $arrayTracks = explode(',', $albumlist[0]['vTrack_ids']);

            if($arrayTracks[0]!=''){

                foreach ($arrayTracks as $key => $value) {

                    $whereCondition2 = array("iId='?' AND iMember_id='?'",array($value, $this->view->loggedUser->iId));

                    $trackList = $trackObj->getList($whereCondition2);

                    $del = $trackObj->doDelete($whereCondition2);

                    $file =$_SERVER['DOCUMENT_ROOT'].$this->view->currentPageDetails['projectPath'].$path.'/audio/'.$trackList[0]['vFile_mp3'];

                    if ( file_exists($file) ) {

                        @unlink($file);

                    }

                }

            }

            $del = $albumObj->doDelete($whereCondition1);

            print jsonResponse(array(

                    'status'=>1

            ));

        }  else {

            $whereCondition3 = "(find_in_set({$allParams['id']},vTrack_ids))";

            $oldAlbum = $albumObj->getList($whereCondition3);

            if(!empty($oldAlbum)){

                $arrayOld = explode(',', $oldAlbum[0]['vTrack_ids']);

                foreach ($arrayOld as $key => $value) {

                    if($value == $allParams['id']){

                        unset($arrayOld[$key]);

                    }

                }

                $arrayNew = implode(',', $arrayOld);

                $arrayOldAlbum = array(

                    'vTrack_ids'=>$arrayNew

                );

                $whereCondition4 = array("iId = '?'",array($oldAlbum[0]['iId']));

                $upOldAlbum = $albumObj->doUpdate($arrayOldAlbum, $whereCondition4);

            }

            $whereCondition5 = array("iId='?' AND iMember_id='?'",array($allParams['id'], $this->view->loggedUser->iId));

            $trackList = $trackObj->getList($whereCondition5);

            $bandObj->setBand($trackList[0]['iBand_id']);

            $path = $bandObj->getPath();

            $del = $trackObj->doDelete($whereCondition5);

            $file =$_SERVER['DOCUMENT_ROOT'].$this->view->currentPageDetails['projectPath'].$path.'/audio/'.$trackList[0]['vFile_mp3'];

            if ( file_exists($file) ) {

                @unlink($file);

            }

            print jsonResponse(array(

                'status'=>1

            ));

        }

    }

    

    public function renameAction(){

        $this->view->loggedUser  = $this->_helper->app->getLoggedUser();

        $this->_helper->viewRenderer->setNoRender();

        $this->_helper->getHelper('layout')->disableLayout();

        $allParams = $this->_getAllParams();

        $trackObj = new Frontend_Model_Tracks();

        $albumObj = new Frontend_Model_Albums();

        if($allParams['type']== 'folder'){

            $renameFolder = array(

                'vTitle'=>$allParams['title']

            );

            $whereCondition1 = array("iId ='?'",array($allParams['id']));

            $up = $albumObj->doUpdate($renameFolder, $whereCondition1);

            print jsonResponse(array(

                'status'=>1,

                'id'=>$up

            ));

        }else{

            $renameFile = array(

                'vTitle'=>$allParams['title']

            );

            $whereCondition2 = array("iId ='?'",array($allParams['id']));

            $up = $trackObj->doUpdate($renameFile, $whereCondition2);

            print jsonResponse(array(

                'status'=>1,

                'id'=>$up

            ));

        }

    }

    

    

    public function getInfoAction(){

        $this->view->loggedUser  = $this->_helper->app->getLoggedUser();

        $this->_helper->viewRenderer->setNoRender();

        $this->_helper->getHelper('layout')->disableLayout();

        $allParams = $this->_getAllParams();

        $trackObj = new Frontend_Model_Tracks();

        $albumObj = new Frontend_Model_Albums();

        if($allParams['type']=='folder'){

            $whereCondition1 = array("iId = '?'",array($allParams['id']));

            $albumList = $albumObj->getList($whereCondition1);

            if($albumList[0]['dDate_release'] != null){

                $albumList[0]['dDate_release']= date('Y-m-d', strtotime($albumList[0]['dDate_release']));

            }

            $array_idTrack = explode(',', $albumList[0]['vTrack_ids']);

            $albumId= new Zend_Session_Namespace('album_id');

            $albumId->iId = $albumList[0]['iId'];

            $this->view->loggedUser->album_id = $albumList[0]['iId'];

            foreach ($array_idTrack as $key => $value) {

                $whereCondition2 = array("iId = '?'",array($value));

                $trackList = $trackObj->getList($whereCondition2);

                $albumList[0]['tracks'][] = $trackList[0];

            }

            print jsonResponse(array(

                'type'=>$allParams['type'],

                'success'=>true,

                'data'=>$albumList[0]

            ));

        }

    }

    public function playAudioAction(){

        $this->view->loggedUser  = $this->_helper->app->getLoggedUser();

        $this->_helper->viewRenderer->setNoRender();

        $this->_helper->getHelper('layout')->disableLayout();

        $allParams = $this->_getAllParams();

        $trackObj = new Frontend_Model_Tracks();

        $whereCondition = array("iId = '?' AND iMember_id ='?'",array($allParams['id'],$this->view->loggedUser->iId));

        $trackList = $trackObj->getList($whereCondition);

        $this->view->loggedUser->track_id = $trackList[0]['iId'];

        if($trackList[0]['dDate_release']!=null){

            $trackList[0]['dDate_release'] = date('Y-m-d', strtotime($trackList[0]['dDate_release']));

        }

        $trackList[0]['vFile_mp3'] = str_replace("\\", "", $trackList[0]['vFile_mp3']);
        $trackList[0]['vFile_ogg'] = str_replace("\\", "", $trackList[0]['vFile_ogg']);
        print jsonResponse(array(

            'success' =>TRUE,

            'data'=>$trackList[0]

        ));

    }

    

    

    public function updateAlbumAction(){

        $this->view->loggedUser  = $this->_helper->app->getLoggedUser();

        $this->_helper->viewRenderer->setNoRender();

        $this->_helper->getHelper('layout')->disableLayout();

        $allParams = $this->_getAllParams();

        $albumId= new Zend_Session_Namespace('album_id');

        $albumObj = new Frontend_Model_Albums();

        $whereCondition = array("iId = '?' AND iMember_id ='?'",array($albumId->iId , $this->view->loggedUser->iId));

        $dateTime = new DateTime;

        $dDate_modify=$dateTime->format('Y-m-d H:m:s');

        $upArray = array();

        if($allParams['vTitle']){

            $upArray['vTitle'] = $allParams['vTitle'];

        }

        if($allParams['desc']){

            $upArray['tDesc']=  $allParams['desc'];

        }

        if($allParams['release']){

            $upArray['dDate_release']=  $allParams['release'];

        }

        $upArray['dDate_modify']=$dDate_modify;

        $up = $albumObj->doUpdate($upArray, $whereCondition);

        if($up){

            $whereCondition1=array("iId = '?' AND iMember_id='?'",array($up,$this->view->loggedUser->iId));

            $listAlbum = $albumObj->getList($whereCondition1);

            $trackIdArray = explode(',', $listAlbum[0]['vTrack_ids']);

            $upTrackArray = array(

                'dDate_release'=>$allParams['release']

            );

            $trackObj = new Frontend_Model_Tracks();

            if($trackIdArray[0]!=''){

                foreach ($trackIdArray as $value) {

                    $whereCondition2 = array("iId = '?'",array($value));

                    $upT = $trackObj->doUpdate($upTrackArray, $whereCondition2);

                }

            }

            print jsonResponse(array(

                'success'=>TRUE

            ));

        }else{

            print jsonResponse(array(

                'success'=>FALSE

            ));

        }

    }

    

    public function updateTrackAction(){

        $this->view->loggedUser  = $this->_helper->app->getLoggedUser();

        $this->_helper->viewRenderer->setNoRender();

        $this->_helper->getHelper('layout')->disableLayout();

        $allParams = $this->_getAllParams();

        $trackObj = new Frontend_Model_Tracks();

        $whereCondition = array("iId = '?' AND iMember_id='?' ",array($allParams['iId'],$this->view->loggedUser->iId));

        

        $date_format = 'Y-m-d';

        $input = $allParams['dDate_release'];

        $input = trim($input);

        $time = strtotime($input);

        $is_valid = date($date_format, $time) == $input;

        if(!$is_valid){

            $allParams['dDate_release'] = null;

        }



        $arrayTrack = array(

            'vTitle'=>$allParams['vTitle'],

            'dDate_release'=>$allParams['dDate_release']

        );

        $upTrack = $trackObj->doUpdate($arrayTrack, $whereCondition);

        if($upTrack){

            print jsonResponse(array(

                'success'=>true

            ));

        }

    }

    

    public function defaultTrackAction(){

        $this->view->loggedUser  = $this->_helper->app->getLoggedUser();

        $this->_helper->viewRenderer->setNoRender();

        $this->_helper->getHelper('layout')->disableLayout();

        $allParams = $this->_getAllParams();

        $trackObj = new Frontend_Model_Tracks();

        $whereCondition3 = array("iId = '?' AND iMember_id='?' ",array($allParams['id'],$this->view->loggedUser->iId));

        if($allParams['undefault']=='Yes'){

            

            $arrayTrack1 = array(

                'eShow_in_profile_playlist'=>'No',

            );

            $upTrack = $trackObj->doUpdate($arrayTrack1, $whereCondition3);

        }else{

            $arrayTrack1 = array(

                'eShow_in_profile_playlist'=>'Yes',

            );

            $upTrack = $trackObj->doUpdate($arrayTrack1, $whereCondition3);

        }



        

        if($upTrack){

            print jsonResponse(array(

                'success'=>true,

                'track_id'=>$upTrack

            ));

        }

    }

}



?>

