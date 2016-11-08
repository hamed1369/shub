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
 * Admin page
 * @author Mohammad
 * @package ow_plugins.iiscontactus.controllers
 * @since 1.0
 */
class IISCONTACTUS_CTRL_Admin extends ADMIN_CTRL_Abstract
{

    public function dept($params)
    {
        OW::getDocument()->setTitle(OW::getLanguage()->text('iiscontactus', 'admin_contactus_settings_heading'));
       $service = $this->getService();
       $sectionId = 1;
        if(isset($params['sectionId'])){
            $sectionId = $params['sectionId'];
        }
        if($sectionId==1) {
            $this->assign('sectionId', 1);
            $this->setPageTitle(OW::getLanguage()->text('iiscontactus', 'admin_dept_title'));
            $this->setPageHeading(OW::getLanguage()->text('iiscontactus', 'admin_dept_heading'));
            $contactEmails = array();
            $deleteUrls = array();
            $contacts = IISCONTACTUS_BOL_Service::getInstance()->getDepartmentList();
            foreach ($contacts as $contact) {
                /* @var $contact IISCONTACTUS_BOL_Department */
                $contactEmails[$contact->id]['name'] = $contact->id;
                $contactEmails[$contact->id]['email'] = $contact->email;
                $contactEmails[$contact->id]['label'] = $contact->label;
                $deleteUrls[$contact->id] = OW::getRouter()->urlFor(__CLASS__, 'delete', array('id' => $contact->id));
            }
            $this->assign('contacts', $contactEmails);
            $this->assign('deleteUrls', $deleteUrls);

            $form = new Form('add_dept');
            $this->addForm($form);

            $fieldEmail = new TextField('email');
            $fieldEmail->setRequired();
            $fieldEmail->addValidator(new EmailValidator());
            $fieldEmail->setInvitation(OW::getLanguage()->text('iiscontactus', 'label_invitation_email'));
            $fieldEmail->setHasInvitation(true);
            $form->addElement($fieldEmail);

            $fieldLabel = new TextField('label');
            $fieldLabel->setRequired();
            $fieldLabel->setInvitation(OW::getLanguage()->text('iiscontactus', 'label_invitation_label'));
            $fieldLabel->setHasInvitation(true);
            $validator = new IISCONTACTUS_CLASS_LabelValidator();
            $language = OW::getLanguage();
            $validator->setErrorMessage($language->text('iiscontactus', 'label_error_already_exist'));
            $fieldLabel->addValidator($validator);
            $form->addElement($fieldLabel);

            $submit = new Submit('add');
            $submit->setValue(OW::getLanguage()->text('iiscontactus', 'form_add_dept_submit'));
            $form->addElement($submit);
            $this->assign('sections', $service->getAdminSections($sectionId));
            if (OW::getRequest()->isPost()) {
                if ($form->isValid($_POST)) {
                    $data = $form->getValues();
                        IISCONTACTUS_BOL_Service::getInstance()->addDepartment($data['email'], $data['label']);
                        $this->redirect();
                }
            }
      }
        else if($sectionId==2)
        {
            $this->assign('sectionId', 2);
            $formSettings = new Form('settings');
            $formSettings->setAjax();
            $formSettings->setAjaxResetOnSuccess(false);
            $formSettings->setAction(OW::getRouter()->urlForRoute('iiscontactus.admin'));
            $formSettings->bindJsFunction(Form::BIND_SUCCESS, 'function(data){if(data.result){OW.info("Settings successfuly saved");}else{OW.error("Parser error");}}');
            $formData = new Form('formData');
            $formData->setAction(OW::getRouter()->urlForRoute('iiscontactus.admin.data'));
            $config = OW::getConfig();
            $configs = $config->getValues('iiscontactus');
            $contacts = IISCONTACTUS_BOL_Service::getInstance()->getDepartmentList();
            $optionsDepartments = array();
            foreach ($contacts as $contact) {
                $optionsDepartments[$contact->label] =  $contact->label;
            }

            $departments = new Selectbox('departments');
            $departments->setHasInvitation(false);
            $departments->setOptions($optionsDepartments);
            $departments->setRequired();
            $departments->setValue($configs['departments']);
            $formData->addElement($departments);
            $numberOfData = new Selectbox('numberOfData');
            $optionsNumberOfData = array();
            $optionsNumberOfData[10] = 10;
            $optionsNumberOfData[50] = 50;
            $optionsNumberOfData[100] = 100;
            $optionsNumberOfData[200] = 200;
            $numberOfData->setHasInvitation(false);
            $numberOfData->setOptions($optionsNumberOfData);
            $numberOfData->setRequired();
            $numberOfData->setValue($configs['numberOfData']);
            $formData->addElement($numberOfData);

            $submitFormData = new Submit('showFormData');
            $submitFormData->setValue(OW::getLanguage()->text("iiscontactus", "showFormData"));
            $formData->addElement($submitFormData);

            $this->addForm($formData);
            $this->assign('sections', $service->getAdminSections($sectionId));
            if ( OW::getRequest()->isAjax() )
            {
                if ( $formData->isValid($_POST) )
                {
                    $data = $formData->getValues();
                    IISCONTACTUS_BOL_Service::getInstance()->addDepartment($data['email'], $data['label']);
                    $this->redirect();
                }
            }
        }
        else if($sectionId=='new')
        {
            $form = new Form('add_adminComment');
            $this->addForm($form);
            $config = OW::getConfig();
            $configs = $config->getValues('iiscontactus');
            $buttons = array(
                BOL_TextFormatService::WS_BTN_BOLD,
                BOL_TextFormatService::WS_BTN_ITALIC,
                BOL_TextFormatService::WS_BTN_UNDERLINE,
                BOL_TextFormatService::WS_BTN_IMAGE,
                BOL_TextFormatService::WS_BTN_LINK,
                BOL_TextFormatService::WS_BTN_ORDERED_LIST,
                BOL_TextFormatService::WS_BTN_UNORDERED_LIST,
                BOL_TextFormatService::WS_BTN_MORE,
                BOL_TextFormatService::WS_BTN_SWITCH_HTML,
                BOL_TextFormatService::WS_BTN_HTML,
                BOL_TextFormatService::WS_BTN_VIDEO
            );
            $this->assign('sectionId', 'new');
            $commentTextArea = new WysiwygTextarea('comment', $buttons);
            $commentTextArea->setSize(WysiwygTextarea::SIZE_L);
            $commentTextArea->setLabel(OW::getLanguage()->text('iiscontactus', 'save_form_lbl_entry'));
            $commentTextArea->setValue($configs['adminComment']);
            $form->addElement($commentTextArea);

            $submitFormData = new Submit('add');
            $submitFormData->setValue(OW::getLanguage()->text("iiscontactus", "addAdminComment"));
            $form->addElement($submitFormData);

            $this->addForm($form);
            $this->assign('sections', $service->getAdminSections($sectionId));
            if (OW::getRequest()->isPost()&& $form->isValid($_POST))
            {
                $data = $form->getValues();
                $text = UTIL_HtmlTag::sanitize($data['comment']);
                if($config->configExists('iiscontactus','adminComment'))  {
                    $config->saveConfig('iiscontactus', 'adminComment', $text);
                }else{
                    $config->addConfig('iiscontactus', 'adminComment', $text);
                }
                OW::getFeedback()->info(OW::getLanguage()->text('iiscontactus', 'modified_successfully'));
            }
        }
    }

