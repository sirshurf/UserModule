<?php
class User_IndexController extends Zend_Controller_Action {
    public function forgotPasswordAction () {
        $objForm = new User_Form_ForgotPassword();
        if ($this->_request->isPost()) {
            $formData = $this->_request->getPost();
            if ($objForm->isValid($formData)) { //  Get User Data    
                $objUserModel = new     User_Model_User ();
                if ($objUserModel->createNewPasswordForUserLogin($formData[User_Model_Db_Users::COL_LOGIN])) {
                    Labels_Model_SystemLabels::setJgrowlMessage("LBL_PASSWORD_RETREAVAL_OK");
                    $strUrl = $this->view->url(array('module' => 'index', 'controller' => 'index', 'action' => 'index'), null, true);
                    $this->_redirect($strUrl);
                } else {
                    Labels_Model_SystemLabels::setJgrowlMessage("LBL_PASSWORD_GENERATION_FAIL");
                }
            } else {
                Labels_Model_SystemLabels::setJgrowlMessage("LBL_NO_MATCH_FOUND");
            }
        }
        // render
        $this->view->objForm = $objForm;
        $arrButtons[] = array('module' => 'user', 'controller' => 'index', "action" => "edit", "onClick" => '$("#' . $objForm->getAttrib('id') . '").submit();', "name" => 'LBL_BUTTON_USER_PASSWORD_NEW');
        $this->view->arrActions = $arrButtons;
    }
    public function indexAction () {
        $objUserTable = new User_Model_Db_Users();
        $objSelect = $objUserTable->select();
        $grid = new Ingot_JQuery_JqGrid('users', new Ingot_JQuery_JqGrid_Adapter_DbTableSelect($objSelect));
        $grid->setIdCol(User_Model_Db_Users::COL_ID_USERS);
        $strUrl = $this->view->url(array('module' => 'user', 'controller' => 'index', 'action' => 'view'), null, true, false);
        $grid->setOption('ondblClickRow', "function(rowId, iRow, iCol, e){ if(rowId){  document.location.href ='" . $strUrl . "/UserId/'+rowId } }");
        $grid->addColumn(new Ingot_JQuery_JqGrid_Column(User_Model_Db_Users::COL_LOGIN));
        $grid->addColumn(new Ingot_JQuery_JqGrid_Column(User_Model_Db_Users::COL_FIRST_NAME));
        $grid->addColumn(new Ingot_JQuery_JqGrid_Column(User_Model_Db_Users::COL_LAST_NAME));
        $grid->addColumn(new Ingot_JQuery_JqGrid_Column_Decorator_Link(new Ingot_JQuery_JqGrid_Column(User_Model_Db_Users::COL_EMAIL), array('link' => 'mailto:%s')));
        $grid->registerPlugin(new Ingot_JQuery_JqGrid_Plugin_ToolbarFilter());
        $this->view->grid = $grid->render();
        $arrActions = array();
        $arrActions[] = array('module' => 'user', 'controller' => 'index', "action" => "edit", "name" => 'Add New User');
        $this->view->arrActions = $arrActions;
    }
    public function editAction () {
        // Get user object
        $objUserData = new User_Model_Db_Users();
        $intUserID = (int) $this->_request->getParam("UserId", 0);
        if (! empty($intUserID)) {
            $objUserRowSet = $objUserData->find($intUserID);
            if (! empty($objUserRowSet)) {
                $objUserRow = $objUserRowSet->current();
            } else {
                // @TODO redirect to error....
                $strUrl = $this->view->url(array('module' => 'users', 'controller' => 'index', 'action' => 'index'), null, true);
                $this->_redirect($strUrl);
                return;
            }
        } else {
            $objUserRow = $objUserData->createRow();
        }
        // Get Extra Data...
        $arrOptions = $this->getInvokeArg('bootstrap')
            ->getOptions();
        $arrExtraData = $arrOptions['extraData']['user']['userDetails'];
        // Create form
        $objForm = new User_Form_UserDetails();
        $objForm->populate($objUserRow->toArray());
        foreach ((array) $arrExtraData as $strExtraDataClass) {
            $objExtraData = new $strExtraDataClass();
            $objExtraData->setMainRow($objUserRow);
            if ($objExtraData instanceof User_Model_User_Extra_Iterface) {
                $objForm->populate($objExtraData->getData());
            }
        }
        if ($this->_request->isPost()) {
            $formData = $this->_request->getPost();
            if ($objForm->isValid($formData)) {
                $objUserRow->setFromArray($objForm->getValues());
                $intUserID = $objUserRow->save();
                if ($intUserID) {
                    // Save Extra Data
                    foreach ((array) $arrExtraData as $strExtraDataClass) {
                        $objExtraData = new $strExtraDataClass();
                        $objExtraData->setMainRow($objUserRow);
                        if ($objExtraData instanceof User_Model_User_Extra_Interface) {
                            $objSubForm = $objForm->getSubForm('subformUserDetails');
                            if (! empty($objSubForm)) {
                                $objForm->save($objSubForm->getValues());
                            }
                        }
                    }
                    $intOriginalId = $this->_request->getParam("UserId", 0);
                    if (empty($intOriginalId)) {
                        $objUserModel = new User_Model_User();
                        if ($objUserModel->createNewPasswordForUser($intUserID)) {
                            Labels_Model_SystemLabels::setJgrowlMessage("LBL_UPDATE_OK");
                            $strUrl = $this->view->url(array('module' => 'user', 'controller' => 'index', 'action' => 'view', "UserId" => $intUserID), null, true);
                            $this->_redirect($strUrl);
                        } else {
                            Labels_Model_SystemLabels::setJgrowlMessage("LBL_PASSWORD_GENERATION_FAIL");
                        }
echo $this->Actions ();
                    } else {
                        Labels_Model_SystemLabels::setJgrowlMessage("LBL_UPDATE_OK");
                        $strUrl = $this->view->url(array('module' => 'user', 'controller' => 'index', 'action' => 'view', "UserId" => $intUserID), null, true);
                        $this->_redirect($strUrl);
                    }
                } else {
                    Labels_Model_SystemLabels::setJgrowlMessage("LBL_UPDATE_FAIL");
                }
            } else {
                $objForm->populate($formData);
                Labels_Model_SystemLabels::setJgrowlMessage("LBL_UPDATE_FAIL");
            }
        }
        // render
        $this->view->form = $objForm;
        $arrButtons[] = array('module' => 'user', 'controller' => 'index', "action" => "edit", "onClick" => '$("#' . $objForm->getAttrib('id') . '").submit();', "name" => 'LBL_BUTTON_USER_EDIT_SAVE');
        if (! empty($intUserID)) {
            $arrButtons[] = array('module' => 'user', 'controller' => 'index', "action" => "view", "name" => 'LBL_BUTTON_USER_DETAILS', "params" => array("UserId" => $intUserID));
        }
        $arrButtons[] = array('module' => 'user', 'controller' => 'index', "action" => "index", "name" => 'LBL_BUTTON_USER_LIST');
        $this->view->arrActions = $arrButtons;
    }
    public function viewAction () {
        // Get user object
        $objUserData = new User_Model_Db_Users();
        $intUserId = (int) $this->_request->getParam("UserId", 0);
        //  Get User Data
        $objUserDataSelect = $objUserData->select();
        $objUserDataSelect->where(User_Model_Db_Users::COL_ID_USERS . " = ?", $intUserId);
        $objUserDataRow = $objUserData->fetchRow($objUserDataSelect);
        if (! empty($objUserDataRow)) {
            $this->view->objUserDataRow = $objUserDataRow;
             // Get the rest of the 
echo $this->Actions ();user data
        // Get Users IM
        // Get User Projects
        } else {
            // Redirect to HomePage with error...
            //            Labadmin_Models_Static::setJgrowlMessage("LBL_ERROR_USER_ACCESS");
            $strUrl = $this->view->url(array('module' => 'user', 'controller' => 'index', 'action' => 'index'), null, true);
            $this->_redirect($strUrl);
        }
        // Create form
        $arrButtons[] = array('module' => 'user', 'controller' => 'index', "action" => "edit", "name" => "LBL_BUTTON_USER_DETAILS_EDIT", "params" => array("UserId" => $intUserId));
        $arrButtons[] = array('module' => 'user', 'controller' => 'index', "action" => "update-password", "name" => "LBL_BUTTON_USER_CHANGE_PWD", "params" => array("UserId" => $intUserId));
        $arrButtons[] = array('module' => 'user', 'controller' => 'authentication', "action" => "takepermition", "name" => "LBL_BUTTON_USER_ASSUME_USER_PERMITION", "params" => array("UserId" => $intUserId));
        $arrButtons[] = array('module' => 'user', 'controller' => 'index', "action" => "index", "name" => "LBL_BUTTON_USER_LIST");
        $this->view->arrActions = $arrButtons;
    }
    public function inituserdetailsAction () {
        $objUserTable = new User_Model_User();
        $objUserTable->forceUserDetailUpdate();
        Bf_Static::setJgrowlMessage("LBL_UPDATE_OK");
        $strUrl = $this->view->url(array('module' => 'semesters', 'controller' => 'semester', 'action' => 'index'), null, true);
        $this->_redirect($strUrl);
    }
    public function initAction () {
        $intRequestInit = $this->_request->getParam("initstart", 0);
        if (! empty($intRequestInit)) {
            // Start table initialization
            $arrClassActions = array();
            $handle = opendir(realpath(dirname(__FILE__) . self::DIRECTORY));
            if ($handle) {
                while (false !== ($file = readdir($handle))) {
                    if ($file != "." && $file != ".." && ! is_dir($file)) {
                        // Now include file
                        include_once realpath(dirname(__FILE__) . self::DIRECTORY) . '/' . $file;
                        $info = pathinfo($file);
                        $file_name = basename($file, '.' . $info['extension']);
                        $intControllerStringPosition = strpos($file_name, self::CONTROLLER);
                        if (! empty($intControllerStringPosition)) {
                            $strControllerName = substr($file_name, 0, $intControllerStringPosition);
                            $strControllerName = self::lcfirst($strControllerName);
                            // Read with Reflection
                            $className = self::CLASS_PREFIX . $file_name;
                            $resourceName = $objReflection = new ReflectionClass($className);
                            $arrClassMethods = $objReflection->getMethods();
                            // Get Actions Names
                            foreach ($arrClassMethods as $objMethod) {
                                $intStringPosition = strpos($objMethod->name, self::ACTION);
                                if (! empty($intStringPosition)) {
                                    $strActionName = substr($objMethod->name, 0, $intStringPosition);
                                    $arrClassActions[$strControllerName][$strActionName][] = User_Model_Roles::DEFAULT_ROLE_GUEST;
                                }
                            }
                        }
                    }
                }
                closedir($handle);
            }
        }
        $arrButtons = array();
        $arrButtons[] = array("module" => "prj", "controller" => "init", "action" => "index", "onClick" => 'document.location.href="' . $this->view->url(array('module' => 'prj', "controller" => "init", "action" => "index", "initstart" => "1")) . '";', "name" => "LBL_BUTTON_ADMIN_INIT_TABLE");
        $this->view->arrActions = $arrButtons;
    }
    public function updatePasswordAction () {
        $intUserId = (int) $this->_request->getParam("UserId", 0);
        if (empty($intUserId)) {
            // Send to index... cannt login.
            Labels_Model_SystemLabels::setJgrowlMessage("LBL_INCORRECT_DATA");
        }
        $objForm = new User_Form_UpdatePassword();
        //  Get User Data        
        $objUserData = new User_Model_Db_Users();
        $objUserDataSelect = $objUserData->select();
        $objUserDataSelect->where(User_Model_Db_Users::COL_ID_USERS . " = ?", $intUserId);
        $objUserDataRow = $objUserData->fetchRow($objUserDataSelect);
        if (empty($objUserDataRow)) 
echo $this->Actions ();{
            // Send to index... cannt login.
            Labels_Model_SystemLabels::setJgrowlMessage("LBL_INCORRECT_DATA");
        }
        if ($this->_request->isPost()) {
            $formData = $this->_request->getPost();
            if ($objForm->isValid($formData)) {
                $objUserDataRow->{User_Model_Db_Users::COL_PWD} = md5($formData['password']);
                $intUserID = $objUserDataRow->save();
                if ($intUserID) {
                    Labels_Model_SystemLabels::setJgrowlMessage("LBL_UPDATE_OK");
                    $strUrl = $this->view->url(array('module' => 'user', 'controller' => 'index', 'action' => 'view', "UserId" => $intUserID), null, true);
                    $this->_redirect($strUrl);
                } else {
                    Labels_Model_SystemLabels::setJgrowlMessage("LBL_UPDATE_FAIL");
                }
            } else {
                Labels_Model_SystemLabels::setJgrowlMessage("LBL_UPDATE_FAIL");
            }
        }
        // render
        $this->view->objForm = $objForm;
        $arrButtons[] = array('module' => 'user', 'controller' => 'index', "action" => "edit", "onClick" => '$("#' . $objForm->getAttrib('id') . '").submit();', "name" => 'LBL_BUTTON_USER_PASSWORD_SAVE');
        $arrButtons[] = array('module' => 'user', 'controller' => 'index', "action" => "view", "name" => 'LBL_BUTTON_USER_DETAILS', "params" => array("UserId" => $intUserId));
        $arrButtons[] = array('module' => 'user', 'controller' => 'index', "action" => "index", "name" => 'LBL_BUTTON_USER_LIST');
        $this->view->arrActions = $arrButtons;
    }
}













