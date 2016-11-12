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
 * Main page
 *
 * @author Mohammad
 * @package ow_plugins.iiscontactus.controllers
 * @since 1.0
 */
class IISCONTACTUS_CTRL_Contact extends OW_ActionController
{

    public function index()
    {
        $this->setPageTitle(OW::getLanguage()->text('iiscontactus', 'index_page_title'));
        $this->setPageHeading(OW::getLanguage()->text('iiscontactus', 'index_page_heading'));

        $contactEmails = array();
        $contacts = IISCONTACTUS_BOL_Service::getInstance()->getDepartmentList();
        foreach ( $contacts as $contact )
        {
            /* @var $contact IISCONTACTUS_BOL_Department */
            $contactEmails[$contact->id]['label'] = $contact->label;
            $contactEmails[$contact->id]['email'] = $contact->email;
        }

        $form = new Form('contact_form');
        $text = "";
        $config = OW::getConfig();
        if($config->configExists('iiscontactus','adminComment')) {
            $text= $config->getValue('iiscontactus', 'adminComment');
            $this->assign('adminComment', $text);
        }
        $fieldTo = new Selectbox('to');
        foreach ( $contactEmails as $id => $value )
        {
            $fieldTo->addOption($id, $value['label']);
        }
        $fieldTo->setRequired();
        $fieldTo->setHasInvitation(false);
        $fieldTo->setLabel($this->text('iiscontactus', 'form_label_to'));
        $form->addElement($fieldTo);

        if ( OW::getUser()->isAuthenticated() )
        {
            $fieldFrom = new HiddenField('from');
            $fieldFrom->setValue( OW::getUser()->getEmail() );
            $this->assign('isAuthenticated', true);
        }else{
            $fieldFrom = new TextField('from');
            $fieldFrom->setLabel($this->text('iiscontactus', 'form_label_from'));
            $this->assign('isAuthenticated', false);
        }
        $fieldFrom->setRequired();
        $fieldFrom->addValidator(new EmailValidator());
        $form->addElement($fieldFrom);

        $fieldSubject = new TextField('subject');
        $fieldSubject->setLabel($this->text('iiscontactus', 'form_label_subject'));
        $fieldSubject->setRequired();
        $form->addElement($fieldSubject);

        $fieldMessage = new Textarea('message');
        $fieldMessage->setLabel($this->text('iiscontactus', 'form_label_message'));
        $fieldMessage->setRequired();
        $form->addElement($fieldMessage);

        $fieldCaptcha = new CaptchaField('captcha');
        $fieldCaptcha->setLabel($this->text('iiscontactus', 'form_label_captcha'));
        $form->addElement($fieldCaptcha);
        $this->assign('captcha_present', 'true');

        $submit = new Submit('send');
        $submit->setValue($this->text('iiscontactus', 'form_label_submit'));
        $form->addElement($submit);

        $this->addForm($form);

        if ( OW::getRequest()->isPost() )
        {
            if ( $form->isValid($_POST) )
            {
                $data = $form->getValues();

                if ( !array_key_exists($data['to'], $contactEmails) )
                {
                    OW::getFeedback()->error($this->text('iiscontactus', 'no_department'));
                    return;
                }

                $mail = OW::getMailer()->createMail();
                $mail->addRecipientEmail($contactEmails[$data['to']]['email']);
                $mail->setSender($data['from']);
                $mail->setSenderSuffix(false);
                $mail->setSubject($data['subject']);
                $mail->setTextContent($data['message']);
                $mail->setHtmlContent($data['message']);
                $iiscontactus = IISCONTACTUS_BOL_Service::getInstance();
                $iiscontactus->addUserInformation($data['subject'],$data['from'],$contactEmails[$data['to']]['label'],$data['message']);
                OW::getMailer()->addToQueue($mail);

                OW::getSession()->set('iiscontactus.dept', $contactEmails[$data['to']]['label']);
                $this->redirectToAction('sent');
            }
        }

        $this->assign('backgroundImage', OW::getPluginManager()->getPlugin('iiscontactus')->getStaticUrl().'img/bg.png');
    }

    public function sent()
    {
        $dept = null;

        if ( OW::getSession()->isKeySet('iiscontactus.dept') )
        {
            $dept = OW::getSession()->get('iiscontactus.dept');
            OW::getSession()->delete('iiscontactus.dept');
        }
        else
        {
            $this->redirectToAction('index');
        }

        $feedback = $this->text('iiscontactus', 'message_sent', ( $dept === null ) ? null : array('dept' => $dept));
        $this->assign('feedback', $feedback);
    }

    private function text( $prefix, $key, array $vars = null )
    {
        return OW::getLanguage()->text($prefix, $key, $vars);
    }
}