<?php

class MoviesController extends Zend_Controller_Action {
    
    public function init() {
        $this->view->currentPageDetails = $this->_helper->page->getDetails();
    	parent::init();
    }


    public function indexAction() {
        $this->view->loggedUser  = $this->_helper->app->getLoggedUser();
        $movie_band = new Zend_Session_Namespace('band');
        $allParams = $this->_getAllParams();
        if($allParams['id']){
            
            $movie_band->member_id = $allParams['id'];
            if( $this->view->loggedUser ){
                
                $this->_helper->_layout->setLayout('layout-logout-not-homepage'); // show account info

            }else{
                $this->_helper->_layout->setLayout('layout-not-homepage'); // show signUp & Login links
//                $this->_redirect('/');
            } 
            
            
        }else{
            unset($movie_band->member_id);
            if( $this->view->loggedUser ){

                    $this->_helper->_layout->setLayout('layout-logout-not-homepage'); // show account info

            }else{
                $this->_helper->_layout->setLayout('layout'); // show signUp & Login links
                $this->_redirect('/');
            }
        }
        
        if($movie_band->member_id == $this->view->loggedUser->iId){
            $this->view->addMovie = 'Yes';
        }else{
            $this->view->addMovie = 'No';
        }
        
        if($allParams['id']==''){
            $this->view->addMovie = 'Yes';
        }
        
        

    }
    
    public function getMovieAction(){
        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->getHelper('layout')->disableLayout();
        $movie_band = new Zend_Session_Namespace('band');
        $this->view->loggedUser  = $this->_helper->app->getLoggedUser();
        if($movie_band->member_id == $this->view->loggedUser->iId){
            $whereCondition = array("iMember_id='?'",array($this->view->loggedUser->iId));
            $show_button = 'Yes';
        }elseif($movie_band->member_id != $this->view->loggedUser->iId AND $movie_band->member_id){
            $whereCondition = array("iMember_id='?'",array($movie_band->member_id));
            $show_button = 'No';
        }else{
            $whereCondition = array("iMember_id='?'",array($this->view->loggedUser->iId));
            $show_button = 'Yes';
        }
        
        $moviesObj = new Frontend_Model_Videos();
        $listMovie = $moviesObj->getList($whereCondition);
        foreach ($listMovie as $key => $value) {
            $listMovie[$key]['tVideo_embed_code'] = str_replace("\\", "", $listMovie[$key]['tVideo_embed_code']);
        }
        
        if(!empty($listMovie)){
            print jsonResponse(array(
                'success'=>true,
                'data'=>$listMovie,
                'show_btn'=>$show_button
            ));
        }  else {
            print jsonResponse(array(
                'success'=>true,
                'data'=>'no result'
            ));
        }
        
    }
    
    public function showMovieAction(){
        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->getHelper('layout')->disableLayout();
        $this->view->loggedUser  = $this->_helper->app->getLoggedUser();
        $movie_band = new Zend_Session_Namespace('band');
        $allParams = $this->_getAllParams();
        if($movie_band->member_id == $this->view->loggedUser->iId){
            $whereCondition = array("iId='?' AND iMember_id='?'",array($allParams['iId'],$this->view->loggedUser->iId));
        }elseif($movie_band->member_id != $this->view->loggedUser->iId AND $movie_band->member_id){
            $whereCondition = array("iId='?' AND iMember_id='?'",array($allParams['iId'],$movie_band->member_id));
        }else{
            $whereCondition = array("iId='?' AND iMember_id='?'",array($allParams['iId'],$this->view->loggedUser->iId));
        }
        $moviesObj = new Frontend_Model_Videos();
        $list = $moviesObj->getList($whereCondition);
        $list[0]['tVideo_embed_code'] = str_replace("\\", "", $list[0]['tVideo_embed_code']);
        $list[0]['tDesc'] = str_replace("\n", "<br />", $list[0]['tDesc']);
        print jsonResponse(array(
            'success'=>true,
            'data'=>$list[0]
        ));
    }

