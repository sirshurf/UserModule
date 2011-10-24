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

    CONST COL_EMAIL = "email";

    CONST COL_IS_ACTIVE = "is_active";

    CONST COL_UPDATED_BY = 'updated_by';

    CONST COL_UPDATED_ON = 'updated_on';

    CONST COL_CREATED_BY = 'created_by';

    CONST COL_CREATED_ON = 'created_on';

    CONST COL_IS_DELETED = 'is_deleted';

}
