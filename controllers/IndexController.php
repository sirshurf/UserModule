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


	public function indexAction() {
		
		$objUserTable = new Labadmin_Models_Users ();
		$objSelect = $objUserTable->select ();
		
		$grid = new Ingot_JQuery_JqGrid ( 'users', new Ingot_JQuery_JqGrid_Adapter_DbTableSelect ( $objSelect ) );
		$grid->setIdCol ( Labadmin_Models_Users::COL_TZ );
		
		$strUrl = $this->view->url ( array (
			'module' => 'users', 'controller' => 'users', 'action' => 'view' 
		), null, true, false );
		$grid->setOption ( 'ondblClickRow', "function(rowId, iRow, iCol, e){ if(rowId){  document.location.href ='" . $strUrl . "/tz/'+rowId } }" );
		
		$grid->addColumn ( new Ingot_JQuery_JqGrid_Column ( Labadmin_Models_Users::COL_TZ ) );		
		$grid->addColumn ( new Ingot_JQuery_JqGrid_Column ( Labadmin_Models_Users::COL_FIRST_NAME ) );
		$grid->addColumn ( new Ingot_JQuery_JqGrid_Column ( Labadmin_Models_Users::COL_LAST_NAME ) );
		$grid->addColumn ( new Ingot_JQuery_JqGrid_Column ( Labadmin_Models_Users::COL_DISPLAY_NAME ) );
		$grid->addColumn ( new Ingot_JQuery_JqGrid_Column_Decorator_Link(new Ingot_JQuery_JqGrid_Column ( Labadmin_Models_Users::COL_EMAIL ), array('link' => 'mailto:%s') ) );
		$grid->addColumn ( new Ingot_JQuery_JqGrid_Column_Decorator_Link(new Ingot_JQuery_JqGrid_Column ( Labadmin_Models_Users::COL_EMAIL2 ), array('link' => 'mailto:%s') ) );
		
		
		$grid->registerPlugin ( new Ingot_JQuery_JqGrid_Plugin_ToolbarFilter () );
		$this->view->grid = $grid->render ();
	}
	
		
	public function editAction() {
		
		$objNavigation = $this->view->navigation ()->findBy ( "controller", "users" )->setActive ();
		
		$objAcl = Openiview_Acl::$objIntance;
		
		// Get user object
		$objUserData = new Labadmin_Models_Users ();
		
		// Get Role Data
		$session_role = new Zend_Session_Namespace ( 'role' );
		$arrRoles = $session_role->role;
		
		$flipped_haystack = array_flip ( $arrRoles );
		
		// Get user data
		$objUserSessionData = new Zend_Session_Namespace ( 'user' );
		
		$nmbTzRequested = $this->_request->getParam ( "tz", $objUserSessionData->tz );
		
		// Create form
		if (($nmbTzRequested !== $objUserSessionData->tz) && ($objAcl->checkPermissions ("users", "userdata", "canedit" ))) {
			$objForm = $objUserData->getUserForm ( $nmbTzRequested, $this->view );
		} else {
			$objForm = $objUserData->getUserForm ( $objUserSessionData->tz, $this->view );
			$nmbTzRequested = $objUserSessionData->tz;
		}
		
		if ($this->_request->isPost ()) {
			$formData = $this->_request->getPost ();
			
			if ($objForm->isValid ( $formData )) {
				
				$objUserRow = $objUserData->getUserById ( $nmbTzRequested );
				
				$objFileData = new Labadmin_Models_Files ();
				$intFileSave = $objFileData->saveImage ( $formData, $objForm, Labadmin_Models_Users::COL_USER_IMG, $objUserRow->{Labadmin_Models_Users::COL_TZ} );
				
				$arrData = $objForm->getValues ();
				$arrUpdateData = $arrData;
				unset ( $arrUpdateData ['tz'] );
				
				if (empty ( $intFileSave )) {
					unset ( $arrUpdateData [Labadmin_Models_Users::COL_USER_IMG] );
				}
				
				$objUserRow->setFromArray ( $arrUpdateData );
				$objUserRow->{Labadmin_Models_Users::COL_NEED_UPDATE} = FALSE;
				
				if (! empty ( $intFileSave )) {
					$objUserRow->{Labadmin_Models_Users::COL_USER_IMG} = $intFileSave;
				}
				
				if ($objUserRow->save ()) {
					
					Labadmin_Models_Static::setJgrowlMessage ( "LBL_UPDATE_OK" );
					
					if ($nmbTzRequested == $objUserSessionData->tz) {
						$objUserData->setSession ( $arrData );
					}
					
					$strUrl = $this->view->url ( array (
						'module' => 'users', 'controller' => 'users', 'action' => 'view', "tz" => $arrData ['tz'] 
					), null, true );
					$this->_redirect ( $strUrl );
				} else {
					Labadmin_Models_Static::setJgrowlMessage ( "LBL_UPDATE_FAIL" );
				
				}
			} else {
				$objForm->populate ( $formData );
				Labadmin_Models_Static::setJgrowlMessage ( "LBL_UPDATE_FAIL" );
			
			}
		}
		// render
		$this->view->form = $objForm;
		
		$arrButtons [] = array (
			'module' => 'users', 'controller' => 'users', "action" => "edit", "onClick" => '$("#' . $objForm->getAttrib ( 'id' ) . '").submit();', "name" => 'LBL_BUTTON_USER_EDIT_SAVE'
		);
		$arrButtons [] = array (
			'module' => 'users', 'controller' => 'users', "action" => "view", "name" => 'LBL_BUTTON_USER_DETAILS', "params" => array (
			"tz" => $nmbTzRequested 
		) 
		);
		$arrButtons [] = array (
			'module' => 'users', 'controller' => 'users', "action" => "index", "name" => 'LBL_BUTTON_USER_LIST'
		);
		
		$this->view->arrActions = $arrButtons;
	
	}
	
	public function viewAction() {
		
		$objNavigation = $this->view->navigation ()->findBy ( "controller", "users" )->setActive ();
		
		$objAcl = Openiview_Acl::$objIntance;
		
		// Get user object
		$objUserData = new Labadmin_Models_Users ();
		
		// Get Role Data
		$session_role = new Zend_Session_Namespace ( 'role' );
		$arrRoles = $session_role->role;
		
		$flipped_haystack = array_flip ( $arrRoles );
		
		// Get user data
		$objUserSessionData = new Zend_Session_Namespace ( 'user' );
		
		$nmbTzRequested = $this->_request->getParam ( "tz", $objUserSessionData->tz );
		
		// Create form
		

		$nmbTzRequested = ( int ) $nmbTzRequested;
		
		//  Get User Data
		$objUserDataSelect = $objUserData->select ();
		$objUserDataSelect->where ( Labadmin_Models_Users::COL_TZ . " = ?", $nmbTzRequested );
		
		$objUserDataRow = $objUserData->fetchRow ( $objUserDataSelect );
		
		if (! empty ( $objUserDataRow )) {
			
			$this->view->objUserDataRow = $objUserDataRow;
		
		// Get the rest of the user data
		

		// Get Users IM
		

		// Get User Projects
		

		} else {
			// Redirect to HomePage with error...
			Labadmin_Models_Static::setJgrowlMessage ( "LBL_ERROR_USER_ACCESS" );
			
			$strUrl = $this->view->url ( array (
				'module' => 'prj', 'controller' => 'project', 'action' => 'awaitsprjapproval' 
			), null, true );
			$this->_redirect ( $strUrl );
		
		}
		
		// Create form
		if ((($nmbTzRequested == $objUserSessionData->tz) || ($objAcl->checkPermissions ("users", "userdata", "canedit" )))) {
			$arrButtons [] = array (
				'module' => 'users', 'controller' => 'users', "action" => "edit", "name" => "LBL_BUTTON_USER_DETAILS_EDIT", "params" => array (
				"tz" => $nmbTzRequested 
			) 
			);
		}
		
		$arrButtons [] = array (
			'module' => 'users', 'controller' => 'authentication', "action" => "takepermition", "name" => "LBL_BUTTON_USER_ASSUME_USER_PERMITION", "params" => array (
			"tz" => $nmbTzRequested 
		) 
		);
		$arrButtons [] = array (
			'module' => 'messages', 'controller' => 'messages', "action" => "send", "name" => "LBL_BUTTON_USER_SEND_MESSAGE", "params" => array (
			"tz" => $nmbTzRequested 
		) 
		);
		$arrButtons [] = array (
			'module' => 'users', 'controller' => 'index', "action" => "prj", "name" => "LBL_BUTTON_USER_PRJ_LIST", "params" => array (
			"tz" => $nmbTzRequested 
		) 
		);
		$arrButtons [] = array (
			'module' => 'users', 'controller' => 'index', "action" => "exp", "name" => "LBL_BUTTON_USER_EXP_LIST", "params" => array (
			"tz" => $nmbTzRequested 
		) 
		);
		
		$arrButtons [] = array (
			'module' => 'users', 'controller' => 'users', "action" => "index", "name" => "LBL_BUTTON_USER_LIST" 
		);
		
		$this->view->arrActions = $arrButtons;
	
	}
	
	public function inituserdetailsAction() {
		$objUserTable = new Labadmin_Models_Users ();
		$objUserTable->forceUserDetailUpdate ();
		
		Labadmin_Models_Static::setJgrowlMessage ( "LBL_UPDATE_OK" );
		
		$strUrl = $this->view->url ( array (
			'module' => 'semesters', 'controller' => 'semester', 'action' => 'index' 
		), null, true );
		$this->_redirect ( $strUrl );
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













