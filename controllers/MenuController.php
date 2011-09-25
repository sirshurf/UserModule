<?php
class User_MenuController extends Zend_Controller_Action {
    
    public function showmenuAction(){
        
        $arrResourcePair = array (
			 '' => 'LBL_MENU_ALL'
		) +  Labadmin_Models_AclResources::getResourcesPair();
        
		$objMenuTable = new Labadmin_Models_Menu();
		$objMenuSelect = $objMenuTable->select (TRUE);
		
		$objMenuSelect->columns(array( 'column_translated' => Labadmin_Models_Menu::COL_LABEL));
		
		$objMenuSelectParent = clone $objMenuSelect;
		
		$objMenuSelectParent->reset ( Zend_Db_Select::COLUMNS );
		$objMenuSelectParent->columns ( array (Labadmin_Models_Menu::COL_ID_MENU, Labadmin_Models_Menu::COL_LABEL ) );
		$arrMenuParents = array (
			 '' => 'LBL_MENU_ALL', 0 => $this->view->translate("LBL_MENU_MAIN")
		) + $objMenuTable->getAdapter ()->fetchPairs ( $objMenuSelectParent );
		
		
		$strUrl = $this->view->url ( array ('module' => 'users', 'controller' => 'authentication', 'action' => 'editmenu' ), null, false, false );
		
		$arrOptions = array ("hiddengrid" => false, "editurl" => $strUrl );
		
		$grid = new Ingot_JQuery_JqGrid ( 'menu', new Ingot_JQuery_JqGrid_Adapter_DbTableSelect ( $objMenuSelect ),$arrOptions );
		$grid->setIdCol ( Labadmin_Models_Menu::COL_ID_MENU );
				
		$grid->addColumn ( new Ingot_JQuery_JqGrid_Column ( Labadmin_Models_Menu::COL_CODE, array('editable' => true) ) );
		$grid->addColumn ( new Ingot_JQuery_JqGrid_Column ( Labadmin_Models_Menu::COL_LABEL, array('editable' => true) ) );
		$grid->addColumn ( new Ingot_JQuery_JqGrid_Column_Decorator_Translate(new Ingot_JQuery_JqGrid_Column ( 'column_translated', array('editable' => FALSE) ) ) );
		$grid->addColumn ( new Ingot_JQuery_JqGrid_Column_Decorator_Search_Select(new Ingot_JQuery_JqGrid_Column ( Labadmin_Models_Menu::COL_ID_PARENT, array('editable' => true,"edittype" => "select", "editoptions" => array (
			"value" => $arrMenuParents 
		)) ), array('value' => $arrMenuParents) ) );
		$grid->addColumn ( new Ingot_JQuery_JqGrid_Column ( Labadmin_Models_Menu::COL_URI, array('editable' => true) ) );
		$grid->addColumn ( new Ingot_JQuery_JqGrid_Column ( Labadmin_Models_Menu::COL_MODULE, array('editable' => true) ) );
		$grid->addColumn ( new Ingot_JQuery_JqGrid_Column ( Labadmin_Models_Menu::COL_CONTROLLER , array('editable' => true)) );
		$grid->addColumn ( new Ingot_JQuery_JqGrid_Column ( Labadmin_Models_Menu::COL_ACTION, array('editable' => true) ) );
		$grid->addColumn ( new Ingot_JQuery_JqGrid_Column ( Labadmin_Models_Menu::COL_CSS, array('editable' => true) ) );
		
		$grid->addColumn ( new Ingot_JQuery_JqGrid_Column_Decorator_Search_Select(new Ingot_JQuery_JqGrid_Column (Labadmin_Models_Menu::COL_ID_RESOURCES, array('editable' => true,"edittype" => "select", "editoptions" => array (
			"value" => $arrResourcePair 
		)) ), array('value' => $arrResourcePair) ) );
		$grid->addColumn ( new Ingot_JQuery_JqGrid_Column ( Labadmin_Models_Menu::COL_PRIVELEGE, array('editable' => true) ) );	
		$grid->addColumn ( new Ingot_JQuery_JqGrid_Column ( Labadmin_Models_Menu::COL_ORDER, array('editable' => true) ) );	
				
		$objPlugin = $grid->getPager ();
		$objPlugin->setDefaultAdd ();
		$objPlugin->setDefaultEdit ();
		$objPlugin->setDefaultDel ();
		$grid->setDblClkEdit();
		
		$grid->registerPlugin ( new Ingot_JQuery_JqGrid_Plugin_ToolbarFilter () );
		$this->view->grid = $grid->render ();
    }
    
    
    public function editmenuAction(){
                
        $strOper = $this->_request->getParam ( 'oper' );
		
		$objMenuTable = new Labadmin_Models_Menu();
		$objRow = null;
		$arrStatus  =array();
		switch ($strOper) {
			case "edit" :
				$intId = ( int ) $this->_request->getParam ( 'id' );
				$objSelect = $objMenuTable->select (TRUE);
				$objSelect->where ( Labadmin_Models_Menu::COL_ID_MENU." = ?", $intId );
				$objRow = $objMenuTable->fetchRow ( $objSelect );
			case "add" :
				if (empty ( $objRow )) {
					$objRow = $objMenuTable->createRow ();
				}
				$arrParams = $this->_request->getParams ();
				$objRow->setFromArray($arrParams);
				if( $objRow->save ()){
				    $arrStatus = array('code' => 'ok', 'msg' => '');
				} else {
				    $arrStatus = array('code' => 'error', 'msg' => $this->view->translate('LBL_UPDATE_FAIL') );
				}
				
				break;
			case "del" :				
				$intId = ( int ) $this->_request->getParam ( 'id' );
				$objSelect = $objMenuTable->select (TRUE);
				$objSelect->where ( Labadmin_Models_Menu::COL_ID_MENU." = ?", $intId );
				$objRow = $objMenuTable->fetchRow ( $objSelect );
				if (! empty ( $objRow )) {
					if ($objRow->delete ()){					    
				        $arrStatus = array('code' => 'ok', 'msg' => '');
					} else {
				    $arrStatus = array('code' => 'error', 'msg' => $this->view->translate('LBL_DEL_FAIL') );
					}
				}
				break;
		
		}
		
		Labadmin_Models_Menu::getMenu(TRUE);
		
		$this->view->arrStatus = $arrStatus;
        
    }
    
}