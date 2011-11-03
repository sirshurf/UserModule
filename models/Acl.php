<?php

/**
 * Standart ACL class, resieves parameters from INI file, acl node
 * 
 * @author sirshurf
 *
 */
class User_Model_Acl extends Zend_Acl
{
    
    /**
     * 
     * @var User_Model_Acl
     */
    public static $objIntance;

    public function __construct ()
    {
        
        $roles = User_Model_Roles::getRoles();
        
        foreach ($roles as $key => $arrRole) {
            
            if (! empty($arrRole[User_Model_Db_Roles::COL_ID_PARENT]) && ($arrRole[User_Model_Db_Roles::COL_ID_PARENT] !== $arrRole[User_Model_Db_Roles::COL_ID_ROLES])) {
                $this->addRole(new Zend_Acl_Role($arrRole[User_Model_Db_Roles::COL_ID_ROLES]), $arrRole[User_Model_Db_Roles::COL_ID_PARENT]);
            } else {
                $this->addRole(new Zend_Acl_Role($arrRole[User_Model_Db_Roles::COL_ID_ROLES]));
            }
        }
        
        $arrResources = User_Model_Resources::getResources(); // Create Resources...
        foreach ($arrResources as $intIdResource => $objResourceRow) {
            // Register new resource
            if (! $this->has($objResourceRow->{User_Model_Db_Resources::COL_ID_RESOURCES})) {
                $this->addResource(new Zend_Acl_Resource($objResourceRow->{User_Model_Db_Resources::COL_ID_RESOURCES}));
            }
        }
        
        // Add roles to the Acl element
        //$this->deny();
        $this->allow();
        
       // $this->allow(((0 == $objPermissionRow->{Labadmin_Models_AclPermission::COL_ID_ROLES}) ? null : $objPermissionRow->{Labadmin_Models_AclPermission::COL_ID_ROLES}), 
     //   $objPermissionRow->{Labadmin_Models_AclPermission::COL_ID_RESOURCES}, 
   //     (('all' == $objPermissionRow->{Labadmin_Models_AclPermission::COL_ACTION}) ? null : $objPermissionRow->{Labadmin_Models_AclPermission::COL_ACTION}));
        
        self::$objIntance = $this;
    }
    
    public function checkPermissionsById ($resource = null, $privilege = null, $intLabId = null, $intProjectId = null, $boolStricked = false)
    {
        if (Zend_Auth::getInstance()->hasIdentity()) {
            $session_role = new Zend_Session_Namespace('userDetails');
            if (! empty($session_role->role)) {
                $role = $session_role->role;
            }
        } else {
            $role = User_Model_Roles::DEFAULT_ROLE_GUEST;
            $role = $role[User_Model_Db_Roles::COL_ID_ROLES];
        }
        
        /**
         * Removes php ending for reports usage...
         */
        if ('php' === substr($privilege, - 3)) {
            $privilege = substr($privilege, 0, - 4);
            $privilege = str_ireplace("_", "-", $privilege);
        }
        
        if ($resource || $privilege) {
            // determine using helper role and page resource/privilege				
//            if ($this->isAllowed($role, $resource, $privilege)) {
                // The user Role is allowed, not check if he has SUB permissions
                return TRUE;
//            }
        }
        
        return false;
    }

    /**
     * Checking that this user has role that can enter here
     * 
     * @param $resource
     * @param $privilege
     * @return bool
     */
    public function checkPermissions ($module = null, $controller = null, $privilege = null, $intLabId = null, $intProjectId = null, $boolStricked = false)
    {
        /**
         * Removes php ending for reports usage...
         */
        if ('php' === substr($privilege, - 3)) {
            $privilege = substr($privilege, 0, - 4);
            $privilege = str_ireplace("_", "-", $privilege);
        }
        // Get Correct resource ID
        //
        $resource = User_Model_Resources::getResourceId($module, $controller);
        if ($resource || $privilege) {
            return $this->checkPermissionsById($resource, $privilege, $intLabId, $intProjectId, $boolStricked);
        }
        return false;
    }
}