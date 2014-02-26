<?php



class Frontend_Model_Members extends Zend_Db_Table_Abstract {



    const RECORD_LIMITATION = 'شما با محدودیت اضا�?ه کردن کاربر جدید مواجه شدید. برای ر�?ع این محدودیت می توانید با مدیر سیستم برای ارتقاء پنل هماهنگی نمایید'; 

    const ASSIGN_LIMITATION = 'شما با محدودیت اختصاص کاربر جدید به پروژه مواجه شدید. برای ر�?ع این محدودیت می توانید با مدیر سیستم برای ارتقاء پنل هماهنگی نمایید'; 

    

    protected $_name = 'members';

    protected $_primary = 'iId';

    

    protected $_insert_fields = array(

        'vEmail' => 'varchar',

        'vPassword' => 'varchar',

        'vWebsite' => 'varchar',

        'eStatus' => 'varchar',

        'vFacebook_id' => 'varchar',

        'dUser_signup_date' => 'varchar',

        'eType' => 'varchar',

        

    );

    

    protected $_update_fields = array(

        'vEmail' => 'varchar',

        'vPassword' => 'varchar',

        'vWebsite' => 'varchar',

        'eStatus' => 'varchar',

        'vFacebook_id' => 'varchar',

        'dUser_signup_date' => 'varchar',

        'eType' => 'varchar',

    );



    protected $_panelId = null;

    // - [START] Public function -----------------------------------------------

    public function getPanel() {

        return $this->_panelId;

    }



    public function setPanel( $pid ) {

        $this->_panelId = $pid;

    }



    public function getPath() {       

        $p = new Frontend_Model_Path( $this->getPanel() );

        return $p->getPath('user');

    }



    public function doInsert(array $arr_data) {

        return $this->_modifyRow($arr_data);

    }



    

    public function doUpdate(array $arr_data, $whereCondition) {

        $whereCondition = prepareCondition($whereCondition);

        return $this->_modifyRow($arr_data, 'update', $whereCondition);

    }



    

    public function doDelete($whereCondition) {

        $whereCondition = prepareCondition($whereCondition);

        $row = $this->fetchRow($whereCondition);

        

        // Remove User Image File 

//        if ( $row->vImg!='male.jpg' and $row->vImg!='female.jpg' ) {
//
//            $file = $this->getPath() . $row->vImg;
//
//            if ( file_exists($file) ) {
//
//                @unlink($file);
//
//            }
//
//        }

        

        return $row->delete();

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

    



    

    public function getRecordById($RecordId) {

        $whereCondition = "iId={$RecordId}";

        $row = $this->fetchRow( $whereCondition );

        return $row;

    }

    

    

 public function getTotalRecord($whereCondition='') {

	$selectCols = 'COUNT(iId) as totalRecord';

        $objSelect = $this->select();

        $objSelect->from($this->_name, $selectCols);

        if ( $whereCondition!='' ) {

            $whereCondition = prepareCondition($whereCondition);

            $objSelect->where( $whereCondition );

        }

        $totalRecord = $objSelect->query()->fetchColumn();

        return $totalRecord;

    }

  

    

   public function getList($whereCondition='', $start='', $limit='', $sort='', $dir='',$array = '*') {

        $objSelect = $this->select();

        $objSelect->from($this->_name,$array);

       

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

            //fb($whereCondition);

            $objSelect->where( prepareCondition($whereCondition) );

                    //fb($objSelect->__toString());

            $rows = $objSelect->query()->fetchAll();

        } else {

            $rows = $objSelect->query()->fetchAll();

        }

        //fb($rows);

        return $rows;

    }

    

//    public function hashPassword($password, $salt) {

//        $salt = sha1($salt);

//        $hash = base64_encode(sha1($password . $salt, true) . $salt);

//        return $hash;

//    }



    

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

        $uploader->setDestination( $this->getPath()  );

        

        $uploader->addFilter(

            'Rename', 

            array(

                'target' => $this->getPath() . $fileName,

                'overwrite' => $overWrite

            )

        );

        

