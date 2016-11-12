<?php

/**
 * Copyright (c) 2016, Yaser Alimardany
 * All rights reserved.
 */

/**
 *
 * @author Yaser Alimardany <yaser.alimardany@gmail.com>
 * @package ow_plugins.iisupdateserver.controllers
 * @since 1.0
 */
class IISUPDATESERVER_CTRL_Iisupdateserver extends OW_ActionController
{
    private $service;
    private $ignorePluginsKeyList = array('iisupdateserver', 'iisdemo', 'iisevaluation', 'iisrules','iispiwik', 'iisheaderimg');
    private $ignoreThemesKeyList = array('iistheme1_maher');

    public function __construct()
    {
        parent::__construct();
    }

    public function index( $params = NULL )
    {
    }

    public function platformInfo( $params = NULL )
    {
        $core_information = (array) (simplexml_load_file(OW_DIR_ROOT . 'ow_version.xml'));
        exit(json_encode(array( 'build' => (string) $core_information['build'],
            'version' => (string) $core_information['version'],
            'info' => '',
            'log' => array())));
    }

    public function downloadUpdatePlatform( $params = NULL )
    {
        $core_information = (array) (simplexml_load_file(OW_DIR_ROOT . 'ow_version.xml'));
        IISUPDATESERVER_BOL_Service::getInstance()->addUser('core',(string) $core_information['build']);

        $rootZipDirectory = Ow::getPluginManager()->getPlugin('iisupdateserver')->getPluginFilesDir();
        IISUPDATESERVER_BOL_Service::getInstance()->checkCoreForUpdate($rootZipDirectory);
        $this->downloadZipFile('core.zip', 'core-' . (string) $core_information['build'] . '.zip', 'core' . DS . 'updates' . DS . (string) $core_information['build']);
    }

    public function downloadFullPlatform( $params = NULL )
    {
        $core_information = (array) (simplexml_load_file(OW_DIR_ROOT . 'ow_version.xml'));
        IISUPDATESERVER_BOL_Service::getInstance()->addUser('core',(string) $core_information['build']);

        $rootZipDirectory = Ow::getPluginManager()->getPlugin('iisupdateserver')->getPluginFilesDir();
        IISUPDATESERVER_BOL_Service::getInstance()->checkCoreForUpdate($rootZipDirectory);
        $this->downloadZipFile('core.zip', 'core-' . (string) $core_information['build'] . '.zip', 'core' . DS . 'main' . DS . (string) $core_information['build']);
    }

    public function getItemsUpdateInfo( $params = NULL )
    {
        $items = array();
        $themes = array();
        $returnResult = array();
        $postInformations = json_decode($_POST['info'], true);
        $postedItems = $postInformations['items'];
        if(isset($postInformations['platform']['build'])){
            if ($postInformations['platform']['build'] < OW::getConfig()->getValue('base', 'soft_build')) {
                $returnResult['update']['platform'] = true;
            }
        }

        foreach($postedItems as $item) {
            if($item['type'] == 'plugin') {
                $requestedPlugin = $this->getPlugin($item['key'], $item['developerKey']);
                if ($requestedPlugin != null) {
                    if ($requestedPlugin['build'] > $item['build']) {
                        $items[] = $item;
                    }
                }
            }

            if($item['type'] == 'theme'){
                $requestedTheme = $this->getTheme($item['key'], $item['developerKey']);
                if ($requestedTheme != null) {
                    if($requestedTheme->build > $item['build']){
                        $items[] = $item;
                    }
                }
            }
        }

        $returnResult['update']['items'] = $items;
        exit(json_encode($returnResult));
    }

    public function getItemInfo( $params = NULL )
    {
        header('Content-Type: text/html; charset=utf-8');

        if(!isset($_GET['key']) || !isset($_GET['developerKey'])){
            exit(json_encode(array( 'freeware' => true)));
        }
        $key = $_GET['key'];
        $developerKey = $_GET['developerKey'];

        $requestedPlugin = $this->getPlugin($key, $developerKey);
        $requestedTheme = $this->getTheme($key, $developerKey);

        if($requestedPlugin!=null){
            $json = json_encode(array( 'type' => 'plugin',
                'title' => iconv('UTF-8', 'UTF-8//IGNORE', $requestedPlugin['title']),
                'description' => iconv('UTF-8', 'UTF-8//IGNORE', $requestedPlugin['description']),
                'freeware' => '1',
                'build' => $requestedPlugin['build'],
                'changeLog' => array()), JSON_UNESCAPED_UNICODE);
            exit($json);
        }

        if ($requestedTheme!=null) {
            $json = json_encode(array('type' => 'theme',
                'title' => iconv('UTF-8', 'UTF-8//IGNORE', (string) $requestedTheme->title),
                'description' => iconv('UTF-8', 'UTF-8//IGNORE', (string) $requestedTheme->description),
                'freeware' => '1',
                'build' => (string) $requestedTheme->build,
                'changeLog' => array()), JSON_UNESCAPED_UNICODE);
            exit($json);
        }

        exit(json_encode(array( 'Update Server' => '1')));
    }

