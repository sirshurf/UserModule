<?php

class User_AssertionController extends Zend_Controller_Action
{

    public function indexAction ()
    {
		$objRolesTable = new User_Model_Db_Assertion ();
		$objRolesSelect = $objRolesTable->select (TRUE);
		
		$arrGridOptions = array('caption' => '');
		
		$objGrid = new Ingot_JQuery_JqGrid ( 'assertion', new Ingot_JQuery_JqGrid_Adapter_DbTableSelect ( $objRolesSelect ), $arrGridOptions );
		$objGrid->setIdCol ( User_Model_Db_Assertion::COL_ID_ASS ); 
		$objGrid->setLocalEdit();		
				
		$objGrid->addColumn ( new Ingot_JQuery_JqGrid_Column ( User_Model_Db_Assertion::COL_ASS_NAME, array('editable' => true, 'editrules'=> array('required'=> true)) ) );		
		
		$objGrid->addColumn ( new Ingot_JQuery_JqGrid_Column ( User_Model_Db_Assertion::COL_ASS_CLASS, array('editable' => true, 'editrules'=> array('required'=> true)) ) );		
		
		$objGrid->addColumn ( new Ingot_JQuery_JqGrid_Column ( User_Model_Db_Assertion::COL_ASS_DESC, array('editable' => true,'edittype'=>'textarea', 'editrules'=> array('required'=> true)) ) );		
		
		$objGridPager = $objGrid->getPager ();		
		$objGridPager->setDefaultAdd ();
		$objGridPager->setDefaultEdit ();
		$objGridPager->setDefaultDel ();
		$objGrid->setDblClkEdit(TRUE);
		
		$objGrid->registerPlugin ( new Ingot_JQuery_JqGrid_Plugin_ToolbarFilter () );
		$this->view->grid = $objGrid->render ();
    }
}