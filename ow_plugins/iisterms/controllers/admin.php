<?php

class IISTERMS_CTRL_Admin extends ADMIN_CTRL_Abstract
{

    public function index($params)
    {
        $language = OW::getLanguage();
        $this->setPageHeading($language->text('iisterms', 'admin_page_heading'));
        $this->setPageTitle($language->text('iisterms', 'admin_page_title'));
        $sectionId = 1;
        if(isset($params['sectionId'])){
            $sectionId = $params['sectionId'];
        }

        $service = $this->getService();

        $addItemCMP = new IISTERMS_CMP_AddItem($sectionId);
        $this->addComponent('addItemCMP',$addItemCMP);

        $allItems = $service->getAllItemSorted($sectionId);

        $activeItems = array();
        $inactiveItems = array();
        $imageDir = Ow::getPluginManager()->getPlugin('iisterms')->getStaticUrl().'images/';

        foreach ( $allItems as $item )
        {
            if($item->use){
                $activeItems[] = array(
                    'langId' => $item->langId,
                    'header' => $item->header,
                    'description' => $item->description,
                    'use' => $item->use,
                    'id' => $item->id,
                    'deleteUrl' => "if(confirm('".OW::getLanguage()->text('iisterms','delete_item_warning')."')){location.href='".OW::getRouter()->urlForRoute('iisterms.admin.delete-item', array('id'=>$item->id))."';}",
                    'activateUrl' => OW::getRouter()->urlForRoute('iisterms.admin.activate-item', array('id'=>$item->id)),
                    'deactivateUrl' => OW::getRouter()->urlForRoute('iisterms.admin.deactivate-item', array('id'=>$item->id)),
                    'editUrl' => "OW.ajaxFloatBox('IISTERMS_CMP_EditItemFloatBox', {id: ".$item->id."} , {iconClass: 'ow_ic_edit', title: '".OW::getLanguage()->text('iisterms', 'edit_item_page_title')."'})",
                    'notification' => (bool) $item->notification,
                    'email' => (bool) $item->email
                );
            }else{
                $inactiveItems[] = array(
                    'langId' => $item->langId,
                    'header' => $item->header,
                    'description' => $item->description,
                    'use' => $item->use,
                    'id' => $item->id,
                    'deleteUrl' => "if(confirm('".OW::getLanguage()->text('iisterms','delete_item_warning')."')){location.href='".OW::getRouter()->urlForRoute('iisterms.admin.delete-item', array('id'=>$item->id))."';}",
                    'activateUrl' => OW::getRouter()->urlForRoute('iisterms.admin.activate-item', array('id'=>$item->id)),
                    'deactivateUrl' => OW::getRouter()->urlForRoute('iisterms.admin.deactivate-item', array('id'=>$item->id)),
                    'editUrl' => "OW.ajaxFloatBox('IISTERMS_CMP_EditItemFloatBox', {id: ".$item->id."} , {iconClass: 'ow_ic_edit', title: '".OW::getLanguage()->text('iisterms', 'edit_item_page_title')."'})",
                    'notification' => (bool) $item->notification,
                    'email' => (bool) $item->email
                );
            }
        }

        $versionMarked = array();
        $versions = array();
        $maxVersion = $service->getMaxVersion($sectionId);
        $itemsVersioned = $service->getItemsAndVersions($sectionId);

        foreach ($itemsVersioned as $item) {
            if (!in_array($item->version, $versionMarked)) {
                $versionMarked[] = $item->version;

//                $time_temp = date_create();
//                date_timestamp_set($time_temp, $item->time);
//                $time_temp = date_format($time_temp, 'Y-m-d H:i:s');
                $formattedDate = UTIL_DateTime::formatSimpleDate($item->time);
                $current = false;
                if ($item->version == $maxVersion) {
                    $current = true;
                }
                $versions[] = array(
                    'deleteVersionUrl' => "if(confirm('".OW::getLanguage()->text('iisterms','delete_section_warning')."')){location.href='".OW::getRouter()->urlForRoute('iisterms.admin.delete-version', array('sectionId'=>$sectionId, 'version' => $item->version))."';}",
                    'time' => $formattedDate,
                    'url' => OW::getRouter()->urlForRoute('iisterms.comparison-archive', array('sectionId' => $sectionId, 'version' => $item->version)),
                    'current' => $current
                );
            }
        }

        $this->assign("versions", $versions);


        if(OW::getConfig()->getValue('iisterms', 'terms'.$sectionId)){
            $this->assign('sectionStatusChangeUrl',OW::getRouter()->urlForRoute('iisterms.admin.deactivate-section', array('sectionId'=>$sectionId)));
            $this->assign('sectionStatusChangeLabel',OW::getLanguage()->text('iisterms','deactivate_section_button'));
            $this->assign('sectionStatus',OW::getLanguage()->text('iisterms','section_is_active'));
        }else{
            $this->assign('sectionStatusChangeUrl',OW::getRouter()->urlForRoute('iisterms.admin.activate-section', array('sectionId'=>$sectionId)));
            $this->assign('sectionStatusChangeLabel',OW::getLanguage()->text('iisterms','activate_section_button'));
            $this->assign('sectionStatus',OW::getLanguage()->text('iisterms','section_is_inactive'));
        }

        $this->assign('notificationImageSrc', $imageDir . 'notification.png');
        $this->assign('emailImageSrc', $imageDir . 'email.png');
        $this->assign('addVersionUrl', "javascript:if(confirm('".addslashes(OW::getLanguage()->text('iisterms','add_version_warning'))."')){location.href='".OW::getRouter()->urlForRoute('iisterms.admin.add-version', array('sectionId'=>$sectionId))."';}");
        $this->assign('addVersionLabel', OW::getLanguage()->text('iisterms','add_version_label'));
        $this->assign('number_of_exist_version', OW::getLanguage()->text('iisterms','number_of_exist_version',array('value' => $maxVersion)));
        $this->assign('sections', $service->getAdminSections($sectionId));
        $this->assign('activeItems',$activeItems);
        $this->assign('inactiveItems',$inactiveItems);

        if(OW::getConfig()->getValue('iisterms', 'showOnRegistrationForm')){
            $this->assign('showOnJoinFormStatusDescription',  OW::getLanguage()->text('iisterms','terms_show_on_join_form_enable'));
            $this->assign('showOnJoinFormStatus',  OW::getLanguage()->text('iisterms','terms_show_in_join_form_set_disable', array('value' => OW::getRouter()->urlForRoute('iisterms.admin.deactivate-terms-on-join', array('sectionId'=>$sectionId)))));
            $this->assign('showOnJoinFormStatusClass',  'ow_green');
        }else{
            $this->assign('showOnJoinFormStatusDescription',  OW::getLanguage()->text('iisterms','terms_show_on_join_form_disable'));
            $this->assign('showOnJoinFormStatus',  OW::getLanguage()->text('iisterms','terms_show_in_join_form_set_enable', array('value' => OW::getRouter()->urlForRoute('iisterms.admin.activate-terms-on-join', array('sectionId'=>$sectionId)))));
            $this->assign('showOnJoinFormStatusClass',  'ow_red');
        }

        $cssDir = OW::getPluginManager()->getPlugin("iisterms")->getStaticCssUrl();
        OW::getDocument()->addStyleSheet($cssDir . "save-ajax-order-item.css");
    }

