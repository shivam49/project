<?php
class Frontend_Model_BandsMembers extends Zend_Db_Table_Abstract{
    protected $_name='bands_members';
    protected $_primary='iId';
    protected $_insert_fields=array(
        'iBand_id'=>'bigint',
        'vName_lastname'=>'varchar',
        'vInstrument'=>'varchar'
    );
    protected $_update_fields = array(
        'iBand_id'=>'bigint',
        'vName_lastname'=>'varchar',
        'vInstrument'=>'varchar'
    );
      // - [START] Public function -----------------------------------------------
    public function doInsert(array $arr_data) {
        return $this->_modifyRow($arr_data);
    }

    
    public function doUpdate(array $arr_data, $whereCondition) {
        $whereCondition = mysql_escape_string($whereCondition);
        return $this->_modifyRow($arr_data, 'update', $whereCondition);
    }
    
     public function hashPassword($pass, $salt) {
        $salt = sha1($salt);
        $hash = base64_encode(sha1($pass . $salt, true) . $salt);
        return $hash;
    }

    public function getRecordById($RecordId) {
        $whereCondition = "iId={$RecordId}";
        $row = $this->fetchRow( $whereCondition );
        return $row;
    }
    
    public function doDelete($whereCondition) {
        $whereCondition = mysql_escape_string($whereCondition);
        $row = $this->fetchRow($whereCondition);
        $row->delete();
    }
      public function uploadFile($picData, $defaultName='') {
        if ( $defaultName ) {
            $names = explode('.', $picData['name']);
            $ext = $names[count($names)-1];
            $fileName = $defaultName . '.' . $ext;
            $overWrite = true;
        } else {
            $fileName = time() . $picData['name'];
            $overWrite = false;
        }
        
        $uploader = new Zend_File_Transfer_Adapter_Http();
        $uploader->setDestination( $this->resourcePath );
        
        $uploader->addFilter(
            'Rename', 
            array(
                'target' => $this->resourcePath . '/' . $fileName,
                'overwrite' => $overWrite
            )
        );
        
        if ( $uploader->receive() ) { 
            return $uploader->getFileName(null, false);
        } else {
            return false;    
        } 
    }

     public function getTotalRecord($whereCondition='') {
	$selectCols = 'COUNT(iId) as totalRecord';
        $objSelect = $this->select();
        $objSelect->from($this->_name, $selectCols);
        if ( $whereCondition!='' ) {
            $objSelect->where($whereCondition );
        }
        $totalRecord = $objSelect->query()->fetchColumn();
        return $totalRecord;
    }
    public function doDeleteById($userId, $loggedUserId, $panelOwnerId) {
        if ($loggedUserId!=$userId and $panelOwnerId!=$userId) {
            $whereCondition = "iId={$userId}";
            $this->doDelete($whereCondition);
            return '';
        } else {
            if ( $loggedUserId==$userId ) {
                return 'you cannot remove yourself';
            }
            if ( $panelOwnerId==$userId ) {
                return 'you cannot remove pannel owner';
            }
        }    
    }
    
    public function getList($whereCondition='', $start=0, $limit=20, $sort='iId', $dir='ASC', $array = '*') {
        $start = mysql_escape_string($start);
        $limit = mysql_escape_string($limit);
        $sort = mysql_escape_string($sort);
        $dir = mysql_escape_string($dir);
        $objSelect = $this->select();
        $objSelect->from($this->_name, $array);        
        $objSelect->limit($limit, $start);
        if ($whereCondition == NULL) {
           $rows = $objSelect->query()->fetchAll();
       } else {
           $whereCondition = prepareCondition($whereCondition);
           $rows = $objSelect->where($whereCondition)->query()->fetchAll();
       }
        return $rows;
    }

    // - [START] Protected function --------------------------------------------
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
                        //fb($arr_data[$field]);
                        $dateObject = new Zend_Date($arr_data[$field]);
                        $data[$field] = $dateObject->get(Zend_Date::TIMESTAMP);
                    } else {
                        if ( array_key_exists($field, $arr_data) ) {
                            $data[$field] = '';
                        }
                    }
                    break;

                case 'file' :
                    if ($arr_data[$field] != '') {
                        $data[$field] = mysql_escape_string($arr_data[$field]);
                    } else {
                        if ( array_key_exists($field, $arr_data) ) {
                            $data[$field] = '';
                        }
                    }
                    break;

                default :
                    if ( isset($arr_data[$field]) ) {
                        $data[$field] = mysql_escape_string($arr_data[$field]);
                    }
            }
        }

        $row->setFromArray($data);
        $row->save();
        $id = $row->iId;
        return $id;
    }
    // - [END] Protected function ----------------------------------------------
    
}