    <?php

class Frontend_Form_SignUpBandsForm extends Zend_Form {
protected $_categories = null;
    
    public function init() {
        $this->setMethod('post');
        $this->setAction('bands');
        $vTitle = new Zend_Form_Element_Text('vTitle');
        $vTitle->setLabel('Title Band')
                ->setRequired(true)
                ->setAttribs(array(
                    'style' => 'width:95%;',
                    'required'=> 'required;'
                    ));
//                ->addValidator(new Zend_Validate_Regex('/^[\w.-]*$/'))
//                ->addValidator(new Zend_Validate_StringLength(array('min' => 5,'max' => 255)), true);
        
        $vEmail = new Zend_Form_Element_Text('vEmail');
        
        $vEmail->setRequired(true)
                ->setAttribs(array(
                    'style' => 'width:95%;',
                    'required'=> 'required;'
                    ))
                ->setLabel('Email')
                ->addFilters(array(
                    new Zend_Filter_StringTrim(),
                    new Zend_Filter_StringToLower()
                ))
                ->addValidator(new Zend_Validate_EmailAddress())
                ->addValidator(new Zend_Validate_Db_NoRecordExists('members', 'vEmail'));
        
        $vEmailConfirm = new Zend_Form_Element_Text('vEmailConfirm');
        
        $vEmailConfirm->setRequired(true)
//                ->setAttrib('required', 'required')
                ->setAttribs(array(
                    'style' => 'width:95%;',
                    'required'=> 'required;'
                    ))
                ->setLabel('Repeat Email')
                ->addFilter('StripTags')
                ->addValidator(new Zend_Validate_EmailAddress())
                 ->addValidator(new Zend_Validate_Identical());
        
        $vPassword = new Zend_Form_Element_Password('vPassword');
        $vPassword->setLabel('Password')
                ->setRequired(true)
                ->addValidator(new Zend_Validate_StringLength(array('min' => 8,'max' => 35)), true)
                ->setAttribs(array(
                    'style' => 'width:95%;',
                    'required'=> 'required;'
                    ));
        
        $vPasswordConfirm = new Zend_Form_Element_Password('vPasswordConfirm');
        $vPasswordConfirm->setLabel('confirm Password')
                        ->setRequired(true)
                        ->setAttribs(array(
                            'style' => 'width:95%;',
                            'required'=> 'required;'
                            ))
                        ->addValidator(new Zend_Validate_Confirm('vPassword'));
        
//        $birthday = new Zend_Form_Element_Text('birthday');
//        $birthday->setLabel('Birthday')
//                ->setValue('1999-01-01')
//                ->addFilter('StripTags')
//                ->setRequired(true)
//                        ->setAttrib('required', 'required')
//                ->addValidator(new Zend_Validate_Date(array('format'=>'yyyy-MM-dd')));
        
        
        
//        $currency = new Zend_Form_Element_Select('currency', array(
//                "label" => "Currency",
////                "required" => true,
//             ));
//        $currency->addMultiOptions($this->_categories);
//        
//        $ref = new Zend_Form_Element_Checkbox('vRef');
//        $ref-> setLabel('I agree to the conditions below:');
        
        
//         $currency->addMultiOptions(array(
//                 "US Dollar" => 1,
//                 "Pound Sterling" => 2,
//             ));

//         $form->addElements(array($currency));

//         $form->populate(array("currency" => "US Dollar"));
        
        
//        $message = new Zend_Form_Element_Textarea('vMessage');
//        $message->setLabel('Desc')
//                ->setAttribs(array('COLS'=>'15','ROWS'=>'5'));
//  
        
//        $recaptcha = new Zend_Service_ReCaptcha("6Lek5t8SAAAAAGa3frY1bE4teTIvzLSLLMDNHLuZ","6Lek5t8SAAAAAIQm5tIU45jrcWv2-Wgj2U9xAfk-");
//        // then set the Recaptcha adapter 
//        $adapter = new Zend_Captcha_ReCaptcha(); 
//        $adapter->setService( $recaptcha ); 
//
//        // then set  the captcha element to use the ReCaptcha Adapter 
//        $captcha = new Zend_Form_Element_Captcha('recaptcha', array( 
//                        'label' => "Are you a human?", 
//                        'captcha' => $adapter 
//        )); 
        
        
        
        
        
        $submit=new Zend_Form_Element_Submit('save_changes');
        $submit->setLabel('Register');
        $submit->setAttrib('class', 'btn btn-danger');
//        ->setAttrib('disabled', 'disabled');
        
        
        

        $this->addElements(array($vTitle, $vEmail,$vEmailConfirm, $vPassword,$vPasswordConfirm,$submit));
        
        
    }

}