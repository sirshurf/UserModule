<?php

class User_Model_Db_Permissions extends Bf_Db_Table
{
	
	/**
	 * The default table name 
	 */
	const TBL_NAME = 'acl_permission';
	
	CONST COL_ID_PERMISSION = 'id_permission';
	CONST COL_ID_RESOURCES = 'id_resources';
	CONST COL_ID_ROLES = 'id_roles';
	CONST COL_ACTION = 'action';
	CONST COL_ASSERTION = 'assertion';
	CONST COL_ENV = 'env';
	
	protected $_referenceMap = array();

	public function __construct ($config = array(), $definition = null, $boolLoadReference = true)
	{
		if ($boolLoadReference) {
			$this->_referenceMap = array(
				'Roles' => array(
					'columns' => array(self::COL_ID_ROLES), 
					'refTableClass' => 'User_Model_Db_Roles', 
					'refColumns' => array(User_Model_Db_Roles::COL_ID_ROLES), 
					'displayColumn' => User_Model_Db_Roles::COL_ROLE), 
				'Assertion' => array(
					'columns' => array(self::COL_ASSERTION), 
					'refTableClass' => 'User_Model_Db_Assertion', 
					'refColumns' => array(User_Model_Db_Assertion::COL_ID_ASS), 
					'displayColumn' => User_Model_Db_Assertion::COL_ASS_NAME), 
			
				'Resources' => array(
					'columns' => array(self::COL_ID_RESOURCES), 
					'refTableClass' => 'User_Model_Db_Resources', 
					'refColumns' => array(User_Model_Db_Resources::COL_ID_RESOURCES), 
					'displayColumn' => new Zend_Db_Expr("CONCAT_WS('/'," . User_Model_Db_Resources::COL_MODULE . "," . User_Model_Db_Resources::COL_CONTROLLER . ")"))
			
			);
		}
		parent::__construct($config, $definition);
	
	}

}
