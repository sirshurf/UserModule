<?php

class User_Model_Assertion
{

	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $boolReload
	 * @throws Exception
	 * @return Zend_Db_Table_Rowset
	 */
	public static function getAssertion ($boolReload = false)
	{
		$objSessionUserData = new Zend_Session_Namespace('userData');
		// Add roles to the Acl element		
		if (true || empty($objSessionUserData->assertion)) {
			try {
				$objAssertion = self::getAssertionFromDb();
			} catch (Exception $objE) {
				throw new Exception("", 0, $objE);
			}
			$objSessionUserData->assertion = $objAssertion;
		}
		return $objSessionUserData->assertion;
	}

	/** 
	 * Fetch Assertion from Db
	 * 
	 * @return Zend_Db_Table_Rowset
	 */
	public static function getAssertionFromDb ()
	{
		$objAssertion = new User_Model_Db_Assertion();
		$objAssertionSelect = $objAssertion->select(TRUE);
		$objAssertionSelect->where(User_Model_Db_Assertion::COL_IS_DELETED . ' = ?', false);
		$objAssertionRowSet = $objAssertion->fetchAll($objAssertionSelect);
		
		return $objAssertionRowSet;
	}

}

