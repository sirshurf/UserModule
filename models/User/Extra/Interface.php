<?php
interface User_Model_User_Extra_Interface {
	
	public function setMainRow(Bf_Db_Table_Row $objRow);
	
	public function getData();
	
	/**
	 * 
	 * Get the sub form of the elements
	 * @return Zend_Form
	 */
	public function getForm();
	
	/**
	 * 
	 * Get Sub Form name
	 */
	public function getFormName();
	
	public function save();
	
}