<?php

class Backend_FansController extends Zend_Controller_Action {
    protected $_rowPerPage = 5;
    protected $_startPoint = 0;
    protected $_sortField = 'signupDate';
    protected $_sortType = 'DESC';
    protected $_searchName = '';
    protected $_searchLastname = '';
    protected $_searchEmail = '';
    
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
    }
    
    public function getList(){
        $db = Zend_Registry::get('db');
        if ( $this->_searchName != '' or $this->_searchLastname != '' or $this->_searchEmail != '') {
            $searchName = $db->quote($this->_searchName);
            $searchName = substr($searchName, 1, strlen($searchName)-2);
            $searchLastname = $db->quote($this->_searchLastname);
            $searchLastname = substr($searchLastname, 1, strlen($searchLastname)-2);
            $sqlStr =  "select 
                                    f.iId as fanId,
                                    f.vName as fanName,
                                    f.vLastname as fanFamily,
                                    m.vEmail as memberEmail

                            from 
                                    fans as f
                            inner join
                                    members as m 
                            ON 
                                    f.iMember_id = m.iId
                            WHERE (f.vName like '%{$this->_searchName}%' AND f.vLastname like '%{$this->_searchLastname}%' AND m.vEmail like '%{$this->_searchEmail}%')


                        ";
            $stmt = $db->query( $sqlStr );
            $rows = $stmt->fetchAll();
            $totalFans = count($rows);
            $page = ceil($totalFans / $this->_rowPerPage);
            $sqlStr =  "select 
                                    f.iId as fanId,
                                    f.vName as fanName,
                                    f.vLastname as fanFamily,
                                    f.vName_title as fanNametitle,
                                    m.vEmail as memberEmail,
                                    m.dUser_signup_date as memberUsersignupDate

                            from 
                                    fans as f
                            inner join
                                    members as m 
                            ON 
                                    f.iMember_id = m.iId
                            WHERE (f.vName like '%{$this->_searchName}%' AND f.vLastname like '%{$this->_searchLastname}%' AND m.vEmail like '%{$this->_searchEmail}%')
                            ORDER BY memberUsersignupDate DESC
                            LIMIT {$this->_startPoint}, {$this->_rowPerPage}


                        ";
            
        }else{
            $sqlStr = "select iId from fans";
            $stmt = $db->query( $sqlStr );
            $rows = $stmt->fetchAll();
            $totalFans = count($rows);
            $page = ceil($totalFans / $this->_rowPerPage);

            $sqlStr =  "select 
                                    f.iId as fanId,
                                    f.vName as fanName,
                                    f.vLastname as fanFamily,
                                    f.vName_title as fanNametitle,
                                    m.vEmail as memberEmail,
                                    m.dUser_signup_date as memberUsersignupDate

                            from 
                                    fans as f
                            left join
                                    members as m 
                            ON 
                                    f.iMember_id = m.iId
                            WHERE(m.eType = 'Fan')
                            ORDER BY memberUsersignupDate DESC
                            LIMIT {$this->_startPoint}, {$this->_rowPerPage}


                        ";
        }
        
        $stmt = $db->query( $sqlStr );
        $rows = $stmt->fetchAll();
        if ( $totalFans ) {
            return array(
                'rows' => $rows,
                'totalPages' => $page
            );
        } else {
            return false;
        }
    }
    public function getFansAction(){
        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->getHelper('layout')->disableLayout();
        
        $allParams = $this->_getAllParams();
        if ($allParams['page'] > 1) {
            $this->_startPoint = ($allParams['page'] - 1) * $this->_rowPerPage;
        } else {
            $this->_startPoint = 0;
        }
        
        $returnData = $this->getList();
        
        if($returnData){
            print jsonResponse(
                        array(
                            'success'=>true,
                            'data'=>$returnData['rows'],
                            'count'=>$returnData['totalPages']
                        )
            );
        }
    }
    
    
    public function searchAction(){
        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->getHelper('layout')->disableLayout();
        $allParams = $this->_getAllParams();
        if ($allParams['page'] > 1) {
            $this->_startPoint = ($allParams['page'] - 1) * $this->_rowPerPage;
        } else {
            $this->_startPoint = 0;
        }

        $searchSess = new Zend_Session_Namespace('searchFans');
        if ( $allParams['vName'] or  $allParams['vLastname'] or $allParams['vEmail']) {
            $this->_searchName = $allParams['vName'];
            $this->_searchLastname = $allParams['vLastname'];
            $this->_searchEmail = $allParams['vEmail'];
            $searchSess->searchName = $this->_searchName;
            $searchSess->searchLastname = $this->_searchLastname;
            $searchSess->searchEmail = $this->_searchEmail;
        } else {
            $this->_searchName = $searchSess->searchName;
            $this->_searchLastname = $searchSess->searchLastname;
            $this->_searchEmail = $searchSess->searchEmail;
        }
        
        $returnData = $this->getList();
        if($returnData){
            print jsonResponse(
                        array(
                            'success'=>true,
                            'data'=>$returnData['rows'],
                            'count'=>$returnData['totalPages']
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
    
    
    
     public function getFanInfoAction(){
        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->getHelper('layout')->disableLayout();
        $memObj = new Frontend_Model_Members();
        $allParams = $this->_getAllParams();
        $wherecO = array("iId = '?'", array($allParams['id']));
        $fanObj = new Frontend_Model_Fans();
        $infoList = $fanObj->getList($wherecO);
        $countryObj = new Frontend_Model_Country();
        if(is_numeric($infoList[0]['vCountry'])){
            $countryList = $countryObj->getRecordById($infoList[0]['vCountry']);
            $country = $countryList['vName'];
        }else{
            $country=$infoList[0]['vCountry'];
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
        $whereCondition = array("iId = '?' ",array($infoList['0']['iMember_id']));
        $memList = $memObj->getList($whereCondition,'','','','',array('vEmail'));
        $infoList[0]['vEmail'] = $memList[0]['vEmail'];
        print jsonResponse(array(
            'success'=>true,
            'data' => $infoList[0]
        ));
    }
    
    
    public function saveFanBasicInfoAction(){
        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->getHelper('layout')->disableLayout();
        $allParams = $this->_getAllParams();
        $memberObj = new Frontend_Model_Members();
        $fansObj = new Frontend_Model_Fans();
        $fansList  = $fansObj->getRecordById($allParams['iId']);
        $listBefor = $memberObj->getRecordById($fansList['iMember_id']);
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
            $whereCondition = array("iId = '?'",array($fansList['iMember_id']));
            $array_member = array(
                'vEmail'=>$allParams['vEmail']
            );
            $up = $memberObj->doUpdate($array_member, $whereCondition);
            if(is_numeric($up)){
               $whereConditionFan = array("iId = '?'",array($allParams['iId']));
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
    
    
        public function saveFanMoreInfoAction(){
        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->getHelper('layout')->disableLayout();
        $allParams = $this->_getAllParams();
        $fansObj = new Frontend_Model_Fans();
        $countryObj = new Frontend_Model_Country();
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
            $whereConditionFan = array("iId = '?'",array($allParams['iId']));
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
    
    
    
    public function deleteFanMemberAction(){
        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->getHelper('layout')->disableLayout();
        $allParams = $this->_getAllParams();
        $memeberObj = new Frontend_Model_Members();
        $fansObj = new Frontend_Model_Fans();
        $fanList = $fansObj->getRecordById($allParams['id']);
        $whereCondition = array("iId = '?'", array($fanList['iMember_id']));
        $delMember = $memeberObj->doDelete($whereCondition);
        if($delMember){
            $whereCondition1 = array("iId = '?'", array($allParams['id']));
            $delFan = $fansObj->doDelete($whereCondition1);
            if($delFan){
                print jsonResponse(
                            array(
                                'success'=>true
                            )
                );
            }else{
                print jsonResponse(
                            array(
                                'success'=>FALSE
                            )
                );
            }
        }else{
                print jsonResponse(
                            array(
                                'success'=>FALSE
                            )
                );
            }
        
    }

}