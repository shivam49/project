<?php

class Frontend_Model_EmailNotifier {
    public function user_add($userId, $userPass){
        $publicVariable = $this->publicVariable($userId);
        $publicVariable['passUser'] = $userPass;
        $this->notifier_email_sender( $publicVariable , 'user_add' );
        
        
    }

    
    
    
    public function forget_password_user($userId , $NewPassword, $newPasswordHash, $security){
        $publicVariable = $this->publicVariable($userId);
        $publicVariable['passwordBase'] = $NewPassword;
        $publicVariable['password'] = $newPasswordHash;
        $publicVariable['security'] = $security;
        $this->notifier_email_sender( $publicVariable, 'forget_password' );
    }
    
    public function send_new_password($userId, $userPass){
        $publicVariable = $this->publicVariable($userId);
        $publicVariable['passUser'] = $userPass;
        $this->notifier_email_sender( $publicVariable , 'send_new_password' );
        
        
    }

    public function notifier_email_sender( $publicVariable, $type ){
        $Config_mail = $this->config_email($publicVariable, $type);
        
        if($publicVariable['eType']== 'Fan'){
            $name = $publicVariable['vName'].' '.$publicVariable['vLastname'];
        }
        if($publicVariable['eType']=='Band'){
            $name = $publicVariable['vTitle'];
        }
         $template = new App_Model_Template("application/modules/frontend/views/templates/mail");
        $content = $template->renderTemplate($Config_mail['temp_name'], array(
            'user_list' => $publicVariable,
                ));
        $mail = new App_Model_Mail();
        $config = array(
            'header' => array(
                'X-MailGenerator' => 'APZ-Mail-Engine',
                'me' => array('Receiver', true), // for multi header items
                'you' => array('Receiver', true) // for multi header items
            ),
            'from' => array(
                '8bitinc' => $Config_mail['from']
            ),
            'to' => array(
                $name => $Config_mail['to']
            ),
            'cc' => array(
                'sb-else' => $Config_mail['cc']
            ),
            'bcc' => array(
                'another-one' => $Config_mail['bcc'] // key are optional
            ),
            'subject' => $Config_mail['subject'],
            'body' => $content, // Text or HTML
        );
        $mail->prepare($config);
        $mail->send();
//        fb($content);
    }
    
    public function config_email($publicVariable , $type){
        $array_config['from'] = 'info@8bitinc.com';
        $array_config['to'] = $publicVariable['vEmail'];
        $array_config['cc'] = '';
        $array_config['bcc'] = '';
        if($type == 'user_add'){
            $array_config['temp_name'] = 'add_user.php';
            if($publicVariable['eType'] == 'Band'){
                $array_config['subject'] = 'Bands Register';
            }
            if($publicVariable['eType'] == 'Fan'){
                $array_config['subject'] = 'Fans Register';
            }
        }
        if($type == 'forget_password'){
            $array_config['temp_name'] = 'forget_password.php';
            if($publicVariable['eType'] == 'Band'){
                $array_config['subject'] = 'Bands New Password';
            }
            if($publicVariable['eType'] == 'Fan'){
                $array_config['subject'] = 'Fans New Password';
            }
        }
        
        if($type == 'send_new_password'){
            $array_config['temp_name'] = 'send_new_password.php';
            if($publicVariable['eType'] == 'Band'){
                $array_config['subject'] = 'Your new password for Bands';
                
            }
            if($publicVariable['eType'] == 'Fan'){
                $array_config['subject'] = 'Your new password for Fans';
            }
        }
       
        
        return $array_config;
        
    }
    
    
    
        public function publicVariable($userId){
        $whereCondition = array("iId='?'",array($userId));
        $membersObj = new Frontend_Model_Members();
        $publicArray = array();
        $membersList = $membersObj->getList($whereCondition);
        $whereConditionUser = array("iMember_id='?'", array($membersList[0]['iId']));
        if($membersList[0]['eType'] == 'Fan'){
            $fanObj = new Frontend_Model_Fans();
            $userList = $fanObj->getList($whereConditionUser);
            
        }
        if($membersList[0]['eType'] == 'Band'){
            $bandObj = new Frontend_Model_Bands();
            $userList = $bandObj->getList($whereConditionUser);
        }
        unset($userList[0]['iId']);
        $publicArray = array_merge($membersList[0], $userList[0]) ;
        return $publicArray;
    }
}
?>
