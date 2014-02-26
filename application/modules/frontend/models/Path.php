<?php
class Frontend_Model_Path {

    private $_path = null;
    private $_pathPrefix = 'public/uploaded_resource/frontend/';
    private $_bandId = null;
    
    public function __construct($bandId) {
        $this->setBand($bandId);
    }

    private function setBandPath() {
        $this->_path = ($this->_pathPrefix . 'band-' . $this->getBand() . '/');
    }

    private function getBandPath() {
        return $this->_path;
    }
    
    public function setBand($bandId) {
        $this->_bandId = (int)$bandId;
        $this->setBandPath();
    }

    public function getBand() {
        return $this->_bandId;
    }

     
    public function getPath($key=null, $endSlash=true) {
        $path = $this->getBandPath();
        
        switch( $key ) {
            case 'band':
                $path .= '';
            break;
            default:
        }
        
        if (!$endSlash) {
            $path = substr($path, 0, strlen($path)-1);
        }
        return $path;
    }
}
?>