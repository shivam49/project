    <?php

class Frontend_Form_ForgetPasswordForm extends Zend_Form {
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
    }

}