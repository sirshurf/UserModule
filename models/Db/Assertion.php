<?php

class User_Model_Db_Assertion extends Bf_Db_Table
{

    /**
     * The default table name 
     */
    const TBL_NAME = 'acl_assertion';

    CONST COL_ID_ASS = "id_assertion";
    
    CONST COL_ASS_NAME = "assertion_name";
    
    CONST COL_ASS_CLASS = "assertion_class";
    
    CONST COL_ASS_DESC = "assertion_desc";

    CONST COL_UPDATED_BY = 'updated_by';

    CONST COL_UPDATED_ON = 'updated_on';

    CONST COL_CREATED_BY = 'created_by';

    CONST COL_CREATED_ON = 'created_on';

    CONST COL_IS_DELETED = 'is_deleted';

}
