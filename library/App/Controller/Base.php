<?php

abstract class App_Controller_Base extends Zend_Controller_Action {

    protected $_loggedUser;
    protected $_pannelProperties;
    protected $_currentPageDetails;

    public function init() {
        $this->_currentPageDetails = $this->_helper->page->getDetails();
        $this->view->currentPageDetails = $this->_currentPageDetails;
        $this->_loggedUser = $this->_helper->app->getLoggedUser();
        $this->view->loggedUser = $this->_loggedUser;
        $this->_pannelProperties = $this->_helper->app->getPannel();
        $this->view->pannelProperties = $this->_pannelProperties;
        $this->view->currentSessionId = session_id();
        $this->view->currentSessionIdSecurity = md5(session_id() . PASSWORD_SALT);

        $projectModuleNs = new Zend_Session_Namespace('projectModule');
        if ( !isset($projectModuleNs->departmentId) ) {
            $projectModuleNs->departmentId = 0;
        }        
        if ( !isset($projectModuleNs->loggedUser) ) {
            $projectModuleNs->loggedUser = $this->_loggedUser;
        }        
        if ( !isset($projectModuleNs->pannelProperties) ) {
            $projectModuleNs->pannelProperties = $this->_pannelProperties;
        }        
        
        
        $projectModuleNs = new Zend_Session_Namespace('projectModule');
        if ($projectModuleNs->projectId) {
            $prjObj = new Frontend_Model_Project();
            $currentProject = $prjObj->getRecordById($projectModuleNs->projectId);
            $this->view->projectProperties = $currentProject;
        }

        parent::init();
    }

    public function preDispatch() {
        $onlineUserObj = new Frontend_Model_OnlineUsers();
        $onlineUserObj->refreshOnlineUserTable();

        if (Zend_Auth::getInstance()->hasIdentity() and $onlineUserObj->isUserOnline($this->_loggedUser->iId)) {

            $userTypeId = $this->_loggedUser->iType_id;
            $currentPosition = $this->_currentPageDetails;
            if (userHasPermission($userTypeId, $currentPosition)) {
                $onlineUserObj->updateOnlineUser($this->_loggedUser->iId);
                //fb('YES permission');
                parent::preDispatch();
            } else {
                /* AJAX check  */
                if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                    //fb('NO AJAX permission');
                    print jsonResponse(array(
                                'success' => false,
                                'expire' => true,
                                'url' => APPLICATION_BASEURL.'/frontend/authentication/login/id/' . $this->_loggedUser->iPanel_id,
                                'error' => 'NotHavePermission'
                            ));
                    die();
                } else {
                    //fb('NO permission');
                    $this->_redirect('/frontend/authentication/login/id/' . $this->_loggedUser->iPanel_id);
                }
            }
        } else {

            /* AJAX check  */
            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                //fb('AJAX EXpired user');
                print jsonResponse(array(
                            'success' => false,
                            'expire' => true,
                            'url' => APPLICATION_BASEURL.'/frontend/authentication/login/id/' . $this->_loggedUser->iPanel_id,
                            'error' => 'NotHavePermission'
                        ));
                die();
            } else {
                //fb('expired user');
                $this->_redirect('/frontend/authentication/login/id/' . $this->_loggedUser->iPanel_id);
            }
        }
    }

    public function countModuleLimitationUsed($panelTypeId, $panelId) {
        $panelTypeObj = new Backend_Model_PanelType();
        $panelDetails = $panelTypeObj->getRecordById($panelTypeId);
        $whereCondition = "iPanel_id ={$panelId}";
        $data = array();
        $departModel = new Frontend_Model_Depart();
        $departCount = $departModel->getTotalRecord($whereCondition);
        if ($departCount == $panelDetails->vDepartment_count) {
            $finishCount = true;
        } else {
            $finishCount = false;
        }

        $data['depart'] = array(
            'title' => $panelDetails->vDepartment_count . ' / ' . $departCount,
            'finish' => $finishCount
        );

        $userModel = new Frontend_Model_User();
        $userCount = $userModel->getTotalRecord($whereCondition);
        if ($userCount == $panelDetails->vUser_count) {
            $finishCount = true;
        } else {
            $finishCount = false;
        }
        $data['user'] = array(
            'title' => $panelDetails->vUser_count . ' / ' . $userCount,
            'finish' => $finishCount
        );

        $projectCategoryModel = new Frontend_Model_ProjectCategory();
        $projectCategoryCount = $projectCategoryModel->getTotalRecord($whereCondition);
        if ($projectCategoryCount == $panelDetails->vProject_category_count) {
            $finishCount = true;
        } else {
            $finishCount = false;
        }
        $data['projectCategory'] = array(
            'title' => $panelDetails->vProject_category_count . ' / ' . $projectCategoryCount,
            'finish' => $finishCount
        );

        $projectModel = new Frontend_Model_Project();
        $projectCount = $projectModel->getTotalRecord($whereCondition);
        if ($projectCount == $panelDetails->vProject_count) {
            $finishCount = true;
        } else {
            $finishCount = false;
        }
        $data['project'] = array(
            'title' => $panelDetails->vProject_count . ' / ' . $projectCount,
            'finish' => $finishCount
        );
        return $data;
    }
    
}

?>
