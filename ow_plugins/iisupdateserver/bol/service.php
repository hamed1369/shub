<?php

/**
 *
 */

/**
 * iisterms Service.
 *
 * @author Yaser Alimardany <yaser.alimardany@gmail.com>
 * @package ow_plugins.iisupdateserver.bol
 * @since 1.0
 */
final class IISUPDATESERVER_BOL_Service
{

    /**
     * @var IISUPDATESERVER_BOL_UpdateInformationDao
     */
    private $updateInformationDao;

    /**
     * @var IISUPDATESERVER_BOL_UsersInformationDao
     */
    private $usersInformationDao;

    /**
     * @var IISUPDATESERVER_BOL_ItemDao
     */
    private $itemDao;

    public $SETTINGS_SECTION = 1;
    public $PLUGIN_ITEMS_SECTION = 2;
    public $THEME_ITEMS_SECTION = 3;
    public $ADD_ITEM_SECTION = 4;
    public $DELETE_ITEM_SECTION = 5;
    public $CHECK_ITEM_SECTION = 6;

    /**
     * Constructor.
     */
    private function __construct()
    {
        $this->updateInformationDao = IISUPDATESERVER_BOL_UpdateInformationDao::getInstance();
        $this->usersInformationDao = IISUPDATESERVER_BOL_UsersInformationDao::getInstance();
        $this->itemDao = IISUPDATESERVER_BOL_ItemDao::getInstance();
    }

    /**
     * Singleton instance.
     *
     * @var IISUPDATESERVER_BOL_Service
     */
    private static $classInstance;

    /**
     * Returns an instance of class (singleton pattern implementation).
     *
     * @return IISUPDATESERVER_BOL_Service
     */
    public static function getInstance()
    {
        if (self::$classInstance === null) {
            self::$classInstance = new self();
        }

        return self::$classInstance;
    }

    /***
     * @param $key
     * @param $buildNumber
     * @param $version
     */
    public function addVersion($key, $buildNumber, $version = null)
    {
        $updateInformation = new IISUPDATESERVER_BOL_UpdateInformation();
        $updateInformation->key= $key;
        $updateInformation->buildNumber = $buildNumber;
        $updateInformation->time = time();
        $updateInformation->version = $version;
        $this->updateInformationDao->save($updateInformation);
    }

    /***
     * @param $name
     * @param $description
     * @param $key
     * @param $image
     * @param $type
     * @param $guidelineurl
     * @return IISUPDATESERVER_BOL_Item|mixed|null
     */
    public function addItem($name, $description, $key, $image, $type, $guidelineurl)
    {
        if(!isset($key)){
            return null;
        }
        $allVersionsOfSelectedKey = $this->getAllVersion($key);
        if($allVersionsOfSelectedKey==null){
            return null;
        }

        $hasItem = $this->itemDao->getItemByKey($key);
        if($hasItem){
            $this->updateItem($name, $description, $key, $image, $type, $guidelineurl);
            return $hasItem;
        }
        $order = $this->getMaxOrderOfItem() + 1;
        $item = new IISUPDATESERVER_BOL_Item();
        $item->name= $name;
        $item->description = $description;
        $item->key = $key;
        $item->image = $image;
        $item->type = $type;
        $item->order = $order;
        $this->itemDao->save($item);
        $this->guidelineurl = $guidelineurl;
        return $item;
    }

    /***
     * @param $item
     */
    public function saveItem($item){
        if($item!=null){
            $this->itemDao->save($item);
        }
    }

    /***
     * @return int|mixed
     */
    public function getMaxOrderOfItem(){
        return $this->itemDao->getMaxOrder();
    }

