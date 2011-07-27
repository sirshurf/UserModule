<?php

class User_IndexController extends Zend_Controller_Action
{

    public function init ()
    {
        /* Initialize action controller here */
    }

    public function indexAction ()
    {

    }

    public function forgotPasswordAction ()
    {
        // action body
    }

    public function updatePasswordAction ()
    {
        // action body
    }

    public function updateProfileAction ()
    {
        // action body
    }

    public function loginAction ()
    {
        $config = $this->getInvokeArg('bootstrap')->getOptions();
        // action body		
        $auth = Zend_Auth::getInstance();
        if ($auth->hasIdentity()) {
            $this->_redirect('/');
        }
        $objForm = new User_Form_Login();
        if ($this->_request->isPost()) {
            if ($form->isValid($this->_request->getPost())) {
                $username = trim($form->getValue('username'));
                $password = trim($form->getValue('password'));
                
                $adapter = new Zend_Auth_Adapter_DbTable($options, $username, 
                $password);
                $adapter->setIdentity($username);
                $adapter->setCredential($password);
                $result = $auth->authenticate($adapter);
                if ($log_enable) {
                    $messages = $result->getMessages();
                    $logger = new Zend_Log();
                    $columnMapping = array('lvl' => 'priority', 
                    'request' => 'message', 'responce' => 'responce', 
                    'created_on' => 'timestamp');
                    $objDb = $this->getInvokeArg('bootstrap')
                        ->getResource('multidb')
                        ->getDb('db1');
                    $writer = new Zend_Log_Writer_Db($objDb, 'ldap_logs', 
                    $columnMapping);
                    $logger->addWriter($writer);
                    $filter = new Zend_Log_Filter_Priority(Zend_Log::DEBUG);
                    $logger->addFilter($filter);
                    //					foreach ( $messages as $i => $message ) {
                    for ($i = 0; $i < count($messages); $i += 2) {
                        $request = $messages[$i];
                        $responce = $messages[$i + 1];
                        if ($i > 1) { // $messages[2] and up are log messages					
                            $request = str_replace(
                            "\n", "\n  ", $request);
                            $responce = str_replace("\n", "\n  ", $responce);
                            try {
                                $logger->log($request, Zend_Log::DEBUG, 
                                array('responce' => $responce));
                            } catch (Exception $e) {
                                $this->view->errorMessage = $e->getMessage();
                                return;
                            }
                        }
                    }
                }
                if ($result->isValid()) {
                    $boolUpdateUser = Labadmin_Models_Users::getUser($adapter);
                    // Get user roles
                    Labadmin_Models_Users::getRole($adapter);
                    if ($boolUpdateUser) {
                        $this->_forward("update", "user");
                    } else {
                        $session = new Zend_Session_Namespace("uri");
                        if (! empty($session->url['params'])) {
                            $strUrl = $this->view->url($session->url['params']);
                            $this->_redirect($strUrl);
                        } else {
                            $this->_redirect("/");
                        }
                    }
                } else {
                    $arrMessages = $result->getMessages();
                    $this->view->errorMessage = $arrMessages[0];
                }
            }
        }
        $this->view->objForm = $objForm;
    }

    public function logoutAction ()
    {
        // action body
    }

    public function accessDeniedAction ()
    {
        // action body
    }

    public function initAction ()
    {
        
        $intRequestInit = $this->_request->getParam("initstart", 0);
        
        if (! empty($intRequestInit)) {
            // Start table initialization
            

            $arrClassActions = array();
            $handle = opendir(realpath(dirname(__FILE__) . self::DIRECTORY));
            if ($handle) {
                while (false !== ($file = readdir($handle))) {
                    if ($file != "." && $file != ".." && ! is_dir($file)) {
                        // Now include file
                        include_once realpath(
                        dirname(__FILE__) . self::DIRECTORY) . '/' . $file;
                        $info = pathinfo($file);
                        $file_name = basename($file, '.' . $info['extension']);
                        $intControllerStringPosition = strpos($file_name, 
                        self::CONTROLLER);
                        if (! empty($intControllerStringPosition)) {
                            $strControllerName = substr($file_name, 0, 
                            $intControllerStringPosition);
                            $strControllerName = self::lcfirst(
                            $strControllerName);
                            // Read with Reflection
                            $className = self::CLASS_PREFIX .
                             $file_name;
                            
                            $resourceName = $objReflection = new ReflectionClass(
                            $className);
                            $arrClassMethods = $objReflection->getMethods();
                            // Get Actions Names
                            foreach ($arrClassMethods as $objMethod) {
                                $intStringPosition = strpos($objMethod->name, 
                                self::ACTION);
                                if (! empty($intStringPosition)) {
                                    $strActionName = substr($objMethod->name, 0, 
                                    $intStringPosition);
                                    $arrClassActions[$strControllerName][$strActionName][] = User_Model_Roles::DEFAULT_ROLE_GUEST;
                                }
                            }
                        }
                    }
                }
                closedir($handle);
            }
            
//            Labadmin_Models_SystemMessages::initTable();
//            Labadmin_Models_ProjectStatuses::initTable();
//            Labadmin_Models_ProjectsRequestsStatuses::initTable();
//            Labadmin_Models_FilesPermission::initTable();
//            Labadmin_Models_SystemSettings::initTable();
//            Labadmin_Models_SystemNotification::initTable();
//            
//            Labadmin_Models_Static::setJgrowlMessage("LBL_ADMIN_INIT_DONE");
        
        }
        
        $arrButtons = array();
        $arrButtons[] = array("module" => "prj", "controller" => "init", 
        "action" => "index", 
        "onClick" => 'document.location.href="' . $this->view->url(
        array('module' => 'prj', "controller" => "init", "action" => "index", 
        "initstart" => "1")) . '";', "name" => "LBL_BUTTON_ADMIN_INIT_TABLE");
        
        $this->view->arrActions = $arrButtons;
    }
}













