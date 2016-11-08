<?php

/**
 * This software is intended for use with Oxwall Free Community Software http://www.oxwall.org/ and is
 * licensed under The BSD license.

 * ---
 * Copyright (c) 2011, Oxwall Foundation
 * All rights reserved.

 * Redistribution and use in source and binary forms, with or without modification, are permitted provided that the
 * following conditions are met:
 *
 *  - Redistributions of source code must retain the above copyright notice, this list of conditions and
 *  the following disclaimer.
 *
 *  - Redistributions in binary form must reproduce the above copyright notice, this list of conditions and
 *  the following disclaimer in the documentation and/or other materials provided with the distribution.
 *
 *  - Neither the name of the Oxwall Foundation nor the names of its contributors may be used to endorse or promote products
 *  derived from this software without specific prior written permission.

 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES,
 * INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR
 * PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO,
 * PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED
 * AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 */

/**
 * Contact us service.
 *
 * @author Mohammad
 * @package ow_plugins.iiscontactus.bol
 * @since 1.0
 */
class IISCONTACTUS_BOL_Service
{
    /**
     * Singleton instance.
     *
     * @var IISCONTACTUS_BOL_Service
     */
    private static $classInstance;


    private  $userinformationDao;
    /**
     * Returns an instance of class (singleton pattern implementation).
     *
     * @return IISCONTACTUS_BOL_Service
     */
    public static function getInstance()
    {
        if ( self::$classInstance === null )
        {
            self::$classInstance = new self();
        }

        return self::$classInstance;
    }

    private function __construct()
    {
        $this->userinformationDao = IISCONTACTUS_BOL_UserInformationDao::getInstance();
    }

    public function getDepartmentLabel( $id )
    {
        return OW::getLanguage()->text('iiscontactus', $this->getDepartmentKey($id));
    }
    public function getDepartmentLabelByID( $id )
    {
        return IISCONTACTUS_BOL_DepartmentDao::getInstance()->findById($id);
    }


    public function addDepartment( $email, $label )
    {
        $contact = new IISCONTACTUS_BOL_Department();
        $contact->email = $email;
        $contact->label = $label;
        IISCONTACTUS_BOL_DepartmentDao::getInstance()->save($contact);
    }

    public function deleteDepartment( $id )
    {
        $id = (int) $id;
        if ( $id > 0 )
        {
            IISCONTACTUS_BOL_DepartmentDao::getInstance()->deleteById($id);
        }
    }

    private function getDepartmentKey( $name )
    {
        return 'dept_' . trim($name);
    }

    public function getDepartmentList()
    {
        return IISCONTACTUS_BOL_DepartmentDao::getInstance()->findAll();
    }

    public function addUserInformation($subject , $useremail , $label , $message)
    {
        $userInfo = new IISCONTACTUS_BOL_UserInformation();
        $userInfo->subject = $subject;
        $userInfo->useremail = $useremail;
        $userInfo->label = $label;
        $userInfo->message = $message;
        IISCONTACTUS_BOL_UserInformationDao::getInstance()->save($userInfo);
    }

    public function deleteUserInformationBylabel( $label )
    {
        if ( isset($label) )
        {
            $this->userinformationDao->deleteByLabel($label);
        }
    }

    /**
     *
     * @return array
     */
    public function getAdminSections($sectionId)
    {
        $sections = array();

        for ($i = 1; $i <= 2; $i++) {
            $sections[] = array(
                'sectionId' => $i,
                'active' => $sectionId == $i ? true : false,
                'url' => OW::getRouter()->urlForRoute('iiscontactus.admin.section-id', array('sectionId' => $i)),
                'label' => $this->getPageHeaderLabel($i)
            );
        }
        $sections[] = array(
            'sectionId' => 'new',
            'active' => $sectionId == 'new' ? true : false,
            'url' => OW::getRouter()->urlForRoute('iiscontactus.admin.section-id', array('sectionId' => 'new')),
            'label' => $this->getPageHeaderLabel('new')
        );
        return $sections;
    }

    public function getPageHeaderLabel($sectionId)
    {
        if ($sectionId == 1) {
            return OW::getLanguage()->text('iiscontactus', 'department');
        } else if ($sectionId == 2) {
            return OW::getLanguage()->text('iiscontactus', 'userInfo');
        }else if ($sectionId == 'new') {
            return OW::getLanguage()->text('iiscontactus', 'adminComment');
        }
    }
    public function getUserInformationList()
    {
        return  $this->userinformationDao->findAll();
    }

    public function getUserInformationListByLabel($department,$numberOfData)
    {
        return $this->userinformationDao->findByLabel($department,$numberOfData);
    }

    public function isExistLabel($label)
    {

        if ( $label === null )
        {
            return false;
        }

        $department = IISCONTACTUS_BOL_DepartmentDao::getInstance()->findIsExistLabel($label);

        if ( isset($department) )
        {
            return true;
        }

        return false;

    }

}