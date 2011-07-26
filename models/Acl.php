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
            
            if (! empty($arrRole['Parent'])) {
                $this->addRole(new Zend_Acl_Role($arrRole['Name']), 
                $arrRole['Parent']);
            } else {
                $this->addRole(new Zend_Acl_Role($arrRole['Name']));
            }
        }
        
        // Add roles to the Acl element
        $this->deny();
        
        $arrResource = User_Model_Resources::getResources();
        
        foreach ($arrResource as $strResourceName => $arrResourceOptions) {
            // Register new resource
            $this->add(new Zend_Acl_Resource($strResourceName));
            
            // check if it's needed, we have a Deny All 7 rows up?
            $this->deny(null, $strResourceName, null);
            
            foreach ($arrResourceOptions as $strOptions => $arrRoles) {
                
                $this->allow((('all' == $arrRoles[0]) ? null : $arrRoles), 
                $strResourceName, (('all' == $strOptions) ? null : $strOptions));
            
            }
        
        }
                
        self::$objIntance = $this;
    }

    /**
     * Checking that this user has role that can enter here
     * 
     * @param $resource
     * @param $privilege
     * @return bool
     */
    public function checkPermissions ($resource = null, $privilege = null )
    {
        if (Zend_Auth::getInstance()->hasIdentity()) {
            $session_role = new Zend_Session_Namespace('userDetails');
            if (! empty($session_role->role)) {
                $role = $session_role->role;
            }
        } else {
            $role = User_Model_Roles::DEFAULT_ROLE_GUEST;
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
            if ($this->isAllowed($role, $resource, $privilege)) {
                // The user Role is allowed, not check if he has SUB permissions
                return TRUE;
            }
        }
        
        return false;
    }
}