<?php

/**
 *
 */

/**
 * iisterms Service.
 *
 * @author Yaser Alimardany <yaser.alimardany@gmail.com>
 * @package ow_plugins.iisterms.bol
 * @since 1.0
 */
final class IISTERMS_BOL_Service
{

    private $sections = ['termsOfService' => 1, 'privacyPolicy' => 2, 'FAQ' => 3, 'default1' => 4, 'default2' => 5];

    /**
     * @var iisterms_BOL_ItemDao
     */
    private $itemDao;

    /**
     * @var iisterms_BOL_ItemVersionDao
     */
    private $itemVersionDao;

    /**
     * Constructor.
     */
    private function __construct()
    {
        $this->itemDao = IISTERMS_BOL_ItemDao::getInstance();
        $this->itemVersionDao = IISTERMS_BOL_ItemVersionDao::getInstance();
    }

    /**
     * Singleton instance.
     *
     * @var iisterms_BOL_Service
     */
    private static $classInstance;

    /**
     * Returns an instance of class (singleton pattern implementation).
     *
     * @return iisterms_BOL_Service
     */
    public static function getInstance()
    {
        if (self::$classInstance === null) {
            self::$classInstance = new self();
        }

        return self::$classInstance;
    }

    /**
     * @param $sectionId
     * @param $newItems
     * @param $informUsers
     */
    public function addVersion($sectionId, $newItems, $informUsers)
    {
        $maxVersion = $this->getMaxVersion($sectionId);
        foreach ($newItems as $item) {
            $itemVersion = new IISTERMS_BOL_ItemVersion();
            $itemVersion->langId = $item->langId;
            $itemVersion->sectionId = $sectionId;
            $itemVersion->header = $item->header;
            $itemVersion->description = $item->description;
            $itemVersion->order = $item->order;
            $itemVersion->version = $maxVersion + 1;
            $itemVersion->time = time();
            $this->itemVersionDao->save($itemVersion);
        }

        if ($informUsers) {
            $this->informUsers($sectionId, $newItems, $maxVersion);
        }
    }

    /**
     * @param int $sectionId
     * @param int $newItems
     * @param int $maxVersion
     */
    public function informUsers($sectionId, $newItems, $maxVersion)
    {
        $itemChanged = $this->findItemChanged($sectionId, $newItems, $maxVersion);

        $numberOfUsers = BOL_UserService::getInstance()->count(true);
        $users = BOL_UserService::getInstance()->findList(0, $numberOfUsers, true);

        $this->sendEmailToUsers($users, $sectionId, $itemChanged);
        $this->sendNotificationToUsers($users, $sectionId, $itemChanged);
    }


    /**
     * @param int $users
     * @param int $sectionId
     * @param int $itemChanged
     */
    public function sendEmailToUsers($users, $sectionId, $itemChanged)
    {
        $changedItemsImportantForEmail = array();
        foreach ($itemChanged as $key => $item) {
            if ($item->email) {
                $changedItemsImportantForEmail[] = $item;
            }
        }

        if (sizeof($changedItemsImportantForEmail)) {
            $mails = array();
            foreach ($users as $key => $user) {
                $mail = OW::getMailer()->createMail();
                $mail->addRecipientEmail($user->email);
                $mail->setSubject(OW::getLanguage()->text('iisterms', 'email_subject', array('value' => $this->getPageHeaderLabel($sectionId))));
                $mail->setHtmlContent($this->getEmailContent($sectionId, $changedItemsImportantForEmail));
                $mail->setTextContent($this->getEmailContent($sectionId, $changedItemsImportantForEmail));
                $mails[] = $mail;
            }
            OW::getMailer()->addListToQueue($mails);
        }
    }

