<?php
class User_Model_Db_Roles extends SirShurf_Db_Table {
    	
    /**
	 * The default table name 
	 */
	const TBL_NAME = 'acl_roles';
	
	CONST COL_ID_ROLES = 'id_roles';
	CONST COL_ROLE = "role";
	CONST COL_ID_PARENT = "id_parent";
	CONST COL_ROLE_TITLE = "title";
	CONST COL_IS_ACTIVE = "is_active";
	CONST COL_ORDER = "order";
	
	CONST COL_UPDATED_BY = 'updated_by';
	CONST COL_UPDATED_ON = 'updated_on';
	CONST COL_CREATED_BY = 'created_by';
	CONST COL_CREATED_ON = 'created_on';
	CONST COL_IS_DELETED = 'is_deleted';
	
}
