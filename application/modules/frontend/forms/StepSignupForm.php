    <?php

class Frontend_Form_StepSignupForm extends Zend_Form {
protected $_categories = null;
    
    public function init() {
        
        $state = new Zend_Form_Element_Text('iState_id');
        
        $state->setRequired(true)
                ->setAttrib('required', 'required')
                ->setAttribs(array(
                    'required'=> 'required;'
                    ));
        $this->addElement($state);
        
        $city = new Zend_Form_Element_Text('vCity');
        
        $city->setRequired(true)
                ->setAttrib('required', 'required')
                ->setAttribs(array(
                    'required'=> 'required;'
                    ));
        $this->addElement($city);
        $market = new Zend_Form_Element_Text('iMarket_id');
        
        $market->setRequired(true)
                ->setAttrib('required', 'required')
                ->setAttribs(array(
                    'required'=> 'required;'
                    ));
        $this->addElement($market);
        $genres = new Zend_Form_Element_Text('vGenre_ids');
        
        $genres->setRequired(true)
                ->setAttrib('required', 'required')
                ->setAttribs(array(
                    'required'=> 'required;'
                    ));
        $this->addElement($genres);
        
    }

}