    public function saveFile($imagePostedName){
        if(!((int)$_FILES[$imagePostedName]['error'] !== 0 || !is_uploaded_file($_FILES[$imagePostedName]['tmp_name']))){
            $iconName = uniqid() . '.' . UTIL_File::getExtension($_FILES[$imagePostedName]['name']);
            $userfilesDir = Ow::getPluginManager()->getPlugin('iisupdateserver')->getUserFilesDir();
            $tmpImgPath = $userfilesDir . $iconName;
            $storage = new BASE_CLASS_FileStorage();
            $storage->copyFile($_FILES[$imagePostedName]['tmp_name'], $tmpImgPath);
            return $iconName;
        }

        return null;
    }

    /***
     * @param $name
     * @param $description
     * @param $key
     * @param $image
     * @param $type
     * @param $guidelineurl
     * @return mixed|null
     */
    public function updateItem($name, $description, $key, $image, $type, $guidelineurl){
        if(!isset($key)){
            return null;
        }
        $newItem = $this->itemDao->getItemByKey($key);
        if($newItem!=null){
            $newItem->name= $name;
            $newItem->description = $description;
            $newItem->key = $key;
            $newItem->image = $image;
            $newItem->type = $type;
            $newItem->guidelineurl = $guidelineurl;
            $this->itemDao->save($newItem);
        }

        return $newItem;
    }

    /***
     * @param null $type
     * @return array
     */
    public function getItems($type = null){
        return $this->itemDao->getItems($type);
    }

    /***
     * @param $id
     * @return mixed|void
     */
    public function deleteItem($id){
        if(!isset($id)){
            return;
        }

        $item = $this->itemDao->getItemById($id);
        $this->itemDao->deleteById($id);

        return $item;
    }

    /***
     * @param $id
     * @return mixed
     */
    public function getItemById($id){
        return $this->itemDao->getItemById($id);
    }

    /***
     * @param $key
     * @return mixed
     */
    public function getItemByKey($key){
        return $this->itemDao->getItemByKey($key);
    }

    public function getItemByKeyAndBuildNumber($key,$buildNumber){
        return $this->updateInformationDao->getItemByKeyAndBuildNumber($key,$buildNumber);
    }

    /***
     * @param $action
     * @param null $nameValue
     * @param null $descriptionValue
     * @param null $keyValue
     * @param null $typeValue
     * @param null $guidelineurl
     * @return Form
     */
    public function getItemForm($action, $nameValue = null, $descriptionValue = null, $keyValue=null, $typeValue=null, $guidelineurl=null){
        $form = new Form('item');
        $form->setAction($action);
        $form->setMethod(Form::METHOD_POST);
        $form->setEnctype(Form::ENCTYPE_MULTYPART_FORMDATA);

        $name = new TextField('name');
        $name->setRequired();
        $name->setValue($nameValue);
        $name->setLabel(OW::getLanguage()->text('iisupdateserver', 'name'));
        $name->setHasInvitation(false);
        $form->addElement($name);

        $description = new Textarea('description');
        $description->setValue($descriptionValue);
        $description->setLabel(OW::getLanguage()->text('iisupdateserver', 'description'));
        $description->setHasInvitation(false);
        $form->addElement($description);

        $name = new TextField('key');
        $name->setRequired();
        $name->setValue($keyValue);
        $name->setLabel(OW::getLanguage()->text('iisupdateserver', 'key'));
        $name->setHasInvitation(false);
        $form->addElement($name);

        $image = new FileField('image');
        $image->setLabel(OW::getLanguage()->text('iisupdateserver', 'image'));
        $form->addElement($image);

        $typeField = new Selectbox('type');
        $typeField->setLabel(OW::getLanguage()->text('iisupdateserver', 'type'));
        $typeField->setHasInvitation(false);
        $options = array();
        $options['plugin'] = OW::getLanguage()->text('iisupdateserver', 'plugin');
        $options['theme'] = OW::getLanguage()->text('iisupdateserver', 'theme');
        $typeField->setOptions($options);
        $typeField->setRequired();
        $typeField->setValue($typeValue);
        $form->addElement($typeField);

        $guidelineurlField = new TextField('guidelineurl');
        $guidelineurlField->setValue($guidelineurl);
        $guidelineurlField->setLabel(OW::getLanguage()->text('iisupdateserver', 'guidelineurl_label'));
        $guidelineurlField->setHasInvitation(false);
        $form->addElement($guidelineurlField);

        $submit = new Submit('submit');
        $form->addElement($submit);

        return $form;
    }

