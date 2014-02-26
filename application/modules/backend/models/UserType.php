<?php

class Backend_Model_UserType extends Zend_Db_Table_Abstract {
    
    protected $_name = 'user_type_admin';
    protected $_primary = 'iId';
    
    protected $_insert_fields = array(
        'eStatus' => 'varchar',
        'vTitle' => 'varchar',
        'vRole' =>'varchar',
        'tPermissions'=>'longtext'
        
    );
    
        protected $_update_fields = array(
        'eStatus' => 'varchar',
        'vTitle' => 'varchar',
        'vRole' =>'varchar',
        'tPermissions'=>'longtext'
    );

    
    public function doInsert(array $arr_data) {
        return $this->_modifyRow($arr_data);
    }

    
    public function doUpdate(array $arr_data, $whereCondition) {
        $whereCondition = mysql_escape_string($whereCondition);
        return $this->_modifyRow($arr_data, 'update', $whereCondition);
    }
    
    public function getRecordById($RecordId) {
        $whereCondition = "iId={$RecordId}";
        $row = $this->fetchRow( $whereCondition );
        return $row->toArray();
    }
    
    public function getPermissions($RecordId){
        $userTypeInfo = $this->getRecordById($RecordId);
        return  $userTypeInfo['tPermissions'];
    }
    
    public function doDelete($whereCondition) {
        $whereCondition = prepareCondition($whereCondition);
        $row = $this->fetchRow($whereCondition); 
        $row->delete();
    }
    
    public function getModulePermissionId( $name ) {
        $id = 0;
        $name = strtolower( $name );
        $moduleObj = new Frontend_Model_UserPermissionModule();
        $whereCondition = array("vModule='?'", array($name));
        $columns = 'iId';
        $rowResult = $moduleObj->getList($whereCondition, $columns);
        if ( $rowResult ) {
            $id = $rowResult[0]['iId'];
        }
        return $id;
    }

    public function getControllerPermissionId( $name ) {
        $id = 0;
        $name = strtolower( $name );
        $controllerObj = new Frontend_Model_UserPermissionController();
        $whereCondition = array("vController='?'", array($name));
        $columns = 'iId';
        $rowResult = $controllerObj->getList($whereCondition, $columns);
        if ( $rowResult ) {
            $id = $rowResult[0]['iId'];
        }
        return $id;
    }

    public function getActionPermissionId( $name ) {
        $id = 0;
        $name = strtolower( $name );
        $actionObj = new Frontend_Model_UserPermissionAction();
        $whereCondition = array("vAction='?'", array($name));
        $columns = 'iId';
        $rowResult = $actionObj->getList($whereCondition, $columns);
        if ( $rowResult ) {
            $id = $rowResult[0]['iId'];
        }
        return $id;
    }
    
    public function getPermissionForJs( $permissionStr ) {
        // @todo : according to user permission compile permission array to JS
        //print $permissionStr;
        $result = array(
            'application_north' => array(
                'hidden' => false,
                'disabled' => false
            )
        );
        return $result;
    }