    public function getItem( $params = NULL )
    {
        header('Content-Type: text/html; charset=utf-8');
        $emptyResult = '_empty_plugin_or_developer_key_';
        $key = $_GET['key'];
        $developerKey = $_GET['developerKey'];

        IISUPDATESERVER_BOL_Service::getInstance()->addUser($key,$developerKey);
        if(!isset($key) || !isset($developerKey)){
            exit($emptyResult);
        }

        $rootZipDirectory = Ow::getPluginManager()->getPlugin('iisupdateserver')->getPluginFilesDir();
        $requestedPlugin = $this->getPlugin($key, $developerKey);
        $requestedTheme = $this->getTheme($key, $developerKey);

        if($requestedPlugin!=null){
            $dir = $requestedPlugin['path'];
            IISUPDATESERVER_BOL_Service::getInstance()->checkPluginForUpdate($requestedPlugin['key'], $requestedPlugin['build'], $dir, $rootZipDirectory);
            $this->downloadZipFile(IISUPDATESERVER_BOL_Service::getInstance()->getReplacedItemName($requestedPlugin['key']).'.zip', $requestedPlugin['key'] . '-' .$requestedPlugin['build'] . '.zip', 'plugins' . DS . $requestedPlugin['key'] . DS . $requestedPlugin['build']);
        }else if($requestedTheme!=null){
            IISUPDATESERVER_BOL_Service::getInstance()->checkThemeForUpdate((string) $requestedTheme->key, (string)$requestedTheme->build, $rootZipDirectory);
            $this->downloadZipFile(IISUPDATESERVER_BOL_Service::getInstance()->getReplacedItemName((string) $requestedTheme->key).'.zip', (string) $requestedTheme->key . '-' .(string) $requestedTheme->build . '.zip', 'themes' . DS . (string) $requestedTheme->key . DS . (string) $requestedTheme->build);
        }

        exit($emptyResult);
    }

    public function deleteAllVersions(){
        $service = IISUPDATESERVER_BOL_Service::getInstance();
        $service->deleteAllVersions();
        OW::getFeedback()->info(OW::getLanguage()->text('iisupdateserver', 'delete_all_versions_successfully'));
        $this->redirect(OW::getRouter()->urlForRoute('iisupdateserver.admin'));
    }

    /***
     * Checking all plugins,themes and core to generate downloading files.
     */
    public function checkAllForUpdate(){
        $rootZipDirectory = Ow::getPluginManager()->getPlugin('iisupdateserver')->getPluginFilesDir();

        //checking plugins for updating
        if (!file_exists($rootZipDirectory . 'plugins')) {
            mkdir($rootZipDirectory . 'plugins', 0777, true);
        }
        $xmlPlugins = BOL_PluginService::getInstance()->getPluginsXmlInfo();
        foreach ($xmlPlugins as $plugin) {
            if(!in_array($plugin['key'], $this->ignorePluginsKeyList)) {
                $dir = $plugin['path'];
                IISUPDATESERVER_BOL_Service::getInstance()->checkPluginForUpdate($plugin['key'], $plugin['build'], $dir, $rootZipDirectory);
            }
        }


        //checking themes for updating
        if (!file_exists($rootZipDirectory . 'themes')) {
            mkdir($rootZipDirectory . 'themes', 0777, true);
        }
        $themes = UTIL_File::findFiles(OW_DIR_THEME, array('xml'), 1);
        foreach ($themes as $themeXml) {
            if ( basename($themeXml) === BOL_ThemeService::THEME_XML) {
                $theme = simplexml_load_file($themeXml);
                if(!in_array((string) $theme->key, $this->ignoreThemesKeyList)) {
                    IISUPDATESERVER_BOL_Service::getInstance()->checkThemeForUpdate((string)$theme->key, (string)$theme->build, $rootZipDirectory);
                }
            }
        }

        //checking core for updating
        IISUPDATESERVER_BOL_Service::getInstance()->checkCoreForUpdate($rootZipDirectory);

        OW::getFeedback()->info(OW::getLanguage()->text('iisupdateserver', 'all_items_checked'));
        $this->redirect(OW::getRouter()->urlForRoute('iisupdateserver.admin'));
    }

