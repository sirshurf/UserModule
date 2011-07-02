<?php

class User_Form_Login extends ZendX_JQuery_Form
{

    public function init()
    {
        /* Form Elements & Other Definitions Here ... */
        
        $objOptions = new Zend_Config_Xml(
        dirname(__FILE__) . '/../configs/forms/forms.xml');
        
        $this->setOptions($objOptions->login);
        
        /*
		$this->setName('LogIn');
		$this->setMethod('post');
		
		$username = new Zend_Form_Element_Text('username');
		$username->setLabel('User Name:')
				 ->setRequired();
		$this->addElement($username);
				 
		$password = new Zend_Form_Element_Password('password');
		$password->setLabel('Password:')
			     ->setRequired();
		$this->addElement($password);
			     
		$login = new Zend_Form_Element_Submit('Login');
		$login->setLabel('Login');
		$this->addElement($login);
		  
		        */
    }


}

