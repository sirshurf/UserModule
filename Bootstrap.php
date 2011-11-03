<?php

class User_Bootstrap extends Bf_Application_Module_Bootstrap
{

    /**
     * 
     * Init Authentication
     */
    protected function _initAuthentication ()
    {
        $fc = Zend_Controller_Front::getInstance();
        $objUserAuthPlugin = new User_Plugin_Authentication(); 
        $fc->registerPlugin($objUserAuthPlugin);
        
        $objView = $this->getApplication()->view;
        $objView->getHelper('navigation')->setDefaultAcl($objUserAuthPlugin->getAcl());
    }


}