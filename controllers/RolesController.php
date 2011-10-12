<?php

class User_RolesController extends Zend_Controller_Action
{


    public function indexAction ()
    {
		$objUserTable = new User_Model_Db_Roles ();
		$objSelect = $objUserTable->select (TRUE);
		
		$objGrid = new Ingot_JQuery_JqGrid ( 'users', new Ingot_JQuery_JqGrid_Adapter_DbTableSelect ( $objSelect ) );
		$objGrid->setIdCol ( User_Model_Db_Roles::COL_ID_ROLES ); 
		$objGrid->setLocalEdit();		
				
		$objGrid->addColumn ( new Ingot_JQuery_JqGrid_Column ( User_Model_Db_Roles::COL_ROLE, array('editable' => true, 'editrules'=> array('required'=> true)) ) );		
		$objGrid->addColumn ( new Ingot_JQuery_JqGrid_Column ( User_Model_Db_Roles::COL_ROLE_TITLE, array('editable' => true, 'editrules'=> array('required'=> true)) ) );
		
		
		
		Ingot_JQuery_JqGrid_Column_DoubleColumn::createSelectColumn($objGrid, 'Parent' );
		
		//$objParentColumn =  new Ingot_JQuery_JqGrid_Column ('parent_name', array('index' => User_Model_Db_Roles::COL_ID_PARENT) ); 
		//$objParentDecorator = new Ingot_JQuery_JqGrid_Column_Decorator_Search_Select($objParentColumn, array('value' => array(1=>1,2=>2)));
		//$objParentEditDecorator = new Ingot_JQuery_JqGrid_Column_Decorator_Edit_Select($objParentDecorator, array('value' => array(1=>1,2=>2)), array('required'=> true));
		//$objGrid->addColumn ($objParentDecorator );
		
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