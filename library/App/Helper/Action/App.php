<?php

class App_Helper_Action_App extends Zend_Controller_Action_Helper_Abstract
{

    #--------------------------------------------------------------------------

    public function getLoggedUser() {
        if ( Zend_Auth::getInstance()->hasIdentity() ) {
            $auth = Zend_Auth::getInstance();
            $loggedUser = $auth->getStorage()->read();
            return $loggedUser;
        } else {
            return false;
        }    
    }
    
    #--------------------------------------------------------------------------
    
    public function getPannel() {
        $app = new Zend_Session_Namespace('app');
        return $app->pannelProperties;
    }
  
    #--------------------------------------------------------------------------
    
    public function setupPannel( $userId ) {
        
        $userId = mysql_escape_string( $userId );
        $dbAdapter = Zend_Db_Table::getDefaultAdapter();
        
        $strSql = "SELECT p.* FROM pannel as p, user as u where( p.iId=u.iPanel_id and u.iId='{$userId}' and p.eStatus='Active' )";
        //todo:use select optject againt sql query!
        $pannelProperties = $dbAdapter->fetchRow( $strSql );
        
        if ( $pannelProperties ) {
            $app = new Zend_Session_Namespace('app');
            $app->pannelProperties = (object) $pannelProperties;
            //$app->lock();

            return $pannelProperties;
        } else {
            return false;
        }
    }

    #--------------------------------------------------------------------------

}
?>