    public function extGetList($whereCondition='', $start='', $limit='', $sort='', $dir='') {
        $objSelect = $this->select();
        $objSelect->from($this->_name);
       
        if($sort!=''){
            $sort = prepareCondition($sort);
            $dir = prepareCondition($dir);
            $objSelect->order($sort . ' ' . $dir);
        }
        if($start!=''){
            $start = prepareCondition($start);
            $limit = prepareCondition($limit);   
            $objSelect->limit($limit, $start);
        }     
        if ( $whereCondition ) {
            $objSelect->where( prepareCondition($whereCondition) );
            $rows = $objSelect->query()->fetchAll();
        } else {
            $rows = $objSelect->query()->fetchAll();
        }
        return $rows;
    }

    
     public function getAvailableActions($userInfo,$panelInfo){
        $actions = array();
        $actionModel = new Frontend_Model_UserPermissionAction();
        $userPermission='';
        $userTypePermission = $this->getPermissions($userInfo->iType_id);
        if($userTypePermission=='*'){
           $panelTypeModel = new Backend_Model_PanelType();
           $panelTypeInfo = $panelTypeModel->getRecordById($panelInfo->iType_id);
           $userPermission = $panelTypeInfo['tPermissions'];
        }
        else{
           $userPermission = $userTypePermission;
        }
        
        if($userPermission=='*'){ 
           return $actionModel->getList(array("eStatus='?'",array('Active')),array('iId','vTitle'));
        }
        else{
           $controllerModel = new Frontend_Model_UserPermissionController();
           $userPermissionArr = explode(',', $userPermission);
           foreach($userPermissionArr as $permInitial){
               if(substr($permInitial,0,1)=='m'){
                   $whereCondition = array("iModule_id=? AND eStatus='?'",array(substr($permInitial,1),'Active'));
                   $listOfControllers = $controllerModel->getList($whereCondition,array('iId'));
                   foreach($listOfControllers as $controller){
                       $whereCondition = array("iController_id=? AND eStatus='?'",array($controller['iId'],'Active'));
                       $actions = array_merge($actions,$actionModel->getList($whereCondition,array('iId','vTitle')));
                   }
               }
               elseif(substr($permInitial,0,1)=='c'){
                   $whereCondition = array("iController_id=? AND eStatus='?'",array(substr($permInitial,1),'Active'));
                   $actions = array_merge($actions,$actionModel->getList($whereCondition,array('iId','vTitle'))); 
               }
               else{
                   $actionIfo = $actionModel->getRecordById(substr($permInitial,1));
                   $actions[] = array('iId' => $actionIfo['iId'] , 'vTitle' => $actionIfo['vTitle'] );
               }
           }
       }
        return $actions;
      }
      
      public function getAvailableControllers($userInfo,$panelInfo){
       $controllers = array();
       $controllerModel = new Frontend_Model_UserPermissionController();
       $userPermission='';
       $userTypePermission = $this->getPermissions($userInfo->iType_id);
       if($userTypePermission=='*'){
           $panelTypeModel = new Backend_Model_PanelType();
           $panelTypeInfo = $panelTypeModel->getRecordById($panelInfo->iType_id);
           $userPermission = $panelTypeInfo['tPermissions'];
       }
       else{
           $userPermission = $userTypePermission;
       }

       if($userPermission=='*'){ 
           return $controllerModel->getList(array("eStatus='?'",array('Active')),array('iId','vTitle'));
       }
       else{
           $userPermissionArr = explode(',', $userPermission);
           foreach($userPermissionArr as $permInitial){
               if(substr($permInitial,0,1)=='m'){
                   $whereCondition = array("iModule_id=? AND eStatus='?'",array(substr($permInitial,1),'Active'));
                   $controllers = array_merge($controllers,$controllerModel->getList($whereCondition,array('iId','vTitle')));
               }
               elseif(substr($permInitial,0,1)=='c'){
                   $controllerInfo = $controllerModel->getRecordById(substr($permInitial,1));
                   $controllers[] = array('iId' => $controllerInfo['iId'] , 'vTitle' => $controllerInfo['vTitle'] );
               }
               else{
                   $actionModel = new Frontend_Model_UserPermissionAction();
                   $actionInfo = $actionModel->getRecordById(substr($permInitial,1));
                   $controllerInfo = $controllerModel->getRecordById($actionInfo['iController_id']);
                   if(!$controllers['c'.$controllerInfo['iId']]){
                       $controllers['c'.$controllerInfo['iId']] = array('iId' => $controllerInfo['iId'] , 'vTitle' => $controllerInfo['vTitle'] );
                   }
               }
           }
       }
    return array_values($controllers);
 }
 
