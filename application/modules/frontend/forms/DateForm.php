    <?php

class Frontend_Form_DateForm extends Zend_Form {
protected $_categories = null;
    
    public function init() {
        
        
        
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