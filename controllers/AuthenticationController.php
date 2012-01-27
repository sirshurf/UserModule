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
        $this->view->strMsgLogout = $this->view->translate('LBL_TEXT_LOGOUT');
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
                $username = trim($objForm->getValue(User_Model_Db_Users::COL_LOGIN));
                $password = trim($objForm->getValue('password'));
                // Remove it from Array... 
                $result = User_Model_User::makeLogin($username, $password);
                if ($result->isValid()) {
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
                    $objForm->setDecorators(array('FormElements', 'FormErrors', 'Form'));
                }
            }
        }
        $this->view->objForm = $objForm;
        $arrButtons[] = array('module' => 'user', 'controller' => 'authentication', "action" => "login", "onClick" => '$("#' . $objForm->getAttrib('id') . '").submit();', 
        "name" => 'LBL_BUTTON_USER_LOGIN');
        $arrButtons[] = array('module' => 'user', 'controller' => 'index', "action" => "forgot-password", "name" => 'LBL_BUTTON_USER_FORGOT_PASSWORD');
        $this->view->arrActions = $arrButtons;
    }

} 