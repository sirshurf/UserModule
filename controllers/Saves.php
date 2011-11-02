<?php
   /**
     public function takepermitionAction ()
    {
        $nmbTzRequested = $this->_request->getParam("tz");
        // Get User Data, if missing redirect to index
        $objUsers = new Labadmin_Models_Users();
        $objUsersRow = $objUsers->find($nmbTzRequested)->current();
        if (empty($objUsersRow)) {
            Labadmin_Models_Static::setJgrowlMessage("LBL_ERROR_UNAUTHORIZED");
            $strUrl = $this->view->url(array('module' => 'prj', 'controller' => 'project', 'action' => 'index', 'unautherised'), false, true);
            $this->_redirect($strUrl);
            return;
        }
        // find out of this user is student from LDAP
        $arrSearch = array();
        $arrSearch['equals']['employeeid'] = str_pad($nmbTzRequested, 9, "0", STR_PAD_LEFT);
        ;
        $arrLdapData = Labadmin_Models_Users::searchStudentLdap($arrSearch);
        if (! empty($arrLdapData)) {
            // If Student call the role action with the peremeter...
            $this->_changeRoles($objUsersRow, array(), true);
            $strUrl = $this->view->url(array('module' => 'default', 'controller' => 'index', 'action' => 'index'), false, true);
            $this->_redirect($strUrl);
            return;
        } else {
            $arrLdapData = Labadmin_Models_Users::searchLdap($arrSearch);
            if (! empty($arrLdapData)) {
                $this->_changeRoles($objUsersRow, $arrLdapData[0]['memberof'], FALSE);
                $strUrl = $this->view->url(array('module' => 'default', 'controller' => 'index', 'action' => 'index'), false, true);
                $this->_redirect($strUrl);
                return;
            }
        }
        $arrButtons = array();
        $arrButtons[] = array('module' => 'users', 'controller' => 'users', "action" => "view", "name" => "LBL_BUTTON_USER_DETAILS", "params" => array("tz" => $nmbTzRequested));
        $this->view->arrActions = $arrButtons;
    }
    private function _changeRoles ($objNewUserRow, $arrOptions, $boolIsStudent)
    {
        // Save new User in On Behalf 
        $session_on_behalf = new Zend_Session_Namespace('onBehalf');
        $session_on_behalf->tz = $objNewUserRow->{Labadmin_Models_Users::COL_TZ};
        $session_on_behalf->first_name = $objNewUserRow->{Labadmin_Models_Users::COL_FIRST_NAME};
        $session_on_behalf->last_name = $objNewUserRow->{Labadmin_Models_Users::COL_LAST_NAME};
        if (! empty($objNewUserRow->{Labadmin_Models_Users::COL_DISPLAY_NAME})) {
            $session_on_behalf->display_name = $objNewUserRow->{Labadmin_Models_Users::COL_DISPLAY_NAME};
        } else {
            $strDisplayName = $objNewUserRow->{Labadmin_Models_Users::COL_FIRST_NAME} . " " . $objNewUserRow->{Labadmin_Models_Users::COL_LAST_NAME};
            $session_on_behalf->display_name = $strDisplayName;
        }
        // Make a copy of User Roles
        $session_members = new Zend_Session_Namespace('memberof');
        $session_members_bkp = new Zend_Session_Namespace('memberofBkp');
        $session_members_bkp->member_of = $session_members->member_of;
        $objStd = new stdClass();
        $objStd->memberof = $arrOptions;
        $session_members->member_of = $objStd;
        Zend_Session::namespaceUnset('role');
        Labadmin_Models_Users::getRole(null, $boolIsStudent);
    }

    public function switchbackAction ()
    {
        $session_members = new Zend_Session_Namespace('memberof');
        $session_members_bkp = new Zend_Session_Namespace('memberofBkp');
        $session_members->member_of = $session_members_bkp->member_of;
        Zend_Session::namespaceUnset('bkp_role');
        Zend_Session::namespaceUnset('onBehalf');
        Labadmin_Models_Users::getRole(null);
        $strUrl = $this->view->url(array('module' => 'default', 'controller' => 'index', 'action' => 'index'), false, true);
        $this->_redirect($strUrl);
        return;
    }


public function editaclAction ()
    {
        $this->view->objRoles = Labadmin_Models_AclRoles::getMapedRoles();
        $this->view->objResources = Labadmin_Models_AclResources::getMapedResource();
        if ($this->_request->isPost()) {
            $formData = $this->_request->getPost();
            if ($formData['op'] == "add") {
                if (Labadmin_Models_AclPermission::saveAcl($formData['resources'], $formData['roles'], $formData['actions'], APPLICATION_ENV)) {
                    Labadmin_Models_Static::setJgrowlMessage("LBL_UPDATE_OK");
                    $strUrl = $this->view->url(array('module' => 'users', 'controller' => 'authentication', 'action' => 'editacl'), false, true);
                    $this->_redirect($strUrl);
                } else {
                    Labadmin_Models_Static::setJgrowlMessage("LBL_UPDATE_FAIL");
                }
            } elseif ($formData['op'] == "del") {
                if (Labadmin_Models_AclPermission::delAcl($formData['resources'], $formData['roles'], $formData['actions'])) {
                    Labadmin_Models_Static::setJgrowlMessage("LBL_DEL_SUCCESS");
                    $strUrl = $this->view->url(array('module' => 'users', 'controller' => 'authentication', 'action' => 'editacl'), false, true);
                    $this->_redirect($strUrl);
                } else {
                    Labadmin_Models_Static::setJgrowlMessage("LBL_DEL_FAIL");
                }
            }
        }
        $this->view->objPermissions = Labadmin_Models_AclPermission::getMappedPermissionsRow(TRUE);
    }
   **/