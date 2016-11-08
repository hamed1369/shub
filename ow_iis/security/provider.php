<?php

/**
 * User: Hamed Tahmooresi
 * Date: 1/3/2016
 * Time: 3:12 PM
 */

class IISSecurityProvider
{
    private static $classInstance;
    public static $prefixBackuplabel = 'iisbckp_';
    public static $prefixRemovedlabel = 'removed_';
    public static $removeTriggerNameBackupTable = 'riistrig_';
    public static $updateTriggerNameBackupTable = 'uiistrig_';
    public static $statusMessage;
    public static $aparatResourceName = 'aparat.com';
    public static $checkingLoadMorePeriod = 1;

    /**
     * Returns an instance of class (singleton pattern implementation).
     *
     * @return OW_EventManager
     */
    public static function getInstance()
    {
        if ( IISSecurityProvider::$classInstance === null )
        {
            IISSecurityProvider::$classInstance = new IISSecurityProvider();
        }

        return IISSecurityProvider::$classInstance;
    }
    public function set_php_ini_params(){
        if ( session_id() != '' ) {
            return;
        }
        //disable transparent sid support
        ini_set('session.use_trans_sid', '0');
        ini_set('session.use_cookies', '1');
        ini_set('session.use_only_cookcheckies', '1');
        ini_set('session.cookie_httponly','1');

        if ( OW::getRequest()->isSsl() ) {
            ini_set('session.cookie_secure','1');
        }

        if (version_compare(PHP_VERSION, '5.3.0') >= 0) {
            ini_set('session.hash_function','sha512');
        }else{
            ini_set('session.hash_function',1);
        }

        ini_set('session.hash_bits_per_character',6);
        ini_set('session.entropy_length',256);
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            // This is a server using Windows!

        } else {
            // This is a server not using Windows!
            // As of PHP 5.4.0 session.entropy_file defaults to /dev/urandom or /dev/arandom if it is available. In PHP 5.3.0 this directive is left empty by default.
            ini_set('session.entropy_file','/dev/urandom');
        }
    }


    public static function parse_size($size) {
        $unit = preg_replace('/[^bkmgtpezy]/i', '', $size); // Remove the non-unit characters from the size.
        $size = preg_replace('/[^0-9\.]/', '', $size); // Remove the non-numeric characters from the size.
        if ($unit) {
            // Find the position of the unit in the ordered string which is the power of magnitude to multiply a kilobyte by.
            return round($size * pow(1024, stripos('bkmgtpezy', $unit[0])));
        }
        else {
            return round($size);
        }
    }

    public static function onBeforeErrorRender(OW_Event $event){
        $params = $event->getParams();
        if(isset($params['errorData'])){
            $errorData = $params['errorData'];
            if(isset($errorData['message']) && $errorData['message']=='General error') {
                $CONTENT_LENGTH = $_SERVER['CONTENT_LENGTH'];
                $post_max_size = IISSecurityProvider::parse_size(ini_get('post_max_size'));
                if ($CONTENT_LENGTH >= $post_max_size) {
                    $errorData['message'] = OW::getLanguage()->text('base', 'upload_file_max_upload_filesize_error');
                } else {
                    $errorData['message'] = OW::getLanguage()->text('base', 'upload_file_fail');
                }
                $event->setData(array('errorData' => $errorData));
            }
        }
    }

    public static function setStatusMessage($statusMessage)
    {
        self::$statusMessage = $statusMessage;
    }
    public static function getStatusMessage()
    {
        return self::$statusMessage;
    }

    public function onBeforeAlterQueryExecuted(OW_Event $event){
        $params = $event->getParams();
        if(isset($params['query'])){
            $query = $params['query'];
            $queryParams = $params['params'];
            $query = trim(preg_replace('/\s\s+/', ' ', str_replace("\n", " ", $query)));
            if(strpos(strtoupper($query), 'ALTER TABLE')!==false){
                $details = explode(' ', $query);
                $tableName = $details[2];
                $tableName = str_replace('`','',$tableName);
                $table = OW::getDbo()->queryForRow('show tables like :tableName', array('tableName' => $tableName));
                if (!empty($table)) {
                    $backupTableName = self::getTableBackupName($tableName);
                    $backupTable = OW::getDbo()->queryForRow('show tables like :tableName', array('tableName' => $backupTableName));
                    if (!empty($backupTable)) {
                        $details[2] = '`'.$backupTableName.'`';
                        $query = implode(' ',$details);
                        OW::getDbo()->query($query, $queryParams);
                    }
                }
            }
        }
    }

    /*
     * Doing backup from all tables to save all removed or updated data.
     * All table schema copied in anohter table with name of *_iisbackup (* = Name of table needed backup) and create trigger for each tables for saving all removed data.
     */
    public static function createBackupTables(OW_Event $event){
        set_time_limit(600);

        //Filter on tables we do not want to backup.
        $tablesDontNeedBackup = array(OW_DB_PREFIX . 'base_user_online', 'phinxlog', OW_DB_PREFIX . 'base_language_value', OW_DB_PREFIX . 'base_language_prefix', OW_DB_PREFIX . 'base_language_key', OW_DB_PREFIX . 'base_language', OW_DB_PREFIX . 'mailbox_user_last_data', OW_DB_PREFIX . 'base_cron_job', OW_DB_PREFIX . 'base_config', OW_DB_PREFIX . 'base_site_statistic', OW_DB_PREFIX . 'base_theme', OW_DB_PREFIX . 'notifications_send_queue', OW_DB_PREFIX . 'newsfeed_action_set', OW_DB_PREFIX . 'base_geolocationdata_ipv4');
        $tablesWithCustomTriggerForUpdate = array(OW_DB_PREFIX . 'base_user');
        //Get all tables
        $queryGetAllTables = 'select * from information_schema.tables WHERE TABLE_SCHEMA = \'' . OW_DB_NAME . '\'';
        $allTables = OW::getDbo()->queryForList($queryGetAllTables);

        foreach ($allTables as $table) {

            //Postfix of backup table name
            $prefixBackuplabel = self::$prefixBackuplabel;

            $prefixRemovedTable = self::$prefixRemovedlabel;

            //Main table name
            $tableName = $table['TABLE_NAME'];

            //Table name for backup updated or removed data
            $backupTableName = self::getTableBackupName($tableName);

            //Trigger name for removed data
            $removeTriggerName = self::$removeTriggerNameBackupTable . $tableName;

            //Trigger name for updated data
            $updateTriggerName = self::$updateTriggerNameBackupTable . $tableName;

            //Check filtering list. Also we do not create a backup table from table that is backup.
            if (!(strpos($tableName, $prefixBackuplabel)===0) && !(strpos($tableName, $prefixRemovedTable)===0) && !in_array($tableName,$tablesDontNeedBackup)) {

                //Check backup table exists or not
                $backupTable = OW::getDbo()->queryForRow('show tables like :tableName', array('tableName' => $backupTableName));
                if (empty($backupTable)) {

                    //Create backup table like targer table
                    $queryCopyTableForCreatingBackupTable = 'CREATE TABLE ' . $backupTableName . ' LIKE ' . $tableName;
                    OW::getDbo()->query($queryCopyTableForCreatingBackupTable);

                    //Modify id column for dropping
                    $queryModifyPrimaryKeyForCreatingBackupTable = 'ALTER TABLE '. $backupTableName .' MODIFY id INT NOT NULL';
                    OW::getDbo()->query($queryModifyPrimaryKeyForCreatingBackupTable);

                    //If table has primary key, drop it.
                    $hasTablePrimaryKey = OW::getDbo()->queryForRow('SHOW INDEXES FROM '.$backupTableName.' WHERE Key_name = \'PRIMARY\'');
                    if (!empty($hasTablePrimaryKey)){
                        //Drop primary key
                        $queryDropContraintOnPrimaryKeyForCreatingBackupTable = 'ALTER TABLE '. $backupTableName .' DROP PRIMARY KEY';
                        OW::getDbo()->query($queryDropContraintOnPrimaryKeyForCreatingBackupTable);
                    }

                    //Add backup_timestamp column
                    $queryAddTimestampColumnForCreatingBackupTable = 'ALTER TABLE '. $backupTableName .' ADD backup_timestamp INT(11)';
                    OW::getDbo()->query($queryAddTimestampColumnForCreatingBackupTable);

                    //Add backup_action column
                    $queryAddActionColumnForCreatingBackupTable = 'ALTER TABLE '. $backupTableName .' ADD backup_action varchar(2)';
                    OW::getDbo()->query($queryAddActionColumnForCreatingBackupTable);

                    //Add backup_id column
                    $queryAddIdColumnForCreatingBackupTable = 'ALTER TABLE '. $backupTableName .' ADD backup_pk_id INT PRIMARY KEY AUTO_INCREMENT';
                    OW::getDbo()->query($queryAddIdColumnForCreatingBackupTable);

                    //If table has unique key, drop it.
                    $listOfRemovedKey = array();
                    $hasTableUniqueKey = OW::getDbo()->queryForList('show indexes from '.$backupTableName . ' WHERE Key_name != \'PRIMARY\'');
                    if (!empty($hasTableUniqueKey)){
                        foreach($hasTableUniqueKey as $row_index){
                            $keyName = $row_index['Key_name'];
                            if(!in_array($keyName,$listOfRemovedKey)){
                                $listOfRemovedKey[] = $keyName;
                                $queryDropContraintOnUniqueKeyForCreatingBackupTable = 'DROP index `'.$keyName.'` on '.$backupTableName;
                                OW::getDbo()->query($queryDropContraintOnUniqueKeyForCreatingBackupTable);
                            }
                        }
                    }

                    //Add trigger for removed data
                    $triggerQueryForDoinBackupBeforeRemove = 'DROP TRIGGER IF EXISTS ' . $removeTriggerName . '; CREATE TRIGGER ' . $removeTriggerName . ' Before DELETE ON ' . $tableName . ' FOR EACH ROW  BEGIN INSERT INTO ' . $backupTableName . ' (select tbl.*, UNIX_TIMESTAMP(NOW()) as backup_timestamp, \'r\' as backup_action, NULL as backup_pk_id from ' . $tableName . ' tbl where tbl.id = OLD.id); END;';
                    OW::getDbo()->query($triggerQueryForDoinBackupBeforeRemove);

                    if(!in_array($tableName,$tablesWithCustomTriggerForUpdate)) {
                        //Add trigger for updated data
                        $triggerQueryForDoinBackupAfterUpdate = 'DROP TRIGGER IF EXISTS ' . $updateTriggerName . '; CREATE TRIGGER ' . $updateTriggerName . ' After UPDATE ON ' . $tableName . ' FOR EACH ROW  BEGIN INSERT INTO ' . $backupTableName . ' (select tbl.*, UNIX_TIMESTAMP(NOW()) as backup_timestamp, \'u\' as backup_action, NULL as backup_pk_id from ' . $tableName . ' tbl where tbl.id = OLD.id); END;';
                        OW::getDbo()->query($triggerQueryForDoinBackupAfterUpdate);
                    }
                }
            }
        }


        foreach($tablesDontNeedBackup as $tableDontNeedBackup){
            $dropTableDontNeedBackupQuery = 'DROP TABLE IF EXISTS ' . self::getTableBackupName($tableDontNeedBackup);
            OW::getDbo()->query($dropTableDontNeedBackupQuery);

            $dropRemoveTriggerOfTableDontNeedBackupQuery =  'DROP TRIGGER IF EXISTS ' . self::$removeTriggerNameBackupTable . $tableDontNeedBackup;
            OW::getDbo()->query($dropRemoveTriggerOfTableDontNeedBackupQuery);

            $dropUpdateTriggerOfTableDontNeedBackupQuery =  'DROP TRIGGER IF EXISTS ' . self::$updateTriggerNameBackupTable . $tableDontNeedBackup;
            OW::getDbo()->query($dropUpdateTriggerOfTableDontNeedBackupQuery);
        }

        //Custom trigger for base_user
        //check if trigger dosesnt exist
        //create (update and remove -> new data arrive + any colmun changed except activity_timestamp)

        $baseUserTrigger = OW::getDbo()->queryForRow('show tables like :tableName', array('tableName' => 'ow_base_user_uiistrig'));
        if (empty($baseUserTrigger)) {
            $updateTriggerName = 'ow_base_user_uiistrig';
            $tableName ='ow_base_user';
            $backupTableName = 'iisbckp_ow_base_user';
            //Add trigger for updated data
            $triggerQueryForDoinBackupAfterUpdate = 'DROP TRIGGER IF EXISTS ' . $updateTriggerName . '; CREATE TRIGGER ' . $updateTriggerName . ' After UPDATE ON ' . $tableName . ' FOR EACH ROW  BEGIN  IF NEW.email <> OLD.email  OR NEW.password <> OLD.password OR NEW.accountType <> OLD.accountType OR NEW.username <> OLD.username THEN  INSERT INTO ' . $backupTableName . ' (select tbl.*, UNIX_TIMESTAMP(NOW()) as backup_timestamp, \'u\' as backup_action, NULL as backup_pk_id from ' . $tableName . ' tbl where tbl.id = OLD.id); END IF; END;';
            OW::getDbo()->query($triggerQueryForDoinBackupAfterUpdate);
        }
    }


    /*
     * Remove data backup using timestamp.
     */
    public static function deleteBackupData(OW_Event $event){
        $params = $event->getParams();
        if(isset($params['timestamp'])){
            $timestampDeadline = $params['timestamp'];
            $timestamp = time() - $timestampDeadline;

            //Get all tables
            $queryGetAllTables = 'select * from information_schema.tables WHERE TABLE_SCHEMA = \'' . OW_DB_NAME . '\'';
            $allTables = OW::getDbo()->queryForList($queryGetAllTables);

            //Postfix of backup table name
            $prefixBackuplabel = self::$prefixBackuplabel;

            foreach ($allTables as $table) {
                //Main table name
                $tableName = $table['TABLE_NAME'];

                //Table name for backup updated or removed data
                $backupTableName = self::getTableBackupName($tableName);

                //Check backup table exists or not
                $backupTable = OW::getDbo()->queryForRow('show tables like :tableName', array('tableName' => $backupTableName));
                if (!empty($backupTable)) {
                    $queryForRemovingOldData = 'delete from ' . $backupTableName . ' where backup_timestamp <'.$timestamp;
                    OW::getDbo()->query($queryForRemovingOldData);
                }
            }
        }
    }

    public static function setInitialData(){
        //set aparat resources for video embedding
        self::setAparatToResource();

        //remove some questions from registration process
        self::removeUserProfileQuestions();

        //Delete social network static page
        self::deleteExternalStaticPage(4);

        //Delete static mobile page
        self::deleteStaticPages(477);

        //Delete static finance page
        self::deleteStaticPages(340);

        //Delete finance widget
        self::deleteWidget('ADMIN_CMP_FinanceStatisticWidget');

        //Delete terms of services static page
        self::deleteStaticPages(411);

        //Delete privacy static page
        self::deleteStaticPages(468);

        //Remove default widget in mobile
        self::deleteWidgetUsingComponentPlaceUniqueName("admin-5295f2e03ec8a");

        //Remove default widget in mobile
        self::deleteWidgetUsingComponentPlaceUniqueName("admin-5295f2e40db5c");

        //Delete mobile page
//        self::deleteStaticPages(479);

        //Delete all mobile configuration
//        self::deleteMobileConfiguration();

        //set default age range to join
        self::setBirthDateInitialRange();
    }

    /***
     * Delete widget by key
     * @param $widgetKey
     */
    public static function deleteWidget($widgetKey){
        BOL_ComponentAdminService::getInstance()->deleteWidget($widgetKey);
    }


    /***
     * Delete widget using componentPlaceUniqueName
     * @param $componentPlaceUniqueName
     */
    public static function deleteWidgetUsingComponentPlaceUniqueName($componentPlaceUniqueName){
        BOL_ComponentAdminService::getInstance()->deletePlaceComponent($componentPlaceUniqueName);
    }

    public static function setBirthDateInitialRange(){
        $qBdate = BOL_QuestionService::getInstance()->findQuestionByName('birthdate');
        if($qBdate!=null)
        {
            $minYear = (int) date("Y") - 7;
            $maxYear = (int) date("Y")- 75;
            $qBdate->custom = '{"year_range":{"from":'.$maxYear.',"to":'.$minYear.'}}';
            BOL_QuestionService::getInstance()->saveOrUpdateQuestion($qBdate);
        }
    }

