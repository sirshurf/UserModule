<?php

class User_RolesController extends Zend_Controller_Action
{

    public function indexAction ()
    {
		$objRolesTable = new User_Model_Db_Roles ();
		$objRolesSelect = $objRolesTable->select (TRUE);
		
		$arrGridOptions = array('caption' => '');
		
		$objGrid = new Ingot_JQuery_JqGrid ( 'users', new Ingot_JQuery_JqGrid_Adapter_DbTableSelect ( $objRolesSelect ), $arrGridOptions );
		$objGrid->setIdCol ( User_Model_Db_Roles::COL_ID_ROLES ); 
		$objGrid->setLocalEdit();		
				
		$objGrid->addColumn ( new Ingot_JQuery_JqGrid_Column ( User_Model_Db_Roles::COL_ROLE, array('editable' => true, 'editrules'=> array('required'=> true)) ) );		
		
		Ingot_JQuery_JqGrid_Column_DoubleColumn::createSelectColumn($objGrid, 'Parent' );
		
		$objGrid->addColumn ( new Ingot_JQuery_JqGrid_Column ( User_Model_Db_Roles::COL_ORDER, array('editable' => true,'edittype'=>'text' , 'editoptions' => array('defaultValue' => '99'), 'editrules'=> array('required'=> true, 'number'=>true)) ) );
				
		$objGridPager = $objGrid->getPager ();		
		$objGridPager->setDefaultAdd ();
		$objGridPager->setDefaultEdit ();
		$objGridPager->setDefaultDel ();
		$objGrid->setDblClkEdit(TRUE);
		
		$objGrid->registerPlugin ( new Ingot_JQuery_JqGrid_Plugin_ToolbarFilter () );
		$this->view->grid = $objGrid->render ();
    }
}