    /***
     * @param null $keyValue
     * @param null $buildNum
     * @return Form
     */
    public function getDeleteItemForm($keyValue=null,$buildNum=null){
        $form = new Form('deleteItem');
        $form->setMethod(Form::METHOD_POST);
        $form->setAjaxResetOnSuccess(true);
        $key = new TextField('key');
        $key->setRequired();
        $key->setValue($keyValue);
        $key->setLabel(OW::getLanguage()->text('iisupdateserver', 'key'));
        $key->setHasInvitation(false);
        $form->addElement($key);

        $build = new Textarea('build');
        $build->setRequired();
        $build->setValue($buildNum);
        $build->setLabel(OW::getLanguage()->text('iisupdateserver', 'buildNumber'));
        $build->setHasInvitation(false);
        $form->addElement($build);

        $submit = new Submit('submit');
        $form->addElement($submit);

        return $form;
    }

    /***
     * @param null $keyValue
     * @return Form
     */
    public function getCheckItemForm($keyValue=null){
        $form = new Form('checkItem');
        $form->setMethod(Form::METHOD_POST);
        $form->setAjaxResetOnSuccess(true);
        $key = new TextField('key');
        $key->setRequired();
        $key->setValue($keyValue);
        $key->setLabel(OW::getLanguage()->text('iisupdateserver', 'key'));
        $key->setHasInvitation(false);
        $form->addElement($key);

        $submit = new Submit('submit');
        $form->addElement($submit);

        return $form;
    }

    public function deleteAllVersions()
    {
        return $this->updateInformationDao->deleteAllVersions();
    }

    public function deleteItemByIDAndBuildNumAndKey($item,$buildNumber,$key)
    {
        return $this->updateInformationDao->deleteItem($item,$buildNumber,$key);
    }

    /***
     * @param $key
     * @param $developerKey
     */
    public function addUser($key, $developerKey)
    {
        $usersInformation = new IISUPDATESERVER_BOL_UsersInformation();
        $usersInformation->key= $key;
        $usersInformation->developerKey = $developerKey;
        $usersInformation->time = time();
        $usersInformation->ip = $this->getCurrentIP();
        $this->usersInformationDao->save($usersInformation);
    }

    public function getCurrentIP(){
        $ip = OW::getRequest()->getRemoteAddress();
        if($ip == '::1'){
            $ip = '127.0.0.1';
        }
        return $ip;
    }

    /***
     * @param $key
     * @param $buildNumber
     * @return IISUPDATESERVER_BOL_UpdateInformation
     */
    public function hasExist( $key, $buildNumber)
    {
        return $this->updateInformationDao->hasExist($key, $buildNumber);
    }

    /***
     * @param null $key
     * @return array
     */
    public function getAllVersion($key = null){
        return $this->updateInformationDao->getAllVersion($key);
    }

