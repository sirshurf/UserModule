<?php
class User_Form_AddAcl extends ZendX_JQuery_Form{
	
	public function __construct($options = null){
		parent:: __construct($options);
		
		$this->init();
	}
	
	public function init(){
		/**
		 * 
		 * Enter description here ...
		 * @var Zend_Translate
		 */
		$objTranslate = Zend_Registry::get("Zend_Translate");
		
		$this->setName('AddAcl');
		$this->setAttrib('id', 'AddAcl');
		
		$objElement = new Zend_Form_Element_Hidden('oper');
		$objElement->setValue('add');
		$this->addElement($objElement);
		
		$objElement = new Bf_Form_Element_DbSelect(User_Model_Db_Permissions::COL_ID_RESOURCES);
		$objElement->setIdentityColumn(User_Model_Db_Resources::COL_ID_RESOURCES)->setDbSelect(User_Model_Resources::getPairSelect())->setValueColumn(User_Model_Resources::CONCAT_RES_NAME)->setRequired(TRUE);
		$objElement->setAllowEmpty(FALSE)->setLabel('LBL_ACL_RESOURCES');
		$objElement->addMultiOption(0,$objTranslate->translate('LBL_MUST_SELECT_ONE') );
		$objElement->setAttrib("onchange", "getActions($(this).val());");		
		$this->addElement($objElement);
		
		
		$objElement = new Zend_Form_Element_Select(User_Model_Db_Permissions::COL_ACTION);
		$objElement->setLabel('LBL_ACL_ACTION');
		$objElement->addMultiOption(0,$objTranslate->translate('LBL_ACTION_SELECT_RESOURCE_FIRST') );		
		$this->addElement($objElement);
		
		$objElement = new Bf_Form_Element_DbSelect(User_Model_Db_Permissions::COL_ID_ROLES);
		$objElement->setIdentityColumn(User_Model_Db_Roles::COL_ID_ROLES)->setDbSelect(User_Model_Roles::getPairSelect())->setValueColumn(User_Model_Db_Roles::COL_ROLE)->setRequired(TRUE);
		$objElement->setAllowEmpty(FALSE)->setLabel('LBL_ACL_ROLES');
		$objElement->addMultiOption(0,$objTranslate->translate('LBL_ALL_ROLES') );		
		$this->addElement($objElement);
		
		$objElement = new Bf_Form_Element_DbSelect(User_Model_Db_Permissions::COL_ASSERTION);
		$objElement->setIdentityColumn(User_Model_Db_Assertion::COL_ID_ASS)->setDbSelect(User_Model_Assertion::getPairSelect())->setValueColumn(User_Model_Db_Assertion::COL_ASS_NAME)->setRequired(TRUE);
		$objElement->setAllowEmpty(FALSE)->setLabel('LBL_ACL_ASSERTION');
		$objElement->addMultiOption(0,$objTranslate->translate('LBL_NO_ASSERTION') );		
		$this->addElement($objElement);
		
		
		$objElement = new Zend_Form_Element_Select(User_Model_Db_Permissions::COL_ENV);
		$objElement->setLabel('LBL_ACL_ENV');
		$objElement->addMultiOption(0,$objTranslate->translate('LBL_ENV_NOT_READY_YET') );		
		$this->addElement($objElement);
		
	}
/*		
	id="resources" name="resources" onchange="getActions($(this).val());">
*/
}
