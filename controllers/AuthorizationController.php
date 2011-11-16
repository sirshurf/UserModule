<?php
class User_AuthorizationController extends Zend_Controller_Action
{

	public function indexAction ()
	{
		$this->view->objForm = new User_Form_AddAcl();
		
		$objAclTable = new User_Model_Db_Permissions();
		$objAclSelect = $objAclTable->select(TRUE);
		$objAclSelect->where(User_Model_Db_Permissions::COL_IS_DELETED . " = ?", FALSE);
		
		$strUrl = $this->view->url(array('module' => 'user', 'controller' => 'authorization', 'action' => 'saveacl'), null, false, false);
		
		$arrGridOptions = array('caption' => '', 'editurl' => $strUrl);
		
		$objGrid = new Ingot_JQuery_JqGrid('acl', new Ingot_JQuery_JqGrid_Adapter_DbTableSelect($objAclSelect), $arrGridOptions);
		$objGrid->setIdCol(User_Model_Db_Permissions::COL_ID_PERMISSION);
		//$objGrid->setLocalEdit();		
		

		Ingot_JQuery_JqGrid_Column_DoubleColumn::createSelectColumn($objGrid, 'Resources');
		
		$objGrid->addColumn(new Ingot_JQuery_JqGrid_Column(User_Model_Db_Permissions::COL_ACTION));
		
		Ingot_JQuery_JqGrid_Column_DoubleColumn::createSelectColumn($objGrid, 'Roles');
		
		Ingot_JQuery_JqGrid_Column_DoubleColumn::createSelectColumn($objGrid, 'Assertion', array(), FALSE);
		
		$objGridPager = $objGrid->getPager();
		
		//$objGridPager->setDefaultAdd ();		
		//		$objGridPager->setDefaultEdit ();
		$objGridPager->setDefaultDel();
		$objGrid->setDblClkEdit(TRUE);
		
		$objGrid->registerPlugin(new Ingot_JQuery_JqGrid_Plugin_CustomButton(array("caption" => "", "title" => "Edit Selected", "buttonicon" => "ui-icon-pencil", "onClickButton" => "function(){ $('#" . $this->view->objForm->getAttrib('id') . "_div').dialog('open'); }", "position" => "first")));
		
		$objGrid->registerPlugin(new Ingot_JQuery_JqGrid_Plugin_ToolbarFilter());
		$this->view->grid = $objGrid->render();
	
	}

	public function getactionAction ()
	{
		Zend_Controller_Action_HelperBroker::getStaticHelper('layout')->disableLayout();
		$intResourceId = $this->_request->getParam("ResourceId");
		$objResourceRow = "";
		$objResourceList = User_Model_Resources::getResources();
		// Find Resource
		foreach ($objResourceList as $objResourceListRow) {
			if ($intResourceId == $objResourceListRow->{User_Model_Db_Resources::COL_ID_RESOURCES}) {
				$objResourceRow = $objResourceListRow;
				break;
			}
		}
		if (empty($objResourceRow)) {
			throw new Exception("Resource Not found");
		}
		if (empty($objResourceRow->{User_Model_Db_Resources::COL_IS_VIRTUAL})) {
			// Create Controller Name
			// :TODO get default controller name
			if ('default' == $objResourceRow->{User_Model_Db_Resources::COL_MODULE}) {
				$strControllerName = ucfirst($objResourceRow->{User_Model_Db_Resources::COL_CONTROLLER}) . ucfirst(User_Model_Resources::GLOBAL_CONTROLLERNAME);
			} else {
				$strControllerName = ucfirst($objResourceRow->{User_Model_Db_Resources::COL_MODULE}) . "_" . ucfirst($objResourceRow->{User_Model_Db_Resources::COL_CONTROLLER}) . ucfirst(User_Model_Resources::GLOBAL_CONTROLLERNAME);
			}
			
			//Autoload correct file...
			$strFile = ucfirst($objResourceRow->{User_Model_Db_Resources::COL_CONTROLLER}) . ucfirst(User_Model_Resources::GLOBAL_CONTROLLERNAME) . ".php";
			
			Zend_Loader::loadFile($strFile, array(APPLICATION_PATH . DIRECTORY_SEPARATOR . User_Model_Resources::GLOBAL_DIRECTORY . DIRECTORY_SEPARATOR . $objResourceRow->{User_Model_Db_Resources::COL_MODULE} . DIRECTORY_SEPARATOR . User_Model_Resources::GLOBAL_CONTROLLER), TRUE);
			
			$arrActionsList = array();
			$arrActionsList['all'] = 'All';
			
			try {
				// Refractore it
				$objReflection = new ReflectionClass($strControllerName);
				// Get List of Action Names
				$arrMethodsList = $objReflection->getMethods(ReflectionMethod::IS_PUBLIC);
				
				foreach ($arrMethodsList as $arrMethod) {
					if (FALSE !== stripos($arrMethod->name, User_Model_Resources::GLOBAL_ACTION)) {
						$strActionName = substr($arrMethod->name, 0, stripos($arrMethod->name, User_Model_Resources::GLOBAL_ACTION));
						$arrActionsList[$strActionName] = $strActionName;
					}
				}
			} catch (Exception $objE) {}
			
			// Return Zend Select obj
			$objSelect = new Zend_Form_Element_Select(User_Model_Db_Permissions::COL_ACTION);
			$objSelect->addMultiOptions($arrActionsList);
			$objSelect->setDecorators(array('ViewHelper'));
			
			$this->view->objSelect = $objSelect;
		} else {
			
			$this->view->objSelect = $this->view->translate("LBL_VIRTUAL_RESOURCE");
		}
	}

