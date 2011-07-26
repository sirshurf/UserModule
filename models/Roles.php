<?php
class User_Model_Roles
{
    
    CONST DEFAULT_ROLE_GUEST = "Guest";
    CONST DEFAULT_ROLE_LOGGEDIN = "LoggedIn";
    CONST DEFAULT_ROLE_SYSADMIN = "SysAdmin";
    
    public static $arrRoles = array(
        self::DEFAULT_ROLE_GUEST => array(
                "Name" => "Guest",
                "Label" => "Guest",
                "Parent" => "",
            ),
        self::DEFAULT_ROLE_LOGGEDIN => array(
                "Name" => "LoggedIn",
                "Label" => "LoggedIn",
                "Parent" => "Guest",
            ),
        self::DEFAULT_ROLE_SYSADMIN => array(
                "Name" => "SysAdmin",
                "Label" => "SysAdmin",
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
	        } catch (Zend_Db_Exception $objE){
	            $roles = self::getDefaultRoles();	            
	        } catch (Exception $objE){
	            throw new Exception("", 0, $objE);
	        }
	        $objSessionUserData->roles = $roles;
	    }
        return $objSessionUserData->roles; 
    }
    
    public static function getRolesFromDb(){
        throw new Zend_Db_Exception();
    } 
    
    public static function getDefaultRoles(){
        return self::$arrRoles;
    }
    
}

