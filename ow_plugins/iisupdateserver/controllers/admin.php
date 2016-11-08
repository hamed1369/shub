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
 * update server admin action controller
 *
 * @author Mohammad Aghaabbasloo
 * @package ow.ow_plugins.iisupdateserver.controllers
 * @since 1.0
 */
class IISUPDATESERVER_CTRL_Admin extends ADMIN_CTRL_Abstract
{
    private $ignorePluginsKeyList = array('iisupdateserver', 'iisdemo', 'iisevaluation', 'iisrules','iispiwik', 'iisheaderimg');
    private $ignoreThemesKeyList = array('iistheme1_maher');
    /**
     * @param array $params
     */
    public function index(array $params = array())
    {
        $language = OW::getLanguage();
        $this->setPageHeading($language->text('iisupdateserver', 'admin_page_heading'));
        $this->setPageTitle($language->text('iisupdateserver', 'admin_page_title'));
        $service = IISUPDATESERVER_BOL_Service::getInstance();

        $config = OW::getConfig();
        $form = new Form('setting');
        $form->setMethod(Form::METHOD_POST);
        $form->setAction(OW::getRouter()->urlForRoute('iisupdateserver.admin'));

        $prefixDownloadUrl = new TextField('prefix_download_path');
        $prefixDownloadUrl->setValue($config->getValue('iisupdateserver', 'prefix_download_path'));
        $prefixDownloadUrl->setRequired();
        $prefixDownloadUrl->setLabel(OW::getLanguage()->text('iisupdateserver', 'prefix_download_path_label'));
        $prefixDownloadUrl->setHasInvitation(false);
        $form->addElement($prefixDownloadUrl);

        $submitField = new Submit('submit');
        $form->addElement($submitField);

        $this->addForm($form);

        if (OW::getRequest()->isPost() && $form->isValid($_POST)) {
            $config->saveConfig('iisupdateserver', 'prefix_download_path', $form->getElement('prefix_download_path')->getValue());
            OW::getFeedback()->info(OW::getLanguage()->text('iisupdateserver', 'saved_successfully'));
            $this->redirect(OW::getRouter()->urlForRoute('iisupdateserver.admin'));
        }

        $this->assign('update_version_url', OW::getRouter()->urlForRoute('server.check_all_for_update'));
        $this->assign('delete_all_versions_url', OW::getRouter()->urlForRoute('server.delete_all_versions'));
        $this->assign('add_item', OW::getRouter()->urlForRoute('iisupdateserver.admin.add.item'));
        $this->assign('plugin_items', OW::getRouter()->urlForRoute('iisupdateserver.admin.items', array('type' => 'plugin')));
        $this->assign('theme_items', OW::getRouter()->urlForRoute('iisupdateserver.admin.items', array('type' => 'theme')));
        $this->assign('sections',$service->getAdminSections($service->SETTINGS_SECTION));

        $cssDir = OW::getPluginManager()->getPlugin("iisupdateserver")->getStaticCssUrl();
        OW::getDocument()->addStyleSheet($cssDir . "iisupdateserver.css");
    }

    /**
     * @param array $params
     */
    public function addItem(array $params = array())
    {
        $service = IISUPDATESERVER_BOL_Service::getInstance();

        $form = $service->getItemForm(OW::getRouter()->urlForRoute('iisupdateserver.admin.add.item'));
        $this->addForm($form);

        if (OW::getRequest()->isPost()) {
            if ($form->isValid($_POST)) {
                $item = $service->getItemByKey($_REQUEST['key']);
                $imageName = $service->saveFile('image');
                if($imageName == null && ($item == null || ($item != null && $item->image==null))){
                    OW::getFeedback()->error(OW::getLanguage()->text('base', 'upload_file_no_file_error'));
                }else {
                    if($item != null && $item->image!=null && $imageName == null){
                        $imageName = $item->image;
                    }
                    $item = $service->addItem($_REQUEST['name'], $_REQUEST['description'], $_REQUEST['key'], $imageName, $_REQUEST['type'], $_REQUEST['guidelineurl']);
                    if ($item == null) {
                        OW::getFeedback()->error(OW::getLanguage()->text('iisupdateserver', 'item_not_exist'));
                        $this->redirect(OW::getRouter()->urlForRoute('iisupdateserver.admin.add.item'));
                    } else {
                        OW::getFeedback()->info(OW::getLanguage()->text('iisupdateserver', 'saved_successfully'));
                        $this->redirect(OW::getRouter()->urlForRoute('iisupdateserver.admin.items', array('type' => $item->type)));
                    }
                }
            }
        }
        $this->assign('sections',$service->getAdminSections($service->ADD_ITEM_SECTION));
    }