    public function downloadZipFile($zipname, $buildNumber , $type=null){
        $zipPath =  IISUPDATESERVER_BOL_Service::getInstance()->getZipPathByKey(IISUPDATESERVER_BOL_Service::getInstance()->getReplacedItemName($buildNumber), $type);
        header("Content-type: application/zip");
        header("Content-Disposition: attachment; filename=" . $zipname);
        header("Content-length: " . filesize($zipPath));
        header("Pragma: no-cache");
        header("Expires: 0");
        set_time_limit(0);
        ob_clean();
        flush();
        readfile($zipPath);
        exit();
    }

    public function getTheme($key, $developerKey){
        $themes = UTIL_File::findFiles(OW_DIR_THEME, array('xml'), 1);
        foreach ($themes as $themeXml) {
            if ( basename($themeXml) === BOL_ThemeService::THEME_XML ) {
                $theme = simplexml_load_file($themeXml);
                if ((string) $theme->key == $key && (string) $theme->developerKey == $developerKey) {
                    return $theme;
                }
            }
        }

        return null;
    }

    public function getPlugin($key, $developerKey){
        $xmlPlugins = BOL_PluginService::getInstance()->getPluginsXmlInfo();

        foreach ($xmlPlugins as $plugin) {
            if($plugin['key'] == $key && $plugin['developerKey'] == $developerKey){
                return $plugin;
            }
        }

        return null;
    }

    public function updateStaticFiles(){
        if(OW::getUser()->isAuthenticated() && OW::getUser()->isAdmin()){
            IISSecurityProvider::updateStaticFiles();
            OW::getFeedback()->info('Static files updated successfully');
        }
        $this->redirect(OW_URL_HOME);
    }


