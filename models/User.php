<?php
class User_Model_User {
    CONST PREFIX_CODE = '{';
    CONST SUFFIX_CODE = '}';
    CONST CODE = '%';
    
    public function createNewPasswordForUserLogin($strLoginName){
        $objUserDb = new User_Model_Db_Users();
        $objUserDbSelect = $objUserDb->select(TRUE);
        $objUserDbSelect->where(User_Model_Db_Users::COL_LOGIN." = ?",$strLoginName);
        $objUserDbRow = $objUserDb->fetchRow($objUserDbSelect);
        if (!empty($objUserDbRow)){
            return $this->createNewPasswordForUser($objUserDbRow->{User_Model_Db_Users::COL_ID_USERS});
        }
        return false;
    }
    
    /**
     * 
     * Crete new password for the user, send it to email and save it.
     * 
     * @param int $intUserID
     * @return bool
     */
    public function createNewPasswordForUser ($intUserID) {
        $objUserDb = new User_Model_Db_Users();
        $objUserDbRowSet = $objUserDb->find($intUserID);
        if ($objUserDbRowSet->count() > 0) {
            $objUserDbRow = $objUserDbRowSet->current();
            // Generate new Password
            $strPassword = $this->_generateNewPassword(9, 8);
             // Send new password to email
             if ($this->_sendNewPasswordToUser ($objUserDbRow, $strPassword)){
                // Save new password
                $objUserDbRow->{User_Model_Db_Users::COL_PWD} = md5($strPassword);
                if ($objUserDbRow->save()){
                    return true;
                }
             }
        }
        return false;
    }
    /**
     * 
     * Generates rundom password
     * 
     * @author
     * @link http://www.webtoolkit.info/php-random-password-generator.html
     * 
     * @param int $length
     * @param int $strength
     * @return string
     */
    private function _generateNewPassword ($length = 9, $strength = 0) {
        $vowels = 'aeuy';
        $consonants = 'bdghjmnpqrstvz';
        if ($strength & 1) {
            $consonants .= 'BDGHJLMNPQRSTVWXZ';
        }
        if ($strength & 2) {
            $vowels .= "AEUY";
        }
        if ($strength & 4) {
            $consonants .= '23456789';
        }
        if ($strength & 8) {
            $consonants .= '@#$%';
        }
        $password = '';
        $alt = time() % 2;
        for ($i = 0; $i < $length; $i ++) {
            if ($alt == 1) {
                $password .= $consonants[(rand() % strlen($consonants))];
                $alt = 0;
            } else {
                $password .= $vowels[(rand() % strlen($vowels))];
                $alt = 1;
            }
        }
        return $password;
    }
    private function _sendNewPasswordToUser ($objUserRow, $strNewPassword) {
        $strNewMailMessage = $this->_preperePasswordMsg($strNewPassword);
        $objMail = new Zend_Mail();
        $objMail->setBodyHtml($strNewMailMessage);
        $objMail->addTo($objUserRow->{User_Model_Db_Users::COL_EMAIL}, $objUserRow->{User_Model_Db_Users::COL_FIRST_NAME} . ' ' . $objUserRow->{User_Model_Db_Users::COL_LAST_NAME});
        $objZendTRanslate = Zend_Registry::get('Zend_Translate');
        $objMail->setSubject($objZendTRanslate->translate('LBL_SUBJECT_EMAIL_NEW_PASSWORD'));
        try {
            $objMail->send();
        } catch (Zend_Mail_Exception $objException) {
            return FALSE;
        }
        return TRUE;
    }
    /**
     * 
     * Prepere Message for sending with password
     * @param string $strNewPassword
     * $return string
     */
    private function _preperePasswordMsg ($strNewPassword) {
        $objZendTRanslate = Zend_Registry::get('Zend_Translate');
        $strNewMessage = $objZendTRanslate->translate('LBL_TEXT_EMAIL_NEW_PASSWORD');
        $strCode = self::PREFIX_CODE . self::CODE . self::SUFFIX_CODE;
        $strPattern = '/[' . $strCode . ']/u';
        $strMessageAfterReplace = preg_replace($strPattern, $strNewPassword, $strNewMessage);
        return $strMessageAfterReplace;
    }
}