    public function deleteItemByNameAndBuildNumber(array $params = array())
    {
        $service = IISUPDATESERVER_BOL_Service::getInstance();
        $this->setPageTitle(OW::getLanguage()->text('iisupdateserver', 'admin_delete_item_title'));
        $this->setPageHeading(OW::getLanguage()->text('iisupdateserver', 'admin_delete_item_title'));
        $form = $service->getDeleteItemForm();
        $this->addForm($form);

        if (OW::getRequest()->isPost()) {
            if ($form->isValid($_POST)) {
                $item = $service->getItemByKeyAndBuildNumber($_REQUEST['key'],$_REQUEST['build']);
                $result= $service->deleteItemByIDAndBuildNumAndKey($item,$_REQUEST['build'],$_REQUEST['key']);
                if ($result) {
                    OW::getFeedback()->info(OW::getLanguage()->text('iisupdateserver', 'item_deleted_successfully'));
                } else {
                    OW::getFeedback()->error(OW::getLanguage()->text('iisupdateserver', 'item_not_exist'));
                }
            }
        }
        $this->assign('sections',$service->getAdminSections($service->DELETE_ITEM_SECTION));
    }

    public function checkUpdateItemAvailableByName(array $params = array())
    {
        $service = IISUPDATESERVER_BOL_Service::getInstance();
        $this->setPageTitle(OW::getLanguage()->text('iisupdateserver', 'admin_check_item_title'));
        $this->setPageHeading(OW::getLanguage()->text('iisupdateserver', 'admin_check_item_title'));
        $form = $service->getCheckItemForm();
        $this->addForm($form);

        if (OW::getRequest()->isPost()) {
            if ($form->isValid($_POST)) {
                $findKey = false;
                $rootZipDirectory = Ow::getPluginManager()->getPlugin('iisupdateserver')->getPluginFilesDir();

                $xmlPlugins = BOL_PluginService::getInstance()->getPluginsXmlInfo();
                foreach ($xmlPlugins as $plugin) {
                    if (!in_array($plugin['key'], $this->ignorePluginsKeyList) && strcmp($plugin['key'], $_REQUEST['key']) == 0) {
                        $findKey = true;
                        if (!file_exists($rootZipDirectory . 'plugins')) {
                            mkdir($rootZipDirectory . 'plugins', 0777, true);
                        }
                        $dir = $plugin['path'];
                        IISUPDATESERVER_BOL_Service::getInstance()->checkPluginForUpdate($plugin['key'], $plugin['build'], $dir, $rootZipDirectory);
                        OW::getFeedback()->info(OW::getLanguage()->text('iisupdateserver', 'items_checked_successfully'));
                    }
                }

                $themes = UTIL_File::findFiles(OW_DIR_THEME, array('xml'), 1);
                foreach ($themes as $themeXml) {
                    if ( basename($themeXml) === BOL_ThemeService::THEME_XML) {
                        $theme = simplexml_load_file($themeXml);
                        if(!in_array((string) $theme->key, $this->ignoreThemesKeyList)&& strcmp($theme->key, $_REQUEST['key']) == 0) {
                            $findKey = true;
                            if (!file_exists($rootZipDirectory . 'themes')) {
                                mkdir($rootZipDirectory . 'themes', 0777, true);
                            }
                            IISUPDATESERVER_BOL_Service::getInstance()->checkThemeForUpdate((string)$theme->key, (string)$theme->build, $rootZipDirectory);
                            OW::getFeedback()->info(OW::getLanguage()->text('iisupdateserver', 'items_checked_successfully'));
                            break;
                        }
                    }
                }

                if(strcmp('core', $_REQUEST['key']) == 0) {
                    $findKey = true;
                    IISUPDATESERVER_BOL_Service::getInstance()->checkCoreForUpdate($rootZipDirectory);
                    OW::getFeedback()->info(OW::getLanguage()->text('iisupdateserver', 'items_checked_successfully'));
                }

                if(!$findKey) {
                    OW::getFeedback()->error(OW::getLanguage()->text('iisupdateserver', 'item_not_exist'));
                }
            }
        }
        $this->assign('sections',$service->getAdminSections($service->CHECK_ITEM_SECTION));
    }

