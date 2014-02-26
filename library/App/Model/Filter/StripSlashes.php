<?php
class App_Model_Filter_StripSlashes implements Zend_Filter_Interface
{ 
    public function filter($value)
    {
        return $this->_clean($value);
    }
  
    protected function _clean($value)
    {
        return is_array($value) ? array_map(array($this, '_clean'), $value) : stripslashes($value);
    }
}
?>