    /**
     *
     * @return array
     */
    public function getAdminSections($sectionId)
    {
        $sections = array();

        $sections[] = array(
            'sectionId' => $this->getInstance()->SETTINGS_SECTION,
            'active' => $sectionId == $this->getInstance()->SETTINGS_SECTION ? true : false,
            'url' => OW::getRouter()->urlForRoute('iisupdateserver.admin'),
            'label' => OW::getLanguage()->text('iisupdateserver', 'settings')
        );

        $sections[] = array(
            'sectionId' => $this->getInstance()->PLUGIN_ITEMS_SECTION,
            'active' => $sectionId == $this->getInstance()->PLUGIN_ITEMS_SECTION ? true : false,
            'url' => OW::getRouter()->urlForRoute('iisupdateserver.admin.items', array('type' => 'plugin')),
            'label' => OW::getLanguage()->text('iisupdateserver', 'plugins')
        );

        $sections[] = array(
            'sectionId' => $this->getInstance()->THEME_ITEMS_SECTION,
            'active' => $sectionId == $this->getInstance()->THEME_ITEMS_SECTION ? true : false,
            'url' => OW::getRouter()->urlForRoute('iisupdateserver.admin.items', array('type' => 'theme')),
            'label' => OW::getLanguage()->text('iisupdateserver', 'themes')
        );

        $sections[] = array(
            'sectionId' => $this->getInstance()->ADD_ITEM_SECTION,
            'active' => $sectionId == $this->getInstance()->ADD_ITEM_SECTION ? true : false,
            'url' => OW::getRouter()->urlForRoute('iisupdateserver.admin.add.item'),
            'label' => OW::getLanguage()->text('iisupdateserver', 'add_item')
        );

        $sections[] = array(
            'sectionId' => $this->getInstance()->DELETE_ITEM_SECTION,
            'active' => $sectionId == $this->getInstance()->DELETE_ITEM_SECTION ? true : false,
            'url' => OW::getRouter()->urlForRoute('iisupdateserver.admin.delete.by.name.and.version'),
            'label' => OW::getLanguage()->text('iisupdateserver', 'delete_item')
        );

        $sections[] = array(
            'sectionId' => $this->getInstance()->CHECK_ITEM_SECTION,
            'active' => $sectionId == $this->getInstance()->CHECK_ITEM_SECTION ? true : false,
            'url' => OW::getRouter()->urlForRoute('iisupdateserver.admin.check.update.by.name'),
            'label' => OW::getLanguage()->text('iisupdateserver', 'check_item')
        );
        return $sections;
    }


    public function getZipPathByKey($buildNumber, $type){
        if($type==null || $buildNumber == null){
            return null;
        }
        return Ow::getPluginManager()->getPlugin('iisupdateserver')->getPluginFilesDir() . $type . DS . $buildNumber;
    }

    /***
     * @param $name
     * @return string
     */
    public function getReplacedItemName($name){
        return 'ms-'.$name;
    }

    public function isInWhiteDirectoryList($whiteDirectories, $relativePath, $forUpdate){
        if($forUpdate==null) {
            foreach ($whiteDirectories as $whiteDir) {
                if (strpos($relativePath, $whiteDir) > -1) {
                    return true;
                }
            }
        }
        return false;
    }

