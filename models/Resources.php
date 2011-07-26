<?php

class User_Model_Resources
{

    CONST DIRECTORY = "/../controllers/";

    CONST CLASS_PREFIX = "User_";

    CONST ACTION = "Action";

    CONST CONTROLLER = "Controller";

    public static function getResources ()
    {
        $objSessionUserData = new Zend_Session_Namespace('userData');
        // Add roles to the Acl element		
        if (true || empty($objSessionUserData->resources)) {
            try {
                $resources = self::getResourcesFromDb();
            } catch (Zend_Db_Exception $objE) {
                $resources = self::getDefaultResources();
            } catch (Exception $objE) {
                throw new Exception("", 0, $objE);
            }
            $objSessionUserData->resources = $resources;
        }
        return $objSessionUserData->resources;
    }

    public static function getResourcesFromDb ()
    {
        throw new Zend_Db_Exception();
    }

    public static function getDefaultResources ()
    {
        $arrClassActions = array();
        $handle = opendir(realpath(dirname(__FILE__). self::DIRECTORY));
        if ($handle) {
            while (false !== ($file = readdir($handle))) {
                if ($file != "." && $file != ".." && !is_dir($file)) {
                    // Now include file
                    include_once realpath(dirname(__FILE__). self::DIRECTORY).'/'.$file;
                    $info = pathinfo($file);
                    $file_name = basename($file, '.' . $info['extension']);
                    $intControllerStringPosition = strpos($file_name, 
                    self::CONTROLLER);
                    if (! empty($intControllerStringPosition)) {
                        $strControllerName = substr($file_name, 0, 
                        $intControllerStringPosition);
                        $strControllerName = self::lcfirst($strControllerName);
                        // Read with Reflection
                        $className = self::CLASS_PREFIX .
                         $file_name;
                         
                        $resourceName = $objReflection = new ReflectionClass(
                        $className);
                        $arrClassMethods = $objReflection->getMethods();
                        // Get Actions Names
                        foreach ($arrClassMethods as $objMethod) {
                            $intStringPosition = strpos($objMethod->name, 
                            self::ACTION);
                            if (! empty($intStringPosition)) {
                                $strActionName = substr($objMethod->name, 0,$intStringPosition);
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
    
  public static function lcfirst($string) {
      $string{0} = strtolower($string{0});
      return $string;
  }
}

