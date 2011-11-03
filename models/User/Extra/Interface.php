<?php
interface User_Model_User_Extra_Interface {
	
	public function setMainRow(Bf_Db_Table_Row $objRow);
	
	public function getData();
	
	public function save();
	
}