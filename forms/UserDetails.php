<?php
class User_Form_UserDetails extends ZendX_JQuery_Form
{

	public function init () {
		$this->addPrefixPath('Bf_Form_Element_', 'Bf/Form/Element/', Zend_Form::ELEMENT);
		
		/* Form Elements & Other Definitions Here ... */
		$objOptions = new Zend_Config_Xml(dirname(__FILE__) . '/../configs/forms/forms.xml');
		$this->setConfig($objOptions->profile);
		// Check if global file exists....
		// If exists, add it as sub form...
		if (is_readable(APPLICATION_PATH . '/configs/forms/user.xml')) {
			$objSubOptions = new Zend_Config_Xml(APPLICATION_PATH . '/configs/forms/user.xml');
		
		// $objSubForm = new ZendX_JQuery_Form($objSubOptions->profile);
		//   $this->addSubForm($objSubForm, 'subformUserDetails');
		}
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
		
		// Check permissions...
		

		$objUserSessionData = new Zend_Session_Namespace('user');
		$objUserDetails = $objUserSessionData->userDetails;
		
		if (! empty($objUserDetails->{User_Model_Db_Users::COL_ID_ROLE})) {
			switch ($objUserDetails->{User_Model_Db_Users::COL_ID_ROLE}) {
				case 4: // Group Member
				case 5: // Group Manager
				case 6: // Site Manager
					// They can only see the current level, not change it.
					$this->getElement(User_Model_Db_Users::COL_ID_ROLE)->setAttrib('disabled', 'disabled');
					break;
				default:
					break;
			
			}
		}	
	}
	
	public function validateElements(){	
		if ('disabled' == $this->getElement(User_Model_Db_Users::COL_ID_ROLE)->getAttrib('disabled')){
			$this->removeElement($this->getElement(User_Model_Db_Users::COL_ID_ROLE)->getName());
		}
	}
}

