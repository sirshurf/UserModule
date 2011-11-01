<?php
/**
 * Class that checks, if a user HAS Identity, if he has none, forward him to the authentication.
 * 
 * Made in the default MVC structure
 * 
 * @author sirshurf
 *
 */
class User_Plugin_Authentication extends Zend_Controller_Plugin_Abstract
{
    
    /**
     * 
     * ACL Object
     * @var User_Model_Acl
     */
    private $_objAcl;
    
    CONST MODULE_LOGIN = "user";
    
    CONST CONTROLLER_LOGIN  = "authentication";
    
    CONST ACTION_LOGIN  = "login";    
    CONST ACTION_UNAUTORISE = "unauthorized";

    public function preDispatch (Zend_Controller_Request_Abstract $request)
    {     
        $this->setAcl(new User_Model_Acl());
        
		$module = $request->getModuleName ();
		$resource = $request->getControllerName ();
		$privilege = $request->getActionName ();
		
		// checking permission our special way
		$boolFlag = $this->getAcl()->checkPermissions ($module, $resource, $privilege );
		
		if (empty ( $boolFlag )) {		    
            if (! Zend_Auth::getInstance()->hasIdentity()) {
                                $request->setModuleName(self::MODULE_LOGIN)
                    ->setControllerName(self::CONTROLLER_LOGIN)
                    ->setActionName(self::ACTION_LOGIN);
            } else {	    
    			// If the user has no access we send him elsewhere by changing the request
			    $request->setModuleName(self::MODULE_LOGIN)->setControllerName ( self::CONTROLLER_LOGIN )->setActionName ( self::ACTION_UNAUTORISE );
            }
		}		
		
		return;		
    }
    
    /**
     * 
     * Enter description here ...
     * @param User_Model_Acl $objAcl
     * @return User_Plugin_Authentication
     */
    public function setAcl(User_Model_Acl $objAcl){
        $this->_objAcl = $objAcl;
        return $this;
    }
    
    /**
     * 
     * Enter description here ...
     * @return User_Model_Acl
     */
    public function getAcl(){
        return $this->_objAcl;
    }
}