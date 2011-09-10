<?php

class User_RolesController extends Zend_Controller_Action
{


    public function indexAction ()
    {
		$objUserTable = new User_Model_Db_Roles ();
		$objSelect = $objUserTable->select ();
		 
		$grid = new Ingot_JQuery_JqGrid ( 'users', new Ingot_JQuery_JqGrid_Adapter_DbTableSelect ( $objSelect ) );
		$grid->setIdCol ( User_Model_Db_Roles::COL_ID_ROLES ); 
		
		$strUrl = $this->view->url ( array (
			'module' => 'users', 'controller' => 'users', 'action' => 'view' 
		), null, true, false );
		$grid->setOption ( 'ondblClickRow', "function(rowId, iRow, iCol, e){ if(rowId){  document.location.href ='" . $strUrl . "/tz/'+rowId } }" );
		
		$grid->addColumn ( new Ingot_JQuery_JqGrid_Column ( User_Model_Db_Roles::COL_ROLE ) );		
		$grid->addColumn ( new Ingot_JQuery_JqGrid_Column ( User_Model_Db_Roles::COL_ROLE_TITLE ) );
		$grid->addColumn ( new Ingot_JQuery_JqGrid_Column ( User_Model_Db_Roles::COL_ID_PARENT ) );
		$grid->addColumn ( new Ingot_JQuery_JqGrid_Column ( User_Model_Db_Roles::COL_ORDER ) );
		
		$grid->registerPlugin ( new Ingot_JQuery_JqGrid_Plugin_ToolbarFilter () );
		$this->view->grid = $grid->render ();
    }
}