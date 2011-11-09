<?php

class User_Model_Db_Users extends Bf_Db_Table
{

    /**
     * The default table name 
     */
    const TBL_NAME = 'users';

    CONST COL_ID_USERS = 'id_users';

    CONST COL_LOGIN = "username";

    CONST COL_PWD = "hashed_password";

    CONST COL_FIRST_NAME = "firstname";

    CONST COL_LAST_NAME = "lastname";
    
    CONST COL_EXTRA_DATA = "extra";

    CONST COL_EMAIL = "email";
    
    CONST COL_ID_ROLE = "id_roles";

    CONST COL_IS_ACTIVE = "is_active";

    CONST COL_UPDATED_BY = 'updated_by';

    CONST COL_UPDATED_ON = 'updated_on';

    CONST COL_CREATED_BY = 'created_by';

    CONST COL_CREATED_ON = 'created_on';

    CONST COL_IS_DELETED = 'is_deleted';

    protected $_referenceMap = array(
    'Roles' => array('columns' => array(self::COL_ID_ROLE), 'refTableClass' => 'User_Model_Db_Roles', 'refColumns' => array(User_Model_Db_Roles::COL_ID_ROLES), 'displayColumn' => User_Model_Db_Roles::COL_ROLE));
    
}