    /**
     * @param int $users
     * @param int $sectionId
     * @param int $itemChanged
     */
    public function sendNotificationToUsers($users, $sectionId, $itemChanged)
    {
        $changedItemsImportantForNotification = array();
        foreach ($itemChanged as $key => $item) {
            if ($item->notification) {
                $changedItemsImportantForNotification[] = $item;
            }
        }

        if (sizeof($changedItemsImportantForNotification)) {
            foreach ($users as $key => $user) {
                $notificationParams = array(
                    'pluginKey' => 'iisterms',
                    'action' => 'terms',
                    'entityType' => 'iisterms-terms',
                    'entityId' => $sectionId,
                    'userId' => $user->getId(),
                    'time' => time()
                );
                $avatars = BOL_AvatarService::getInstance()->getDataForUserAvatars(array($user->getId()));

                $notificationData = array(
                    'string' => array(
                        'key' => 'iisterms+notification_content',
                        'vars' => array(
                            'value1' => $this->getPageHeaderLabel($sectionId),
                            'value2' => sizeof($changedItemsImportantForNotification)
                        )
                    ),
                    'avatar' => $avatars[$user->getId()],
                    'url' => OW::getRouter()->urlForRoute('iisterms.index.section-id', array('sectionId' => $sectionId))
                );
                $event = new OW_Event('notifications.add', $notificationParams, $notificationData);
                OW::getEventManager()->trigger($event);
            }
        }
    }

    /**
     * @param int $sectionId
     * @param int $newItems
     * @param int $oldVersionId
     * @return array
     */
    public function findItemChanged($sectionId, $newItems, $previousVersion)
    {
        if ($previousVersion == 0) {
            //All items are new.
            return $newItems;
        }
        $oldItems = $this->getItemsUsingVersion($previousVersion, $sectionId);
        $changedItems = array();
        foreach ($newItems as $key => $newItem) {
            $change = true;
            foreach ($oldItems as $key => $oldItem) {
                if ($oldItem->description == $newItem->description) {
                    $change = false;
                }
            }
            if ($change) {
                $changedItems[] = $newItem;
            }
        }
        return $changedItems;
    }

    /**
     * @param int $sectionId
     * @param int $itemChanged
     * @return string
     */
    public function getEmailContent($sectionId, $itemChanged)
    {
        $html = "<p>" . OW::getLanguage()->text('iisterms', 'email_html_content', array('value' => OW::getRouter()->urlForRoute('iisterms.index.section-id', array('sectionId' => $sectionId)))) . "</p>";
        $html .= "<table>";
        foreach ($itemChanged as $key => $item) {
            if ($item->header) {
                $html .= "<tr><td><h1>" . $item->header . "</h1></td></tr>";
            }
            $html .= "<tr><td>" . $item->description . "</td></tr>";
        }
        $html .= "</table>";

        return $html;
    }

    /**
     * @param int $sectionId
     * @param int $header
     * @param int $description
     * @param int $use
     * @param int $notification
     * @param string $email
     * @return IISTERMS_BOL_Item
     */
    public function addItem($sectionId, $header, $description, $use, $notification, $email)
    {
        if ($use == null) {
            $use = false;
        }
        if ($notification == null) {
            $notification = false;
        }
        if ($email == null) {
            $email = false;
        }
        $item = new IISTERMS_BOL_Item();
        $item->langId = OW::getLanguage()->getInstance()->getCurrentId();
        $item->sectionId = $sectionId;
        $item->header = $header;
        $item->description = $description;
        $item->use = $use;
        $item->order = $this->getMaxOrder($use, $sectionId) + 1;
        $item->notification = $notification;
        $item->email = $email;
        $this->itemDao->save($item);
        return $item;
    }

    /**
     *
     * @param int $sectionId
     * @param int $header
     * @param int $description
     * @param int $langId
     * @return IISTERMS_BOL_Item
     */
    public function addDefault($sectionId, $header, $description, $langId)
    {
        $item = new IISTERMS_BOL_Item();
        $item->langId = $langId;
        $item->sectionId = $sectionId;
        $item->header = $header;
        $item->description = $description;
        $item->use = true;
        $item->order = $this->getMaxOrder(true, $sectionId) + 1;
        $item->notification = true;
        $item->email = true;
        $this->itemDao->save($item);
    }


