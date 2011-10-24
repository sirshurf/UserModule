<?php

class User_IndexController extends Zend_Controller_Action
{

    public function forgotPasswordAction ()
    {
        // action body
    }

    public function updatePasswordAction ()
    {
        // action body
    }

    public function indexAction ()
    {
        
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

    public function editAction ()
    {
        
        // Get user object
        $objUserData = new User_Model_Db_Users();
        
        $intUserID = $this->_request->getParam("UserId");
        
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
        
        // Create form
        $objForm = new User_Form_UserDetails();
        $objForm->populate($objUserRow->toArray());
        

        if ($this->_request->isPost()) {
            $formData = $this->_request->getPost();
            
            if ($objForm->isValid($formData)) {
                
                $objUserRow->setFromArray($objForm->getValues());
                
                $intUserID = $objUserRow->save();
                
                if ($intUserID) {
                    
                    //Labadmin_Models_Static::setJgrowlMessage("LBL_UPDATE_OK");
                    
                    //if ($nmbTzRequested == $objUserSessionData->tz) {
                    //    $objUserData->setSession($arrData);
                    //}
                    
                    $strUrl = $this->view->url(array('module' => 'user', 'controller' => 'index', 'action' => 'view', "UserId" => $intUserID), null, true);
                    $this->_redirect($strUrl);
                } else {
//                    Labadmin_Models_Static::setJgrowlMessage("LBL_UPDATE_FAIL");
                
                }
            } else {
                $objForm->populate($formData);
//                Labadmin_Models_Static::setJgrowlMessage("LBL_UPDATE_FAIL");
            
            }
        }
        // render
        $this->view->form = $objForm;
        
        $arrButtons[] = array('module' => 'user', 'controller' => 'index', "action" => "edit", "onClick" => '$("#' . $objForm->getAttrib('id') . '").submit();', "name" => 'LBL_BUTTON_USER_EDIT_SAVE');
        
        if (! empty($intUserID)) {
            $arrButtons[] = array('module' => 'user', 'controller' => 'users', "action" => "view", "name" => 'LBL_BUTTON_USER_DETAILS', "params" => array("UserId" => $intUserID));
        }
        $arrButtons[] = array('module' => 'user', 'controller' => 'index', "action" => "index", "name" => 'LBL_BUTTON_USER_LIST');
        
        $this->view->arrActions = $arrButtons;
    
    }

    public function viewAction ()
    {
        
        // Get user object
        $objUserData = new User_Model_Db_Users();
        
        $intUserId = $this->_request->getParam("UserId");
                
        //  Get User Data
        $objUserDataSelect = $objUserData->select();
        $objUserDataSelect->where(User_Model_Db_Users::COL_ID_USERS . " = ?", $intUserId);
        
        $objUserDataRow = $objUserData->fetchRow($objUserDataSelect);
        
        if (! empty($objUserDataRow)) {
            
            $this->view->objUserDataRow = $objUserDataRow;
        
     // Get the rest of the user data
        

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
        
        $arrButtons[] = array('module' => 'user', 'controller' => 'authentication', "action" => "takepermition", "name" => "LBL_BUTTON_USER_ASSUME_USER_PERMITION", 
        "params" => array("UserId" => $intUserId));
        
        $arrButtons[] = array('module' => 'user', 'controller' => 'index', "action" => "index", "name" => "LBL_BUTTON_USER_LIST");
        
        $this->view->arrActions = $arrButtons;
    
    }

    public function inituserdetailsAction ()
    {
        $objUserTable = new Labadmin_Models_Users();
        $objUserTable->forceUserDetailUpdate();
        
        Labadmin_Models_Static::setJgrowlMessage("LBL_UPDATE_OK");
        
        $strUrl = $this->view->url(array('module' => 'semesters', 'controller' => 'semester', 'action' => 'index'), null, true);
        $this->_redirect($strUrl);
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
        $arrButtons[] = array("module" => "prj", "controller" => "init", "action" => "index", 
        "onClick" => 'document.location.href="' . $this->view->url(array('module' => 'prj', "controller" => "init", "action" => "index", "initstart" => "1")) . '";', 
        "name" => "LBL_BUTTON_ADMIN_INIT_TABLE");
        
        $this->view->arrActions = $arrButtons;
    }
}













