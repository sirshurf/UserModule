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

}