    public function activateTermsOnJoin($params){
        OW::getConfig()->saveConfig('iisterms', 'showOnRegistrationForm', true);
        OW::getFeedback()->info(OW::getLanguage()->text('iisterms', 'terms_show_in_join_form'));
        $this->redirect( OW::getRouter()->urlForRoute('iisterms.admin.section-id', array('sectionId'=>$params['sectionId'])) );
    }

    public function deactivateTermsOnJoin($params){
        OW::getConfig()->saveConfig('iisterms', 'showOnRegistrationForm', false);
        OW::getFeedback()->info(OW::getLanguage()->text('iisterms', 'terms_hide_in_join_form'));
        $this->redirect( OW::getRouter()->urlForRoute('iisterms.admin.section-id', array('sectionId'=>$params['sectionId'])) );
    }

    public function getService(){
        return IISTERMS_BOL_Service::getInstance();
    }

    public function deleteItem($params)
    {
        $item = $this->getService()->deleteItem($params['id']);
        OW::getFeedback()->info(OW::getLanguage()->text('iisterms', 'database_record_deleted'));
        $this->redirect( OW::getRouter()->urlForRoute('iisterms.admin.section-id', array('sectionId'=>$item->sectionId)) );
    }

    public function deleteVersion($params)
    {
        $this->getService()->deleteVersion($params['sectionId'], $params['version']);
        OW::getFeedback()->info(OW::getLanguage()->text('iisterms', 'database_record_deleted'));
        $this->redirect( OW::getRouter()->urlForRoute('iisterms.admin.section-id', array('sectionId'=>$params['sectionId'])) );
    }

