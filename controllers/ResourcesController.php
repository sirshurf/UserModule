<?php

class User_ResourcesController extends Zend_Controller_Action
{

    public function indexAction ()
    {
        $objResourceTable = new User_Model_Db_Resources();
        $objResourcesSelect = $objResourceTable->select(TRUE);
        
        $arrGridOptions = array('caption' => '');
        
        $objGrid = new Ingot_JQuery_JqGrid('users', new Ingot_JQuery_JqGrid_Adapter_DbTableSelect($objResourcesSelect), $arrGridOptions);
        $objGrid->setIdCol(User_Model_Db_Resources::COL_ID_RESOURCES);
        $objGrid->setLocalEdit();
        
        $objGrid->addColumn(new Ingot_JQuery_JqGrid_Column(User_Model_Db_Resources::COL_MODULE, array('editable' => true)));
        $objGrid->addColumn(new Ingot_JQuery_JqGrid_Column(User_Model_Db_Resources::COL_CONTROLLER, array('editable' => true)));
        
        $objGrid->addColumn(new Ingot_JQuery_JqGrid_Column(User_Model_Db_Resources::COL_IS_VIRTUAL, array('editable' => true)));
        
        $objGridPager = $objGrid->getPager ();
        $objGridPager->setDefaultAdd ();
        $objGrid->setDblClkEdit(TRUE);
        
        $objGrid->registerPlugin(new Ingot_JQuery_JqGrid_Plugin_ToolbarFilter());
        $this->view->grid = $objGrid->render();
        
        $arrActions = array();
        
		$arrActions [] = array (
			'module' => 'user', 'controller' => 'resources', "action" => "init", "name" => 'Init Resources List'
		);
		
		$this->view->arrActions = $arrActions;
        
    }
    
    public function initAction(){
        
        User_Model_Resources::initTable();

        $strUrl = $this->view->url(array('module' => 'user', 'controller' => 'resources', 'action' => 'index'), false, true);
        $this->_redirect($strUrl);
    }
}













