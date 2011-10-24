<?php
class User_Model_Roles
{
    
    CONST DEFAULT_ROLE_GUEST = "1";
    CONST DEFAULT_ROLE_LOGGEDIN = "2";
    CONST DEFAULT_ROLE_SYSADMIN = "3";
    
    public static $arrRoles = array(
        self::DEFAULT_ROLE_GUEST => array(
                'id_roles' => 1,
                "role" => "guest",
                "Parent" => 1,
            ),
        self::DEFAULT_ROLE_LOGGEDIN => array(
                "Name" => "LoggedIn",
                "Parent" => "Guest",
            ),
        self::DEFAULT_ROLE_SYSADMIN => array(
                "Name" => "SysAdmin",
                "Parent" => "LoggedIn",
            )
    );
    
    public static function getRoles(){
    
	    $objSessionUserData = new Zend_Session_Namespace('userData');
	    
		// Add roles to the Acl element		
	    if (!empty($objSessionUserData->roles)){
		    $roles = $objSessionUserData->roles;
	    } else {
	        try {
	            $roles = self::getRolesFromDb();
	            if (!$roles->count()){
	                $roles = self::getDefaultRoles();
	            }
	        } catch (Zend_Db_Exception $objE){
	            $roles = self::getDefaultRoles();	            
	        } catch (Exception $objE){
	            throw new Exception("", 0, $objE);
	        }
	        $objSessionUserData->roles = $roles;
	    }
        return $objSessionUserData->roles; 
    }
    
    /**
     * 
     * Enter description here ...
     * @return Zend_Db_Table_Rowset
     */
    public static function getRolesFromDb(){        
        $objRoles = new User_Model_Db_Roles();
        $objRolesSelect = $objRoles->select(TRUE);
        $objRolesSelect->where(User_Model_Db_Resources::COL_IS_DELETED . ' = ?', false);
        
        $objRolesSelect->order(User_Model_Db_Roles::COL_ID_PARENT);
        
        $objRolesRowSet = $objRoles->fetchAll($objRolesSelect);
        
        return $objRolesRowSet;
    } 
    
    public static function getDefaultRoles(){
        return self::$arrRoles;
    }
    
}