       public function hasUserAccess($permInitial,$userPermissionArray){
        
       if(in_array($permInitial, $userPermissionArray)){
           return true;
       }
       $controllerModel = new Frontend_Model_UserPermissionController();
       if(substr($permInitial,0,1)=='c'){
         $controllerInfo = $controllerModel->getRecordById(substr($permInitial,1));  
         $moduleKey = 'm'.$controllerInfo['iModule_id'];
         if(in_array($moduleKey, $userPermissionArray)){
             return true;
         }
         else{
             return false;
         }
       }
       $actionModel = new Frontend_Model_UserPermissionAction();
       if(substr($permInitial,0,1)=='a'){
          $actionInfo = $actionModel->getRecordById(substr($permInitial,1));
          $controllerKey = 'c'.$actionInfo['iController_id'];
          if(in_array($controllerKey, $userPermissionArray)){
              return true;
          }
          $controllerInfo = $controllerModel->getRecordById($actionInfo['iController_id']); 
          $moduleKey = 'm'.$controllerInfo['iModule_id'];
          if(in_array($moduleKey, $userPermissionArray)){
              return true;
          }
       }
       return false;       
      }
      
      
    public function getPanelHighestAccessHirachy($panelTypeId){
              $panelTypeModel = new Backend_Model_PanelType();
              $modulePermissionModel = new Frontend_Model_UserPermissionModule();
              $controllerPermissionModel = new Frontend_Model_UserPermissionController();
              $actionPermissionModel = new Frontend_Model_UserPermissionAction();
              
              $panelTypeInfo = $panelTypeModel->getRecordById($panelTypeId);
              $panelPermission = $panelTypeInfo->tPermissions;
              $fullPermission=array();
              if($panelPermission=='*'){
                  $modules = $modulePermissionModel->getList(array('eStatus="?"',array('Active')));
                  foreach($modules as $module){
                      $moduleId = $module['iId'];
                      $moduleKey = 'm'.$module['iId'];
                      $controllers=$controllerPermissionModel->getList(array("iModule_id=? AND eStatus='?'",array($moduleId,'Active')));
                      $controllersList = array();
                      foreach($controllers as $controller){
                          $controllerId=$controller['iId'];
                          $controllerKey = 'c'.$controllerId;
                          $controllerInfo = $controllerPermissionModel->getRecordById($controllerId);
                          $controllersList[$controllerKey] = array('cname'=>$controllerInfo['vTitle'],'status'=>true,
                                                            'aname'=>$this->getActionsOfController($controllerId));

                      }
                      $moduleInfo = $modulePermissionModel->getRecordById($moduleId);
                      $fullPermission[$moduleKey]=array('mname'=>$moduleInfo['vTitle'],'status'=>true,'controllers'=>$controllersList);
                  }
                  return $fullPermission;
              }
              $permissionInitial =  explode(',',$panelPermission);
              foreach($permissionInitial as $value){
                  if(substr($value,0,1)=='m'){
                      $moduleId = substr($value,1);
                      $moduleKey = $value;
                      $moduleInfo = $modulePermissionModel->getRecordById($moduleId);
                      $controllersOfModule = $controllerPermissionModel->getList(array('iModule_id=? and eStatus="?"',array($moduleId,'Active')));
                      $controllersList =array();
                      foreach($controllersOfModule as $controller){
                         $controllerId=$controller['iId'];
                         $controllerKey = 'c'.$controllerId;
                         $controllerInfo = $controllerPermissionModel->getRecordById($controllerId);
                         $controllersList[$controllerKey] = array('cname'=>$controllerInfo['vTitle'],'status'=>true,
                                                            'aname'=>$this->getActionsOfController($controllerId)); 
                      }
                      $fullPermission[$moduleKey]=array('mname'=>$moduleInfo['vTitle'],'status'=>true,'controllers'=>$controllersList);
                      
                  }
                  elseif(substr($value,0,1)=='c'){
                      $controllerId=substr($value,1);
                      $controllerKey = $value;
                      $controllerInfo = $controllerPermissionModel->getRecordById($controllerId);
                      $moduleId = $controllerInfo['iModule_id'];
                      $moduleKey ='m'.$moduleId;
                      if($fullPermission[$moduleKey]){
                          $fullPermission[$moduleKey]['controllers'][$controllerKey]=array('cname'=>$controllerInfo['vTitle'],'status'=>true,
                                                                                 'aname'=>$this->getActionsOfController($controllerId)      
                              );
                      }
                      else{
                          $controller=array('cname'=>$controllerInfo['vTitle'],'status'=>true,
                                                            'aname'=>$this->getActionsOfController($controllerId));
                          $moduleInfo = $modulePermissionModel->getRecordById($moduleId);
                          $fullPermission[$moduleKey]=array('mname'=>$moduleInfo['vTitle'],'status'=>false,'controllers'=>array($controllerKey => $controller));
                      }
                                          
                  }
                  else{
                      $actionId = substr($value,1);
                      $actionKey = $value;
                      $actionInfo = $actionPermissionModel->getRecordById($actionId);
                      $controllerId=$actionInfo['iController_id'];
                      $controllerKey = 'c'.$controllerId ;
                      $controllerInfo = $controllerPermissionModel->getRecordById($controllerId);
                      $moduleId = $controllerInfo['iModule_id'];
                      $moduleKey ='m'.$moduleId;
                      if($fullPermission[$moduleKey]){
                          if($fullPermission[$moduleKey]['controllers'][$controllerKey]){
                              $fullPermission[$moduleKey]['controllers'][$controllerKey]['aname'][$actionKey] = $actionInfo['vTitle'];
                          }
                          else{
                              $fullPermission[$moduleKey]['controllers'][$controllerKey]=array('cname'=>$controllerInfo['vTitle'],'status'=>false,
                                                 'aname'=>array($actionKey => $actionInfo['vTitle']));

                          }
                      }
                      else{
                          $moduleInfo = $modulePermissionModel->getRecordById($moduleId);
                          $controller=array('cname'=>$controllerInfo['vTitle'],'status'=>false,
                                                            'aname'=>array($actionKey=>$actionInfo['vTitle']));
                          $fullPermission[$moduleKey]=array('mname'=>$moduleInfo['vTitle'],'status'=>false,
                                                              'controllers'=>array($controllerKey => $controller));
                      }
                  }
                 
              }     

       return    $fullPermission;    
    }
    