    public function isInEssentialStaticFiles($essentialStaticDirectories, $relativePath, $forUpdate){
        if($forUpdate==null) {
            foreach ($essentialStaticDirectories as $staticsDir) {
                if (strpos($relativePath, $staticsDir) > -1) {
                    return true;
                }
            }
        }
        return false;
    }
    public function isInIgnoreDirectoryList($relativePath, $forUpdate = true){
        $ignoreDirectories = array('.git' . DS,
            'template_c' . DS,
            'ow_log' . DS,
            'ow_unittest' . DS,
            'ow_userfiles' . DS,
            '.idea' . DS,
            'ow_includes' . DS . 'config.php',
            'ow_plugins' . DS . 'iisnews',
            'ow_plugins' . DS . 'iisoghat',
            'ow_download_update_files' . DS,
            'composer.phar',
            'composer.lock',
            'ow_plugins' . DS . 'iisupdateserver',
            'ow_plugins' . DS . 'iissuggestfriend',
            'ow_plugins' . DS . 'coverphoto',
            'ow_plugins' . DS . 'iisrules',
            'ow_plugins' . DS . 'iispiwik',
            'ow_plugins' . DS . 'iisheaderimg',
            'ow_plugins' . DS . 'birthdays',
            'ow_plugins' . DS . 'iisreveal',
            'ow_plugins' . DS . 'iisdemo',
            'ow_plugins' . DS . 'iiscontactus',
            'ow_plugins' . DS . 'iisterms',
            'ow_plugins' . DS . 'iismutual',
            'ow_plugins' . DS . 'iisimport',
            'ow_plugins' . DS . 'iisevaluation',
            'ow_plugins' . DS . 'iissmartscroll',
            'oxwall1.8-db-backup.sql',
            'composer.json',
            '.gitignore',
            'ow_pluginfiles' . DS,
            'ow_themes',
            'ow_static' . DS . 'themes',
            'ow_static' . DS . 'plugins');

        $whiteDirectories = array('ow_pluginfiles' . DS . 'base',
            'ow_pluginfiles' . DS . 'admin',
            'ow_pluginfiles' . DS . 'ow',
            'ow_pluginfiles' . DS . 'plugin',
            'ow_pluginfiles' . DS . 'plugins');
        $essentialStaticFiles = array('ow_static' .DS. 'plugins' .DS. 'admin',
            'ow_static' .DS. 'plugins' .DS. 'base',
            'ow_static' . DS . 'themes' . DS . 'iissocialcity',
            'ow_themes' . DS . 'iissocialcity');
        if($forUpdate){
            $ignoreDirectories[] = 'ow_install' . DS;
            $ignoreDirectories[] = 'ow_plugins' . DS;
        }

        foreach($ignoreDirectories as $ignoreDir) {
            $isinWhiteList = $this->isInWhiteDirectoryList($whiteDirectories, $relativePath, $forUpdate);
            $isInEssentialStatics = $this->isInEssentialStaticFiles($essentialStaticFiles, $relativePath, $forUpdate);
            if (strpos($relativePath, $ignoreDir) > -1 && !$isinWhiteList && !$isInEssentialStatics) {
                return true;
            }
        }
        return false;
    }


    public function addStaticFile($dir, $toDir, $type, $name){
        $mdFiles = UTIL_File::findFiles($dir, array($type), 1);
        foreach ( $mdFiles as $mdFile )
        {
            if ( basename($mdFile) === $name.$type )
            {
                copy($mdFile, $toDir . DS . $name.$type);
            }
        }
    }

    public function isConfigFileNeededtoInstall($filePath,$relativePath,$forUpdate)
    {
        if($forUpdate==null && (strcmp('ow_includes' . DS . 'config_default_for_installation.php',$relativePath)==0))
        {
            return true;
        }
        return false;
    }


    public function addSha256Hashfile($toDir,$filename){
        $hashCode = hash_file("sha256",$toDir . DS . $filename);
        $hashCodeFile = fopen($toDir . DS . $filename .'.sha256', "w");
        $txt = $hashCode;
        fwrite($hashCodeFile, $txt);
        fclose($hashCodeFile);
    }

    private function addFileToZipArchive($zip, $filePath, $relativePath)
    {
        $stat = stat($filePath);
        $zip->addFile($filePath, $relativePath);
        $zip->setExternalAttributesName($relativePath, ZipArchive::OPSYS_UNIX, $stat['mode'] << 16);
    }

    public function zipFolder($dir, $zipPath, $forUpdate = true, $key = null, $isCore = true){
        // Get real path for our folder
        $rootPath = realpath($dir);

        // Initialize archive object
        $zip = new ZipArchive();
        $zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE);

        $keyPath = '';
        if($key!=null && !$isCore){
            $zip->addEmptyDir($key);
            $keyPath = $key . DS;
        }

