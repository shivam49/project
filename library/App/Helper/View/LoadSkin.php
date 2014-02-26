<?php
/**
* this class loads the skin of template
*
*/
class App_Helper_View_LoadSkin extends Zend_View_Helper_Abstract {
	
    protected $_baseUrl = '';
    protected $_skin = 'public/skins/';
    protected $_templatePath = '';
    protected $_pathPrefix = '';

    public function loadSkin ($skin, $currentPageFilename) {
         $this->_baseUrl = Zend_Controller_Front::getInstance()->getBaseUrl();
        $this->_pathPrefix = $this->getResourcePrefix();
        $this->_templatePath = $this->_pathPrefix . $this->_skin;

        $xmlSkinPath = './' . $this->_skin . $skin . '/' . $currentPageFilename . '.xml';
        if ( file_exists($xmlSkinPath) ) {
            $skinData = new Zend_Config_Xml($xmlSkinPath);

            if ( $skinData->stylesheets ) {
                if ( is_object($skinData->stylesheets->stylesheet) ) {
                    $stylesheets = $skinData->stylesheets->stylesheet->toArray();
                    // append each stylesheet
                    if (is_array($stylesheets)) {
                            foreach ($stylesheets as $stylesheet) {
                                    $this->view->headLink()->appendStylesheet($this->_templatePath . $skin . '/' . $stylesheet);
                                    //$this->view->headLink()->appendStylesheet($this->_baseUrl . '/share/' . $stylesheet);
                            }
                    }
                } else {
                    $this->view->headLink()->appendStylesheet($this->_templatePath . $skin . '/' . $skinData->stylesheets->stylesheet);
                }    
            }
            
            // Load CSS file from path module-controller-action.css
            $pathOfCssFile =  $skin . '/css/' . $currentPageFilename . '.css';
            if ( file_exists('./' . $this->_skin . $pathOfCssFile) ) {
                    $this->view->headLink()->appendStylesheet($this->_templatePath . $pathOfCssFile);
            }

            // ---------------------------------------------------------------
            
            if ( $skinData->javascripts ) {
                if ( is_object($skinData->javascripts->javascript) ) {
                    $javascripts = $skinData->javascripts->javascript->toArray();
                    // append each js
                    if (is_array($javascripts)) {
                            foreach ($javascripts as $js) {

                                    if ( strtolower(substr($js,0,4))=='http' ) {
                                            $this->view->headScript()->appendFile($js);
                                    } else {
                                            $this->view->headScript()->appendFile($this->_templatePath . $skin . '/' . $js);
                                    }

                            }
                    }
                } else {
                    $this->view->headScript()->appendFile($this->_templatePath . $skin . '/' . $skinData->javascripts->javascript);
                }    
            }

            // Load JS file from path module-controller-action.js
            $pathOfJsFile =  $skin . '/js/' . $currentPageFilename . '.js';
            if ( file_exists('./' . $this->_skin . $pathOfJsFile) ) {
                    $this->view->headScript()->appendFile($this->_templatePath . $pathOfJsFile);
            }
         
        }
    }

    
    protected function getResourcePrefix() {
        $uri = $_SERVER['REQUEST_URI'];
        $decrease = false;
        if ( substr($uri, strlen($uri)-1, 1)!='/' ) {
            //$uri .= '/';
            //print 'decrease: YES';
            $decrease = true;
        }
        //print '<br>uri:'.$uri;
        $path = str_replace($this->_baseUrl, '', $uri);
        //print '<br>path:'.$path;
        $path = rtrim($path, '/');
        $path = ltrim($path, '/');
        //print '<br>path2:'.$path;
        if ( $path!='' ) {
            $pathParts = explode('/', $path);
            //var_dump(count($pathParts));
            if ( is_array($pathParts) ) {
                $counter = count($pathParts);
                if ( $decrease ) {
                    $counter--;
                }
                $path = './'. str_repeat('../', $counter);
                //print 'here';
            } else {
                $path = '';
            }
            //$path = rtrim($path, '/');
            //print '<br>path end:'.$path;
            return $path;
        } else {
            return '';
        }
            
    }
    
}
?>