//    public static function deleteMobileConfiguration(){
//        OW::getConfig()->saveConfig('base', 'disable_mobile_context', 1);
//
//        OW::getNavigation()->deleteMenuItem('mobile', 'mobile_admin_navigation');
//        OW::getNavigation()->deleteMenuItem('mobile', 'mobile_admin_pages_index');
//        OW::getNavigation()->deleteMenuItem('mobile', 'mobile_admin_pages_dashboard');
//        OW::getNavigation()->deleteMenuItem('mobile', 'mobile_admin_settings');
//        OW::getNavigation()->deleteMenuItem('mobile', 'mobile_pages_dashboard');
//    }

    public static function deleteExternalStaticPage($id){
        if(empty($id)){
            return;
        }
        $menu = BOL_NavigationService::getInstance()->findMenuItemById($id);
        if($menu!=null) {
            $service = BOL_NavigationService::getInstance();

            $languageService = BOL_LanguageService::getInstance();

            $langKey = $languageService->findKey($menu->getPrefix(), $menu->getKey());

            if (!empty($langKey)) {
                $list = $languageService->findAll();

                foreach ($list as $dto) {
                    $langValue = $languageService->findValue($dto->getId(), $langKey->getId());

                    if (empty($langValue)) {
                        continue;
                    }

                    $languageService->deleteValue($langValue);
                }

                $languageService->deleteKey($langKey->getId());
            }

            $service->deleteMenuItem($menu);
        }
    }

    public static function deleteStaticPages($id){
        if(empty($id)){
            return;
        }
        $menu = BOL_NavigationService::getInstance()->findMenuItemById($id);

        if($menu!=null) {

            $navigationService = BOL_NavigationService::getInstance();
            $navigationService->deleteMenuItem($menu);

            if (!empty($menu->getDocumentKey())) {
                $document = $navigationService->findDocumentByKey($menu->getDocumentKey());

                $navigationService->deleteDocument($document);

                $languageService = BOL_LanguageService::getInstance();


                $langKey = $languageService->findKey($menu->getPrefix(), $menu->getKey());
                $languageService->deleteKey($langKey->getId());

                $langKey = $languageService->findKey('base', 'local_page_meta_tags_' . $document->getKey());
                if ($langKey !== null) {
                    $languageService->deleteKey($langKey->getId());
                }

                $langKey = $languageService->findKey('base', 'local_page_title_' . $document->getKey());
                if ($langKey !== null) {
                    $languageService->deleteKey($langKey->getId());
                }

                $langKey = $languageService->findKey('base', 'local_page_content_' . $document->getKey());
                if ($langKey !== null) {
                    $languageService->deleteKey($langKey->getId());
                }
            }
        }
    }

    public static function removeUserProfileQuestions(){
        $questionsIdList = array(111, 112);
        BOL_QuestionService::getInstance()->deleteQuestion($questionsIdList);
    }

    public static function setAparatToResource(){
        $resources = BOL_TextFormatService::getInstance()->getMediaResourceList();
        $findAparatResource = false;
        foreach ($resources as $resource) {
            if(strpos($resource, self::$aparatResourceName)===0 || strpos($resource, self::$aparatResourceName)>0){
                $findAparatResource = true;
            }
        }
        if(!$findAparatResource){
            $resources[] = self::$aparatResourceName;
            OW::getConfig()->saveConfig('base', BOL_TextFormatService::CONF_MEDIA_RESOURCE_LIST, json_encode($resources));
        }
    }

    public function installComplete(){
        require_once OW_DIR_ROOT . 'ow_iis' . DS . 'language' . DS . 'update.php';

        //Update default theme
        self::updateDefaultTheme();

        //check persian language exist
        self::checkPersianLanguageExist();

        //set initial data
        self::setInitialData();

        //Update all plugins languages
        self::updateLanguages();

        //Update all languages
        IISLanguageUpdater::updateLanguageValues();

        //alter some table columns (add index key)
        self::alterToIndexedColumns();
    }


    /*
 * ALTER
 * ow_base_user_suspend.userId
 * ow_newsfeed_action_feed.activityId
 * ow_newsfeed_action_feed.feedId
 * ow_newsfeed_action_feed.feedType
 * ow_newsfeed_follow.feedId
 * ow_newsfeed_follow.feedTyp
 * ow_newsfeed_activity.privacy
 * ow_newsfeed_activity.visibility
 * ow_newsfeed_activity.status
 * COLUMNS TO INDEXED COLUMNS
 */
    public static function alterToIndexedColumns(){
        $hasTableKey = OW::getDbo()->queryForRow('SHOW INDEXES FROM ow_base_user_suspend WHERE Key_name = \'userId2\'');
        if (empty($hasTableKey)) {
            $query = 'ALTER TABLE `ow_base_user_suspend` ADD INDEX userId2 (userId)';
            OW::getDbo()->query($query);
        }
        $hasTableKey = OW::getDbo()->queryForRow('SHOW INDEXES FROM ow_newsfeed_action_feed WHERE Key_name = \'feedId\'');
        if (empty($hasTableKey)) {
            $query = 'ALTER TABLE `ow_newsfeed_action_feed` ADD INDEX feedId (feedId)';
            OW::getDbo()->query($query);
        }
        $hasTableKey = OW::getDbo()->queryForRow('SHOW INDEXES FROM ow_newsfeed_action_feed WHERE Key_name = \'feedType\'');
        if (empty($hasTableKey)) {
            $query = 'ALTER TABLE `ow_newsfeed_action_feed` ADD INDEX `feedType` (`feedType`)';
            OW::getDbo()->query($query);
        }
        $hasTableKey = OW::getDbo()->queryForRow('SHOW INDEXES FROM ow_newsfeed_follow WHERE Key_name = \'feedId\'');
        if (empty($hasTableKey)) {
            $query = 'ALTER TABLE `ow_newsfeed_follow` ADD INDEX feedId (feedId)';
            OW::getDbo()->query($query);
        }
        $hasTableKey = OW::getDbo()->queryForRow('SHOW INDEXES FROM ow_newsfeed_follow WHERE Key_name = \'feedType\'');
        if (empty($hasTableKey)) {
            $query = 'ALTER TABLE `ow_newsfeed_follow` ADD INDEX `feedType` (`feedType`)';
            OW::getDbo()->query($query);
        }
        $hasTableKey = OW::getDbo()->queryForRow('SHOW INDEXES FROM ow_newsfeed_activity WHERE Key_name = \'privacy\'');
        if (empty($hasTableKey)) {
            $query = 'ALTER TABLE `ow_newsfeed_activity` ADD INDEX `privacy` (`privacy`)';
            OW::getDbo()->query($query);
        }
        $hasTableKey = OW::getDbo()->queryForRow('SHOW INDEXES FROM ow_newsfeed_activity WHERE Key_name = \'visibility\'');
        if (empty($hasTableKey)) {
            $query = 'ALTER TABLE `ow_newsfeed_activity` ADD INDEX visibility (visibility)';
            OW::getDbo()->query($query);
        }
        $hasTableKey = OW::getDbo()->queryForRow('SHOW INDEXES FROM ow_newsfeed_activity WHERE Key_name = \'status\'');
        if (empty($hasTableKey)) {
            $query = 'ALTER TABLE `ow_newsfeed_activity` ADD INDEX `status` (`status`)';
            OW::getDbo()->query($query);
        }
    }
    public static function updateLanguages(){
//        $oldLocaledKey = self::getSavedLocaledLanguages();

        $plugins = BOL_PluginService::getInstance()->findActivePlugins();
        foreach ($plugins as $plugin) {
            $path = OW::getPluginManager()->getPlugin($plugin->getKey())->getRootDir() . 'langs.zip';
            if (file_exists($path)) {
//                $prefixId = BOL_LanguageService::getInstance()->findPrefixId($plugin->getKey());
//                if (!empty($prefixId)) {
//                    BOL_LanguageService::getInstance()->deletePrefix($prefixId, false);
//                }
//                echo "Installing langugage of plugin: ". $plugin->getKey() . "\n";
                BOL_LanguageService::getInstance()->importPrefixFromZip($path, $plugin->getKey(), false);
            }
        }

        $language_tag = 'fa-IR';
        $languagePersianDto = BOL_LanguageService::getInstance()->findByTag('fa-IR');
        $prefix_base_languages = array('base','admin','nav','mobile');
        foreach($prefix_base_languages as $prefixLanguage){
            $path =  OW_DIR_ROOT . 'ow_iis' . DS . 'translation'  . DS . $language_tag . DS . $prefixLanguage . DS . 'langs.zip';
            if (file_exists($path)) {
//                echo "Installing langugage of ". $prefixLanguage . "\n";
//                $prefix = BOL_LanguageService::getInstance()->findPrefix($prefixLanguage);
//                $keys = BOL_LanguageKeyDao::getInstance()->findAllPrefixKeys($prefix->getId());
//                $keysId = '';
//                foreach($keys as $key){
//                    $keysId = $keysId.$key->getId().',';
//                }
//                $keysId = substr($keysId, 0, -1);
//                $query = 'delete from '.OW_DB_PREFIX.'base_language_value where keyId in ('.$keysId.') and languageId = '. $languagePersianDto->getId();
//                OW::getDbo()->query($query);
                BOL_LanguageService::getInstance()->importPrefixFromZip($path, $prefixLanguage, false);
            }
        }

//        self::setSavedLocaledLanguages($oldLocaledKey);
        BOL_LanguageService::getInstance()->generateCacheForAllActiveLanguages();
    }

    public static function checkPersianLanguageExist(){
        $languagePersianDto = BOL_LanguageService::getInstance()->findByTag('fa-IR');
        if (!empty($languagePersianDto) && $languagePersianDto->status == "active") {
            self::setDefaultLanguageToPersian($languagePersianDto);
        }else if(empty($languagePersianDto) || $languagePersianDto->status != "active"){
            //Update english order
            $languageEnDto = BOL_LanguageService::getInstance()->findByTag('en');
            $languageEnOrder = $languageEnDto->getOrder();
            $languageEnDto->setOrder(BOL_LanguageService::getInstance()->findMaxOrder()+1);
            BOL_LanguageService::getInstance()->save($languageEnDto);

            $persian_label = 'فارسی';
            $persian_tag = 'fa-IR';
            $persian_status = 'active';
            $persian_order = $languageEnOrder;
            $persian_rtl = true;

            if(empty($languagePersianDto)) {
                //Insert persian languages
                $languagePersianDto = new BOL_Language();
            }

            $languagePersianDto->setLabel($persian_label)
                ->setTag($persian_tag)
                ->setStatus($persian_status)
                ->setOrder($persian_order)
                ->setRtl($persian_rtl);

            BOL_LanguageService::getInstance()->save($languagePersianDto);
            self::setDefaultLanguageToPersian($languagePersianDto);
        }
    }

    public static function setDefaultLanguageToPersian($languagePersianDto){
        BOL_LanguageService::getInstance()->setCurrentLanguage($languagePersianDto, false);
        OW::getSession()->set('base.language_id', $languagePersianDto->getId());
        setcookie('base_language_id', (string) $languagePersianDto->getId(), time() + 60 * 60 * 24 * 30, "/");
    }

    public static function updateDefaultTheme(){
        try {
            if (defined('OW_DB_HOST') && !OW::getThemeManager()->getThemeService()->themeExists(BOL_ThemeService::DEFAULT_THEME)) {
                OW::getThemeManager()->getThemeService()->updateThemeList();
                OW::getConfig()->saveConfig('base', 'selectedTheme', BOL_ThemeService::DEFAULT_THEME);
            }
        }catch(Exception $e){
            //Do nothing
        }
    }

    public static function onBeforeEmailSend(OW_Event $event)
    {
        $styleDivConstant = 'border: solid 1px #BEE6BE;padding: 10px;';

        $styleDiv = '';
        $styleIMG = '';
        $data = $event->getData();
        $float = 'right';
        $direction = 'rtl';
        if(BOL_LanguageService::getInstance()->getCurrent()->getRtl()){
            $styleDiv = 'style="direction: rtl;' . $styleDivConstant . '"';
            $styleIMG = 'style="float: right;max-width: 50px"';
        }else{
            $styleDiv = 'style="direction: ltr;' . $styleDivConstant . '"';
            $styleIMG = 'style="float: right;max-width: 50px"';
            $float = 'left';
            $direction = 'ltr';
        }

        BOL_MailService::getInstance()->getMailer()->addEmbeddedImage(OW::getThemeManager()->getCurrentTheme()->getImagesDir(). 'logo.png','embed_logo');
        $sign = "<br><br><br><br><br>".OW::getLanguage()->text('base', 'ow_mail_information');
        $oldData = $data['htmlContent'];
        $data['htmlContent'] = '<div style="padding: 1%;border-bottom: solid 1px lightgray;margin-bottom: 10px;width: 98%;float: '.$float.';"><img ' . $styleIMG . '  src="cid:embed_logo" />';
        $data['htmlContent'] .= '<span style="font-size: 40px;margin-right: 20px;float: '.$float.';direction: '.$direction.'">'.OW::getConfig()->getValue('base', 'site_name').'</span></div>';
        $data['htmlContent'] .= '<div ' . $styleDiv . '>' . $oldData . $sign . '</div>';
        $event->setData($data);
    }

    public static function getTableBackupName($table_name){
        return self::$prefixBackuplabel . $table_name;
    }

    public static function updateStaticFiles(){
        $plugins = BOL_PluginService::getInstance()->findAllPlugins();
        foreach($plugins as $plugin){
            $pluginStaticDir = OW_DIR_PLUGIN . $plugin->getModule() . DS . 'static' . DS;

            if ( !defined('OW_PLUGIN_XP') && file_exists($pluginStaticDir) )
            {
                $staticDir = OW_DIR_STATIC_PLUGIN . $plugin->getModule() . DS;

                if ( !file_exists($staticDir) )
                {
                    mkdir($staticDir);
                    chmod($staticDir, 0777);
                }

                UTIL_File::copyDir($pluginStaticDir, $staticDir);
            }
        }

//        BOL_ThemeService::getInstance()->updateThemeList();
        try{
            BOL_ThemeService::getInstance()->processAllThemes();
        }catch(Exception $e){
            //Do nothing
        }

    }

    public static function checkDiff(OW_Event $event){
        $params = $event->getParams();
        $phpExtenstions = get_loaded_extensions();
        if(isset($params['diff'])){
            $diff = $params['diff'];
            $keyMySql = array_search('mysql',$diff);
            if($keyMySql!==false && array_search('mysqli',$phpExtenstions)!==false) {
                unset($diff[$keyMySql]);
            }

            $event->setData(array('diff' => $diff));
        }
    }


    public static function checkMasterPageBlankHtml(OW_Event $event){
        $params = $event->getParams();
        if(isset($params['assignedVars']) && isset($params['viewRenderer']) && isset($params['assignedVars']['pageSimple'])){
            $params['viewRenderer']->assignVar('pageSimple',1);
        }
    }
    public static function getSavedLocaledLanguages(){
        $query = 'SELECT * FROM `ow_base_language_key` l_key,`ow_base_language_value` l_value,`ow_base_language_prefix` l_prefix  WHERE l_prefix.`id` = l_key.`prefixId` and l_value.`keyId` =  l_key.`id` and l_key.`key` LIKE \'%page_%\'';
        $result = OW::getDbo()->queryForList($query);
        return $result;
    }

    public static function setSavedLocaledLanguages($result){

        foreach($result as $row){
            $langKey = BOL_LanguageService::getInstance()->findKey($row['prefix'], $row['key']);
            if ( !empty($langKey) )
            {
                $langValue = BOL_LanguageService::getInstance()->findValue($row['languageId'], $row['keyId']);

                if ( $langValue === null )
                {
                    $langValue = new BOL_LanguageValue();
                    $langValue->setKeyId($row['keyId']);
                    $langValue->setLanguageId($row['languageId']);
                    BOL_LanguageService::getInstance()->saveValue($langValue->setValue($row['value']), false);
                }
            }
        }
    }

    public static function createUser($username, $email, $password, $date, $sex,$accountType=null){
        $user = BOL_UserService::getInstance()->findByUsername($username);
        if($user!=null){
            self::deleteUser($user->username);
        }

        $user = BOL_UserService::getInstance()->createUser($username, $password, $email,$accountType,true);
        $questionService = BOL_QuestionService::getInstance();
        $data = array();
        $data['username'] =   $username;
        $data['email'] =  $email;
        $data['realname'] = $username;
        $data['sex'] = $sex;
        $data['birthdate'] = $date;
        $questionService->saveQuestionsData($data, $user->getId());
    }

    public static function deleteUser($username){
        $user = BOL_UserService::getInstance()->findByUsername($username);
        if($user!=null) {
            BOL_QuestionService::getInstance()->deleteQuestionDataByUserId($user->getId());
            BOL_UserService::getInstance()->deleteUser($user->getId());
        }
    }

    public static function installAllAvailablePlugins(){
        $availablePlugins = BOL_PluginService::getInstance()->getAvailablePluginsList();
        foreach($availablePlugins as $availablePlugin ) {
            if ($availablePlugin['key'] == 'iispreloader' || $availablePlugin['key'] == 'iispiwik') {
                continue;
            }
            echo '>';
            $plugin = BOL_PluginService::getInstance()->install($availablePlugin['key']);
            OW::getPluginManager()->initPlugin(OW::getPluginManager()->getPlugin($plugin->getKey()));
        }
    }

    public static function setAlbumCoverDefault(OW_Event $event)
    {
        $params = $event->getParams();
        if(isset($params['albumId'])){
            $albumId = $params['albumId'];

            $coverDao = PHOTO_BOL_PhotoAlbumCoverDao::getInstance();

            if ( ($coverDto = $coverDao->findByAlbumId($albumId)) === null )
            {
                if ( ($photo = PHOTO_BOL_PhotoAlbumService::getInstance()->getLastPhotoByAlbumId($albumId)) === null )
                {
                    $coverUrl = $coverDao->getAlbumCoverDefaultUrl();
                }
                else
                {
                    $coverUrl = PHOTO_BOL_PhotoService::getInstance()->getPhotoUrlByType($photo->id, PHOTO_BOL_PhotoService::TYPE_MAIN, $photo->hash, !empty($photo->dimension) ? $photo->dimension : false);
                }

                $event->setData(array('coverUrl' => $coverUrl));
            }
        }
    }

    public static function existPluginKeyInActivePlugins($activePlugins, $key){
        foreach($activePlugins as $activePlugin){
            if($activePlugin->key == $key){
                return true;
            }
        }

        return false;
    }

    public function onBeforeActionsListReturn(OW_Event $event){
        $params = $event->getParams();
        if(isset($params['limit']) && isset($params['driver'])){
            $limit = $params['limit'];
            $driver = $params['driver'];
            $idList = $params['idList'];
            $result = array();
            $count = (floor($limit[1]/($limit[1]-self::$checkingLoadMorePeriod)))*$limit[0]+sizeof($idList);
            if(isset($limit[2]) && $limit[2] && sizeof($idList)>=$limit[1] && $driver!=null){
                $result['count'] = $count;
                array_pop($idList);
                $result['idList'] = $idList;
                $event->setData($result);
            }else if($driver!=null){
                $result['count'] = $count;
                $result['idList'] = $idList;
                $event->setData($result);
            }
        }
    }

    public function onAfterPluginUnistall(OW_Event $event){
        $params = $event->getParams();
        if(isset($params['tables']) && is_array($params['tables'])){
            $tables = $params['tables'];
            foreach($tables as $table){
                $removeTriggerName = self::$removeTriggerNameBackupTable . $table;
                $updateTriggerName = self::$updateTriggerNameBackupTable . $table;
                $triggerQueryForDoinBackupBeforeRemove = 'DROP TRIGGER IF EXISTS ' . $removeTriggerName . ';';
                OW::getDbo()->query($triggerQueryForDoinBackupBeforeRemove);

                $triggerQueryForDoinBackupBeforeRemove = 'DROP TRIGGER IF EXISTS ' . $updateTriggerName . ';';
                OW::getDbo()->query($triggerQueryForDoinBackupBeforeRemove);

                $backupTableName = self::getTableBackupName($table);
                $newBackupTableName = str_replace(self::$prefixBackuplabel, self::$prefixRemovedlabel ,$backupTableName) . '_' .UTIL_String::getRandomString(4, UTIL_String::RND_STR_ALPHA_WITH_CAPS_NUMERIC);
                $backupTable = OW::getDbo()->queryForRow('show tables like :tableName', array('tableName' => $backupTableName));
                if (!empty($backupTable)) {
                    $queryForReplaceTableName = 'RENAME TABLE '.$backupTableName.' TO ' . $newBackupTableName . ';';
                    try{
                        OW::getDbo()->query($queryForReplaceTableName);
                    }catch(Exception $e){
                        //Do nothing
                    }
                }
            }
        }
    }

    public function decideToShowCurrencySetting(OW_Event $event){
        $event->setData(array('hide' => true));
    }

    public static function multipleLanguageSentenceAlignmentCorrection(OW_Event $event){
        $params = $event->getParams();
        $correctedSentence="";
        if(isset($params['sentence'])){
            $correctedSentence = "&#x202B ".$params['sentence'];
        }
        $event->setData(array('correctedSentence' => $correctedSentence));
    }

    public function validateHtmlContent(OW_Event $event){
        $params = $event->getParams();
        if(isset($params['settingList']) && $this->isHTMLValidatorExtensionExist()){
            $settingList = $params['settingList'];
            if (isset($settingList['content'])) {
                $tidy = new tidy;
                $content = '<!DOCTYPE html><html><head><title></title></head><body>' . $settingList['content'] . '</body></html>';
                $tidy->parseString($content, array(), 'utf8');
                if ($tidy->errorBuffer != null) {
                    $errorsText = str_replace("\r\n", '<br>', UTIL_HtmlTag::escapeHtml(preg_replace('/line[\s\S]+?-/', '', $tidy->errorBuffer)));
                    $exceptionText = OW::getLanguage()->text('base', 'html_error');
                    $exceptionText .= '<span class="ow_button"><span><input class="accordion ow_ic_info" type="button" onclick="initAccordionButtonsProcessing(this);" value="'.OW::getLanguage()->text('base', 'html_error_details').'"></span></span>';
                    $exceptionText .= '<div class="html_error_content_panel" style="direction: ltr !important; text-align: left;">' . $errorsText . '</div>';
                    throw new WidgetSettingValidateException($exceptionText, 'content');
                }
            }
        }
    }

    public function beforeAllowCustomizationChanged(OW_Event $event){
        $params = $event->getParams();
        if(!$this->isHTMLValidatorExtensionExist() && isset($params['placeName']) && in_array($params['placeName'], array('profile', 'dashboard', 'group'))){
            $event->setData(array('error' => true));
        }
    }

    public function beforeCustomizationPageRenderer(OW_Event $event){
        $params = $event->getParams();
        if(isset($params['this']) && !$this->isHTMLValidatorExtensionExist() && isset($params['customizeAllowed']) && isset($params['placeName'])){
            if(in_array($params['placeName'], array('profile', 'dashboard', 'group'))){
                if ($params['customizeAllowed']) {
                    BOL_ComponentAdminService::getInstance()->saveAllowCustomize($params['placeName'], false);
                }
                $params['this']->assign('allowCustomizationLocked' , true);
                $event->setData(array('customizeAllowed' => false));
            }
        }
    }

    public function isHTMLValidatorExtensionExist(){
        return extension_loaded('tidy');
    }

    public function partialHalfSpaceCodeCorrection(OW_Event $event){
        $params = $event->getParams();
        if(isset($params['sentence']))
        {
            $sentence = $params['sentence'];
        }
        if(isset($params['trimLength']))
        {
            $trimLength = $params['trimLength'];
        }
        $sentence =  UTIL_String::truncate($sentence, $trimLength);
        $specificCharacters = substr($sentence, -6);
        if(strcmp($specificCharacters,"&zwnj;")==0){
            $correctedSentence = substr($sentence, 0,strlen($sentence)-6);
        }
        else {
            $specificCharacters = substr($sentence, -5);
            if (strcmp($specificCharacters, "&zwnj") == 0) {
                $correctedSentence = substr($sentence, 0,strlen($sentence)-5);
            }
            else {
                $specificCharacters = substr($sentence, -4);
                if (strcmp($specificCharacters, "&zwn") == 0) {
                    $correctedSentence = substr($sentence, 0,strlen($sentence)-4);
                }
                else {
                    $specificCharacters = substr($sentence, -3);
                    if (strcmp($specificCharacters, "&zw") == 0) {
                        $correctedSentence = substr($sentence, 0,strlen($sentence)-3);
                    }
                    else {
                        $specificCharacters = substr($sentence, -2);
                        if (strcmp($specificCharacters, "&z") == 0) {
                            $correctedSentence = substr($sentence, 0,strlen($sentence)-2);
                        }
                        else {
                            $specificCharacters = substr($sentence, -1);
                            if (strcmp($specificCharacters, "&") == 0) {
                                $correctedSentence = substr($sentence, 0,strlen($sentence)-1);
                            }
                        }
                    }

                }
            }
        }
        if(isset($correctedSentence)){
            $event->setData(array('correctedSentence' => $correctedSentence));
        }
    }

    public function setDistinguishForRequiredField(OW_Event $event){
        $params = $event->getParams();
        if(isset($params['element'])) {
            if ($params['element']->isRequired()) {
                $label = $params['element']->getLabel();
                if(strpos($label, 'ow_required_star')===false) {
                    $label .= '<span class="ow_required_star">*<span>';
                    $event->setData(array('distinguishedRequiredLabels' => $label));
                }
            }
        }
    }

    public function onBeforeNewsFeedStatusStringWrite( OW_Event $event )
    {
        $params = $event->getParams();
        if (isset($params['string'])) {
            $event->setData(array( 'string' => $this->setHomeUrlVariable($params['string'])));
        }
    }

    public function onAfterNewsFeedStatusStringRead( OW_Event $event )
    {
        $params = $event->getParams();
        if (isset($params['string'])) {
            $event->setData(array( 'string' => $this->correctHomeUrlVariable($params['string'])));
        }
    }

    public function onAfterNotificationDataRead( OW_Event $event )
    {
        $params = $event->getParams();
        if (isset($params['data'])) {
            $data = $params['data'];
            if(!empty($data)) {
                $data = json_decode($data, true);
                if (isset($data['string']['vars']['receiver'])) {
                    $data['string']['vars']['receiver'] = $this->correctHomeUrlVariable($data['string']['vars']['receiver']);
                }
                if(isset($data['string']['vars']['actorUrl'])){
                    $data['string']['vars']['actorUrl'] = $this->correctHomeUrlVariable($data['string']['vars']['actorUrl']);
                }
                if(isset($data['string']['vars']['url'])){
                    $data['string']['vars']['url'] = $this->correctHomeUrlVariable($data['string']['vars']['url']);
                }
                if(isset($data['string']['vars']['userUrl'])){
                    $data['string']['vars']['userUrl'] = $this->correctHomeUrlVariable($data['string']['vars']['userUrl']);
                }
                if(isset($data['string']['vars']['videoUrl'])){
                    $data['string']['vars']['videoUrl'] = $this->correctHomeUrlVariable($data['string']['vars']['videoUrl']);
                }
                if(isset($data['string']['vars']['photoUrl'])){
                    $data['string']['vars']['photoUrl'] = $this->correctHomeUrlVariable($data['string']['vars']['photoUrl']);
                }
                if(isset($data['string']['vars']['postUrl'])){
                    $data['string']['vars']['postUrl'] = $this->correctHomeUrlVariable($data['string']['vars']['postUrl']);
                }
                if(isset($data['string']['vars']['topicUrl'])){
                    $data['string']['vars']['topicUrl'] = $this->correctHomeUrlVariable($data['string']['vars']['topicUrl']);
                }
                if (isset($data['avatar']['src'])) {
                    $data['avatar']['src'] = $this->correctHomeUrlVariable($data['avatar']['src']);
                }
                if (isset($data['avatar']['url'])) {
                    $data['avatar']['url'] = $this->correctHomeUrlVariable($data['avatar']['url']);
                }
                if(isset($data['contentImage']['src'])){
                    $data['contentImage']['src'] = $this->correctHomeUrlVariable($data['contentImage']['src']);
                }else if(isset($data['contentImage'])){
                    $data['contentImage'] = $this->correctHomeUrlVariable($data['contentImage']);
                }
                if (isset($data['url'])) {
                    $data['url'] = $this->correctHomeUrlVariable($data['url']);
                }

                $event->setData(array('data' => $data));
            }
        }
    }

    public function onBeforeNotificationDataWrite( OW_Event $event )
    {
        $params = $event->getParams();
        if (isset($params['data'])) {
            $data = $params['data'];

            if(isset($data['string']['vars']['receiver'])){
                $data['string']['vars']['receiver'] = $this->setHomeUrlVariable($data['string']['vars']['receiver']);
            }
            if(isset($data['string']['vars']['actorUrl'])){
                $data['string']['vars']['actorUrl'] = $this->setHomeUrlVariable($data['string']['vars']['actorUrl']);
            }
            if(isset($data['string']['vars']['url'])){
                $data['string']['vars']['url'] = $this->setHomeUrlVariable($data['string']['vars']['url']);
            }
            if(isset($data['string']['vars']['userUrl'])){
                $data['string']['vars']['userUrl'] = $this->setHomeUrlVariable($data['string']['vars']['userUrl']);
            }
            if(isset($data['string']['vars']['videoUrl'])){
                $data['string']['vars']['videoUrl'] = $this->setHomeUrlVariable($data['string']['vars']['videoUrl']);
            }
            if(isset($data['string']['vars']['photoUrl'])){
                $data['string']['vars']['photoUrl'] = $this->setHomeUrlVariable($data['string']['vars']['photoUrl']);
            }
            if(isset($data['string']['vars']['postUrl'])){
                $data['string']['vars']['postUrl'] = $this->setHomeUrlVariable($data['string']['vars']['postUrl']);
            }
            if(isset($data['string']['vars']['topicUrl'])){
                $data['string']['vars']['topicUrl'] = $this->setHomeUrlVariable($data['string']['vars']['topicUrl']);
            }
            if(isset($data['avatar']['src'])){
                $data['avatar']['src'] = $this->setHomeUrlVariable($data['avatar']['src']);
            }
            if(isset($data['avatar']['url'])){
                $data['avatar']['url'] = $this->setHomeUrlVariable($data['avatar']['url']);
            }
            if(isset($data['contentImage']['src'])){
                $data['contentImage']['src'] = $this->setHomeUrlVariable($data['contentImage']['src']);
            }else if(isset($data['contentImage'])){
                $data['contentImage'] = $this->setHomeUrlVariable($data['contentImage']);
            }
            if(isset($data['url'])){
                $data['url'] = $this->setHomeUrlVariable($data['url']);
            }
            $event->setData(array( 'data' => $data));
        }
    }


    public function setHomeUrlVariable($string){
        return str_replace(OW_URL_HOME, '$$BASE_URL$$', $string);
    }

    public function correctHomeUrlVariable($string){
        return preg_replace('/\$\$BASE_URL\$\$/', OW_URL_HOME, $string);
    }

    public function onAfterGetTplData( OW_Event $event )
    {
        $params = $event->getParams();
        $hasMobileVersion = true;
        if (isset($params['item'])) {
            $item = $params['item'];
            if(!empty($item["disabled"]) && $item["disabled"]){
                $hasMobileVersion = false;
            }
            $event->setData(array( 'hasMobileVersion' => $hasMobileVersion));
        }
    }

    public function isMobileVersion( OW_Event $event )
    {
        $params = $event->getParams();
        if(isset($params['check']) && $params['check']==true) {
            if (OW::getApplication()->getContext() == OW::CONTEXT_MOBILE) {
                $event->setData(array('isMobileVersion' => true));
            } else {
                $event->setData(array('isMobileVersion' => false));
            }
        }
    }

}