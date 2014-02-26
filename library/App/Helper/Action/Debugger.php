<?php

class App_Helper_Action_Debugger extends Zend_Controller_Action_Helper_Abstract
{

    public function alert( $param ) {
        print '<br><hr>';
        if ( gettype($param)=='array' or gettype($param)=='object' ) {
            print '<pre>';
            print_r($param);
            print '</pre><br>';
        } else {
            print "{$param}<br>";
        }
    }

}
?>