    public function deactivateItem($params)
    {
        $item = $this->getService()->deactivateItem($params['id']);
        OW::getFeedback()->info(OW::getLanguage()->text('iisterms', 'database_record_deactivate_item'));
        $this->redirect( OW::getRouter()->urlForRoute('iisterms.admin.section-id', array('sectionId'=>$item->sectionId)) );
    }

    public function activateItem($params)
    {
        $item = $this->getService()->activateItem($params['id']);
        OW::getFeedback()->info(OW::getLanguage()->text('iisterms', 'database_record_activate_item'));
        $this->redirect( OW::getRouter()->urlForRoute('iisterms.admin.section-id', array('sectionId'=>$item->sectionId)) );
    }

    public function deactivateSection($params)
    {
        $this->getService()->deactivateSection($params['sectionId']);
        OW::getFeedback()->info(OW::getLanguage()->text('iisterms', 'database_record_deactivate_section'));
        $this->redirect( OW::getRouter()->urlForRoute('iisterms.admin.section-id', array('sectionId'=>$params['sectionId'])) );
    }

    public function activateSection($params)
    {
        $this->getService()->activateSection($params['sectionId']);
        OW::getFeedback()->info(OW::getLanguage()->text('iisterms', 'database_record_activate_section'));
        $this->redirect( OW::getRouter()->urlForRoute('iisterms.admin.section-id', array('sectionId'=>$params['sectionId'])) );
    }

    public function addItem($params)
    {
        $form = $this->getService()->getItemForm();
        if ( $form->isValid($_POST) ) {
            $item = $this->getService()->addItem($form->getElement('sectionId')->getValue(),$form->getElement('header')->getValue(),$form->getElement('description')->getValue(), $form->getElement('use')->getValue(), $form->getElement('notification')->getValue(), $form->getElement('email')->getValue());

            OW::getFeedback()->info(OW::getLanguage()->text('iisterms', 'database_record_add'));
            $this->redirect(OW::getRouter()->urlForRoute('iisterms.admin.section-id', array('sectionId'=>$item->sectionId)));
        }else{
            $this->redirect(OW::getRouter()->urlForRoute('iisterms.admin'));
        }
    }

    public function addVersion($params)
    {
        $sectionId = $params['sectionId'];
        $items = $this->getService()->getItemsUsingStatus(true,$sectionId);
        if(empty($items)){
            OW::getFeedback()->error(OW::getLanguage()->text('iisterms', 'add_version_without_items'));
            $this->redirect(OW::getRouter()->urlForRoute('iisterms.admin.section-id', array('sectionId' => $sectionId)));
        }else{
            $this->getService()->addVersion($sectionId, $items, true);
            OW::getFeedback()->info(OW::getLanguage()->text('iisterms', 'database_record_add_version'));
            $this->redirect(OW::getRouter()->urlForRoute('iisterms.admin.section-id', array('sectionId' => $sectionId)));
        }
    }

    public function editItem()
    {
        $form = $this->getService()->getItemForm($_POST['id']);
        if ( $form->isValid($_POST) ) {
            $item = $this->getService()->editItem($form->getElement('id')->getValue(), $form->getElement('header')->getValue(), $form->getElement('description')->getValue(), $form->getElement('use')->getValue(), $form->getElement('notification')->getValue(), $form->getElement('email')->getValue());

            OW::getFeedback()->info(OW::getLanguage()->text('iisterms', 'database_record_edit'));
            $this->redirect(OW::getRouter()->urlForRoute('iisterms.admin.section-id', array('sectionId'=>$item->sectionId)));
        }else{
            $this->redirect(OW::getRouter()->urlForRoute('iisterms.admin'));
        }
    }

    public function ajaxSaveOrder(){
        if ( !empty($_POST['active']) && is_array($_POST['active']) )
        {
            foreach ( $_POST['active'] as $index => $id )
            {
                $item = $this->getService()->getItemById($id);
                $item->order = $index + 1;
                $item->use = true;
                $this->getService()->saveItem($item);
            }
        }

        if ( !empty($_POST['inactive']) && is_array($_POST['inactive']) )
        {
            foreach ( $_POST['inactive'] as $index => $id )
            {
                $item = $this->getService()->getItemById($id);
                $item->order = $index + 1;
                $item->use = false;
                $this->getService()->saveItem($item);
            }
        }
    }

}