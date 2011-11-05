<?php

class User_Form_Login extends ZendX_JQuery_Form
{

    public function init()
    {
        
        $this->setName('login');
        $this->setAttrib('id', 'login');
        
        /* Form Elements & Other Definitions Here ... */
        
        $objOptions = new Zend_Config_Xml(
        dirname(__FILE__) . '/../configs/forms/forms.xml');
        
        $this->setConfig($objOptions->login);

    }


}