    /***
     * @param $sectionId
     * @param $header
     * @param $description
     * @param $langId
     * @return mixed
     */
    public function getItem($sectionId, $header, $description, $langId)
    {
        return $this->itemDao->getItem($sectionId, $header, $description, $langId);
    }

    public function importingDefaultItems()
    {
        if (!OW::getConfig()->getValue('iisterms', 'importDefaultItem')) {
            OW::getConfig()->saveConfig('iisterms', 'importDefaultItem', true);
            $xml = simplexml_load_file(OW::getPluginManager()->getPlugin('iisterms')->getStaticDir() . 'xml'.DIRECTORY_SEPARATOR.'defaultItems.xml');
            $sectionsXML = $xml->xpath("/sections");
            $sectionXML = $sectionsXML[0]->xpath('child::section');
            foreach ($sectionXML as $section) {
                $sectionId = (int)$section->attributes()->sectionId;
                $allLangsXml = $section->xpath("langs");
                $langsXml = $allLangsXml[0]->xpath('child::lang');
                foreach ($langsXml as $langXml) {
                    $lang_tag = (string)$langXml->attributes()->name;
                    $lang = BOL_LanguageService::getInstance()->findByTag($lang_tag);
                    if ($lang != null) {
                        $langId = $lang->getId();
                        $items = $langXml[0]->xpath('child::item');
                        foreach ($items as $item) {
                            if($this->getItem($sectionId, (string)$item->header, (string)$item->description, $langId) == null) {
                                $this->addDefault($sectionId, (string)$item->header, (string)$item->description, $langId);
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     *
     * @param int $id
     * @param int $header
     * @param int $description
     * @param int $use
     * @param int $notification
     * @param string $email
     * @return IISTERMS_BOL_Item
     */
    public function editItem($id, $header, $description, $use, $notification, $email)
    {
        $item = $this->getItemById($id);
        if ($item == null) {
            return;
        }
        if ($use == null) {
            $use = false;
        }
        if ($notification == null) {
            $notification = false;
        }
        if ($email == null) {
            $email = false;
        }
        $item->header = $header;
        $item->description = $description;
        $item->use = $use;
        $item->notification = $notification;
        $item->email = $email;
        $this->itemDao->save($item);
        return $item;
    }


    /**
     * @param int $use
     * @param int $sectionId
     * @return int
     */
    public function getMaxOrder($use, $sectionId)
    {
        $maxOrder = $this->itemDao->getMaxOrder($use, $sectionId);
        if ($maxOrder == null) {
            $maxOrder = 0;
        }
        return $maxOrder;
    }

    /**
     * @param int $sectionId
     * @return int
     */
    public function getMaxVersion($sectionId)
    {
        $maxVersion = $this->itemVersionDao->getMaxVersion($sectionId);
        if ($maxVersion == null) {
            $maxVersion = 0;
        }
        return $maxVersion;
    }


    /**
     * @param int $use
     * @param int $sectionId
     * @return array
     */
    public function getItemsUsingStatus($use, $sectionId)
    {
        return $this->itemDao->getItemsUsingStatus($use, $sectionId);
    }

    /**
     * @param int $version
     * @param int $sectionId
     * @return array
     */
    public function getItemsUsingVersion($version, $sectionID)
    {
        return $this->itemVersionDao->getItemsUsingVersion($version, $sectionID);
    }

    /**
     * @param int $sectionId
     * @return array
     */
    public function getItemsUsingMaxVersion($sectionID)
    {
        $maxVersion = $this->getMaxVersion($sectionID);
        return $this->itemVersionDao->getItemsUsingVersion($maxVersion, $sectionID);
    }

    /**
     * @param int $sectionId
     * @return array
     */
    public function getItemsAndVersions($sectionID)
    {
        return $this->itemVersionDao->getItems($sectionID);
    }

    public function saveItem($item)
    {
        $this->itemDao->save($item);
    }

    public function saveItemVersion($item)
    {
        $this->itemVersionDao->save($item);
    }

    /**
     * @return IISTERMS_BOL_Item
     */
    public function getItemById($id)
    {
        return $this->itemDao->getItemById($id);
    }

    /**
     *
     * @param int $id
     * @return IISTERMS_BOL_Item
     */
    public function deleteItem($id)
    {
        $item = $this->getItemById($id);
        $this->itemDao->deleteById($id);
        return $item;
    }

    /**
     *
     * @param int $sectionId
     * @param int $version
     */
    public function deleteVersion($sectionId, $version)
    {
        $this->itemVersionDao->deleteVersion($sectionId, $version);
    }

    /**
     * @param $sectionId
     */
    public function deleteItemsBySectionId($sectionId)
    {
        $this->itemDao->deleteItemsBySectionId($sectionId);
    }

    /**
     *
     * @param int $id
     * @return IISTERMS_BOL_Item
     */
    public function deactivateItem($id)
    {
        $item = $this->itemDao->getItemById($id);
        $item->use = false;
        $this->itemDao->save($item);
        return $item;
    }

    /**
     *
     * @param int $id
     * @return IISTERMS_BOL_Item
     */
    public function activateItem($id)
    {
        $item = $this->itemDao->getItemById($id);
        $item->use = true;
        $this->itemDao->save($item);
        return $item;
    }

    /**
     *
     * @param int $id
     */
    public function deactivateSection($sectionId)
    {
        OW::getConfig()->saveConfig('iisterms', 'terms' . $sectionId, false);
    }

    /**
     *
     * @param int $id
     */
    public function activateSection($sectionId)
    {
        OW::getConfig()->saveConfig('iisterms', 'terms' . $sectionId, true);
    }


    /**
     *
     * @param int $sectionId
     * @return array
     */
    public function getAllItemSorted($sectionId)
    {
        return $this->itemDao->getAllItemSorted($sectionId);
    }

    /**
     * @param int $id
     * @param int $sectionId
     * @return Form
     */
    public function getItemForm($id = null, $sectionId = null)
    {
        $item = null;
        $formName = 'add-item';
        $submitLabel = 'add';
        $actionRoute = OW::getRouter()->urlFor('IISTERMS_CTRL_Admin', 'addItem');

        if ($id != null) {
            $item = $this->getItemById($id);
            $formName = 'edit-item';
            $submitLabel = 'edit';
            $actionRoute = OW::getRouter()->urlFor('IISTERMS_CTRL_Admin', 'editItem');
        }

        $form = new Form($formName);
        $form->setAction($actionRoute);

        if ($item != null) {
            $idField = new HiddenField('id');
            $idField->setValue($item->id);
            $form->addElement($idField);
        }

        $sectionIdField = new HiddenField('sectionId');
        if ($item != null) {
            $sectionIdField->setValue($item->sectionId);
        } else {
            $sectionIdField->setValue($sectionId);
        }
        $form->addElement($sectionIdField);

        $header = new TextField('header');
        $header->setRequired(false);
        $header->setLabel(OW::getLanguage()->text('iisterms', 'header_label'));
        $header->setHasInvitation(false);
        if ($item != null) {
            $header->setValue($item->header);
        }
        $form->addElement($header);

        $description = new WysiwygTextarea('description');
        $description->setRequired(true);
        $description->setLabel(OW::getLanguage()->text('iisterms', 'description_label'));
        $description->setHasInvitation(false);
        if ($item != null) {
            $description->setValue($item->description);
        }
        $form->addElement($description);

        $use = new CheckboxField('use');
        $use->setLabel(OW::getLanguage()->text('iisterms', 'active_label'));
        if ($item == null) {
            $use->setValue(true);
        } else {
            $use->setValue($item->use);
        }
        $form->addElement($use);

        $notification = new CheckboxField('notification');
        $notification->setLabel(OW::getLanguage()->text('iisterms', 'notification_on_changing_label'));
        if ($item == null) {
            $notification->setValue(true);
        } else {
            $notification->setValue($item->notification);
        }
        $form->addElement($notification);

        $email = new CheckboxField('email');
        $email->setLabel(OW::getLanguage()->text('iisterms', 'email_on_changing_label'));
        if ($item == null) {
            $email->setValue(true);
        } else {
            $email->setValue($item->email);
        }
        $form->addElement($email);

        $submit = new Submit('submit', 'button');
        $submit->setValue(OW::getLanguage()->text('iisterms', $submitLabel));
        $form->addElement($submit);

        return $form;
    }

    /**
     *
     * @return array
     */
    public function getAdminSections($sectionId)
    {
        $sections = array();

        for ($i = 1; $i <= 5; $i++) {
            $sections[] = array(
                'sectionId' => $i,
                'active' => $sectionId == $i ? true : false,
                'url' => OW::getRouter()->urlForRoute('iisterms.admin.section-id', array('sectionId' => $i)),
                'label' => $this->getPageHeaderLabel($i)
            );
        }
        return $sections;
    }

    public function getFirstFilledSection()
    {
        for ($i = 1; $i <= 5; $i++) {
            if (OW::getConfig()->getValue('iisterms', 'terms' . $i) && !empty($this->getItemsUsingMaxVersion($i))) {
                return $i;
            }
        }
        return -1;
    }

    /**
     *
     * @return array
     */
    public function getClientSections($sectionId)
    {
        $sections = array();

        for ($i = 1; $i <= 5; $i++) {
            if (OW::getConfig()->getValue('iisterms', 'terms' . $i)) {
                $sections[] = array(
                    'sectionId' => $i,
                    'active' => $sectionId == $i ? true : false,
                    'url' => OW::getRouter()->urlForRoute('iisterms.index.section-id', array('sectionId' => $i)),
                    'label' => $this->getPageHeaderLabel($i)
                );
            }
        }
        return $sections;
    }

    public function getPageHeaderLabel($sectionId)
    {
        if ($sectionId == 1) {
            return OW::getLanguage()->text('iisterms', 'terms_of_service_page');
        } else if ($sectionId == 2) {
            return OW::getLanguage()->text('iisterms', 'privacy_policy_page');
        } else if ($sectionId == 3) {
            return OW::getLanguage()->text('iisterms', 'FAQ_page');
        } else if ($sectionId == 4) {
            return OW::getLanguage()->text('iisterms', 'default1');
        } else if ($sectionId == 5) {
            return OW::getLanguage()->text('iisterms', 'default2');
        }
    }

    /**
     * @param OW_Event $event
     */
    function on_render_join_form( OW_Event  $event )
    {
        if(OW::getConfig()->getValue('iisterms','showOnRegistrationForm')) {
            $param = $event->getParams();
            if ($param['joinForm']) {
                if ($param['joinForm']->getElement('termOfUse')) {
                    $param['joinForm']->deleteElement('termOfUse');
                }
                $param['controller']->assign('display_terms_of_use', true);
                $termOfUse = new CheckboxField('termOfUse');
                $termOfUse->setLabel(OW::getLanguage()->text('iisterms', 'agree_with_terms', array('value' => OW::getRouter()->urlForRoute('iisterms.index'))));
                $termOfUse->setRequired();
                $param['joinForm']->addElement($termOfUse);
            }
        }
    }

    /**
     * Adding section to notifications settings page
     *
     * @param BASE_CLASS_EventCollector $event
     */
    function on_notify_actions( BASE_CLASS_EventCollector $event )
    {
        $event->add(array(
            'section' => 'iistrms',
            'action' => 'terms',
            'description' => OW::getLanguage()->text('iisterms', 'send_notification_description'),
            'selected' => true,
            'sectionLabel' => OW::getLanguage()->text('iisterms', 'bottom_menu_item'),
            'sectionIcon' => 'ow_ic_write'
        ));
    }
}