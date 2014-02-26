<?php

class ImagesController extends Zend_Controller_Action {
    
    public function init() {
        $this->view->currentPageDetails = $this->_helper->page->getDetails();
    	parent::init();
    }


    public function indexAction() {
        $this->view->loggedUser  = $this->_helper->app->getLoggedUser();
         if($this->view->loggedUser){
             $this->_helper->_layout->setLayout('layout-logout-not-homepage');
             $this->view->status= 'login';
         }else{
            $this->_helper->_layout->setLayout('layout-not-homepage'); // show signUp & Login links
            $this->view->status= 'notLogin';
        }
        $idImgSession = new Zend_Session_Namespace('imgSession');
        $allparams= $this->_getAllParams();
        if($allparams['id']){
            $idImgSession->iImgId= $allparams['id'];
        }  else {
            unset($idImgSession->iImgId);
        }
    }
    
    public function saveImgAction(){
       
        $this->_helper->getHelper('layout')->disableLayout();
        $this->_helper->viewRenderer->setNoRender();
        $allParams= $this->_getAllParams();
        $this->view->loggedUser  = $this->_helper->app->getLoggedUser();
////        $idUserLogin= $this->getUserLogInId();
//         print 'ffffffffffffffffffffffffffffffffffffffffffffffffffffff';
//         print_r($_POST[iMember_id]);
//        print 'pppppppppppppppppppppppppppppppppppppppppppppppppppppp';
//        $targetFolder = '/uploads'; // Relative to the root

//        $verifyToken = md5('unique_salt' . $_POST['timestamp']);
//        echo 'ppppppppppppppppppppppppppppppppppppppppppppppppppppppppp';echo "<br />";
//print_r( $_FILES['the_files']);
//        echo $_POST['timestamp'];
//        echo "<br />";
//        echo 'rrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrr';
//        if (!empty($_FILES) && $_POST['token'] == $verifyToken) {
//                $tempFile = $_FILES['Filedata']['tmp_name'];
//                $targetPath = $_SERVER['DOCUMENT_ROOT'] . $this->view->currentPageDetails['projectPath']."public/uploaded_resource/frontend/member/profile-image";
//                $targetFile = rtrim($targetPath,'/') . '/' . $_FILES['Filedata']['name'];
//
//                // Validate the file type
//                $fileTypes = array('jpg','jpeg','gif','png'); // File extensions
//                $fileParts = pathinfo($_FILES['Filedata']['name']);
//
//                if (in_array($fileParts['extension'],$fileTypes)) {
//                        move_uploaded_file($tempFile,$targetFile);
//                        echo '1';
//                } else {
//                        echo 'Invalid file type.';
//                }
//        }
        $imagesOb= new Frontend_Model_Images();
        if($_FILES['the_files']['error'] > 0){
            print jsonResponse(array(
                'success'=>FALSE
            ));
        }  else {
            $list= array(
                'iMember_id'=> $_POST[iMember_id],
                'dDate_create'=> date('Y-m-d H:i:s'),
                'eProfile_default'=> 'No',
                'tDesc'=> $allParams['tDesc']
            );
            $add= $imagesOb->doInsert($list);
            $imgName= $add.'-'.$_FILES['the_files']['name'];
            $updateArr= array(
                'vImg'=> $imgName
            );
            $whereCondition= "iId={$add}";
            $update= $imagesOb->doUpdate($updateArr, $whereCondition);
            move_uploaded_file($_FILES['the_files']['tmp_name'], $_SERVER['DOCUMENT_ROOT'].$this->view->currentPageDetails['projectPath']."public/uploaded_resource/frontend/band/image/".$imgName);
            $listPic= $imagesOb->getList($whereCondition);
            
            $whereConditionProfile= "iMember_id={$_POST[iMember_id]}";
            $picProfile= array();
            $bandOb= new Frontend_Model_Bands();
            $listBand= $bandOb->getList($whereConditionProfile);
            $picProfile['vTitle']= $listBand[0]['vTitle'];
            $listProfile= $imagesOb->getList($whereConditionProfile);
            foreach ($listProfile as $key=>$value){
                if($listProfile[$key]['eProfile_default'] == 'Yes'){
                    $picProfile['img']= $listProfile[$key];
                }
            }
            print jsonResponse(array(
                'success'=> true,
                'data'=> $listPic[0],
                'picProfile'=> $picProfile
//                'list'=> 
            ));   
        }
    }
    