    public function data($params)
    {
        if(!isset($_POST['departments']) || !isset($_POST['numberOfData'])){
            $this->redirect(OW::getRouter()->urlForRoute('iiscontactus.admin'));
        }else {
            $colnames = array();
            $department = $_POST['departments'];
            $numberOfData = $_POST['numberOfData'];
            $information = $this->getDepartmentsData($department, $numberOfData);
            foreach ($information['columns'] as $columns) {
                array_push($colnames, OW::getLanguage()->text("iiscontactus",$columns['COLUMN_NAME']));
            }
            $this->assign('tableColumns', $colnames);
            $this->assign('tableData', $information['data']);
            $this->assign('returnToSetting', OW::getRouter()->urlForRoute('iiscontactus.admin'));
        }
    }

    /**
     *
     * @return array
     */
    public function getDepartmentsData($department, $numberOfData)
    {
        $data = IISCONTACTUS_BOL_Service::getInstance()->getUserInformationListByLabel($department,$numberOfData);
        return $data;
    }
    public function getService(){
        return IISCONTACTUS_BOL_Service::getInstance();
    }


    public function delete( $params )
    {
        if ( isset($params['id']) )
        {
            $department = IISCONTACTUS_BOL_Service::getInstance()->getDepartmentLabelByID((int) $params['id']);
            IISCONTACTUS_BOL_Service::getInstance()->deleteUserInformationBylabel(trim($department->label));
            IISCONTACTUS_BOL_Service::getInstance()->deleteDepartment((int) $params['id']);
        }
        $this->redirect(OW::getRouter()->urlForRoute('iiscontactus.admin'));
    }
}
