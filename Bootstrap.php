<?php

class User_Bootstrap extends Bf_Application_Module_Bootstrap
{
    /**
     * Init config settings and resoure for this module
     * 
     */
    protected function _initModuleConfig ()
    {
        // load ini file
             
        $objOptions = new Zend_Config_Ini(dirname(__FILE__) . '/configs/module.ini', APPLICATION_ENV);
        $options = $objOptions->toArray();
        if (! empty($options['config'])) {
            if (is_array($options['config'])) {
                $_options = array();
                foreach ($options['config'] as $tmp) {
                    $_options = $this->mergeOptions($_options, $this->_loadConfig(dirname(__FILE__) . '/configs/' . $tmp));
                }
                $options = $this->mergeOptions($_options, $options);
            } else {
                $options = $this->mergeOptions($this->_loadConfig($options['config']), $options);
            }
        }
        // Set this bootstrap options
        $this->setOptions($options);
    }
    
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