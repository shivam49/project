    <?php

class Frontend_Form_LoginForm extends Zend_Form {
protected $_categories = null;
    
    public function init() {
        
        $vEmail = new Zend_Form_Element_Text('vEmail');
        
        $vEmail->setRequired(true)
                ->setAttrib('required', 'required')
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
                ->addValidator(new Zend_Validate_Db_RecordExists('members', 'vEmail'));
        $this->addElement($vEmail);
        
                
        $vPassword = new Zend_Form_Element_Password('vPassword');
        $vPassword->setRequired(true)
                ->setAttrib('required', 'required')
                ->setAttribs(array(
                    'style' => 'width:95%;',
                    'required'=> 'required;'
                    ));
        $this->addElement($vPassword);
        
        
                $birthday = new Zend_Form_Element_Text('dBirth_date');
//        $birthday->setLabel('Birthday')
//                ->setDefaults(array('name' => 'some value'))
//                $birthday->setValue('1999-01-01')
//                ->addFilter('StripTags')
                
//                ->setDefault('1999-01-01')
                
                $birthday->addValidator(new Zend_Validate_Date(array('format'=>'yyyy-MM-dd','Between', false, 'min' => 1900, 'max' => 9999)));
//                'validators' => array(
//                    array('Between', false, array('min' => 1900, 'max' => 9999))
//                 )
                $this->addElement($birthday);
    }

}