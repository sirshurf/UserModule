<?php

class User_ResourcesController extends Zend_Controller_Action
{

    public function editaclAction ()
    {
        $this->view->objRoles = Labadmin_Models_AclRoles::getMapedRoles();
        $this->view->objResources = Labadmin_Models_AclResources::getMapedResource();
        if ($this->_request->isPost()) {
            $formData = $this->_request->getPost();
            if ($formData['op'] == "add") {
                if (Labadmin_Models_AclPermission::saveAcl(
                $formData['resources'], $formData['roles'], $formData['actions'], 
                APPLICATION_ENV)) {
                    Labadmin_Models_Static::setJgrowlMessage("LBL_UPDATE_OK");
                    $strUrl = $this->view->url(
                    array('module' => 'users', 'controller' => 'authentication', 
                    'action' => 'editacl'), false, true);
                    $this->_redirect($strUrl);
                } else {
                    Labadmin_Models_Static::setJgrowlMessage("LBL_UPDATE_FAIL");
                }
            } elseif ($formData['op'] == "del") {
                if (Labadmin_Models_AclPermission::delAcl(
                $formData['resources'], $formData['roles'], $formData['actions'])) {
                    Labadmin_Models_Static::setJgrowlMessage("LBL_DEL_SUCCESS");
                    $strUrl = $this->view->url(
                    array('module' => 'users', 'controller' => 'authentication', 
                    'action' => 'editacl'), false, true);
                    $this->_redirect($strUrl);
                } else {
                    Labadmin_Models_Static::setJgrowlMessage("LBL_DEL_FAIL");
                }
            }
        }
        $this->view->objPermissions = Labadmin_Models_AclPermission::getMappedPermissionsRow(
        TRUE);
    }

    public function getactionAction ()
    {
        Zend_Controller_Action_HelperBroker::getStaticHelper('layout')->disableLayout();
        $intResourceId = $this->_request->getParam("ResourceId");
        $objResourceRow = "";
        $objResourceList = Labadmin_Models_AclResources::getResources();
        // Find Resource
        foreach ($objResourceList as $objResourceListRow) {
            if ($intResourceId ==
             $objResourceListRow->{Labadmin_Models_AclResources::COL_ID_RESOUCE}) {
                $objResourceRow = $objResourceListRow;
                break;
            }
        }
        if (empty($objResourceRow)) {
            throw new Exception("Resource Not found");
        }
        if (empty(
        $objResourceRow->{Labadmin_Models_AclResources::COL_IS_VIRTUAL})) {
            // Create Controller Name
            if ('default' ==
             $objResourceRow->{Labadmin_Models_AclResources::COL_MODULE}) {
                $strControllerName = ucfirst(
                $objResourceRow->{Labadmin_Models_AclResources::COL_CONTROLLER}) .
                 ucfirst(Labadmin_Models_AclResources::CONTROLLERNAME);
            } else {
                $strControllerName = ucfirst(
                $objResourceRow->{Labadmin_Models_AclResources::COL_MODULE}) . "_" .
                 ucfirst(
                $objResourceRow->{Labadmin_Models_AclResources::COL_CONTROLLER}) .
                 ucfirst(Labadmin_Models_AclResources::CONTROLLERNAME);
            }
            
            //Autoload correct file...
            $strFile = ucfirst(
            $objResourceRow->{Labadmin_Models_AclResources::COL_CONTROLLER}) .
             ucfirst(Labadmin_Models_AclResources::CONTROLLERNAME);
            try {
                Zend_Loader::loadClass($strFile, 
                array(
                APPLICATION_PATH . DIRECTORY_SEPARATOR .
                 Labadmin_Models_AclResources::DIRECTORY . DIRECTORY_SEPARATOR .
                 $objResourceRow->{Labadmin_Models_AclResources::COL_MODULE} .
                 DIRECTORY_SEPARATOR . Labadmin_Models_AclResources::CONTROLLER));
            } catch (Exception $objE) {}
            
            $arrActionsList = array();
            $arrActionsList['all'] = 'All';
            
            try {
                // Refractore it
                $objReflection = new ReflectionClass(
                $strControllerName);
                // Get List of Action Names
                $arrMethodsList = $objReflection->getMethods(
                ReflectionMethod::IS_PUBLIC);
                
                foreach ($arrMethodsList as $arrMethod) {
                    if (FALSE !==
                     stripos($arrMethod->name, 
                    Labadmin_Models_AclResources::ACTION)) {
                        $strActionName = substr($arrMethod->name, 0, 
                        stripos($arrMethod->name, 
                        Labadmin_Models_AclResources::ACTION));
                        $arrActionsList[$strActionName] = $strActionName;
                    }
                }
            } catch (Exception $objE) {}
            
            // Return Zend Select obj
            $objSelect = new Zend_Form_Element_Select('actions');
            $objSelect->addMultiOptions($arrActionsList);
            $objSelect->setDecorators(array('ViewHelper'));
            
            $this->view->objSelect = $objSelect;
        } else {
            
            $this->view->objSelect = $this->view->translate(
            "LBL_VIRTUAL_RESOURCE");
        }
    }

}