    public function viewDownloadPage(){
        $service = IISUPDATESERVER_BOL_Service::getInstance();

        $this->assign('download_themes_description', OW::getLanguage()->text('iisupdateserver', 'download_themes_description', array('url' => $this->getPathOfFTP().'themes')));
        $this->assign('download_plugins_description', OW::getLanguage()->text('iisupdateserver', 'download_plugins_description', array('url' => $this->getPathOfFTP().'plugins')));

        $allVersionsOfCore = $service->getAllVersion('core');
        $buildNumber = '';
        $time = '-';
        if(sizeof($allVersionsOfCore)>0){
            $time = UTIL_DateTime::formatSimpleDate($allVersionsOfCore[0]->time);
            $buildNumber = $allVersionsOfCore[0]->version;
        }
        $allVersions = $service->getAllVersion();

        $this->assign('download_core_main_description', OW::getLanguage()->text('iisupdateserver', 'download_core_main_description'));
        $this->assign('download_core_update_description', OW::getLanguage()->text('iisupdateserver', 'download_core_update_description', array('version' => $buildNumber)));
        $this->assign('download_last_core_version', OW::getLanguage()->text('iisupdateserver', 'download_last_core_version', array('version' => $buildNumber)));
        $this->assign('download_last_core_update', OW::getLanguage()->text('iisupdateserver', 'download_last_core_update-version', array('version' => $buildNumber)));

        $this->assign('urlOfCoreMainLatestVersions', $this->getUrlOfLastVersionsOfItem('core', $allVersions, 'core/main'));
        $this->assign('urlOfSha256CoreMainLatestVersions', $this->getUrlOfLastVersionsOfItem('core', $allVersions, 'core/main').'.sha256');
        $this->assign('urlOfCoreUpdateLatestVersions', $this->getUrlOfLastVersionsOfItem('core', $allVersions, 'core/updates'));
        $this->assign('urlOfSha256CoreUpdateLatestVersions', $this->getUrlOfLastVersionsOfItem('core', $allVersions, 'core/updates').'.sha256');
        $this->assign('urlOfCoreMainVersions', $this->getPathOfFTP() . 'core/main');
        $this->assign('urlOfCoreUpdateVersions', $this->getPathOfFTP() . 'core/updates');
        $this->assign('urlOfAllCoreVersions', $this->getPathOfFTP() . 'core');
        $this->assign('urlOfPluginsVersions', $this->getPathOfFTP() . 'plugins');
        $this->assign('urlOfThemesVersions', $this->getPathOfFTP() . 'themes');
        $this->assign('date_core_released', $time);



        $pluginItems = $service->getItems('plugin');
        $pluginItemsInformation = array();
        foreach($pluginItems as $item){
            $itemInfo = $this->findItemInArrayListOfItems($allVersions, $item->key);
            $itemInformation = array();
            $itemInformation['name'] = $item->name;
            $itemInformation['description'] = $item->description;
            $itemInformation['versionsUrl'] = $this->getPathOfFTP() . 'plugins/'. $item->key;
            $itemInformation['downloadUrl'] =  $this->getUrlOfLastVersionsOfItem($item->key, $allVersions, 'plugins/'.$item->key);
            $itemInformation['downloadSha256Url'] =  $this->getUrlOfLastVersionsOfItem($item->key, $allVersions, 'plugins/'.$item->key).'.sha256';
            $itemInformation['image'] = Ow::getPluginManager()->getPlugin('iisupdateserver')->getUserFilesUrl() . $item->image;
            $itemInformation['releasedDate']=UTIL_DateTime::formatSimpleDate($itemInfo->time);
            $itemInformation['version']=$itemInfo->buildNumber;
            if(isset($item->guidelineurl) && !empty($item->guidelineurl)){
                $itemInformation['guidelineUrl'] = $item->guidelineurl;
            }
            $pluginItemsInformation[] = $itemInformation;
        }

        $themeItems = $service->getItems('theme');
        $themeItemsInformation = array();
        foreach($themeItems as $item){
            $itemInformation = array();
            $itemInfo = $this->findItemInArrayListOfItems($allVersions, $item->key);
            $itemInformation['name'] = $item->name;
            $itemInformation['description'] = $item->description;
            $itemInformation['versionsUrl'] = $this->getPathOfFTP() . 'themes/'. $item->key;
            $itemInformation['downloadUrl'] =  $this->getUrlOfLastVersionsOfItem($item->key, $allVersions, 'themes/'.$item->key);
            $itemInformation['downloadSha256Url'] =  $this->getUrlOfLastVersionsOfItem($item->key, $allVersions, 'themes/'.$item->key).'.sha256';
            $itemInformation['image'] = Ow::getPluginManager()->getPlugin('iisupdateserver')->getUserFilesUrl() . $item->image;
            $itemInformation['releasedDate']=UTIL_DateTime::formatSimpleDate($itemInfo->time);
            $itemInformation['version']=$itemInfo->buildNumber;
            if(isset($item->guidelineurl)){
                $itemInformation['guidelineUrl'] = $item->guidelineurl;
            }
            $themeItemsInformation[] = $itemInformation;
        }

        $this->assign('pluginItems', $pluginItemsInformation);
        $this->assign('themeItems', $themeItemsInformation);

        $cssDir = OW::getPluginManager()->getPlugin("iisupdateserver")->getStaticCssUrl();
        OW::getDocument()->addStyleSheet($cssDir . "iisupdateserver.css");

        $this->assign('coreImageUrl', OW::getPluginManager()->getPlugin("iisupdateserver")->getStaticUrl() . 'images/core.png');
        $this->assign('pluginsImageUrl', OW::getPluginManager()->getPlugin("iisupdateserver")->getStaticUrl() . 'images/plugins.png');
        $this->assign('sha256IconUrl', OW::getPluginManager()->getPlugin("iisupdateserver")->getStaticUrl() . 'images/sha256.png');
        $this->assign('themesImageUrl', OW::getPluginManager()->getPlugin("iisupdateserver")->getStaticUrl() . 'images/themes.png');
        $this->assign('downloadIconUrl', OW::getPluginManager()->getPlugin("iisupdateserver")->getStaticUrl() . 'images/download.png');
        $this->assign('archivesIconUrl', OW::getPluginManager()->getPlugin("iisupdateserver")->getStaticUrl() . 'images/archive.png');
        $this->assign('guidelineIconUrl', OW::getPluginManager()->getPlugin("iisupdateserver")->getStaticUrl() . 'images/help.png');
    }

    public function findItemInArrayListOfItems($items, $key){
        foreach($items as $item){
            if($item->key == $key){
                return $item;
            }
        }

        return null;
    }

    /***
     * @param $key
     * @param $allVersions
     * @param $path
     * @return string
     */
    public function getUrlOfLastVersionsOfItem($key, $allVersions, $path){
        $versionsOfSelectedKey = array();
        foreach($allVersions as $allVersion){
            if($allVersion->key == $key){
                $versionsOfSelectedKey[] = $allVersion;
            }
        }

        if(sizeof($versionsOfSelectedKey)>0){
            return $this->getPathOfFTP() . $path . '/' . $versionsOfSelectedKey[0]->buildNumber . '/' . IISUPDATESERVER_BOL_Service::getInstance()->getReplacedItemName($key) . '-' . $versionsOfSelectedKey[0]->buildNumber . '.zip';
        }

        return $this->getPathOfFTP(). $path . '/';
    }

    /***
     * @return string
     */
    public function getPathOfFTP(){
        $path = OW::getConfig()->getValue('iisupdateserver', 'prefix_download_path');
        if(!isset($path) || $path == ''){
            $path = OW_URL_HOME;
        }else{
            $path = $path . '/';
        }

        return $path;
    }
}