    /**
     * @param array $params
     */
    public function ajaxSaveItemsOrder(array $params = array()){
        if (!empty($_POST['items']) && is_array($_POST['items'])) {
            $service = IISUPDATESERVER_BOL_Service::getInstance();
            foreach ($_POST['items'] as $index => $id) {
                $item = $service->getItemById($id);
                $item->order = $index + 1;
                $service->saveItem($item);
            }
        }
    }

    /**
     * @param array $params
     */
    public function editItem(array $params = array())
    {
        $service = IISUPDATESERVER_BOL_Service::getInstance();

        if(!isset($params['id'])){
            $this->redirect(OW::getRouter()->urlForRoute('iisupdateserver.admin.add.item'));
        }else{
            $item = $service->getItemById($params['id']);
            if($item==null){
                $this->redirect(OW::getRouter()->urlForRoute('iisupdateserver.admin.add.item'));
            }else{
                $form = $service->getItemForm(OW::getRouter()->urlForRoute('iisupdateserver.admin.add.item'), $item->name, $item->description, $item->key, $item->type, $item->guidelineurl);
                $this->addForm($form);
                if(isset($item->image)){
                    $this->assign('oldIconSrc', Ow::getPluginManager()->getPlugin('iisupdateserver')->getUserFilesUrl() . $item->image);
                }
                $this->assign('returnUrl', OW::getRouter()->urlForRoute('iisupdateserver.admin'));
            }
        }

    }


    /**
     * @param array $params
     */
    public function deleteItem(array $params = array())
    {
        if(isset($params['id'])){
            $service = IISUPDATESERVER_BOL_Service::getInstance();
            $item = $service->deleteItem($params['id']);
            OW::getFeedback()->info(OW::getLanguage()->text('iisupdateserver', 'removed_successfully'));
        }


        $this->redirect(OW::getRouter()->urlForRoute('iisupdateserver.admin.items', array('type' => $item->type)));
    }

    /**
     * @param array $params
     */
    public function items(array $params = array())
    {

        $service = IISUPDATESERVER_BOL_Service::getInstance();
        $items = array();
        $itemsInformation = array();
        if(isset($params['type'])){
            $items = $service->getItems($params['type']);
            $this->assign('typeLabel', OW::getLanguage()->text('iisupdateserver', $params['type'].'s'));
        }else{
            $items = $service->getItems();
        }

        foreach($items as $item){
            $itemInformation = array();
            $itemInformation['id'] = $item->id;
            $itemInformation['name'] = $item->name;
            $itemInformation['deleteUrl'] = OW::getRouter()->urlForRoute('iisupdateserver.admin.delete.item', array('id' => $item->id));
            $itemInformation['editUrl'] = OW::getRouter()->urlForRoute('iisupdateserver.admin.edit.item', array('id' => $item->id));
            $itemInformation['image'] = Ow::getPluginManager()->getPlugin('iisupdateserver')->getUserFilesUrl() . $item->image;
            $itemsInformation[] = $itemInformation;
        }
        $this->assign('items', $itemsInformation);
        $this->assign('sections',$service->getAdminSections($params['type']=='plugin'?$service->PLUGIN_ITEMS_SECTION:$service->THEME_ITEMS_SECTION));

        $cssDir = OW::getPluginManager()->getPlugin("iisupdateserver")->getStaticCssUrl();
        OW::getDocument()->addStyleSheet($cssDir . "iisupdateserver.css");
    }
}