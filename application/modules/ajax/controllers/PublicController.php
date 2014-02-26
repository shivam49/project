<?php

class Ajax_PublicController extends Zend_Controller_Action {
    
    public function init() {
        $this->view->currentPageDetails = $this->_helper->page->getDetails();
    	parent::init();
    }


    public function getTemplateAction() {
        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->getHelper('layout')->disableLayout();
        $allParams = $this->_getAllParams();
        $templateFile = $allParams['fileName'];
        //fb($allParams);
        if ( $allParams['path']!=null ) {
            $templatePath = $allParams['path'];
        } else {
            $templatePath = '';
        }

        if ( $allParams['modulation']!=null ) {
            $templateModule = $allParams['modulation'];
        } else {
            $templateModule = 'frontend';
        }

        $fileAddress = 'public/skins/' . $templateModule . '/default/templates/' . $templatePath . $templateFile . '.tpl';
        //fb($fileAddress);
        if (file_exists($fileAddress) ) {
            $fileContent = file_get_contents( $fileAddress );           
        } else {
            $fileContent = 'Template not exist!';
        }
        print $fileContent;
    }

}
?>