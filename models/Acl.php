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
	
	protected $_arrAssertions;

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
		
		// Create Assertion Objects
		$arrAssertions = User_Model_Assertion::getAssertion();
		foreach ($arrAssertions as $objAssertionRow){
			
			$strAssertionClassName = $objAssertionRow->{User_Model_Db_Assertion::COL_ASS_CLASS};			
			$objAssertion = new $strAssertionClassName();
			$this->_arrAssertions[$objAssertionRow->{User_Model_Db_Assertion::COL_ID_ASS}] = $objAssertion;
		}
		
		// Add roles to the Acl element
		$arrPermissions = User_Model_Permissions::getPermission();
		
		if ($arrPermissions->count() > 0) {
			// Permission are set, iterate and add them
			$this->deny();
			
			foreach ($arrPermissions as $objPermissionRow) {				
				$intIdRole = ((0 == $objPermissionRow->{User_Model_Db_Permissions::COL_ID_ROLES}) ? null : $objPermissionRow->{User_Model_Db_Permissions::COL_ID_ROLES});
				$intIdResource = $objPermissionRow->{User_Model_Db_Permissions::COL_ID_RESOURCES};
				$strAction = (('all' == $objPermissionRow->{User_Model_Db_Permissions::COL_ACTION}) ? null : $objPermissionRow->{User_Model_Db_Permissions::COL_ACTION});
				$objAssertion = ((empty($objPermissionRow->{User_Model_Db_Permissions::COL_ASSERTION})) ? null : $this->_arrAssertions[$objPermissionRow->{User_Model_Db_Permissions::COL_ASSERTION}]);  
				$this->allow($intIdRole, $intIdResource,$strAction, $objAssertion);
			}
		} else {
			// No Permission are set, create White List
			$this->allow();
		}
		self::$objIntance = $this;
	}

	public function checkPermissionsById ($resource = null, $privilege = null, $intLabId = null, $intProjectId = null, $boolStricked = false)
	{
		if (Zend_Auth::getInstance()->hasIdentity()) {
			$session = new Zend_Session_Namespace("user");
			if (! empty($session->userDetails) && ! empty($session->userDetails->{User_Model_Db_Roles::COL_ID_ROLES})) {
				$role = $session->userDetails->{User_Model_Db_Roles::COL_ID_ROLES};
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
			if ($this->isAllowed($role, $resource, $privilege)) {
				// The user Role is allowed, not check if he has SUB permissions
				return TRUE;
			}
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