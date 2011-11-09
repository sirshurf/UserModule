<?php

class User_Model_Resources
{

    CONST GLOBAL_DIRECTORY = 'modules';

    CONST GLOBAL_CONTROLLER = 'controllers';

    CONST GLOBAL_CONTROLLERNAME = 'controller';

    CONST GLOBAL_ACTION = 'action';

    CONST DIRECTORY = "/../controllers/";

    CONST CLASS_PREFIX = "User_";

    CONST ACTION = "Action";

    CONST CONTROLLER = "Controller";
    
    CONST CONCAT_RES_NAME = 'concat_resource';

    public static function getResources ($boolReload = false)
    {
        $objSessionUserData = new Zend_Session_Namespace('userData');
        // Add roles to the Acl element		
        if (true || empty($objSessionUserData->resources)) {
            try {
                $resources = self::getResourcesFromDb();
                if (!$resources->count()){
                   $resources = self::getDefaultResources();
                }
            } catch (Zend_Db_Exception $objE) {
                $resources = self::getDefaultResources();
            } catch (Exception $objE) {
                throw new Exception("", 0, $objE);
            }
            $objSessionUserData->resources = $resources;
        }
        return $objSessionUserData->resources;
    }

    /** 
     * Fetch Resources from Db
     * 
     * @return Zend_Db_Table_Rowset
     */
    public static function getResourcesFromDb ()
    {
        $objResources = new User_Model_Db_Resources();
        $objResourcesSelect = $objResources->select(TRUE);
        $objResourcesSelect->where(User_Model_Db_Resources::COL_IS_DELETED . ' = ?', false);
        $objResourcesRowSet = $objResources->fetchAll($objResourcesSelect);
        $objAclSession->resources = $objResourcesRowSet;
        
        return $objAclSession->resources;
    }

    public static function getDefaultResources ()
    {
        $arrClassActions = array();
        $handle = opendir(realpath(dirname(__FILE__) . self::DIRECTORY));
        if ($handle) {
            while (false !== ($file = readdir($handle))) {
                if ($file != "." && $file != ".." && ! is_dir($file)) {
                    // Now include file
                    include_once realpath(dirname(__FILE__) . self::DIRECTORY) . '/' . $file;
                    $info = pathinfo($file);
                    $file_name = basename($file, '.' . $info['extension']);
                    $intControllerStringPosition = strpos($file_name, self::CONTROLLER);
                    if (! empty($intControllerStringPosition)) {
                        $strControllerName = substr($file_name, 0, $intControllerStringPosition);
                        $strControllerName = self::lcfirst($strControllerName);
                        // Read with Reflection
                        $className = self::CLASS_PREFIX . $file_name;
                        
                        $resourceName = $objReflection = new ReflectionClass($className);
                        $arrClassMethods = $objReflection->getMethods();
                        // Get Actions Names
                        foreach ($arrClassMethods as $objMethod) {
                            $intStringPosition = strpos($objMethod->name, self::ACTION);
                            if (! empty($intStringPosition)) {
                                $strActionName = substr($objMethod->name, 0, $intStringPosition);
                                $arrClassActions[$strControllerName][$strActionName][] = User_Model_Roles::DEFAULT_ROLE_GUEST;
                            }
                        }
                    }
                }
            }
            closedir($handle);
        }
        
        return $arrClassActions;
    }

    public static function getMapedResource ($boolReload = false)
    {
        $objAclSession = new Zend_Session_Namespace('userData');
        if ($boolReload || empty($objAclSession->resources_maped)) {
            $objResourcesRowSet = self::getResources($boolReload);
            // Iterate over the
            $arrMapedResourceSet = array();
            foreach ($objResourcesRowSet as $objResourcesRow) {
                $arrMapedResourceSet[$objResourcesRow->{User_Model_Db_Resources::COL_MODULE}][$objResourcesRow->{User_Model_Db_Resources::COL_CONTROLLER}] = $objResourcesRow->{User_Model_Db_Resources::COL_ID_RESOURCES};
            }
            $objAclSession->resources_maped = $arrMapedResourceSet;
        }
        return $objAclSession->resources_maped;
    }

