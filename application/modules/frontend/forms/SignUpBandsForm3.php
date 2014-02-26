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
        
        $vPassword = new Zend_Form_Element_Password('vPassword');
        $vPassword->setLabel('Password')
                ->setRequired(true)
                ->addValidator(new Zend_Validate_StringLength(array('min' => 8,'max' => 35)), true)
                ->setAttribs(array(
                    'style' => 'width:95%;',
                    'required'=> 'required;'
                    ));
        
        $submit=new Zend_Form_Element_Submit('save_changes');
        $submit->setLabel('Next Step');
        $submit->setAttrib('class', 'btn btn-primary');

        $this->addElements(array($vTitle, $vEmail, $vPassword,$submit));
        
        
    }

}