<?php

class User_Form_ForgotPassword extends Zend_Form
{

    public function init()
    {
        
        $this->setName('updatePassword');
        $this->setAttrib('id', 'updatePassword');
        
        /* Form Elements & Other Definitions Here ... */
        
        $objElement = new Zend_Form_Element_Text(User_Model_Db_Users::COL_LOGIN);
        $objElement->setLabel('LBL_USERNAME')
            ->setRequired(TRUE)
            ->addFilter(new Zend_Filter_StringTrim())
            ->addValidator(new Zend_Validate_NotEmpty())
            ->addValidator(new Bf_Validate_Db_DoubleRecordExists(array('token'=>User_Model_Db_Users::COL_EMAIL,'table' => User_Model_Db_Users::TBL_NAME, 'field' => User_Model_Db_Users::COL_LOGIN)));
        $this->addElement($objElement);
        $objElement = new Zend_Form_Element_Text(User_Model_Db_Users::COL_EMAIL);
        $objElement->setLabel('LBL_EMAIL')
            ->setRequired(TRUE)
            ->addFilter(new Zend_Filter_StringTrim())
            ->addValidator(new Zend_Validate_EmailAddress())
            ->addValidator(new Zend_Validate_NotEmpty());
        $this->addElement($objElement);
    }


}

