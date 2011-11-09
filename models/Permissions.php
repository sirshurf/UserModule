<?php

class User_Model_Permissions
{

	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $boolReload
	 * @throws Exception
	 * @return Zend_Db_Table_Rowset
	 */
	public static function getPermission ($boolReload = false)
	{
		$objSessionUserData = new Zend_Session_Namespace('userData');
		// Add roles to the Acl element		
		if (true || empty($objSessionUserData->permissions)) {
			try {
				$objPermissions = self::getPermissionsFromDb();
			} catch (Exception $objE) {
				throw new Exception("", 0, $objE);
			}
			$objSessionUserData->permissions = $objPermissions;
		}
		return $objSessionUserData->permissions;
	}

	/** 
	 * Fetch Permissions from Db
	 * 
	 * @return Zend_Db_Table_Rowset
	 */
	public static function getPermissionsFromDb ()
	{
		$objPermission = new User_Model_Db_Permissions();
		$objPermissionSelect = $objPermission->select(TRUE);
		$objPermissionSelect->where(User_Model_Db_Permissions::COL_IS_DELETED . ' = ?', false);
		$objResourcesRowSet = $objPermission->fetchAll($objPermissionSelect);
		
		return $objResourcesRowSet;
	}

}

