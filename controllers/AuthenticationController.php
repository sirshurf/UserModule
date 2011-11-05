<?php
class User_AuthenticationController extends Zend_Controller_Action {
    public function selfonlyAction () {
        // Empty Action, only view		
    }
    public function unauthorizedAction () {
        // Empty Action, only view	
    }
    /**
     * Standart logout, clears all ident data
     */
    public function logoutAction () {
        Zend_Auth::getInstance()->clearIdentity();
        Zend_Session::destroy(TRUE);
    }
    public function loginAction () {
        // First check if you have Identity... If you do redirecto to home
        $auth = Zend_Auth::getInstance();
        if ($auth->hasIdentity()) {
            $this->_redirect('/');
        }
        $objForm = new User_Form_Login();
        if ($this->_request->isPost()) {
            if ($objForm->isValid($this->_request->getPost())) {
                $username = trim($objForm->getValue(User_Model_Db_Users::COL_LOGIN));
                $password = trim($objForm->getValue('password'));
                // ...or configure the instance with setter methods
                $authAdapter = new Zend_Auth_Adapter_DbTable();
                $authAdapter->setTableName(User_Model_Db_Users::TBL_NAME)
                    ->setIdentityColumn(User_Model_Db_Users::COL_LOGIN)
                    ->setCredentialColumn(User_Model_Db_Users::COL_PWD)
                    ->setCredentialTreatment('md5(?)')
                    ->setIdentity($username)
                    ->setCredential($password);
                // Remove it from Array... 
                $result = $auth->authenticate($authAdapter);
                if ($result->isValid()) {
                    $session = new Zend_Session_Namespace("user");
                    $session->userDetails = $authAdapter->getResultRowObject();
                    $session = new Zend_Session_Namespace("uri");
                    if (! empty($session->url['params'])) {
                        $strUrl = $this->view->url($session->url['params']);
                        $this->_redirect($strUrl);
                    } else {
                        $this->_redirect("/");
                    }
                } else {
                    $arrMessages = $result->getMessages();
                    $objForm->addError($arrMessages[0]);
                }
            }
        }
        $this->view->objForm = $objForm;
    }
}