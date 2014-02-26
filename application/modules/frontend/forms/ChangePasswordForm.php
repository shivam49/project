    <?php

class Frontend_Form_ChangePasswordForm extends Zend_Form {
protected $_categories = null;
    
    public function init() {
        
        $vPassword = new Zend_Form_Element_Password('vPassword');
        $vPassword->setLabel('Password')
                ->setRequired(true)
                ->addValidator(new Zend_Validate_StringLength(array('min' => 8,'max' => 35)), true)
                ->setAttribs(array(
                    'style' => 'width:95%;',
                    'required'=> 'required;'
                    ));
        $this->addElement($vPassword);
        $vPasswordConfirm = new Zend_Form_Element_Password('vPasswordConfirm');
        $vPasswordConfirm->setLabel('confirm Password')
                        ->setRequired(true)
                        ->setAttribs(array(
                            'style' => 'width:95%;',
                            'required'=> 'required;'
                            ))
                        ->addValidator(new Zend_Validate_Confirm('vPassword'));
        $this->addElement($vPasswordConfirm);
    }

}