    public static function lcfirst ($string)
    {
        $string{0} = strtolower($string{0});
        return $string;
    }

    public static function initTable ()
    {
        $arrResourcesList = self::getMapedResource();
        // Get List of modules
        $objModuleDir = opendir(realpath(APPLICATION_PATH . DIRECTORY_SEPARATOR . self::GLOBAL_DIRECTORY));
        if ($objModuleDir) {
            // Iterate each module, get list of controllers...
            while (false !== ($strModulDirElement = readdir($objModuleDir))) {
                $strModuleDir = realpath(APPLICATION_PATH . DIRECTORY_SEPARATOR . self::GLOBAL_DIRECTORY . DIRECTORY_SEPARATOR . $strModulDirElement);
                if ($strModulDirElement != "." && $strModulDirElement != ".." && is_dir($strModuleDir)) {
                    $objControllerDir = opendir(realpath($strModuleDir . DIRECTORY_SEPARATOR . self::GLOBAL_CONTROLLER));
                    if ($objControllerDir) {
                        while (false !== ($file = readdir($objControllerDir))) {
                            if ($file != "." && $file != ".." && ! is_dir($file)) {
                                $info = pathinfo($file);
                                $file_name = basename($file, '.' . $info['extension']);
                                $intControllerStringPosition = stripos($file_name, self::GLOBAL_CONTROLLERNAME);
                                if (! empty($intControllerStringPosition)) {
                                    $strControllerName = substr($file_name, 0, $intControllerStringPosition);
                                    $strControllerName = self::lcfirst($strControllerName);
                                    // Read with Reflection
                                    if (! isset($arrResourcesList[$strModulDirElement][$strControllerName])) {
                                        // Each controller check in DB
                                        // If not exists add to database
                                        $objResources = new User_Model_Db_Resources();
                                        $objResourcesRow = $objResources->createRow();
                                        $objResourcesRow->{User_Model_Db_Resources::COL_MODULE} = $strModulDirElement;
                                        $objResourcesRow->{User_Model_Db_Resources::COL_CONTROLLER} = $strControllerName;
                                        $objResourcesRow->save();
                                    }
                                }
                            }
                        }
                        closedir($objControllerDir);
                    }
                }
            }
            closedir($objModuleDir);
        }
        // Reload Resource List
        self::getMapedResource(TRUE);
    }
    
     public static function getResourceId ($strModule, $strController)
    {
        $arrMapedResources = self::getMapedResource();
        if (isset($arrMapedResources, $arrMapedResources[$strModule], $arrMapedResources[$strModule][$strController])) {
            return $arrMapedResources[$strModule][$strController];
        }
        self::initTable();
    
        $arrMapedResources = self::getMapedResource();
        if (isset($arrMapedResources, $arrMapedResources[$strModule], $arrMapedResources[$strModule][$strController])) {
            return $arrMapedResources[$strModule][$strController];
        }   
        throw new Zend_Acl_Exception('Resources Id Not Found: M:' . $strModule . ' C:' . $strController);
    }
    
	/**
	 * 
	 * Enter description here ...
	 * @return Zend_Db_Select
	 */
	public static function getPairSelect(){
		$objModel = new User_Model_Db_Resources();
		$objSelect = $objModel->select(TRUE);
		$objSelect->reset(Zend_Db_Select::COLUMNS);
		$objSelect->columns(array(User_Model_Db_Resources::COL_ID_RESOURCES,self::CONCAT_RES_NAME => self::getConcatColumn()));
		$objSelect->where(User_Model_Db_Resources::COL_IS_DELETED." = ?",FALSE);		
		return $objSelect;
	}
	
	public static function getConcatColumn(){
		return new Zend_Db_Expr("CONCAT_WS('/'," . User_Model_Db_Resources::COL_MODULE . "," . User_Model_Db_Resources::COL_CONTROLLER . ")");
	}
}

