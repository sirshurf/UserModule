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
    CONST MODULE = "user";
    CONST CONTROLLER = "index";
    CONST ACTION_LOGIN = "login";
    private $_acl = null;
    public function __construct ()
    {}
    public function preDispatch (Zend_Controller_Request_Abstract $request)
    {
        if (! Zend_Auth::getInstance()->hasIdentity()) {
            $module = $request->getModuleName();
            $resource = $request->getControllerName();
            $action = $request->getActionName();
            $params = $request->getUserParams();
            $arrOptions = $db = Zend_Controller_Front::getInstance()->getParam(
            "bootstrap")->getOptions();
            if (! empty($arrOptions['excludeAuthentication'])) {
                $arrExcludeAuthentication = $arrOptions['excludeAuthentication'];
            } else {
                $arrExcludeAuthentication = array();
            }
            if (! in_array($resource, $arrExcludeAuthentication)) {
                // 'contact' !== $resource && 'soap' !== $resource) {
                if (($module !== self::MODULE) &&
                 ($resource !== self::CONTROLLER)) {
                    $redirect = array("module" => $module, 
                    "resorce" => $resource, "action" => $action, 
                    "params" => $params);
                    $session = new Zend_Session_Namespace("uri");
                    $session->url = $redirect;
                }
                $request->setModuleName(self::MODULE)
                    ->setControllerName(self::CONTROLLER)
                    ->setActionName(self::ACTION_LOGIN);
            }
        }
    }
}