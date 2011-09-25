<?php
class User_AuthorizationController extends Zend_Controller_Action {
    
     public function takepermitionAction ()
    {
        $nmbTzRequested = $this->_request->getParam("tz");
        // Get User Data, if missing redirect to index
        $objUsers = new Labadmin_Models_Users();
        $objUsersRow = $objUsers->find($nmbTzRequested)->current();
        if (empty($objUsersRow)) {
            Labadmin_Models_Static::setJgrowlMessage("LBL_ERROR_UNAUTHORIZED");
            $strUrl = $this->view->url(array('module' => 'prj', 'controller' => 'project', 'action' => 'index', 'unautherised'), false, true);
            $this->_redirect($strUrl);
            return;
        }
        // find out of this user is student from LDAP
        $arrSearch = array();
        $arrSearch['equals']['employeeid'] = str_pad($nmbTzRequested, 9, "0", STR_PAD_LEFT);
        ;
        $arrLdapData = Labadmin_Models_Users::searchStudentLdap($arrSearch);
        if (! empty($arrLdapData)) {
            // If Student call the role action with the peremeter...
            $this->_changeRoles($objUsersRow, array(), true);
            $strUrl = $this->view->url(array('module' => 'default', 'controller' => 'index', 'action' => 'index'), false, true);
            $this->_redirect($strUrl);
            return;
        } else {
            $arrLdapData = Labadmin_Models_Users::searchLdap($arrSearch);
            if (! empty($arrLdapData)) {
                $this->_changeRoles($objUsersRow, $arrLdapData[0]['memberof'], FALSE);
                $strUrl = $this->view->url(array('module' => 'default', 'controller' => 'index', 'action' => 'index'), false, true);
                $this->_redirect($strUrl);
                return;
            }
        }
        $arrButtons = array();
        $arrButtons[] = array('module' => 'users', 'controller' => 'users', "action" => "view", "name" => "LBL_BUTTON_USER_DETAILS", "params" => array("tz" => $nmbTzRequested));
        $this->view->arrActions = $arrButtons;
    }
    private function _changeRoles ($objNewUserRow, $arrOptions, $boolIsStudent)
    {
        // Save new User in On Behalf 
        $session_on_behalf = new Zend_Session_Namespace('onBehalf');
        $session_on_behalf->tz = $objNewUserRow->{Labadmin_Models_Users::COL_TZ};
        $session_on_behalf->first_name = $objNewUserRow->{Labadmin_Models_Users::COL_FIRST_NAME};
        $session_on_behalf->last_name = $objNewUserRow->{Labadmin_Models_Users::COL_LAST_NAME};
        if (! empty($objNewUserRow->{Labadmin_Models_Users::COL_DISPLAY_NAME})) {
            $session_on_behalf->display_name = $objNewUserRow->{Labadmin_Models_Users::COL_DISPLAY_NAME};
        } else {
            $strDisplayName = $objNewUserRow->{Labadmin_Models_Users::COL_FIRST_NAME} . " " . $objNewUserRow->{Labadmin_Models_Users::COL_LAST_NAME};
            $session_on_behalf->display_name = $strDisplayName;
        }
        // Make a copy of User Roles
        $session_members = new Zend_Session_Namespace('memberof');
        $session_members_bkp = new Zend_Session_Namespace('memberofBkp');
        $session_members_bkp->member_of = $session_members->member_of;
        $objStd = new stdClass();
        $objStd->memberof = $arrOptions;
        $session_members->member_of = $objStd;
        Zend_Session::namespaceUnset('role');
        Labadmin_Models_Users::getRole(null, $boolIsStudent);
    }

    public function switchbackAction ()
    {
        $session_members = new Zend_Session_Namespace('memberof');
        $session_members_bkp = new Zend_Session_Namespace('memberofBkp');
        $session_members->member_of = $session_members_bkp->member_of;
        Zend_Session::namespaceUnset('bkp_role');
        Zend_Session::namespaceUnset('onBehalf');
        Labadmin_Models_Users::getRole(null);
        $strUrl = $this->view->url(array('module' => 'default', 'controller' => 'index', 'action' => 'index'), false, true);
        $this->_redirect($strUrl);
        return;
    }

    public function unauthorizedAction ()
    {
        // Empty Action, only view	
    }

    public function selfonlyAction ()
    {
        // Empty Action, only view		
    }
    
    /**
     * Standart logout, clears all ident data
     */
    public function logoutAction ()
    {
        Zend_Auth::getInstance()->clearIdentity();
        Zend_Session::destroy(TRUE);
    }
    

    public function localAction ()
    {
        $auth = Zend_Auth::getInstance();
        if ($auth->hasIdentity()) {
            $this->_redirect('/');
        }
        $request = $this->getRequest();
        $form = new Openiview_Form_LoginForm();
        if ($this->_request->isPost()) {
            if ($form->isValid($this->_request->getPost())) {
                $config = Zend_Registry::get('config');
                $username = trim($form->getValue('username'));
                $password = trim($form->getValue('password'));
                $log_enable = $config->ldap->log_enable;
                $options = $config->ldap->toArray();
                // Remove it from Array... 
                unset($options['log_enable']);
                $adapter = new Labadmin_Auth_Adapter_Ldap($options, $username, $password);
                $result = $auth->authenticate($adapter);
                if ($log_enable) {
                    $messages = $result->getMessages();
                    $logger = new Zend_Log();
                    $columnMapping = array('lvl' => 'priority', 'request' => 'message', 'responce' => 'responce', 'created_on' => 'timestamp');
                    $objDb = $this->getInvokeArg('bootstrap')
                        ->getResource('multidb')
                        ->getDb('db1');
                    $writer = new Zend_Log_Writer_Db($objDb, 'ldap_logs', $columnMapping);
                    $logger->addWriter($writer);
                    $filter = new Zend_Log_Filter_Priority(Zend_Log::DEBUG);
                    $logger->addFilter($filter);
                    //					foreach ( $messages as $i => $message ) {
                    for ($i = 0; $i < count($messages); $i += 2) {
                        $request = $messages[$i];
                        $responce = $messages[$i + 1];
                        if ($i > 1) { // $messages[2] and up are log messages					
                            $request = str_replace("\n", "\n  ", $request);
                            $responce = str_replace("\n", "\n  ", $responce);
                            try {
                                $logger->log($request, Zend_Log::DEBUG, array('responce' => $responce));
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
                        //						$this->_forward ( "update", "user" );
                        $strUrl = $this->view->url(array('module' => 'users', 'controller' => 'users', 'action' => 'edit'), null, true);
                        $this->_redirect($strUrl);
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
                    $form->addError($arrMessages[0]);
                }
            }
        }
        $this->view->form = $form;
    }
    
}