    public function getActionsOfController($controllerId){
        $actionPermissionModel = new Frontend_Model_UserPermissionAction();  
        $listOfActions = $actionPermissionModel->getList(array('iController_id=? and eStatus="?"',array($controllerId,'Active')));
        $actionsName=array();
        foreach($listOfActions as $value){
           $actionKey='a'.$value['iId'];
           $actionsName[$actionKey]=$value['vTitle'];
        }
        return $actionsName;
    }
    
    /******************protected****/
        protected function _modifyRow(array $arr_data, $type='insert', $where='') {

        switch ($type) {
            case 'insert' :
                $row = $this->createRow();
                $arr_fields = $this->_insert_fields;
                break;

            case 'update' :
                $row = $this->fetchRow($this->select()->where($where));
                $arr_fields = $this->_update_fields;
                break;

            default :
                return false;
        }

        foreach ($arr_fields as $field => $field_type) {
            switch ($field_type) {
                case 'date' :
                    if ($arr_data[$field] != '') {
                        $dateObject = new Zend_Date($field);
                        $data[$field] = $dateObject->get(Zend_Date::TIMESTAMP);
                    }
                    break;

                case 'file' :
                    if ($arr_data[$field] != '') {
                        $data[$field] = mysql_escape_string($arr_data[$field]);
                    }
                    break;

                default :
                    if (isset($arr_data[$field])) {
                        $data[$field] = mysql_escape_string($arr_data[$field]);
                    }
            }
        }

        $row->setFromArray($data);
        $row->save();
        $id = $row->iId;
        return $id;
    }
    
//---------------------------------------------------------------------------------------------------------------------------------------------------
    
    
}


?>