	public function saveaclAction ()
	{
		
		$intId = (int) $this->getRequest()->getParam('id');
		
		$objDbTable = new User_Model_Db_Permissions();
		
		if (! empty($intId)) {
			$objRows = $objDbTable->find($intId);
			if (! empty($objRows)) {
				$objRow = $objDbTable->find($intId)->current();
			} else {
				$objRow = array();
			}
		} else {
			if ("add" == $this->getRequest()->getParam("oper")) {
				$objSelect = $objDbTable->select(TRUE);
				$objSelect->where(User_Model_Db_Permissions::COL_ID_RESOURCES . " = ?", $this->getRequest()
					->getParam(User_Model_Db_Permissions::COL_ID_RESOURCES));
				$objSelect->where(User_Model_Db_Permissions::COL_ID_ROLES . " = ?", $this->getRequest()
					->getParam(User_Model_Db_Permissions::COL_ID_ROLES));
				$objSelect->where(User_Model_Db_Permissions::COL_ACTION . " = ?", $this->getRequest()
					->getParam(User_Model_Db_Permissions::COL_ACTION));
				
				$objRow = $objDbTable->fetchRow($objSelect);
				if (empty($objRow)) {
					$objRow = $objDbTable->createRow();
				}
				$objRow->{User_Model_Db_Permissions::COL_IS_DELETED} = FALSE;
			} else {
				$this->view->data = array("code" => "error", "msg" => $this->view->translate("LBL_ERROR_UNAUTHORIZED"));
				return;
			}
		}
		
		if (empty($objRow)) {
			$this->view->data = array("code" => "error", "msg" => $this->view->translate("LBL_ERROR_UNAUTHORIZED"));
			return;
		}
		
		if ("del" == $this->getRequest()->getParam("oper")) {
			if ($objRow->delete()) {
				// Deleted 
				$this->view->data = array("code" => "ok", "msg" => "");
			} else {
				// Delete failed
				$this->view->data = array("code" => "error", "msg" => $this->view->translate("LBL_DEL_FAIL"));
			}
		} else {
			if ($this->getRequest()->isPost()) {
				$arrData = $this->getRequest()->getPost();
				
				if (!empty($arrData[User_Model_Db_Permissions::COL_ACTION]) && 'all' == $arrData[User_Model_Db_Permissions::COL_ACTION]){
					$arrData[User_Model_Db_Permissions::COL_ACTION] = null;
				}
				
				$objRow->setFromArray($arrData);
				
				$intId = $objRow->save();
				
				if (! empty($intId)) {
					$this->view->data = array("code" => "ok", "msg" => "");
				} else {
					$this->view->data = array("code" => "error", "msg" => $this->view->translate("LBL_UPDATE_FAIL"));
				}
			} else {
				$this->view->data = array("code" => "error", "msg" => $this->view->translate("LBL_UPDATE_FAIL"));
			
			}
		}
	
		// :TODO RELOAD ACL
	}
}