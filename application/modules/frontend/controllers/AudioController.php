<?php
    class AudioController extends Zend_Controller_Action {
        
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
        }
        
        public function saveMusicAction(){
            $this->_helper->getHelper('layout')->disableLayout();
            $this->_helper->viewRenderer->setNoRender();
            $audioOb= new Frontend_Model_Tracks();
            $bandOb= new Frontend_Model_Bands();
            $allParams= $this->_getAllParams();
            
            if($_FILES['the_files']['error'] > 0){
                print jsonResponse(array(
                    'success'=>FALSE
                ));
            } else {
                $iMember_id= $_POST[iMember_id];
                $whereConditionBand= "iMember_id={$iMember_id}";
                $listBand= $bandOb->getList($whereConditionBand, '', '', '', '', array('iId'));
                $iBand_id= $listBand[0]['iId'];
                $dDate_create= date('Y-m-d H:i:s');
                $nameMusic= $_FILES['the_files']['name'];
                $vTitle= substr($nameMusic, 0, -4);
                $listInsert= array(
                    'vTitle'=> $vTitle,
                    'dDate_create'=> $dDate_create,
                    'iMember_id'=> $iMember_id,
                    'iBand_id'=> $iBand_id
                );
                $insertId= $audioOb->doInsert($listInsert);
                $whereConditionAudio= "iId={$insertId}";
                $vFile_mp3= $insertId.'-'.$nameMusic;
                $updateArray= array(
                    'vFile_mp3'=> $vFile_mp3
                );
                $audioOb->doUpdate($updateArray, $whereConditionAudio);
                move_uploaded_file($_FILES['the_files']['tmp_name'], $_SERVER['DOCUMENT_ROOT'].$this->view->currentPageDetails['projectPath']."public/uploaded_resource/frontend/band/music/".$vFile_mp3);
                $listInsert['iId']= $insertId;
                print jsonResponse(array(
                    'success'=> TRUE,
                    'data'=> $listInsert
                ));
            }
        }
        
        public function loadMusicAction(){
            $this->_helper->getHelper('layout')->disableLayout();
            $this->_helper->viewRenderer->setNoRender();
            $allParams= $this->_getAllParams();
            $audioOb= new Frontend_Model_Tracks();
            $whereCondition= "iMember_id={$allParams['iId']}";
            $listMusic= $audioOb->getList($whereCondition, '', '', '', '', array('iId', 'vTitle', 'iSort'));
//            fb($listMusic);
            $listMusicSort= array();
            if(!empty($listMusic)){
                $listMusicSort= $audioOb->subvalSort($listMusic, 'iSort');
            }
            print jsonResponse(array(
                'success'=> TRUE,
                'data'=> $listMusicSort
            ));
        }
        
        public function changeTitleAction(){
            $this->_helper->getHelper('layout')->disableLayout();
            $this->_helper->viewRenderer->setNoRender();
            $allParams= $this->_getAllParams();
            $audioOb= new Frontend_Model_Tracks();
//            fb($allParams);
            $whereCondition= "iId={$allParams['iId']}";
            $updateArray= array(
                'vTitle'=> $allParams['vTitle']
            );
            $audioOb->doUpdate($updateArray, $whereCondition);
            $updateArray['iId']= $allParams['iId'];
            print jsonResponse(array(
                'success'=> TRUE,
                'data'=> $updateArray
            ));
        }
        
        public function deleteMusicAction(){
            $this->_helper->getHelper('layout')->disableLayout();
            $this->_helper->viewRenderer->setNoRender();
            $allParams= $this->_getAllParams();
            $audioOb= new Frontend_Model_Tracks();
            $whereCondition= "iId={$allParams['iId']}";
            $listMusic= $audioOb->getList($whereCondition, '', '', '', '', array('vFile_mp3'));
            $audioOb->doDelete($whereCondition);
            @unlink($_SERVER['DOCUMENT_ROOT'].$this->view->currentPageDetails['projectPath']."public/uploaded_resource/frontend/band/music/".$listMusic[0]['vFile_mp3']);
            $count= $audioOb->getTotalRecord();
            print jsonResponse(array(
                'success'=> TRUE,
                'data'=> $allParams['iId'],
                'count'=> $count
            ));
        }
        
        public function sortableMusicAction(){
            $this->_helper->getHelper('layout')->disableLayout();
            $this->_helper->viewRenderer->setNoRender();
            $allParams= $this->_getAllParams();
            $audioOb= new Frontend_Model_Tracks();
            $arrayIdEx= explode('&', $allParams['data']);
            foreach ($arrayIdEx as $key=>$value){
                $arrayId[]= explode('=', $arrayIdEx[$key]);
            }
            foreach ($arrayId as $key2=>$value2){
                $whereCondition= "iId={$arrayId[$key2][0]}";
                $arrayUp= array(
                    'iSort'=> $arrayId[$key2][1]
                );
                if($arrayUp['iSort'] != NULL){
                    $audioOb->doUpdate($arrayUp, $whereCondition);
                }
                
            }
            
        }
    }
?>