        // Create recursive directory iterator
        /** @var SplFileInfo[] $files */
        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($rootPath),
            RecursiveIteratorIterator::LEAVES_ONLY
        );
        if($forUpdate==null){
            $zip->addEmptyDir('ow_pluginfiles');
        }
        foreach ($files as $name => $file)
        {
            // Skip directories (they would be added automatically)
            if (!$file->isDir())
            {
                // Get real and relative path for current file
                $filePath = $file->getRealPath();
                $relativePath = substr($filePath, strlen($rootPath) + 1);
                if(!$this->isInIgnoreDirectoryList($relativePath, $forUpdate)) {
                    // Add current file to archive
                    if($this->isConfigFileNeededtoInstall($filePath,$relativePath,$forUpdate)){
                        $this->addFileToZipArchive($zip, $filePath, 'ow_includes' . DS . 'config.php');
                    }
                    $this->addFileToZipArchive($zip, $filePath, $keyPath . $relativePath);
                }
            }
        }
        if($forUpdate==null) {
            $zip->addEmptyDir('ow_userfiles');
            $zip->addEmptyDir('ow_userfiles' .DS .'plugins');
            $zip->addEmptyDir('ow_userfiles' .DS .'plugins' .DS .'admin');
            $zip->addEmptyDir('ow_userfiles' .DS .'plugins' .DS .'base');
            $zip->addEmptyDir('ow_userfiles' .DS .'plugins' .DS .'base' . DS . 'attachments');
            $zip->addEmptyDir('ow_userfiles' .DS .'plugins' .DS .'base' . DS . 'attachments' . DS . 'temp');
            $zip->addEmptyDir('ow_userfiles' .DS .'plugins' .DS .'base' . DS . 'avatars');
            $zip->addEmptyDir('ow_log');
            $zip->addEmptyDir('ow_smarty' . DS . 'template_c');
        }

        // Zip archive will be created only after closing object
        $zip->close();
    }

    public function checkPluginForUpdate($key, $buildNumber, $sourceDir, $rootZipDirectory){
        $add_version = false;
        $allVersion = IISUPDATESERVER_BOL_Service::getInstance()->getAllVersion($key);
        if(sizeof($allVersion)>0){
            $allVersion = $allVersion[0];
            if($allVersion->buildNumber < $buildNumber){
                $add_version = true;
            }
        }else{
            $add_version = true;
        }

        if($add_version){
            $pluginDir = 'plugins' . DS . $key;
            if (!file_exists($rootZipDirectory . $pluginDir)) {
                mkdir($rootZipDirectory . $pluginDir, 0777, true);
            }
            $pluginDir = 'plugins' . DS . $key . DS . $buildNumber;
            if (!file_exists($rootZipDirectory . $pluginDir)) {
                mkdir($rootZipDirectory . $pluginDir, 0777, true);
            }
            $zipPath = $this->getZipPathByKey($this->getReplacedItemName($key) . '-' . $buildNumber . '.zip', $pluginDir);
            $this->zipFolder($sourceDir, $zipPath, true, $key, false);
            $this->addStaticFile($sourceDir, $rootZipDirectory . $pluginDir, 'txt', 'CHANGELOG.');
            $this->addStaticFile($sourceDir, $rootZipDirectory . $pluginDir, 'md', 'CHANGELOG.');
            $this->addStaticFile($sourceDir, $rootZipDirectory . $pluginDir, 'md', 'README.');
            $fileName = $this->getReplacedItemName($key) . '-' . $buildNumber . '.zip';
            $this->addSha256Hashfile($rootZipDirectory . $pluginDir,$fileName);
            IISUPDATESERVER_BOL_Service::getInstance()->addVersion($key, $buildNumber);

        }
    }

    public function checkThemeForUpdate($key, $buildNumber, $rootZipDirectory){
        $dir = OW_DIR_THEME  . $key;
        $add_version = false;
        $allVersion = IISUPDATESERVER_BOL_Service::getInstance()->getAllVersion($key);
        if(sizeof($allVersion)>0){
            $allVersion = $allVersion[0];
            if($allVersion->buildNumber < $buildNumber){
                $add_version = true;
            }
        }else{
            $add_version = true;
        }

        if($add_version){
            $themeDir = 'themes' . DS . $key;
            if (!file_exists($rootZipDirectory . $themeDir)) {
                mkdir($rootZipDirectory . $themeDir, 0777, true);
            }
            $themeDir = 'themes' . DS . $key . DS . $buildNumber;
            if (!file_exists($rootZipDirectory . $themeDir)) {
                mkdir($rootZipDirectory . $themeDir, 0777, true);
            }
            $zipPath = $this->getZipPathByKey($this->getReplacedItemName($key) . '-' . $buildNumber . '.zip', $themeDir);
            $this->zipFolder($dir, $zipPath, true, $key, false);
            $fileName = $this->getReplacedItemName($key) . '-' . $buildNumber . '.zip';
            $this->addSha256Hashfile($rootZipDirectory . $themeDir,$fileName);
            IISUPDATESERVER_BOL_Service::getInstance()->addVersion($key, $buildNumber);
        }
    }

    public function checkCoreForUpdate($rootZipDirectory, $forUpdate = null, $addVersionManually = null){
        $dir = OW_DIR_ROOT;
        if (!file_exists($rootZipDirectory . 'core')) {
            mkdir($rootZipDirectory . 'core', 0777, true);
        }
        $add_version = false;
        $core_information = (array) (simplexml_load_file(OW_DIR_ROOT . 'ow_version.xml'));
        $allVersion = IISUPDATESERVER_BOL_Service::getInstance()->getAllVersion('core');
        if(sizeof($allVersion)>0){
            $allVersion = $allVersion[0];
            if($allVersion->buildNumber < (string) $core_information['build']){
                $add_version = true;
            }
        }else{
            $add_version = true;
        }

        if($addVersionManually!=null && $addVersionManually){
            $add_version = true;
        }

        if($add_version){
            $coreDir = 'core' . DS . 'main' . DS . (string) $core_information['build'];
            if (!file_exists($rootZipDirectory . 'core' . DS . 'main')) {
                mkdir($rootZipDirectory . 'core' . DS . 'main', 0777, true);
            }
            if (!file_exists($rootZipDirectory . 'core' . DS . 'main' . DS . (string) $core_information['build'])) {
                mkdir($rootZipDirectory . 'core' . DS . 'main' . DS . (string) $core_information['build'], 0777, true);
            }
            if($forUpdate!=null && $forUpdate){
                $coreDir = 'core' . DS . 'updates' . DS . (string) $core_information['build'];
                if (!file_exists($rootZipDirectory . 'core' . DS . 'updates')) {
                    mkdir($rootZipDirectory . 'core' . DS . 'updates', 0777, true);
                }
                if (!file_exists($rootZipDirectory . 'core' . DS . 'updates' . DS . (string) $core_information['build'])) {
                    mkdir($rootZipDirectory . 'core' . DS . 'updates' . DS . (string) $core_information['build'], 0777, true);
                }
            }
            $zipPath = $this->getZipPathByKey( $this->getReplacedItemName('core') . '-' . (string) $core_information['build'] . '.zip', $coreDir);
            $this->zipFolder($dir, $zipPath, $forUpdate);
            if(file_exists($zipPath)){
                $this->addStaticFile($dir, $rootZipDirectory . $coreDir, 'txt', 'CHANGELOG.');
                $this->addStaticFile($dir, $rootZipDirectory . $coreDir, 'pdf', 'ReadMe.');
                $fileName = $this->getReplacedItemName('core') . '-' . (string) $core_information['build'] . '.zip';
                $this->addSha256Hashfile($rootZipDirectory . $coreDir,$fileName);
                if($forUpdate==null) {
                    IISUPDATESERVER_BOL_Service::getInstance()->addVersion('core', (string)$core_information['build'], (string)$core_information['version']);
                }
            }else{
                //error
            }

            if($forUpdate==null){
                $this->checkCoreForUpdate($rootZipDirectory, true, true);
            }
        }
    }
}