<?php

class User_AuthenticationController extends Zend_Controller_Action
{

    public function selfonlyAction ()
    {
        // Empty Action, only view		
    }

    public function unauthorizedAction ()
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

    public function loginAction ()
    {
        // First check if you have Identity... If you do redirecto to home
        $auth = Zend_Auth::getInstance();
        if ($auth->hasIdentity()) {
            $this->_redirect('/');
        }
        
        $objForm = new User_Form_Login();
        if ($this->_request->isPost()) {
            if ($objForm->isValid($this->_request->getPost())) {
                $config = Zend_Registry::get('config');
                
                $username = trim($form->getValue('username'));
                $password = trim($form->getValue('password'));
                
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