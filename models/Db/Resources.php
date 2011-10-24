<?php

class User_Model_Db_Resources extends Bf_Db_Table
{

    /**
     * The default table name 
     */
    const TBL_NAME = 'acl_resources';

    CONST COL_ID_RESOURCES = 'id_resources';

    CONST COL_MODULE = "module";

    CONST COL_CONTROLLER = "controller";

    CONST COL_IS_VIRTUAL = "is_virtual";

    CONST COL_UPDATED_BY = 'updated_by';

    CONST COL_UPDATED_ON = 'updated_on';

    CONST COL_CREATED_BY = 'created_by';

    CONST COL_CREATED_ON = 'created_on';

    CONST COL_IS_DELETED = 'is_deleted';
    
}
