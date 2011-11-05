<?php
class User_Form_UpdatePassword extends Zend_Form {
    public function init () {
        
        $this->setName('updatePassword');
        $this->setAttrib('id', 'updatePassword');
        
        /* Form Elements & Other Definitions Here ... */
        $objElement = new Zend_Form_Element_Password('password');
        $objElement->setLabel('LBL_PASSWORD')
            ->setRequired('true')
            ->addFilter(new Zend_Filter_StringTrim())
            ->addValidator(new Zend_Validate_NotEmpty());
        $this->addElement($objElement);
        $objElement = new Zend_Form_Element_Password('confirm_password');
        $objElement->setLabel('LBL_RETYPE_PASSWORD')
            ->setRequired('true')
            ->addFilter(new Zend_Filter_StringTrim())
            ->addValidator(new Zend_Validate_Identical('password'));
        $this->addElement($objElement);                
    }
}

