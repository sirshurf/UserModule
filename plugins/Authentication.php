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
    CONST MODULE_LOGIN = "user";
    
    CONST CONTROLLER_LOGIN  = "index";
    
    CONST ACTION_LOGIN  = "login";    
    CONST ACTION_UNAUTORISE = "unauthorized";

    public function preDispatch (Zend_Controller_Request_Abstract $request)
    {        
        $objAcl = new User_Model_Acl();
        
    	//For this example, we will use the controller as the resource:
		$resource = $request->getControllerName ();
		$privilege = $request->getActionName ();
		
		// checking permission our special way
		$boolFlag = $objAcl->checkPermissions ( $resource, $privilege );
		
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
}