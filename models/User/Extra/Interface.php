<?php
interface User_Model_User_Extra_Interface {
	
	public function setMainRow(Bf_Db_Table_Row $objRow);
	
	/**
	 * 
	 * GEt Spesific Data
	 */
	public function getData();
	
	/**
	 * 
	 * Get the sub form of the elements
	 * @param array $arrGetPost
	 * @return Zend_Form
	 */
	public function getForm($arrGetPost);
	
	/**
	 * 
	 * Get Sub Form name
	 */
	public function getFormName();
	
	public function save();
	
}