        if ( $uploader->receive() ) { 

            return $uploader->getFileName(null, false);

        } else {

            return false;    

        } 

    }





    public function getUploadErrors($file) {        

        $upload = new Zend_File_Transfer();

        $upload->addValidator('Extension', false, 'jpg,png,gif')

                ->getValidator('Extension');

        $upload->addValidator('FilesSize', false, array('max' => '2MB'))

                ->getValidator('FilesSize');

        $upload->isValid( $file['name'] );

        $uploadErrors = $upload->getMessages();

        if ( count($uploadErrors) ) {

            $arr_return['photo'] = implode($uploadErrors, '<br/>');

            return $arr_return;

        } else {

            return false;

        }

    }





    public function ChangePassword($userId, $old_pass, $new_pass, $repeat_new_pass) {

        $userinfo = $this->getRecordById($userId);

        $userPass = $this->hashPassword($old_pass, PASSWORD_SALT);



        $whereCondition = mysql_escape_string("iId={$userId}");

        $selectUser = $this->fetchRow($whereCondition); //get password of db



        if ($selectUser['vPassword'] == $userPass) {// Found User!

            if ($new_pass == $repeat_new_pass) {

                $userNewPass = $this->hashPassword($new_pass, PASSWORD_SALT);

                $arr['vPassword'] = $userNewPass;

                $this->doUpdate($arr, $whereCondition); // Update Password

                $output = 'success';

            } else {

                $output = 'inValidNewPassword';

            }

        } else {

            $output = 'inValidOldPassword';

        }



        return $output;

    }

    // - [END] Public function -------------------------------------------------

 

    

    

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

                        $dateObject = new Zend_Date($field);

                        $data[$field] = $dateObject->get(Zend_Date::TIMESTAMP);

                    }

                    break;



                case 'file' :

                    if ($arr_data[$field] != '') {

                        $data[$field] = $arr_data[$field];

                    }

                    break;



                default :

                    if (isset($arr_data[$field])) {

                        $data[$field] = $arr_data[$field];

                    }

            }

        }



        //var_dump($data);

        $row->setFromArray($data);

        $row->save();

        $id = $row->iId;

        return $id;

    }

    // - [END] Protected function ----------------------------------------------



    



        public function randString( $length ) {

        

		$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";	

		

		$size = strlen( $chars );

		for( $i = 0; $i < $length; $i++ ) {

			$str .= $chars[ rand( 0, $size - 1 ) ];

		}

		

		return $str;

	}

        public function hashPasswordDB($password) {       

            $hash =md5(substr(md5($password),4,25));

            return $hash;

        }

    public function hashPassword( $password ) {

        $hash = md5( $password );

        return $hash;

    }

    

    

    

    

//        public function getJoinedMember($whereCondition) {

//        $select = $this->_db->select();

//        $select->from(array('m' => 'members'),array('iId','vEmail'))

//                ->joinLeft(array('b' => 'bands'), 'b.iMember_id = m.iId',array('iBand_id'=>'iId','vTitle', 'vCity', 'iState_id'));

//        if ($whereCondition) {

//            $select->where(prepareCondition($whereCondition));

//            $results = $select->query()->fetchAll();

//        }

//        return $results;

//    }

    

        

        public function getJoinedMember($whereCondition,$start='', $limit='') {

            

        $select = $this->_db->select();

        $select->from(array('m' => 'members'),array('iId','vEmail'))

                ->joinLeft(array('b' => 'bands'), 'b.iMember_id = m.iId',array('iBand_id'=>'iId','vTitle', 'vCity', 'iState_id'));

        

//        if ($start<0) {

            $start = prepareCondition($start);

            $limit = prepareCondition($limit);

            $select->limit($limit, $start);

//        }

        if ($whereCondition) {

            $select->where(prepareCondition($whereCondition));

            $results = $select->query()->fetchAll();

        }

        return $results;

    }

 

    

}

?>