    public function loadImgAction(){
        $this->_helper->getHelper('layout')->disableLayout();
        $this->_helper->viewRenderer->setNoRender();
        $allParams= $this->_getAllParams();
        $idImgSession = new Zend_Session_Namespace('imgSession');
        $idImgSession->iImgId;
        $this->view->loggedUser  = $this->_helper->app->getLoggedUser();
        if($idImgSession->iImgId == NULL){
            $whereCondition= "iMember_id={$this->view->loggedUser->iId}";
        }  else {
            $whereCondition= "iMember_id={$idImgSession->iImgId}";
        }
        
//        fb($allParams);
//        fb($this->view->loggedUser);
        $picProfile= array();
        $imagesOb= new Frontend_Model_Images();
        $bandOb= new Frontend_Model_Bands();
        $listBand= $bandOb->getList($whereCondition);
        $picProfile['vTitle']= $listBand[0]['vTitle'];
        $list= $imagesOb->getList($whereCondition);
        foreach ($list as $key=>$value){
            if($list[$key]['eProfile_default'] == 'Yes'){
                $picProfile['img']= $list[$key];
            }
        }
        $count= count($list);
//        fb($picProfile);
        print jsonResponse(array(
            'success'=> true,
            'data'=> $list,
            'count'=> $count,
            'picProfile'=> $picProfile
        ));
    }
    
    public function autoJeditableDescAction(){
        $this->_helper->getHelper('layout')->disableLayout();
        $this->_helper->viewRenderer->setNoRender();
        $allParams= $this->_getAllParams();
        $imagesOb= new Frontend_Model_Images();
//        fb($allParams);
        $idArray= explode('-', $allParams['id']);
//        fb($idArray);
        $id= $idArray[0];
        $whereCondition= "iId={$id}";
        
        if(array_key_exists('value', $allParams)){
            $updArray['tDesc']= array();
            $updArray['tDesc']= $allParams['value'];
//            fb($updArray['tDesc']);
            $order= array("\r\n", "\n", "\r");
            $replace= '<br />';
            $arrayFinal['tDesc']= str_replace($order, $replace, $updArray['tDesc']);
            $arrayFinal['dDate_modify']= date('Y-m-d H:i:s');
//            fb($arrayFinal);
            $imagesOb->doUpdate($arrayFinal, $whereCondition);
            echo $arrayFinal['tDesc'];
        }  else {
            $list= $imagesOb->getList($whereCondition);
            $order= array("<br />");
            $replace= "\n";
            $arrayFinalStart['tDesc']= str_replace($order, $replace, $list[0]['tDesc']);
//            fb($arrayFinalStart['tDesc']);
            echo $arrayFinalStart['tDesc'];
        }
    }
    
    public function setProflePicAction(){
        $this->_helper->getHelper('layout')->disableLayout();
        $this->_helper->viewRenderer->setNoRender();
        $this->view->loggedUser  = $this->_helper->app->getLoggedUser();
        $allParams= $this->_getAllParams();
        $imagesOb= new Frontend_Model_Images();
//        fb($allParams);
//        fb($this->view->loggedUser);
        $whereCondition= "iMember_id={$this->view->loggedUser->iId}";
        $list= $imagesOb->getList($whereCondition);
//        fb($list);
        foreach ($list as $key=>$value){
            if($value['eProfile_default'] == 'Yes'){
                $whereConditionYes= "iId={$list[$key]['iId']}";
                $arrayYes['eProfile_default']= 'No';
                $imagesOb->doUpdate($arrayYes, $whereConditionYes);
            }
        }
        $whereConditionSet= "iId={$allParams['iId']}";
        $arraySet['eProfile_default']= 'Yes';
        $imagesOb->doUpdate($arraySet, $whereConditionSet);
        $listSet= $imagesOb->getList($whereConditionSet);
        print jsonResponse(array(
            'success'=> TRUE,
            'data'=> $listSet[0]
        ));
    }
    
    public function deletePicAction(){
        $this->_helper->getHelper('layout')->disableLayout();
        $this->_helper->viewRenderer->setNoRender();
        $allParams= $this->_getAllParams();
        $imagesOb= new Frontend_Model_Images();
        $whereCondition= "iId={$allParams['iId']}";
        $list= $imagesOb->getList($whereCondition);
        $img= $list[0]['vImg'];
//        fb($img);
        $imagesOb->doDelete($whereCondition);
        @unlink($_SERVER['DOCUMENT_ROOT'].$this->view->currentPageDetails['projectPath']."public/uploaded_resource/frontend/band/image/".$img);
        print jsonResponse(array(
            'success'=> true,
            'data'=> $allParams['iId'],
            'picProfile'=> $list[0]['eProfile_default']
        ));
    }
    
    public function dateEditAction(){
        $this->_helper->getHelper('layout')->disableLayout();
        $this->_helper->viewRenderer->setNoRender();
        $imagesOb= new Frontend_Model_Images();
        $allParams= $this->_getAllParams();
//        fb($allParams);
        $idArray= explode('-', $allParams['vImgResult']);
        $id= $idArray[0];
        $whereCondition= "iId={$id}";
        $list= $imagesOb->getList($whereCondition);
//        fb($list);
        print jsonResponse(array(
            'data'=> $list[0]
        ));
    }
//    
//    public function getUserLogInId(){
//        $this->view->loggedUser  = $this->_helper->app->getLoggedUser();
//        $id= $this->view->loggedUser->iId;
//        $s = $this->_loggedUser;
//        fb($s);
//        print($id);
//        return $id;
//    }

}
?>