    public function saveMovieAction(){
        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->getHelper('layout')->disableLayout();
        $this->view->loggedUser  = $this->_helper->app->getLoggedUser();
        $allParams = $this->_getAllParams();
        $videoObj = new Frontend_Model_Videos();
        $iframe = $allParams['tVideo_embed_code'];
        $idMovie = $this->getIdYoutube($iframe);
        $infoMovie = $this->getInfoMovie($idMovie);
        $imageLink = $this->createImageLink($idMovie);
        $dateTime = new DateTime;
        $dDate_create=$dateTime->format('Y-m-d H:m:s');
        if($allParams['vVideo_title']!=''){
            $title = $allParams['vVideo_title'];
        }else{
            $title = $infoMovie['title'];
        }
        $array_movie = array(
            'iMember_id'=>$this->view->loggedUser->iId,
            'dDate_create'=> $dDate_create,
            'tVideo_embed_code'=> $allParams['tVideo_embed_code'],
            'tDesc'=>$allParams['tDesc'],
            'tVideo_image'=>$imageLink,
            'vVideo_title'=>$title
        );
        $id = $videoObj->doInsert($array_movie);
        if(is_numeric($id)){
            print jsonResponse(array(
                'success'=>true,
                'data'=>$id
            ));
        }  else {
            print jsonResponse(array(
                'success'=>FALSE,
            ));
        }
        
        
    }
    
    public function editMovieAction(){
        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->getHelper('layout')->disableLayout();
        $this->view->loggedUser  = $this->_helper->app->getLoggedUser();
        $allParams = $this->_getAllParams();
        $videoObj = new Frontend_Model_Videos();
        $iframe = $allParams['tVideo_embed_code'];
        $idMovie = $this->getIdYoutube($iframe);
        $infoMovie = $this->getInfoMovie($idMovie);
        $imageLink = $this->createImageLink($idMovie);
        $dateTime = new DateTime;
        $dDate_create=$dateTime->format('Y-m-d H:m:s');
        if($allParams['vVideo_title']!=''){
            $title = $allParams['vVideo_title'];
        }else{
            $title = $infoMovie['title'];
        }
        $array_movie = array(
            'iMember_id'=>$this->view->loggedUser->iId,
            'dDate_modify'=> $dDate_create,
            'tVideo_embed_code'=> $allParams['tVideo_embed_code'],
            'tDesc'=>$allParams['tDesc'],
            'tVideo_image'=>$imageLink,
            'vVideo_title'=>$title
        );
        $whereCondition = array("iId = '?' AND iMember_id = '?'",array($allParams['iId'],$this->view->loggedUser->iId));
        $up = $videoObj->doUpdate($array_movie, $whereCondition);
        if(is_numeric($up)){
            print jsonResponse(array(
                'success'=>true,
                'data'=>$up
            ));
        }  else {
            print jsonResponse(array(
                'success'=>false,
            ));
        }
            
        
        
    }

    public function removeMovieAction(){
        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->getHelper('layout')->disableLayout();
        $this->view->loggedUser  = $this->_helper->app->getLoggedUser();
        $allParams = $this->_getAllParams();
        $videosObj = new Frontend_Model_Videos();
        $whereCondition = array("iId = '?' AND iMember_id='?'",array($allParams['iId'], $this->view->loggedUser->iId));
        $deleteId = $videosObj->doDelete($whereCondition);
        if($deleteId == 1){
            print jsonResponse(array(
                'success'=>true,
                
            ));
        }else{
            print jsonResponse(array(
                'success'=>false,
                
            ));
        }
    }

        //--------------------------------------protected function
    
    protected function createImageLink($idMovie){
        if($idMovie!=null){
            return 'http://img.youtube.com/vi/'.$idMovie.'/1.jpg';
        }  else {
            return 'http://placehold.it/120x90';
        }
        
    }
    protected function getIdYoutube($iframe){
        $pos = strpos($iframe, 'embed/');
        if($pos){
            $pos_id = $pos + 6;
            $rest = substr($iframe, $pos_id); 
            $array_pos = explode("\"", $rest);
            return $array_pos[0];
        }  else {
            return null;
        }
        
        
    }
    
    protected function getInfoMovie($video_id){
        if($video_id != null){
            $content = file_get_contents("http://youtube.com/get_video_info?video_id=" . $video_id);
            parse_str($content, $ytarr);
            return $ytarr;
        }else{
            $ytarr['title'] = 'Untitle';
            return $ytarr ;
        }
        
    }

}
?>