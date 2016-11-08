<?php

/**
 * Created by Yaser Alimardany.
 * User: pars
 * Date: 6/6/2016
 * Time: 10:59 AM
 */
class IISLanguageUpdater
{
    private static $classInstance;

    /**
     * Returns an instance of class (singleton pattern implementation).
     *
     * @return OW_EventManager
     */
    public static function getInstance()
    {
        if (IISLanguageUpdater::$classInstance === null) {
            IISLanguageUpdater::$classInstance = new IISLanguageUpdater();
        }

        return IISLanguageUpdater::$classInstance;
    }

    public static function updateLanguageValues()
    {
        if(UPDATE_DIR_ROOT=='UPDATE_DIR_ROOT') {
            define('UPDATE_DIR_ROOT', OW_DIR_ROOT . 'ow_updates' . DS);
        }
        require_once UPDATE_DIR_ROOT . 'classes' . DS . 'autoload.php';
        require_once UPDATE_DIR_ROOT . 'classes' . DS . 'updater.php';

        if (!class_exists('BASE_CLASS_EventCollector')) {
            OW::getPluginManager()->initPlugins();
        }

        spl_autoload_register(array('UPDATE_Autoload', 'autoload'));

        $autoloader = UPDATE_Autoload::getInstance();
        try {
            $autoloader->addPackagePointer('BOL', OW_DIR_SYSTEM_PLUGIN . 'base' . DS . 'bol' . DS);
            $autoloader->addPackagePointer('OW', OW_DIR_CORE);
            $autoloader->addPackagePointer('UTIL', OW_DIR_UTIL);
            $autoloader->addPackagePointer('UPDATE', UPDATE_DIR_ROOT . 'classes' . DS);
        }catch(Exception $e){
            //
        }
        $languageService = Updater::getLanguageService();
        $languages = $languageService->getLanguages();
        $langEnId = null;
        $langFaId = null;
        foreach ($languages as $lang) {
            if ($lang->tag == 'en') {
                $langEnId = $lang->id;
            }
            if ($lang->tag == 'fa-IR') {
                $langFaId = $lang->id;
            }
        }

        $activePlugins = BOL_PluginService::getInstance()->findActivePlugins();
        if (!is_null($langEnId)) {
            self::updateBaseOfEnLanguageValues($langEnId, $languageService);
            self::updatePluginsOfEnLanguageValues($langEnId, $languageService, $activePlugins);
        }

        if (!is_null($langFaId)) {
            self::updateBaseOfFaLanguageValues($langFaId, $languageService);
            self::updatePluginsOfFaLanguageValues($langFaId, $languageService, $activePlugins);
        }

        BOL_LanguageService::getInstance()->generateCacheForAllActiveLanguages();
    }

    public static function updatePluginsOfEnLanguageValues($langEnId, $languageService, $activePlugins)
    {
        //forum plugins
        if (IISSecurityProvider::existPluginKeyInActivePlugins($activePlugins, 'forum')) {
            $languageService->addOrUpdateValue($langEnId, 'forum', 'admin_forum_settings_heading', 'Forum plugin settings');
            $languageService->addOrUpdateValue($langEnId, 'forum', 'forum_sitemap', 'Forum');
            $languageService->addOrUpdateValue($langEnId, 'forum', 'seo_meta_section', 'Forum');
            $languageService->addOrUpdateValue($langEnId, 'forum', 'seo_meta_home_label', 'Forum Homepage');
            $languageService->addOrUpdateValue($langEnId, 'forum', 'meta_title_home', 'Forum | {$site_name}');
            $languageService->addOrUpdateValue($langEnId, 'forum', 'meta_desc_home', 'Welcome to the discussion forum at {$site_name}. Create new posts, read what others have to say, and join the conversation.');
            $languageService->addOrUpdateValue($langEnId, 'forum', 'meta_keywords_home', '');
            $languageService->addOrUpdateValue($langEnId, 'forum', 'seo_meta_adv_search_label', 'Forum Advanced Search Page');
            $languageService->addOrUpdateValue($langEnId, 'forum', 'meta_title_adv_search', 'Advanced search {$site_name} Forum');
            $languageService->addOrUpdateValue($langEnId, 'forum', 'meta_desc_adv_search', 'Use advanced search to find information you need within {$site_name} forum.');
            $languageService->addOrUpdateValue($langEnId, 'forum', 'meta_keywords_adv_searche', '');
            $languageService->addOrUpdateValue($langEnId, 'forum', 'seo_meta_adv_search_result_label', 'Forum Advanced Search Results Page');
            $languageService->addOrUpdateValue($langEnId, 'forum', 'meta_title_adv_search_result', 'Search results for {$site_name} Forum');
            $languageService->addOrUpdateValue($langEnId, 'forum', 'meta_desc_adv_search_result', 'View the results of your forum search on {$site_name}.');
            $languageService->addOrUpdateValue($langEnId, 'forum', 'meta_keywords_adv_searche_result', '');
            $languageService->addOrUpdateValue($langEnId, 'forum', 'seo_meta_section_label', 'Separate Sub-forum Page');
            $languageService->addOrUpdateValue($langEnId, 'forum', 'meta_title_section', '{$section_name} at {$site_name} Forum');
            $languageService->addOrUpdateValue($langEnId, 'forum', 'meta_desc_section', '{$section_name}');
            $languageService->addOrUpdateValue($langEnId, 'forum', 'meta_keywords_section', '');
            $languageService->addOrUpdateValue($langEnId, 'forum', 'seo_meta_group_label', 'Separate Sub-forum Category Page');
            $languageService->addOrUpdateValue($langEnId, 'forum', 'meta_title_group', '{$group_name} at {$site_name} Forum');
            $languageService->addOrUpdateValue($langEnId, 'forum', 'meta_desc_group', '{$group_description}');
            $languageService->addOrUpdateValue($langEnId, 'forum', 'meta_keywords_group', '');
            $languageService->addOrUpdateValue($langEnId, 'forum', 'seo_meta_topic_label', 'Separate Forum Thread Page');
            $languageService->addOrUpdateValue($langEnId, 'forum', 'meta_title_topic', '{$topic_name} at {$site_name} Forum');
            $languageService->addOrUpdateValue($langEnId, 'forum', 'meta_desc_topic', '{$topic_description}');
            $languageService->addOrUpdateValue($langEnId, 'forum', 'meta_keywords_topic', '');
            $languageService->addOrUpdateValue($langEnId, 'forum', 'seo_meta_section_search_label', 'Separate Sub-forum Search Page');
            $languageService->addOrUpdateValue($langEnId, 'forum', 'meta_title_section_search', '{$section_name} search at {$site_name} Forum');
            $languageService->addOrUpdateValue($langEnId, 'forum', 'meta_desc_section_search', 'Search results for {$section_name} sub-forum on {$site_name} forum.');
            $languageService->addOrUpdateValue($langEnId, 'forum', 'meta_keywords_section_search', '');
            $languageService->addOrUpdateValue($langEnId, 'forum', 'seo_meta_group_search_label', 'Sub-forum Category Search Page');
            $languageService->addOrUpdateValue($langEnId, 'forum', 'meta_title_group_search', '{$group_name} search at {$site_name} Forum');
            $languageService->addOrUpdateValue($langEnId, 'forum', 'meta_desc_group_search', 'Search results for {$group_name} threads on {$site_name} forum.');
            $languageService->addOrUpdateValue($langEnId, 'forum', 'meta_keywords_group_search', '');
            $languageService->addOrUpdateValue($langEnId, 'forum', 'seo_meta_topic_search_label', 'Forum Thread Search');
            $languageService->addOrUpdateValue($langEnId, 'forum', 'meta_title_topic_search', '{$topic_name} search at {$site_name} Forum');
            $languageService->addOrUpdateValue($langEnId, 'forum', 'meta_desc_topic_search', 'Search results for {$topic_name} topic on {$site_name} forum.');
            $languageService->addOrUpdateValue($langEnId, 'forum', 'meta_keywords_topic_search', '');
        }
        //iisblockingip plugins
        if (IISSecurityProvider::existPluginKeyInActivePlugins($activePlugins, 'iisblockingip')) {
            $languageService->addOrUpdateValue($langEnId, 'iisblockingip', 'admin_page_heading', 'blocking-ip plugin settings');
            $languageService->addOrUpdateValue($langEnId, 'iisblockingip', 'admin_page_title', 'blocking-ip plugin settings');
        }
        //iisrules plugins
        if (IISSecurityProvider::existPluginKeyInActivePlugins($activePlugins, 'iisrules')) {
            $languageService->addOrUpdateValue($langEnId, 'iisrules', 'admin_page_heading', 'Rules plugin settings');
            $languageService->addOrUpdateValue($langEnId, 'iisrules', 'admin_page_title', 'Rules plugin settings');
            $languageService->addOrUpdateValue($langEnId, 'iisrules', 'guidelineFieldLabel', 'Guideline text');
            $languageService->addOrUpdateValue($langEnId, 'iisrules', 'filer_by_category', 'Categories');
        }
        //iiscontactus plugins
        if (IISSecurityProvider::existPluginKeyInActivePlugins($activePlugins, 'iiscontactus')) {
            $languageService->addOrUpdateValue($langEnId, 'iiscontactus', 'admin_contactus_settings_heading', 'Contact us settings');
			
			$languageService->addOrUpdateValue($langEnId, 'iiscontactus', 'modified_successfully', 'Changes saved successfully');
        }
        //iisimport plugins
        if (IISSecurityProvider::existPluginKeyInActivePlugins($activePlugins, 'iisimport')) {
            $languageService->addOrUpdateValue($langEnId, 'iisimport', 'admin_page_heading', 'Import plugin settings');
            $languageService->addOrUpdateValue($langEnId, 'iisimport', 'admin_page_title', 'Import plugin settings');
        }
        //iiscontrolkids plugins
        if (IISSecurityProvider::existPluginKeyInActivePlugins($activePlugins, 'iiscontrolkids')) {
            $languageService->addOrUpdateValue($langEnId, 'iiscontrolkids', 'admin_page_heading', 'Control kids plugin settings');
            $languageService->addOrUpdateValue($langEnId, 'iiscontrolkids', 'admin_page_title', ' Control kids plugin settings');
        }
        //iispasswordstrengthmeter plugins
        if (IISSecurityProvider::existPluginKeyInActivePlugins($activePlugins, 'iispasswordstrengthmeter')) {
            $languageService->addOrUpdateValue($langEnId, 'iispasswordstrengthmeter', 'admin_page_heading', 'Password strength meter plugin settings');
            $languageService->addOrUpdateValue($langEnId, 'iispasswordstrengthmeter', 'admin_page_title', 'Password strength meter plugin settings');
        }
        //iisuserlogin plugins
        if (IISSecurityProvider::existPluginKeyInActivePlugins($activePlugins, 'iisuserlogin')) {
            $languageService->addOrUpdateValue($langEnId, 'iisuserlogin', 'admin_page_heading', 'User login plugin settings');
            $languageService->addOrUpdateValue($langEnId, 'iisuserlogin', 'admin_page_title', 'User login plugin settings');
        }
        //iisterms plugins
        if (IISSecurityProvider::existPluginKeyInActivePlugins($activePlugins, 'iisterms')) {
            $languageService->addOrUpdateValue($langEnId, 'iisterms', 'admin_page_heading', 'Terms plugin settings');
            $languageService->addOrUpdateValue($langEnId, 'iisterms', 'admin_page_title', 'Terms plugin settings');
			$languageService->addOrUpdateValue($langEnId, 'iisterms', 'mobile_notification_content', '<a href="{$url}">A new version of {$value1} released. {$value2} important items changed, added or removed.</a>');
			$languageService->addOrUpdateValue($langEnId, 'iisterms', 'mobile_bottom_menu_item', 'Terms');
        }
        //iismutual plugins
        if (IISSecurityProvider::existPluginKeyInActivePlugins($activePlugins, 'iismutual')) {
            $languageService->addOrUpdateValue($langEnId, 'iismutual', 'admin_page_heading', 'Mutual friends plugin setting');
            $languageService->addOrUpdateValue($langEnId, 'iismutual', 'admin_page_title', 'Mutual friends plugin setting');
        }
        //iispasswordchangeinterval plugins
        if (IISSecurityProvider::existPluginKeyInActivePlugins($activePlugins, 'iispasswordchangeinterval')) {
            $languageService->addOrUpdateValue($langEnId, 'iispasswordchangeinterval', 'admin_page_heading', 'Password interval change settings');
            $languageService->addOrUpdateValue($langEnId, 'iispasswordchangeinterval', 'admin_page_title', 'Password interval change settings');
        }
        //notifications plugins
        if (IISSecurityProvider::existPluginKeyInActivePlugins($activePlugins, 'notifications')) {
            $languageService->addOrUpdateValue($langEnId, 'notifications', 'email_txt_head', 'Dear user ({$userName}), Here shows activities related to you at {$site_name}. ');
			
			$languageService->addOrUpdateValue($langEnId, 'notifications', 'email_txt_bottom', '');
			
        }

        //iissecurityessentials plugin
        if (IISSecurityProvider::existPluginKeyInActivePlugins($activePlugins, 'iissecurityessentials')) {
            $languageService->addOrUpdateValue($langEnId, 'iissecurityessentials', 'verify_using_code', 'Account activation via the verification code that has been sent');
            $languageService->addOrUpdateValue($langEnId, 'iissecurityessentials', 'admin_page_heading', 'Security essentials plugin settings');
            $languageService->addOrUpdateValue($langEnId, 'iissecurityessentials', 'admin_page_title', 'Security essentials plugin settings');

        }

        //video plugin
        if (IISSecurityProvider::existPluginKeyInActivePlugins($activePlugins, 'video')) {
            $languageService->addOrUpdateValue($langEnId, 'video', 'menu_latest', 'Latest Public');
            $languageService->addOrUpdateValue($langEnId, 'video', 'admin_page_heading', 'Video plugin settings');
            $languageService->addOrUpdateValue($langEnId, 'video', 'admin_page_title', 'Video plugin settings');
            $languageService->addOrUpdateValue($langEnId, 'video', 'video_sitemap', 'Video');
            $languageService->addOrUpdateValue($langEnId, 'video', 'seo_meta_section', 'Video');
            $languageService->addOrUpdateValue($langEnId, 'video', 'seo_meta_tagged_list_label', 'All Tagged Videos Page');
            $languageService->addOrUpdateValue($langEnId, 'video', 'meta_title_tagged_list', 'Tagged Video | {$site_name}');
            $languageService->addOrUpdateValue($langEnId, 'video', 'meta_desc_tagged_list', 'Watch all tagged videos at {$site_name}.');
            $languageService->addOrUpdateValue($langEnId, 'video', 'meta_keywords_tagged_list', '');
            $languageService->addOrUpdateValue($langEnId, 'video', 'seo_meta_view_list_label', 'Videos by List Type (Latest / Top Rated / Most Discussed) Page');
            $languageService->addOrUpdateValue($langEnId, 'video', 'meta_title_view_list', '{$video_list} videos | {$site_name}');
            $languageService->addOrUpdateValue($langEnId, 'video', 'meta_desc_view_list', 'Watch all {$video_list} videos at {$site_name}.');
            $languageService->addOrUpdateValue($langEnId, 'video', 'meta_keywords_view_list', '');
            $languageService->addOrUpdateValue($langEnId, 'video', 'featured_list_label', 'Featured');
            $languageService->addOrUpdateValue($langEnId, 'video', 'latest_list_label', 'Latest');
            $languageService->addOrUpdateValue($langEnId, 'video', 'toprated_list_label', 'Top rated');
            $languageService->addOrUpdateValue($langEnId, 'video', 'tagged_list_label', 'Tagged');
            $languageService->addOrUpdateValue($langEnId, 'video', 'seo_meta_view_clip_label', 'View Specific Video Page');
            $languageService->addOrUpdateValue($langEnId, 'video', 'meta_title_view_clip', '"{$video_title}" video by {$user_name} | {$site_name}');
            $languageService->addOrUpdateValue($langEnId, 'video', 'meta_desc_view_clip', 'Watch "{$video_title}" video on {$site_name} uploaded by {$user_name}.');
            $languageService->addOrUpdateValue($langEnId, 'video', 'meta_keywords_view_clip', '');
            $languageService->addOrUpdateValue($langEnId, 'video', 'seo_meta_tag_list_label', 'Videos Tagged by Specific Tag Page');
            $languageService->addOrUpdateValue($langEnId, 'video', 'meta_title_tag_list', '"{$video_tag_name}" tagged videos | {$site_name}');
            $languageService->addOrUpdateValue($langEnId, 'video', 'meta_desc_tag_list', 'Watch all videos tagged "{$video_tag_name}" at {$site_name}.');
            $languageService->addOrUpdateValue($langEnId, 'video', 'meta_keywords_tag_list', '');
            $languageService->addOrUpdateValue($langEnId, 'video', 'seo_meta_user_video_list_label', 'Individual Member\'s Videos Page');
            $languageService->addOrUpdateValue($langEnId, 'video', 'meta_title_user_video_list', 'Videos by {$user_name}, {$user_age} | {$site_name}');
            $languageService->addOrUpdateValue($langEnId, 'video', 'meta_desc_user_video_list', 'Watch all videos uploaded by {$user_name}, {$user_age} on {$site_name}.');
            $languageService->addOrUpdateValue($langEnId, 'video', 'meta_keywords_user_video_list', '');
			$languageService->addOrUpdateValue($langEnId, 'video', 'latest_friends_list_label', 'Latest friends');
			$languageService->addOrUpdateValue($langEnId, 'video', 'video_mobile', 'Video');
			$languageService->addOrUpdateValue($langEnId, 'video', 'latest_myvideo_list_label', 'My Videos – {$site_name}');
        }
		
		//mailbox plugin
        if (IISSecurityProvider::existPluginKeyInActivePlugins($activePlugins, 'mailbox')) {
		
            $languageService->addOrUpdateValue($langEnId, 'mailbox', 'upload_file_extension_is_not_allowed', 'The file extention is not allowed.');
			
			$languageService->addOrUpdateValue($langEnId, 'mailbox', 'send_message_promoted', 'Please subscribe or buy credits to send messages');
			
			$languageService->addOrUpdateValue($langEnId, 'mailbox', 'reply_to_message_promoted', 'Please subscribe or buy credits to reply to conversation');
			
			$languageService->addOrUpdateValue($langEnId, 'mailbox', 'send_chat_message_promoted', 'Please subscribe or buy credits to send chat message');
			
			$languageService->addOrUpdateValue($langEnId, 'mailbox', 'reply_to_chat_message_promoted', 'Please subscribe or buy credits to reply to chat message');
        }
	
		//photo plugin
        if (IISSecurityProvider::existPluginKeyInActivePlugins($activePlugins, 'photo')) {
		
            $languageService->addOrUpdateValue($langEnId, 'photo', 'accepted_filesize_desc', 'Maximum acceptable size for uploading a photo. Minimum acceptable size is <b><i>0.5</i></b>  Mb.');

            $languageService->addOrUpdateValue($langEnId, 'photo', 'photo_list', 'Photo list');

            $languageService->addOrUpdateValue($langEnId, 'photo', 'choose_type_of_photo_list', 'Choose type of photo list');

            $languageService->addOrUpdateValue($langEnId, 'photo', 'album_list', 'Album list');

            $languageService->addOrUpdateValue($langEnId, 'photo', 'choose_type_of_album_list', 'Choose type of album cover');

            $languageService->addOrUpdateValue($langEnId, 'photo', 'photo_view', 'Photo view');

            $languageService->addOrUpdateValue($langEnId, 'photo', 'choose_type_of_photo_view', 'Choose type of photo view');

            $languageService->addOrUpdateValue($langEnId, 'photo', 'photo_sitemap', 'Photos');
            $languageService->addOrUpdateValue($langEnId, 'photo', 'seo_meta_section', 'Photos');
            $languageService->addOrUpdateValue($langEnId, 'photo', 'seo_meta_tagged_list_label', 'All Tagged Photos Page');
            $languageService->addOrUpdateValue($langEnId, 'photo', 'meta_title_tagged_list', '{$tag} photos | {$site_name}');
            $languageService->addOrUpdateValue($langEnId, 'photo', 'meta_desc_tagged_list', 'The list of all photos tagged "{$tag}" at {$site_name}.');
            $languageService->addOrUpdateValue($langEnId, 'photo', 'meta_keywords_tagged_list', '');
            $languageService->addOrUpdateValue($langEnId, 'photo', 'seo_meta_photo_list_label', 'Photos by List Type (Latest / Top Rated / Most Discussed) Page');
            $languageService->addOrUpdateValue($langEnId, 'photo', 'meta_title_photo_list', '{$list_type} photos | {$site_name}');
            $languageService->addOrUpdateValue($langEnId, 'photo', 'meta_desc_photo_list', 'All {$list_type} photos at {$site_name}');
            $languageService->addOrUpdateValue($langEnId, 'photo', 'meta_keywords_photo_list', '');
            $languageService->addOrUpdateValue($langEnId, 'photo', 'list_type_label_featured', 'Featured');
            $languageService->addOrUpdateValue($langEnId, 'photo', 'list_type_label_latest', 'Latest');
            $languageService->addOrUpdateValue($langEnId, 'photo', 'list_type_label_toprated', 'Top rated');
            $languageService->addOrUpdateValue($langEnId, 'photo', 'list_type_label_tagged', 'Tagged');
            $languageService->addOrUpdateValue($langEnId, 'photo', 'list_type_label_most_discussed', 'Most discussed');
            $languageService->addOrUpdateValue($langEnId, 'photo', 'seo_meta_user_albums_label', 'Individual Member\'s Photo Albums Page');
            $languageService->addOrUpdateValue($langEnId, 'photo', 'meta_title_user_albums', 'Photo albums by {$user_name}, {$user_age} | {$site_name}');
            $languageService->addOrUpdateValue($langEnId, 'photo', 'meta_desc_user_albums', 'View all photo albums uploaded by {$user_name}, {$user_age} on {$site_name}.');
            $languageService->addOrUpdateValue($langEnId, 'photo', 'meta_keywords_user_albums', '');
            $languageService->addOrUpdateValue($langEnId, 'photo', 'seo_meta_user_album_label', 'Individual Member\'s Specific Photo Album Page');
            $languageService->addOrUpdateValue($langEnId, 'photo', 'meta_title_user_album', '"{$album_name}" photo album by {$user_name}, {$user_age} | {$site_name}');
            $languageService->addOrUpdateValue($langEnId, 'photo', 'meta_desc_user_album', 'View all photos in "{$album_name}" uploaded by {$user_name}, {$user_age} on {$site_name}.');
            $languageService->addOrUpdateValue($langEnId, 'photo', 'meta_keywords_user_album', '');
            $languageService->addOrUpdateValue($langEnId, 'photo', 'seo_meta_user_photos_label', 'Individual Member\'s Specific Photo Album Page');
            $languageService->addOrUpdateValue($langEnId, 'photo', 'meta_title_user_photos', 'All photos by {$user_name}, {$user_age} | {$site_name}');
            $languageService->addOrUpdateValue($langEnId, 'photo', 'meta_desc_user_photos', 'View all photos uploaded by {$user_name}, {$user_age} on {$site_name}.');
            $languageService->addOrUpdateValue($langEnId, 'photo', 'meta_keywords_user_photos', '');
            $languageService->addOrUpdateValue($langEnId, 'photo', 'seo_meta_photo_view_label', 'Specific Photo Page');
            $languageService->addOrUpdateValue($langEnId, 'photo', 'meta_title_photo_view', 'View "{$photo_id}" photo by {$user_name} | {$site_name}');
            $languageService->addOrUpdateValue($langEnId, 'photo', 'meta_desc_photo_view', 'View photo "{$photo_id}" uploaded by {$user_name} on {$site_name}.');
            $languageService->addOrUpdateValue($langEnId, 'photo', 'meta_keywords_photo_view', '');
			$languageService->addOrUpdateValue($langEnId, 'photo', 'list_type_label_photo_friends', 'Friends');
			$languageService->addOrUpdateValue($langEnId, 'photo', 'meta_title_photo_photo_friends', 'Photos of Friends – {$site_name}');
        }
	
        //iisupdateserver
        if (IISSecurityProvider::existPluginKeyInActivePlugins($activePlugins, 'iisupdateserver')) {
            $languageService->addOrUpdateValue($langEnId, 'iisupdateserver', 'plugins_sample', 'Plugins');
            $languageService->addOrUpdateValue($langEnId, 'iisupdateserver', 'themes_sample', 'Themes');
            $languageService->addOrUpdateValue($langEnId, 'iisupdateserver', 'download_plugins_description', 'You can find plugins in the page of <a href="{$url}" target="_blank"> download plugins</a> for downloading in any versions. Also you can find all plugins in the list.');
            $languageService->addOrUpdateValue($langEnId, 'iisupdateserver', 'download_themes_description', 'You can find themes in the page of <a href="{$url}" target="_blank">download themes</a> for downloading in any versions. Also you can find all themes in the list.');
            $languageService->addOrUpdateValue($langEnId, 'iisupdateserver', 'download_core_update_description', 'You can update manually your social network using with any version to version of {$version}.');
            $languageService->addOrUpdateValue($langEnId, 'iisupdateserver', 'view_guideline', 'User manual');
			$languageService->addOrUpdateValue($langEnId, 'iisupdateserver', 'guidelineurl_label', 'Guideline url');
			$languageService->addOrUpdateValue($langEnId, 'iisupdateserver', 'sha256_label', 'Information verification (Sha256)');
            $languageService->addOrUpdateValue($langEnId, 'iisupdateserver', 'admin_page_heading', 'Update server plugin settings');
            $languageService->addOrUpdateValue($langEnId, 'iisupdateserver', 'admin_page_title', 'Update server plugin settings');
            $languageService->addOrUpdateValue($langEnId, 'iisupdateserver', 'return', 'Return');
            $languageService->addOrUpdateValue($langEnId, 'iisupdateserver', 'update_guideline', 'Update guideline');
			$languageService->addOrUpdateValue($langEnId, 'iisupdateserver', 'delete_item', 'Delete Item');
			$languageService->addOrUpdateValue($langEnId, 'iisupdateserver', 'buildNumber', 'Build Number');
			$languageService->addOrUpdateValue($langEnId, 'iisupdateserver', 'item_deleted_successfully', 'Item Deleted Successfully');
			$languageService->addOrUpdateValue($langEnId, 'iisupdateserver', 'admin_delete_item_title', 'Update Server Plugin setting');
			$languageService->addOrUpdateValue($langEnId, 'iisupdateserver', 'last_version_buildNumber', 'Last Version Build Number');
			$languageService->addOrUpdateValue($langEnId, 'iisupdateserver', 'admin_check_item_title', 'Check item for update');
			$languageService->addOrUpdateValue($langEnId, 'iisupdateserver', 'check_item', 'Update item');
			

        }

        //iisrules
        if (IISSecurityProvider::existPluginKeyInActivePlugins($activePlugins, 'iisrules')) {
            $languageService->addOrUpdateValue($langEnId, 'iisrules', 'guideline', 'Guideline');
        }

        //iisevaluation
        if (IISSecurityProvider::existPluginKeyInActivePlugins($activePlugins, 'iisevaluation')) {
            $languageService->addOrUpdateValue($langEnId, 'iisevaluation', 'degree_header', 'Current level:');
            $languageService->addOrUpdateValue($langEnId, 'iisevaluation', 'results_header', 'Results of evaluation:');
            $languageService->addOrUpdateValue($langEnId, 'iisevaluation', 'requirement_suggest', 'Assessment of suggested requirements in separate categories');
            $languageService->addOrUpdateValue($langEnId, 'iisevaluation', 'requirement_normal', 'Assessment of normal requirements in separate categories');
            $languageService->addOrUpdateValue($langEnId, 'iisevaluation', 'requirement_important', 'Assessment of important requirements in separate categories');
            $languageService->addOrUpdateValue($langEnId, 'iisevaluation', 'requirement_fundamental', 'Assessment of fundamental requirements in separate categories');
            $languageService->addOrUpdateValue($langEnId, 'iisevaluation', 'user_value', 'Earned point');
            $languageService->addOrUpdateValue($langEnId, 'iisevaluation', 'remaining_value', 'Remaining point');
            $languageService->addOrUpdateValue($langEnId, 'iisevaluation', 'total_value', 'Maximum point');
            $languageService->addOrUpdateValue($langEnId, 'iisevaluation', 'questions_without_values', 'This question has not any values');
            $languageService->addOrUpdateValue($langEnId, 'iisevaluation', 'category_questions_header', 'Questions');
            $languageService->addOrUpdateValue($langEnId, 'iisevaluation', 'admin_evaluation_settings_heading', 'Evaluation plugin setting');
			$languageService->addOrUpdateValue($langEnId, 'iisevaluation', 'delete_item_warning', 'Are you sure to delete this item?');
			$languageService->addOrUpdateValue($langEnId, 'iisevaluation', 'active_item_warning', 'Are you sure to active this item?');
			$languageService->addOrUpdateValue($langEnId, 'iisevaluation', 'lock_item_warning', 'Are you sure to lock this item?');
        }

		//iisdatabackup plugin
		if (IISSecurityProvider::existPluginKeyInActivePlugins($activePlugins, 'iisdatabackup')) {
			$languageService->addOrUpdateValue($langEnId, 'iisdatabackup', 'newsfeed_status', 'Newsfeed status(Except the last)');
            $languageService->addOrUpdateValue($langEnId, 'iisdatabackup', 'admin_page_heading', 'Data backup plugin settings');
            $languageService->addOrUpdateValue($langEnId, 'iisdatabackup', 'admin_page_title', 'Data backup plugin settings');

        }
		
			
        //iisnews plugin
        if (IISSecurityProvider::existPluginKeyInActivePlugins($activePlugins, 'iisnews')) {
		
            $languageService->addOrUpdateValue($langEnId, 'iisnews', 'save_form_lbl_date_enable', 'Enable publish date modification');
			
			$languageService->addOrUpdateValue($langEnId, 'iisnews', 'save_form_lbl_date', 'Publish date');
			
			$languageService->addOrUpdateValue($langEnId, 'iisnews', 'news_notification_string', '<a href="{$actorUrl}">{$actor}</a> published a news: <a href="{$url}">"{$title}"</a>');
			$languageService->addOrUpdateValue($langEnId, 'iisnews', 'iisnews_mobile', 'News');
			$languageService->addOrUpdateValue($langEnId, 'iisnews', 'index_page_title', 'News');
			$languageService->addOrUpdateValue($langEnId, 'iisnews', 'index_page_heading', 'News');
			
        }

		//privacy plugin
        if (IISSecurityProvider::existPluginKeyInActivePlugins($activePlugins, 'privacy')) {

			$languageService->addOrUpdateValue($langEnId, 'privacy', 'no_permission_message', 'You do not have permission to view this page');
        }

        //blogs plugin
        if (IISSecurityProvider::existPluginKeyInActivePlugins($activePlugins, 'blogs')) {
            $languageService->addOrUpdateValue($langEnId, 'blogs', 'blogs_sitemap', 'Blogs');
            $languageService->addOrUpdateValue($langEnId, 'blogs', 'blogs_sitemap_desc', 'Blogs and lists');
            $languageService->addOrUpdateValue($langEnId, 'blogs', 'seo_meta_section', 'Blogs');
            $languageService->addOrUpdateValue($langEnId, 'blogs', 'seo_meta_blogs_list_label', 'Blogs List Page');
            $languageService->addOrUpdateValue($langEnId, 'blogs', 'meta_title_blogs_list', '{$blog_list} blogs | {$site_name}');
            $languageService->addOrUpdateValue($langEnId, 'blogs', 'meta_desc_blogs_list', 'Read all {$blog_list} blog posts at {$site_name}, leave your own comments, and discuss the topics with other site members.');
            $languageService->addOrUpdateValue($langEnId, 'blogs', 'meta_keywords_blogs_list', '');
            $languageService->addOrUpdateValue($langEnId, 'blogs', 'seo_meta_user_blog_label', 'Individual Member Blog Page');
            $languageService->addOrUpdateValue($langEnId, 'blogs', 'meta_title_user_blog', 'Blog by {$user_name}, {$user_age} | {$site_name}');
            $languageService->addOrUpdateValue($langEnId, 'blogs', 'meta_desc_user_blog', 'Read all {$user_name}\'s posts at {$site_name}, and leave your own comments.');
            $languageService->addOrUpdateValue($langEnId, 'blogs', 'meta_keywords_user_blog', '');
            $languageService->addOrUpdateValue($langEnId, 'blogs', 'seo_meta_blog_post_label', 'Individual Member Blog Post Page');
            $languageService->addOrUpdateValue($langEnId, 'blogs', 'meta_title_blog_post', '{$post_subject} | {$site_name} Blog');
            $languageService->addOrUpdateValue($langEnId, 'blogs', 'meta_desc_blog_post', '{$post_body}');
            $languageService->addOrUpdateValue($langEnId, 'blogs', 'meta_keywords_blog_post', '');
        }

        //event plugin
        if (IISSecurityProvider::existPluginKeyInActivePlugins($activePlugins, 'event')) {
            $languageService->addOrUpdateValue($langEnId, 'event', 'event_sitemap', 'Events');
            $languageService->addOrUpdateValue($langEnId, 'event', 'seo_meta_section', 'Events');
            $languageService->addOrUpdateValue($langEnId, 'event', 'seo_meta_events_list_label', 'Events List Page');
            $languageService->addOrUpdateValue($langEnId, 'event', 'meta_title_events_list', '{$event_list} events | {$site_name}');
            $languageService->addOrUpdateValue($langEnId, 'event', 'meta_desc_events_list', 'Find out more information about all {$event_list} events at {$site_name}, leave your comments, and discuss them with other site members.');
            $languageService->addOrUpdateValue($langEnId, 'event', 'meta_keywords_events_list', '');
            $languageService->addOrUpdateValue($langEnId, 'event', 'created_events_page_title', 'Created');
            $languageService->addOrUpdateValue($langEnId, 'event', 'joined_events_page_title', 'Joined');
            $languageService->addOrUpdateValue($langEnId, 'event', 'seo_meta_event_view_label', 'Separate Event Page');
            $languageService->addOrUpdateValue($langEnId, 'event', 'meta_title_event_view', '{$event_title} | {$site_name}');
            $languageService->addOrUpdateValue($langEnId, 'event', 'meta_desc_event_view', '{$event_description}');
            $languageService->addOrUpdateValue($langEnId, 'event', 'meta_keywords_event_view', '');
            $languageService->addOrUpdateValue($langEnId, 'event', 'seo_meta_event_users_label', 'Separate Event Participants Page');
            $languageService->addOrUpdateValue($langEnId, 'event', 'meta_title_event_users', 'All users of "{$event_title}" | {$site_name}');
            $languageService->addOrUpdateValue($langEnId, 'event', 'meta_desc_event_users', 'The list of all {$site_name} users who singed up for the "{$event_title}". Join in to not miss all the fun!');
            $languageService->addOrUpdateValue($langEnId, 'event', 'meta_keywords_event_users', '');
			$languageService->addOrUpdateValue($langEnId, 'event', 'event_mobile', 'Event');
			$languageService->addOrUpdateValue($langEnId, 'event', 'back‌', 'Back');
        }

        //groups plugin
        if (IISSecurityProvider::existPluginKeyInActivePlugins($activePlugins, 'groups')) {
            $languageService->addOrUpdateValue($langEnId, 'groups', 'groups_sitemap', 'Groups');
            $languageService->addOrUpdateValue($langEnId, 'groups', 'groups_sitemap_desc', 'Groups and lists');
            $languageService->addOrUpdateValue($langEnId, 'groups', 'seo_meta_section', 'Groups');
            $languageService->addOrUpdateValue($langEnId, 'groups', 'seo_meta_most_popular_label', 'Most Popular Groups Page');
            $languageService->addOrUpdateValue($langEnId, 'groups', 'meta_title_most_popular', 'Most Popular Groups | {$site_name}');
            $languageService->addOrUpdateValue($langEnId, 'groups', 'meta_desc_most_popular', 'The list of most popular groups at {$site_name}. Join us!');
            $languageService->addOrUpdateValue($langEnId, 'groups', 'meta_keywords_most_popular', '');
            $languageService->addOrUpdateValue($langEnId, 'groups', 'seo_meta_latest_label', 'Latest Groups Page');
            $languageService->addOrUpdateValue($langEnId, 'groups', 'meta_title_latest', 'Latest Groups | {$site_name}');
            $languageService->addOrUpdateValue($langEnId, 'groups', 'meta_desc_latest', 'The list of all recently created groups at {$site_name}. Join us!');
            $languageService->addOrUpdateValue($langEnId, 'groups', 'meta_keywords_latest', '');
            $languageService->addOrUpdateValue($langEnId, 'groups', 'seo_meta_user_groups_label', 'Individual Member\'s Groups Page');
            $languageService->addOrUpdateValue($langEnId, 'groups', 'meta_title_user_groups', 'Groups joined by {$user_name}, {$user_age} | {$site_name}');
            $languageService->addOrUpdateValue($langEnId, 'groups', 'meta_desc_user_groups', 'All groups joined by {$user_name}, {$user_age} on {$site_name}.');
            $languageService->addOrUpdateValue($langEnId, 'groups', 'meta_keywords_user_groups', '');
            $languageService->addOrUpdateValue($langEnId, 'groups', 'seo_meta_groups_page_label', 'Separate Group Page');
            $languageService->addOrUpdateValue($langEnId, 'groups', 'meta_title_groups_page', '{$group_title} | {$site_name}');
            $languageService->addOrUpdateValue($langEnId, 'groups', 'meta_desc_groups_page', '{$group_description}');
            $languageService->addOrUpdateValue($langEnId, 'groups', 'meta_keywords_groups_page', '');
            $languageService->addOrUpdateValue($langEnId, 'groups', 'seo_meta_group_users_label', 'Separate Group Participants Page');
            $languageService->addOrUpdateValue($langEnId, 'groups', 'meta_title_group_users', 'All members of {$group_title} | {$site_name}');
            $languageService->addOrUpdateValue($langEnId, 'groups', 'meta_desc_group_users', 'The list of all members of the "{$group_title}" at {$site_name}.');
            $languageService->addOrUpdateValue($langEnId, 'groups', 'meta_keywords_group_users', '');
			$languageService->addOrUpdateValue($langEnId, 'groups', 'mobile_main_menu_list', 'Group');
        }

    }


    public static function updateBaseOfEnLanguageValues($langEnId, $languageService)
    {
        $languageService->addOrUpdateValue($langEnId, 'base', 'invalid_smtp_setting', 'SMTP setting is invalid');
        $languageService->addOrUpdateValue($langEnId, 'admin', 'all_files', 'All files');
        $languageService->addOrUpdateValue($langEnId, 'base', 'ow_mail_information', 'This email has just been sent from {$site_name}');
        $languageService->addOrUpdateValue($langEnId, 'base', 'email_verify_promo', '<p>An email has just been sent for verifying your account <br />You have to verify your account by clicking on the sent link or entering activation code (available via below of the form) </p><b>.  You can also use the form below for changing or modifying your email or resending the verification email. <br /> </b> If you have not yet receive any email please contact <i>{$site_email}</i>  <br />By clicking the link in the bottom of this form, you could also verify your account later.');
        $languageService->addOrUpdateValue($langEnId, 'base', 'email_verify_template_html', 'Dear user ({$username}),<br /><br />Thank you for registering at {$site_name}! Now you need to verify your email by <a href="{$url}">clicking here</a><br /><br />Alternatively you can insert this code at the <a href="{$verification_page_url}">verification page</a>: {$code}<br /><br />Thank you,<br />{$site_name} administration');
        $languageService->addOrUpdateValue($langEnId, 'base', 'forgot_password_mail_template_content_html', 'Dear {$username},<br /><br />You requested to reset your password. Here\'s your new password: <b>{$password}</b><br /><br />\r\nFeel free to log in to your account at <a href="{$site_url}">{$site_url}</a> and change your password again if necessary.<br /><br />Thank you,<br />{$site_name}');
        $languageService->addOrUpdateValue($langEnId, 'base', 'forgot_password_mail_template_content_txt', 'Dear user ({$username}),\r\n\r\nYou requested to reset your password. Here\'s your new password: {$password}\r\n\r\nFeel free to log in to your account at {$site_url} and change your password again if necessary.\r\n\r\nThank you,\r\n{$site_name}');
        $languageService->addOrUpdateValue($langEnId, 'base', 'reset_password_mail_template_content_txt', 'Dear user ({$username}),\r\n\r\nYou requested to reset your password. Follow this link ({$resetUrl}) to change your password.\r\n\r\nIf the link doesn\'t work, please enter the code manually here ({$requestUrl}). Code: {$code}\r\n\r\nIf you didn\'t request password reset, please ignore this email.\r\n\r\nThank you,\r\n{$site_name}');
        $languageService->addOrUpdateValue($langEnId, 'base', 'email_verify_template_text', 'Dear user ({$username}),\r\n\r\nThank you for registering at {$site_name}! Now you need to verify your email by clicking this link: {$url}\r\n\r\nAlternatively you can insert this code at the verification page {$verification_page_url} : \r\n{$code}\r\n\r\nThank you,\r\n{$site_name} administration');
        $languageService->addOrUpdateValue($langEnId, 'base', 'suspend_notification_html', 'Dear user ({$realName}),<br><br>We are informing you that your account on {$site_name} has been suspended with the following reason given:<br>{$suspendReason}<br><br>Thank you,<br>{$site_name} team');
        $languageService->addOrUpdateValue($langEnId, 'base', 'suspend_notification_text', 'Dear user ({$realName}),\r\nWe are informing you that your account on {$site_name} has been suspended with the following reason given:\r\n{$suspendReason}\r\nThank you,\r\n{$site_name} team');
        $languageService->addOrUpdateValue($langEnId, 'base', 'user_approved_mail_html', '<p>Dear user ({$user_name}),</p><p>We are glad to let you know that your account on <a href="{$site_url}">{$site_name}</a> has been approved. Now you can sign in here: <a href="{$site_url}">{$site_url}</a></p><p>We hope you enjoy our site to the fullest.</p><p>Thank you,<br />Administration<br /><a href="{$site_url}">{$site_name}</a></p>');
        $languageService->addOrUpdateValue($langEnId, 'base', 'user_approved_mail_txt', 'Dear user ({$user_name}),\r\n\r\nWe are glad to let you know that your account on {$site_name} has been approved. Now you can sign in here: {$site_url}\r\n\r\nWe hope you enjoy our site to the fullest.\r\n\r\nThank you,\r\nAdministration\r\n{$site_name}\r\n{$site_url}');
        $languageService->addOrUpdateValue($langEnId, 'base', 'close', 'close');
        $languageService->addOrUpdateValue($langEnId, 'base', 'powered_by_community', 'Powered by Motoshub');
        $languageService->addOrUpdateValue($langEnId, 'base', 'reset_password_mail_template_content_html', 'Dear user ({$username}),<br />You requested to reset your password. Follow this link <a href="{$resetUrl}"> {$resetUrl}</a> to change your password.<br />If the link doesn\'t work, please enter the code manually here <a href="{$requestUrl}"> {$requestUrl}</a>. Code: {$code}<br />If you didn\'t request password reset, please ignore this email.<br /><br />Thank you,{$site_name}<br />');
        $languageService->addOrUpdateValue($langEnId, 'admin', 'external_url', 'External URL');
        $languageService->addOrUpdateValue($langEnId, 'admin', 'local_page', 'Local page');
		$languageService->addOrUpdateValue($langEnId, 'admin', 'check_updates', 'Check for updates');
        $languageService->addOrUpdateValue($langEnId, 'base', 'copyright', '© Copyright - {$site_name}');
		$languageService->addOrUpdateValue($langEnId, 'base', 'html_error', 'Text is not valid.');
		$languageService->addOrUpdateValue($langEnId, 'base', 'widgets_allow_customize_locked_text', 'You can not enable this feature because tidy extension (PHP extension) is not enable.');
		$languageService->addOrUpdateValue($langEnId, 'base', 'html_error_details', 'Error Details');
		
		//replace profile picture instead of avatar
		$languageService->addOrUpdateValue($langEnId, 'base', 'avatar_avatar_is', 'Profile picture is a graphic picture/photo of a reduced size displayed for your profile');
		
		$languageService->addOrUpdateValue($langEnId, 'base', 'avatar_current', 'Your Profile Picture');
		
		$languageService->addOrUpdateValue($langEnId, 'base', 'avatar_new', 'Upload new profile picture');
		
		$languageService->addOrUpdateValue($langEnId, 'base', 'content_avatars_label', 'Profile Pictures');
		
		$languageService->addOrUpdateValue($langEnId, 'base', 'not_writable_avatar_dir', 'Profile picture folder is missing or not writeable.');
		
		$languageService->addOrUpdateValue($langEnId, 'base', 'avatar_crop', 'Crop Profile Picture');
		
		$languageService->addOrUpdateValue($langEnId, 'base', 'avatar_crop_instructions', 'Choose an area of your profile picture for cropping with the help of mouse cursor. The cropping result will be displayed on the right. Once you are satisfied with the result click the \"Apply crop\" button.');
		
		$languageService->addOrUpdateValue($langEnId, 'base', 'avatar_picture', 'Your profile picture');
		
		$languageService->addOrUpdateValue($langEnId, 'base', 'avatar_upload_types', 'Available formats for Profile Picture uploading are <span class=\"ow_txt_value\">JPG</span>/<span class=\"ow_txt_value\">GIF</span>/<span class=\"ow_txt_value\">PNG</span>');
		
		$languageService->addOrUpdateValue($langEnId, 'base', 'avatar_change_avatar', 'Change profile picture');
		
		$languageService->addOrUpdateValue($langEnId, 'base', 'avatar_change', 'Change Profile Picture');
		
		$languageService->addOrUpdateValue($langEnId, 'admin', 'user_avatar_settings', 'Profile Picture Settings');
		
		$languageService->addOrUpdateValue($langEnId, 'admin', 'user_settings_avatar_size', 'Profile Picture size');
		
		$languageService->addOrUpdateValue($langEnId, 'admin', 'user_settings_big_avatar_size', 'Big profile picture size');
		
		$languageService->addOrUpdateValue($langEnId, 'base', 'avatar_widget', 'Profile Picture');
		
		$languageService->addOrUpdateValue($langEnId, 'base', 'avatar_activity_string', '<a href=\"{$userUrl}\">{$user}</a> changed their profile picture.');
		
		$languageService->addOrUpdateValue($langEnId, 'base', 'avatar_update_string', 'changed their profile picture');
		
		$languageService->addOrUpdateValue($langEnId, 'base', 'avatar_feed_string', 'changed their profile picture');
		
		$languageService->addOrUpdateValue($langEnId, 'admin', 'avatar_feed_string', 'changed their profile picture');
		
		$languageService->addOrUpdateValue($langEnId, 'admin', 'avatar_label', 'Label on profile picture');
		
		$languageService->addOrUpdateValue($langEnId, 'admin', 'display_avatar_label', 'Display role label on profile picture');
		
		$languageService->addOrUpdateValue($langEnId, 'admin', 'default_avatar_deleted', 'Default profile picture deleted');
		
		$languageService->addOrUpdateValue($langEnId, 'admin', 'confirm_avatar_delete', 'Are sure you want to delete default profile picture?');
		
		$languageService->addOrUpdateValue($langEnId, 'admin', 'user_settings_avatar_image_desc', 'Change to override theme default profile picture');
		
		$languageService->addOrUpdateValue($langEnId, 'admin', 'user_settings_avatar_image', 'Default profile picture');
		
		$languageService->addOrUpdateValue($langEnId, 'admin', 'input_settings_avatar_max_upload_size_label', 'Profile picture file size limit');
		
		$languageService->addOrUpdateValue($langEnId, 'base', 'avatar_changed', 'Profile picture has been changed');
		
		$languageService->addOrUpdateValue($langEnId, 'base', 'avatar_has_been_approved', 'Profile picture has been approved');
		
		$languageService->addOrUpdateValue($langEnId, 'base', 'avatar_image_too_small', 'This photo is too small to be set as profile picture. <br />Minimum size is {$width}px x {$height}px');
		
		$languageService->addOrUpdateValue($langEnId, 'admin', 'avatar_pending_approval', 'Profile picture is pending approval');
		
		$languageService->addOrUpdateValue($langEnId, 'admin', 'join_display_photo_upload_desc', 'Let users upload profile picture on registration');
		
		$languageService->addOrUpdateValue($langEnId, 'admin', 'join_display_photo_upload', 'Profile picture upload');
		
		$languageService->addOrUpdateValue($langEnId, 'admin', 'user_settings_avatar_size_error', 'The max reasonable profile picture size is {$max}px');
		
		$languageService->addOrUpdateValue($langEnId, 'admin', 'user_settings_avatar_size_label', 'Profile picture<br /> crop size');
		
		$languageService->addOrUpdateValue($langEnId, 'admin', 'user_settings_big_avatar_size_error', 'The max reasonable big profile picture size is {$max}px');
		
		$languageService->addOrUpdateValue($langEnId, 'admin', 'user_settings_big_avatar_size_label', 'Big profile picture<br /> crop size');
		
		$languageService->addOrUpdateValue($langEnId, 'base', 'feed_activity_avatar_string', 'commented on {$user}\'s new profile picture');
		
		$languageService->addOrUpdateValue($langEnId, 'base', 'feed_activity_avatar_string_like', 'liked {$user}\'s new profile picture');
		
		$languageService->addOrUpdateValue($langEnId, 'admin', 'feed_content_avatar_change', 'User profile picture change');
		
		$languageService->addOrUpdateValue($langEnId, 'base', 'feed_activity_avatar_string_like_own', 'liked their new profile picture');
		
		$languageService->addOrUpdateValue($langEnId, 'base', 'feed_activity_avatar_string_own', 'commented on their new profile picture');
		
		$languageService->addOrUpdateValue($langEnId, 'base', 'content_avatar_label', 'Profile Picture');
		
		$languageService->addOrUpdateValue($langEnId, 'base', 'crop_avatar_failed', 'Crop profile picture failed.');
		
		$languageService->addOrUpdateValue($langEnId, 'base', 'input_settings_avatar_max_upload_size_label', 'Maximum upload profile picture size');
		
		$languageService->addOrUpdateValue($langEnId, 'admin', 'welcome_letter_template_html' , 'Welcome to <a href=\"{$site_url}\">{$site_name}</a>! Thanks for joining us. Here are some quick links that you need to find your way around:<br/><br/>\r\n- <a href=\"{$site_url}\">Main page</a><br/>\r\n- <a href=\"{$site_url}my-profile\">Change profile picture</a><br/>\r\n- <a href=\"{$site_url}photo/viewlist/latest\">Upload Photos</a><br/>\r\n- <a href=\"{$site_url}profile/edit\">Change profile details</a><br/>\r\n- <a href=\"{$site_url}users\">Look who\'s in</a><br/><br/>\r\nFeel free to participate in our community!<br/><br/>\r\n<a href=\"{$site_url}\">{$site_name}</a> administration<br/>');
		
		$languageService->addOrUpdateValue($langEnId, 'base', 'welcome_widget_legend', 'photo_upload - Photo upload link nchange_avatar - Change profile picture link');
		
		$languageService->addOrUpdateValue($langEnId, 'base', 'welcome_letter_template_text', 'Welcome to {$site_name}! Thanks for joining us. Here are some quick links that you need to find your way around:\r\n\r\n- Main page: {$site_url}\r\n- Change profile picture: {$site_url}my-profile\r\n- Upload photos: {$site_url}photo/viewlist/latest\r\n- Change profile details: {$site_url}profile/edit\r\n- Look who\'s in: {$site_url}users\r\n\r\nFeel free to participate in our community!\r\n\r\n{$site_name} administration\r\n\r\n{$site_url}');
		
		$languageService->addOrUpdateValue($langEnId, 'base', 'welcome_widget_content', '<p>Welcome to our site! Here are a few quick links that you need to start your way around:</p><ul class=\"ow_regular\"><li><a href=\"profile/avatar\" change_avatar>Change profile picture</a></li><li><a href=\"javascript://\" photo_upload>Upload photos</a></li><li><a href=\"profile/edit\">Change profile details</a></li><li><a href=\"my-profile\">Preview/rearrange my profile</a></li><li><a href=\"users\">Look who\'s in</a></li></ul><p>Feel free to participate in our community!</p>');
		
		$languageService->addOrUpdateValue($langEnId, 'admin', 'warning_cron_is_not_active', 'Cron job script is not active. Please add it to cron jobs list ({$path}).');
		
		$languageService->addOrUpdateValue($langEnId, 'admin', 'auth_success_message_not_ajax', 'Authorization was successful');
        $languageService->addOrUpdateValue($langEnId, 'base', 'upload_bad_request_error', 'The uploaded file is not allowed. (File name may contains invalid characters)');
		$languageService->addOrUpdateValue($langEnId, 'mobile', 'page_is_not_available', 'This page can not be opened in mobile version, please visit desktop version of this site');
    }

    public static function updateBaseOfFaLanguageValues($langFaId, $languageService)
    {
	
		$languageService->addOrUpdateValue($langFaId, 'admin', 'auth_success_message_not_ajax', 'احراز هویت با موفقیت انجام شد.');
		
        $languageService->addOrUpdateValue($langFaId, 'base', 'invalid_smtp_setting', 'تنظیمات رایانامه نادرست است');

        $languageService->addOrUpdateValue($langFaId, 'base', 'email_verify_promo', '<p>رایانامه جهت تایید حساب کاربری برای شما ارسال شده است. <br />شما باید با باز کردن پیوند ارسال شده یا واردسازی کد فعال‌سازی (از طریق پیوندی که در پایین فرم قابل دسترسی است) حساب کاربری خود را تایید کنید. </p><b>همچنین در صورت اصلاح نمودن رایانامه به منظور ثبت‌نام با رایانامه دیگری یا دوباره ارسال شدن رایانامه تایید حساب کاربری، از فرم زیر استفاده کنید. <br /> </b>  اگر شما رایانامه دریافت نکرده‌اید با رایانشانی <i>{$site_email}</i> تماس بگیرید. <br />همچنین می‌توانید با استفاده از پیوندی که در پایین فرم قرار دارد، حساب کاربری خود را بعدا تایید کنید.');

        $languageService->addOrUpdateValue($langFaId, 'base', 'email_verify_template_html', 'کاربرگرامی ({$username})،<br />با سپاس از شما برای ثبت نام در {$site_name}، برای تکمیل فرآیند ثبت‌نام لطفا  <a href="{$url}"> اینجا را کلیک کنید</a><br /><br />همچنین شما می‌توانید با واردسازی کد زیر در <a href="{$verification_page_url}">صفحه تاییدسازی</a> ثبت‌نام خود را تکمیل کنید. <br />{$code}<br /><br />با سپاس،<br />مدیریت {$site_name} <br />');

        $languageService->addOrUpdateValue($langFaId, 'base', 'forgot_password_mail_template_content_html', 'کاربر گرامی ({$username})، <br /> <br /> شما برای گذرواژه جدید درخواست داده بودید. گذرواژه شما:  <b>{$password}</b><br /> <br /> با راحتی وارد حساب خود شوید در <a href="{$site_url}">{$site_url}</a> و  گذرواژه خود را اگر لازم بود دوباره تغییر دهید . <br />');

        $languageService->addOrUpdateValue($langFaId, 'base', 'forgot_password_mail_template_content_txt', 'کاربر گرامی ({$username})، شما برای گذرواژه جدید درخواست داده بودید. گذرواژه شما: {$password} با راحتی تمام با حساب خوب وارد {$site_url} شوید در و  گذرواژه خود را اگر لازم بود دوباره تغییر دهید');

        $languageService->addOrUpdateValue($langFaId, 'base', 'reset_password_mail_template_content_txt', 'کاربر گرامی «{$username}»،


شما درخواست بازتنظیم گذرواژه خود را کرده‌اید. پیوند زیر را برای تغییر گذرواژه خود دنبال کنید: ({$resetUrl}) .



اگر در استفاده از پیوند بالا مشکلی بود، پیوند مقابل را دنبال کرده و کد زیر را در آن وارد کنید: ({$requestUrl}). Code: 
{$code}


اگر شما در خواست کد تنظیم مجدد نکرده‌اید لطفا این رایانامه را نادیده بگیرید');


        $languageService->addOrUpdateValue($langFaId, 'base', 'email_verify_template_text', '		کاربر گرامی ({$username})،


		با سپاس از شما برای ثبت‌نام در {$site_name} اکنون شما نیاز به تایید رایانامه خود با کلیک کردن بر روی این پیوند دارید {$url}



		همچنین شما می‌توانید این کد را در صفحه تایید سازی وارد کنید {$verification_page_url} :

		{$code}');
		
        $languageService->addOrUpdateValue($langFaId, 'base', 'suspend_notification_html', 'کاربر گرامی ({$realName})،<br><br> حساب کاربری شما در سایت {$site_name} به دلیل<br> {$suspendReason}<br><br> به حالت تعلیق درآمده است,<br> {$site_name} مدیریت');
        $languageService->addOrUpdateValue($langFaId, 'base', 'suspend_notification_text', 'کاربر گرامی ({$realName}) ، حساب کاربری شما در وب‌گاه {$site_name} به دلیل {$suspendReason} به حالت تعلیق درآمده است، {$site_name} مدیریت');

        $languageService->addOrUpdateValue($langFaId, 'base', 'user_approved_mail_txt', 'کاربر گرامی ({$user_name})،
ما بسیار خوشحالیم که به شما اطلاع می‌دهیم که حساب شما در {$site_name} فعال شده است . اکنون شما می‌توانید از این قسمت وارد وب‌گاه شوید {$site_url}



امیدواریم که از وب‌گاه ما لذت ببرید .

');

        $languageService->addOrUpdateValue($langFaId, 'base', 'close', 'بستن');
		$languageService->addOrUpdateValue($langFaId, 'base', 'user_block_btn_lbl', 'مسدود کردن');
		$languageService->addOrUpdateValue($langFaId, 'base', 'user_unblock_btn_lbl', 'رفع انسداد کاربر');
		$languageService->addOrUpdateValue($langFaId, 'base', 'user_block_message', 'کاربری که انتخاب کرده‌اید، قصد دوستی با شما را ندارد.');
        $languageService->addOrUpdateValue($langFaId, 'base', 'powered_by_community', 'تولید شده توسط موتوشاب');
        $languageService->addOrUpdateValue($langFaId, 'base', 'welcome_letter_template_html', 'به <a href="{$site_url}">{$site_name}</a> خوش آمدید! برای پیوستن به شبکه متشکریم. چندین پیوند سریع برای صرفه جویی در وقت شما آورده‌ شده است:<br/> - <a href="{$site_url}">صفحه اصلی</a><br/> - <a href="{$site_url}profile/edit">تغییر جزئیات نمایه</a><br/> - <a href="{$site_url}my-profile">مشاهده نمایه</a><br/> - <a href="{$site_url}users">مشاهده کاربران</a><br/>از اشتراک مطالب با یکدیگر لذت ببرید!<br/> از طرف مدیریت <a href="{$site_url}">{$site_name}</a> <br/>');
		$languageService->addOrUpdateValue($langFaId, 'base', 'rate_cmp_owner_cant_rate_error_message', 'شما نمی‌توانید مطالب خودتان را امتیازدهی کنید');
        $languageService->addOrUpdateValue($langFaId, 'admin', 'external_url', 'نشانی خارجی');
        $languageService->addOrUpdateValue($langFaId, 'admin', 'local_page', 'صفحه داخلی');
		$languageService->addOrUpdateValue($langFaId, 'base', 'console_item_label_sign_out', 'خروج');
		$languageService->addOrUpdateValue($langFaId, 'base', 'preference_mass_mailing_subscribe_description', 'با برداشتن این گزینه، پیام‌های دسته جمعی خبرنامه سامانه به شما ارسال نمی‌شود');
		$languageService->addOrUpdateValue($langFaId, 'base', 'preference_section_general', 'پیام‌های دسته‌جمعی');
		$languageService->addOrUpdateValue($langFaId, 'base', 'flag_accepted', 'گزارش تخلف پذیرفته شد');
		$languageService->addOrUpdateValue($langFaId, 'base', 'moderation_feedback_unflag', '{$content} گزارش تخلف برداشته شده است');
		$languageService->addOrUpdateValue($langFaId, 'base', 'moderation_feedback_unflag_multiple', '{$count} {$content} گزارش تخلف برداشته شده است');
        $languageService->addOrUpdateValue($langFaId, 'base', 'preference_menu_item', 'ترجیحات عمومی');
        $languageService->addOrUpdateValue($langFaId, 'base', 'invalid_csrf_token_error_message', 'توکن امنیتی شما منقضی شده یا وجود ندارد. لطفا صفحه را بازنشانی کنید');
		$languageService->addOrUpdateValue($langFaId, 'base', 'moderation_flags_item_string', 'یک مورد تخلف از کاربر <a href="{$userUrl}"><b>{$userName}</b></a>  در ارتباط با {$content}، گزارش شده است');
		$languageService->addOrUpdateValue($langFaId, 'base', 'flagged_time', 'زمان گزارش تخلف: {$time}');	
		$languageService->addOrUpdateValue($langFaId, 'base', 'unflag', 'برداشتن گزارش تخلف');	
		$languageService->addOrUpdateValue($langFaId, 'base', 'flagged_content', 'گزارشات تخلف');
		$languageService->addOrUpdateValue($langFaId, 'base', 'welcome_widget_content', '<p> به شبکه ما خوش آمدید! در این‌جا چند پیوند سریع برای راحتی شما ارائه شده </p> <ul class="ow_regular"> <li><a href="profile/avatar" change_avatar>تغییر آواتار</a></li> <li><a href="javascript://" photo_upload>بارگذاری تصاویر</a></li> <li><a href="profile/edit">ویرایش نمایه</a></li> <li><a href="my-profile">پیش‌نمایش / بازتنظیم مشخصات</a></li> <li><a href="users">کاربرانی که هم‌اکنون حاضر هستند</a></li> </ul> <p> </p>');
		$languageService->addOrUpdateValue($langFaId, 'admin', 'manage_plugins_install_empty_key_error_message', 'نصب افزونه امکان‌پذیر نیست زیرا شناسه یکتا برای این افزونه وجود ندارد.');
		$languageService->addOrUpdateValue($langFaId, 'admin', 'captcha_settings', 'تنظیمات عبارت امنیتی');
		$languageService->addOrUpdateValue($langFaId, 'admin', 'display_captcha_label', 'فعال‌سازی در فرم عضویت');
        $languageService->addOrUpdateValue($langFaId, 'admin', 'dnd_support', 'تصاویر را بکشید و در این‌جا بیندازید یا برای مرور آن‌ها کلیک کنید');
        $languageService->addOrUpdateValue($langFaId, 'admin', 'notification_plugins_to_update', 'به‌روزرسانی <b>{$count}</b> افزونه در دسترس است. <a href="{$link}">مشاهده</a>');
        $languageService->addOrUpdateValue($langFaId, 'admin', 'url_copied', 'نشانی اینترنتی نسخه برداری شد');
        $languageService->addOrUpdateValue($langFaId, 'admin', 'check_updates', 'بررسی به‌روزرسانی');
        $languageService->addOrUpdateValue($langFaId, 'admin', 'sidebar_menu_admin', 'صفحه مدیریت');
		$languageService->addOrUpdateValue($langFaId, 'admin', 'page_settings_form_headcode_label', 'سفارشی‌سازی کد در قسمت سرآیند');
		$languageService->addOrUpdateValue($langFaId, 'admin', 'user_settings_big_avatar_size_label', 'ابعاد آواتار بزرگ');
		$languageService->addOrUpdateValue($langFaId, 'admin', 'input_settings_avatar_max_upload_size_label', 'محدودیت حجم فایل تصویر نمایه');
		$languageService->addOrUpdateValue($langFaId, 'admin', 'site_email', 'رایانشانی وب‌گاه');
		$languageService->addOrUpdateValue($langFaId, 'admin', 'themes_admin_list_cap_title', 'پوسته‌های داشبورد مدیریت');
		$languageService->addOrUpdateValue($langFaId, 'admin', 'splash_button_label', 'برچسب دکمه ورود به سامانه');
		$languageService->addOrUpdateValue($langFaId, 'admin', 'splash_button_value', 'ورود');
        $languageService->addOrUpdateValue($langFaId, 'base', 'ws_button_label_image', 'درج تصویر');
		$languageService->addOrUpdateValue($langFaId, 'admin', 'pages_and_menus_legend_label', 'شرح رنگ صفحات');
		$languageService->addOrUpdateValue($langFaId, 'base', 'custom_html_widget_content_label', 'متن (عادی/HTML)');
        $languageService->addOrUpdateValue($langFaId, 'base', 'ow_ic_add', 'اضافه کردن');
        $languageService->addOrUpdateValue($langFaId, 'base', 'ow_ic_aloud', 'صدای زیاد');
        $languageService->addOrUpdateValue($langFaId, 'base', 'ow_ic_app', 'برنامه');
        $languageService->addOrUpdateValue($langFaId, 'base', 'ow_ic_attach', 'پیوست');
        $languageService->addOrUpdateValue($langFaId, 'base', 'ow_ic_birthday', 'تولد');
        $languageService->addOrUpdateValue($langFaId, 'base', 'ow_ic_bookmark', 'نشان');
        $languageService->addOrUpdateValue($langFaId, 'base', 'ow_ic_calendar', 'تقویم');
        $languageService->addOrUpdateValue($langFaId, 'base', 'ow_ic_cart', 'سبد خرید');
        $languageService->addOrUpdateValue($langFaId, 'base', 'ow_ic_edit', 'ویرایش');
        $languageService->addOrUpdateValue($langFaId, 'base', 'ow_ic_chat', 'پیام');
        $languageService->addOrUpdateValue($langFaId, 'base', 'ow_ic_clock', 'ساعت');
        $languageService->addOrUpdateValue($langFaId, 'base', 'ow_ic_comment', 'نظر');
        $languageService->addOrUpdateValue($langFaId, 'base', 'ow_ic_cut', 'برش');
        $languageService->addOrUpdateValue($langFaId, 'base', 'ow_ic_dashboard', 'داشبورد');
        $languageService->addOrUpdateValue($langFaId, 'base', 'ow_ic_delete', 'حذف');
        $languageService->addOrUpdateValue($langFaId, 'base', 'ow_ic_down_arrow', 'فلش پایین');
        $languageService->addOrUpdateValue($langFaId, 'base', 'ow_ic_female', 'جنسیت ماده');
        $languageService->addOrUpdateValue($langFaId, 'base', 'ow_ic_file', 'پرونده');
        $languageService->addOrUpdateValue($langFaId, 'base', 'ow_ic_files', 'پرونده‌ها');
        $languageService->addOrUpdateValue($langFaId, 'base', 'ow_ic_flag', 'پرچم');
        $languageService->addOrUpdateValue($langFaId, 'base', 'ow_ic_folder', 'پوشه');
        $languageService->addOrUpdateValue($langFaId, 'base', 'ow_ic_forum', 'انجمن');
        $languageService->addOrUpdateValue($langFaId, 'base', 'ow_ic_friends', 'دوستان');
        $languageService->addOrUpdateValue($langFaId, 'base', 'ow_ic_gear_wheel', 'چرخ');
        $languageService->addOrUpdateValue($langFaId, 'base', 'ow_ic_help', 'کمک‌رسانی');
        $languageService->addOrUpdateValue($langFaId, 'base', 'ow_ic_heart', 'قلب');
        $languageService->addOrUpdateValue($langFaId, 'base', 'ow_ic_house', 'خانه');
        $languageService->addOrUpdateValue($langFaId, 'base', 'ow_ic_info', 'اطلاعات');
        $languageService->addOrUpdateValue($langFaId, 'base', 'ow_ic_key', 'کلید');
        $languageService->addOrUpdateValue($langFaId, 'base', 'ow_ic_left_arrow', 'فلش چپ');
        $languageService->addOrUpdateValue($langFaId, 'base', 'ow_ic_lens', 'ذره‌بین');
        $languageService->addOrUpdateValue($langFaId, 'base', 'ow_ic_link', 'پیوند');
        $languageService->addOrUpdateValue($langFaId, 'base', 'ow_ic_lock', 'قفل');
        $languageService->addOrUpdateValue($langFaId, 'base', 'ow_ic_mail', 'رایانامه');
        $languageService->addOrUpdateValue($langFaId, 'base', 'ow_ic_male', 'جنسیت نر');
        $languageService->addOrUpdateValue($langFaId, 'base', 'ow_ic_mobile', 'تلفن همراه');
        $languageService->addOrUpdateValue($langFaId, 'base', 'ow_ic_moderator', 'الماس');
        $languageService->addOrUpdateValue($langFaId, 'base', 'ow_ic_monitor', 'مانیتور');
        $languageService->addOrUpdateValue($langFaId, 'base', 'ow_ic_move', 'جابجایی');
        $languageService->addOrUpdateValue($langFaId, 'base', 'ow_ic_music', 'آهنگ');
        $languageService->addOrUpdateValue($langFaId, 'base', 'ow_ic_new', 'جدید');
        $languageService->addOrUpdateValue($langFaId, 'base', 'ow_ic_ok', 'تایید');
        $languageService->addOrUpdateValue($langFaId, 'base', 'ow_ic_online', 'برخط');
        $languageService->addOrUpdateValue($langFaId, 'base', 'ow_ic_picture', 'تصویر');
        $languageService->addOrUpdateValue($langFaId, 'base', 'ow_ic_plugin', 'افزونه');
        $languageService->addOrUpdateValue($langFaId, 'base', 'ow_ic_push_pin', 'سنجاق');
        $languageService->addOrUpdateValue($langFaId, 'base', 'ow_ic_reply', 'پاسخ');
        $languageService->addOrUpdateValue($langFaId, 'base', 'ow_ic_right_arrow', 'فلش راست');
        $languageService->addOrUpdateValue($langFaId, 'base', 'ow_ic_rss', 'خبرخوان');
        $languageService->addOrUpdateValue($langFaId, 'base', 'ow_ic_save', 'ذخیره');
        $languageService->addOrUpdateValue($langFaId, 'base', 'ow_ic_script', 'نوشته');
        $languageService->addOrUpdateValue($langFaId, 'base', 'ow_ic_server', 'میزبان');
        $languageService->addOrUpdateValue($langFaId, 'base', 'ow_ic_star', 'ستاره');
        $languageService->addOrUpdateValue($langFaId, 'base', 'ow_ic_tag', 'برچسب');
        $languageService->addOrUpdateValue($langFaId, 'base', 'ow_ic_trash', 'سطل زباله');
        $languageService->addOrUpdateValue($langFaId, 'base', 'ow_ic_unlock', 'قفل باز');
        $languageService->addOrUpdateValue($langFaId, 'base', 'ow_ic_up_arrow', 'فلش بالا');
        $languageService->addOrUpdateValue($langFaId, 'base', 'ow_ic_update', 'به‌روزرسانی');
        $languageService->addOrUpdateValue($langFaId, 'base', 'ow_ic_user', 'کاربر');
        $languageService->addOrUpdateValue($langFaId, 'base', 'ow_ic_video', 'ویدئو');
        $languageService->addOrUpdateValue($langFaId, 'base', 'ow_ic_warning', 'هشدار');
        $languageService->addOrUpdateValue($langFaId, 'base', 'ow_ic_write', 'نوشتن');
		$languageService->addOrUpdateValue($langFaId, 'admin', 'massmailing_email_format_label', 'فرمت رایانامه');
		$languageService->addOrUpdateValue($langFaId, 'admin', 'massmailing_body_label', 'متن پیام رایانامه');
		$languageService->addOrUpdateValue($langFaId, 'admin', 'admin_password_html', 'گذرواژه جدید برای {$sitename}<br> تنظیم شد. گذرواژه جدید شما <b>{$password}</b><br> است.');
		$languageService->addOrUpdateValue($langFaId, 'admin', 'invalid _license_item_notification', 'مجوز برای افزونه/پوسته <a href=\"{$url}\">{$name}</a> که برای استفاده از آن تلاش می‌کنید نامعتبر است.');
		$languageService->addOrUpdateValue($langFaId, 'admin', 'changelog_heading', 'فهرست تغییرات به صورت زیر است:');
		$languageService->addOrUpdateValue($langFaId, 'admin', 'mail_template_admin_invalid_license_subject', 'هشدار در مورد افزونه/پوسته بدون مجوز');
		$languageService->addOrUpdateValue($langFaId, 'admin', 'manage_plugins_add_license_label', 'افزودن مجوز');
		$languageService->addOrUpdateValue($langFaId, 'admin', 'plugins_manage_license_key_check_success', 'شناسه مجوز اعتبار سنجی شد.');
		$languageService->addOrUpdateValue($langFaId, 'admin', 'questions_infinite_possible_values_description', 'افزودن تعداد نامحدودی از مقادیر. مقادیر فیلد به طور منظم ذخیره می‌شوند که ممکن است سرعت جستجوی نمایه‌ها را تحت تاثیر قرار دهد.');
		$languageService->addOrUpdateValue($langFaId, 'admin', 'questions_infinite_possible_values_label', 'مقادیر ممکن');
		$languageService->addOrUpdateValue($langFaId, 'admin', 'questions_values_should_not_be_empty', 'این مقدار نباید خالی باشد');
		$languageService->addOrUpdateValue($langFaId, 'admin', 'check_updates_fail_error_message', 'عدم امکان اتصال به کارگزار راه دور');
		$languageService->addOrUpdateValue($langFaId, 'admin', 'manage_theme_activate_invalid_license_key', 'شناسه مجوز خالی یا نامعتبر است.');
		$languageService->addOrUpdateValue($langFaId, 'admin', 'check_license_invalid_server_responce_err_msg', 'کارگزار پاسخی نامعتبر یا ناشناخته را باز می‌گرداند.');	
		$languageService->addOrUpdateValue($langFaId, 'admin', 'admin_password', 'گذرواژه وب‌گاه: {$password}');
		$languageService->addOrUpdateValue($langFaId, 'admin', 'back_to_site_label', 'بازگشت به وب‌گاه');
		$languageService->addOrUpdateValue($langFaId, 'admin', 'export_lang_note', 'شما می‌توانید از هر زبانی برای هر افزونه‌ای که بر روی وب‌گاه خود نصب کرده‌اید پشتیبان تهیه نمایید.');
		$languageService->addOrUpdateValue($langFaId, 'admin', 'input_settings_resource_list_desc', 'لیست دامنه‌هایی که شما اجازه می‌دهید تا کاربران از آن‌جا در شبکه فیلم قرار دهند، وب‌گاه‌هایی را قرار دهید که از کد نشانی پشتیبانی می‌کنند(در هر خط یک دامنه وارد کنید)');
		$languageService->addOrUpdateValue($langFaId, 'admin', 'joined', 'زمان پیوستن به وب‌گاه');
		$languageService->addOrUpdateValue($langFaId, 'admin', 'mail_smtp_connection_desc', ' از آزمایش اتصال SMTP قبل از آغاز استفاده آن برای ارسال رایانامه اطمینان حاصل کنید. در غیر این‌صورت وب‌گاه شما ممکن است فرستادن رایانامه‌ها را به طور کلی متوقف کند.');
		$languageService->addOrUpdateValue($langFaId, 'admin', 'mail_smtp_title_enabled_desc', 'لطفا اگر طریقه کار را نمی‌دانید آن‌را فعال نکنید، چون ممکن است وب‌گاه شما دیگر رایانامه نفرستد');
		$languageService->addOrUpdateValue($langFaId, 'admin', 'maintenance_enable_desc', '<p>این امکان وب‌گاه شما را از دسترس خارج می‌کند و به همه پیام تعمیراتی را نمایش می‌دهد</p><b>ولی شما همچنان قادر خواهید بود که از این نشانی وارد شوید <a href="{$site_url}sign-in">{$site_url}sign-in</a></b>');
		$languageService->addOrUpdateValue($langFaId, 'admin', 'maintenance_enable_label', 'وب‌گاه خود را غیرفعال کنید تا صفحه تعمیر و نگهداری نمایش داده شود');
		$languageService->addOrUpdateValue($langFaId, 'admin', 'maintenance_text_value', '<h1 class="ow_stdmargin ow_ic_gear_wheel">در حال تعمیر</h1><center><b> با عرض پوزش، وب‌گاه در دست تعمیر و نگهداری است و به زودی آماده خدمت‌رسانی مجدد به شما کاربران خواهد بود.</b></center>');
		$languageService->addOrUpdateValue($langFaId, 'admin', 'mobile_settings_mobile_context_disable_label', 'غیرفعال‌سازی نسخه موبایل وب‌گاه');
		$languageService->addOrUpdateValue($langFaId, 'admin', 'pages_and_menus_hidden_desc', 'این صفحه‌ها در واقع وجود دارند ولی در وب‌گاه نمایش داده نمی‌شوند، بخش‌هایی را که می‌خواهید نمایش داده شود<span class="ow_highlight">بکشید و رها </span> کنید');
		$languageService->addOrUpdateValue($langFaId, 'admin', 'pages_and_menus_instructions', 'شما می‌توانید صفحات و بخش‌های منو که در وب‌گاه شما وجود دارد را مشاهده کنید. بعضی از صفحات را شما بوجود آوردید و بعضی دیگر صفحاتی هستند که به وسیله افزونه فعال شده است، برای تغییر مکان منو‌ها شما باید&lt;span class="ow_highlight"&gt;بکشید و رها&lt;/span&gt; کنید');
		$languageService->addOrUpdateValue($langFaId, 'admin', 'page_note_part_2', '<br />ترتیب زبان برای نمایش در وب‌گاه. اولین زبان به عنوان زبان پیش‌فرض وب‌گاه شما انتخاب می‌شود');
		$languageService->addOrUpdateValue($langFaId, 'admin', 'page_settings_form_favicon_desc', 'تصویر وب‌گاه با فرمت Ico با انداره 16 در 16');
		$languageService->addOrUpdateValue($langFaId, 'admin', 'page_settings_form_favicon_label', 'فاوآیکن (شمایل وب‌گاه)');
		$languageService->addOrUpdateValue($langFaId, 'admin', 'permissions_feedback_user_kicked_from_moders', 'کاربر از بخش ناظم‌های وب‌گاه پاک شد');
		$languageService->addOrUpdateValue($langFaId, 'admin', 'permissions_index_guests_can_view_site', 'مهمانان می‌توانند وب‌گاه را مشاهده کنند');
		$languageService->addOrUpdateValue($langFaId, 'admin', 'permissions_moders_make_moderator', 'ناظم وب‌گاه شود');
		$languageService->addOrUpdateValue($langFaId, 'admin', 'questions_cant_delete_last_account_type', 'شما نمی‌توانید این نوع حساب را پاک کنید. کمینه باید یک نوع حساب در وب‌گاه موجود باشد');
		$languageService->addOrUpdateValue($langFaId, 'admin', 'questions_delete_account_type_confirmation', '  به خاطر داشته باشید کاربرانی که این حساب را دارند: 1- تمام اطلاعات نمایه مرتبط با این حساب را از دست می‌دهند؛ 2- تا زمانی که وارد وب‌گاه شوند، نوع حساب دیگری را انتخاب کنند، و اطلاعات لازم را وارد کنند، نخواهند توانست از وب‌گاه استفاده کنند');
		$languageService->addOrUpdateValue($langFaId, 'admin', 'site_description_desc', 'لطفا در چند جمله کوتاه وب‌گاه خود را معرفی کنید');
		$languageService->addOrUpdateValue($langFaId, 'admin', 'site_installation', 'نصب و راه‌اندازی وب‌گاه');
		$languageService->addOrUpdateValue($langFaId, 'admin', 'site_password', 'گذرواژه وب‌گاه');
		$languageService->addOrUpdateValue($langFaId, 'admin', 'site_tagline_desc', 'کوتاه،جذاب،یک قطعه که وب‌گاه شما را شرح دهد');
		$languageService->addOrUpdateValue($langFaId, 'admin', 'site_title', 'نام وب‌گاه');
		$languageService->addOrUpdateValue($langFaId, 'admin', 'splash_button_label_desc', 'برچسب انتخابی شما برای ورود به وب‌گاه');
		$languageService->addOrUpdateValue($langFaId, 'admin', 'splash_enable_desc', 'با صفحه نمایش (شروع)، شما می‌توانید به طور مستقیم از کاربرانتان بپرسید که اگر آنها موافق هستند وارد وبوب‌گاه شما شوند');
		$languageService->addOrUpdateValue($langFaId, 'admin', 'splash_intro_value', 'آیا از قرار دادن این وب‌گاه اطمینان دارید؟');
		$languageService->addOrUpdateValue($langFaId, 'admin', 'splash_leave_url_desc', 'اگر کاربران خروج را انتخاب کنند، به جای وب‌گاه شما، به این آدرس می‌روند');
		$languageService->addOrUpdateValue($langFaId, 'admin', 'user_settings_confirm_email_desc', 'اگر این گزینه را انتخاب کنید، کاربران قبل از دسترسی به وب‌گاه باید رایانشانی‌های خودشان را تایید کنند.');
		$languageService->addOrUpdateValue($langFaId, 'admin', 'user_settings_display_name_desc', ' معمولا لازم است تا میان <b><i>نام کاربری</i></b> و نام واقعی یکی را انتخاب کنید، این از تنظیمات کلی وب‌گاه است.');
		$languageService->addOrUpdateValue($langFaId, 'admin', 'page_default_heading', 'مدیریت {$site_name}');
		$languageService->addOrUpdateValue($langFaId, 'admin', 'mail_template_admin_invite_user_subject', 'دعوت به {$site_name}');
		$languageService->addOrUpdateValue($langFaId, 'admin', 'profile_view_description', 'مشخصات نمایه {$username} در {$site_name}');
		$languageService->addOrUpdateValue($langFaId, 'admin', 'welcome_letter_subject', 'ثبت نام {$site_name}');
		$languageService->addOrUpdateValue($langFaId, 'admin', 'page_default_title', 'صفحه مدیریت {$site_name}');
		$languageService->addOrUpdateValue($langFaId, 'admin', 'questions_page_description', 'در اینجا شما می‌توانید اطلاعات اضافه شده توسط کاربران را ویرایش کنید.');
		
		$languageService->addOrUpdateValue($langFaId, 'nav', 'page_default_description', 'انجمن  {$site_name}');
		$languageService->addOrUpdateValue($langFaId, 'nav', 'page_default_heading', '{$site_name}');
		$languageService->addOrUpdateValue($langFaId, 'nav', 'page_default_title', '{$site_name}');
		
		$languageService->addOrUpdateValue($langFaId, 'base', 'billing_currency_not_supported', 'ارائه دهنده پرداخت از واحد پول فعال وب‌گاه پشتیبانی نمی‌کند (<b>{$currency}</b>)');
		$languageService->addOrUpdateValue($langFaId, 'base', 'cannot_delete_admin_user_message', 'شما نمی‌توانید یک مدیر وب‌گاه را حذف کنید');
		$languageService->addOrUpdateValue($langFaId, 'base', 'feed_activity_join_profile_string', 'بر عضو شدن {$user} در وب‌گاه ما نظر داد.');
		$languageService->addOrUpdateValue($langFaId, 'base', 'feed_activity_join_profile_string_like', 'می‌پسندد که {$user} عضو وب‌گاه ما شد.');
		$languageService->addOrUpdateValue($langFaId, 'base', 'feed_user_join', 'به وب‌گاه ما پیوست!');
		$languageService->addOrUpdateValue($langFaId, 'base', 'massmailing_unsubscribe_confirmation', 'لطفا تائید کنید که شما نمی‌خواهید رایانامه حجیم از این وب‌گاه دریافت کنید.');
		$languageService->addOrUpdateValue($langFaId, 'base', 'privacy_action_view_my_presence_on_site', 'نمایش حضور من در وب‌گاه');
		$languageService->addOrUpdateValue($langFaId, 'base', 'billing_currency_not_supported', 'ارائه دهنده پرداخت از واحد پول فعال وب‌گاه پشتیبانی نمی‌کند (<b>{$currency}</b>)');
		$languageService->addOrUpdateValue($langFaId, 'base', 'questions_question_presentation_url_label', 'نشانی وب‌گاه');
		$languageService->addOrUpdateValue($langFaId, 'base', 'rss_widget_url_invalid_msg', 'یک نشانی وب‌گاه معتبر وارد کنید');
		$languageService->addOrUpdateValue($langFaId, 'base', 'rss_widget_url_label', 'نشانی وب‌گاه');
		$languageService->addOrUpdateValue($langFaId, 'base', 'suspend_notification_text', 'کاربر گرامی ({$realName}) ، حساب کاربری شما در وب‌گاه {$site_name} به دلیل {$suspendReason} به حالت تعلیق درآمده است، {$site_name} مدیریت');
		$languageService->addOrUpdateValue($langFaId, 'base', 'tf_img_from_url', 'از نشانی وب‌گاه');
		$languageService->addOrUpdateValue($langFaId, 'base', 'tf_img_url', 'نشانی وب‌گاه تصویر');
		$languageService->addOrUpdateValue($langFaId, 'base', 'user_approved_mail_txt', 'کاربر گرامی ({$user_name})، ما بسیار خوشحالیم که به شما اطلاع می‌دهیم که حساب شما در {$site_name} فعال شده است . اکنون شما می‌توانید از این قسمت وارد وب‌گاه شوید {$site_url} امیدواریم که ازوب‌گاه ما لذت ببرید . با سپاس از شما  مدیریت {$site_name} {$site_url}');
		$languageService->addOrUpdateValue($langFaId, 'base', 'wackwall', 'ایجاد وب‌گاه اجتماعی');
		$languageService->addOrUpdateValue($langFaId, 'base', 'ws_link_empty_fields', 'لطفا توضیح و نشانی وب‌گاه را کامل کنید');
		
		
		$languageService->addOrUpdateValue($langFaId, 'base', 'enable_captcha', 'آیا عبارت امنیتی در فرم عضویت فعال است؟');
		$languageService->addOrUpdateValue($langFaId, 'base', 'edit_profile_warning', 'به خاطر داشته باشید که ویرایش فیلدهای متنی منجر به فرستاده شدن نمایه شما برای تایید خواهد شد. در طول فرآیند تایید شما قادر به استفاده از وب‌گاه نخواهید بود.');
		$languageService->addOrUpdateValue($langFaId, 'base', 'moderation_user_update', 'نمایه به‌روزرسانی شد. <a href=\"{$profileUrl}\">مشاهده نمایه</a>');
		$languageService->addOrUpdateValue($langFaId, 'base', 'message_label', 'پیام');
		$languageService->addOrUpdateValue($langFaId, 'base', 'subject_label', 'عنوان');
		$languageService->addOrUpdateValue($langFaId, 'base', 'send_message_to_email', 'فرستادن پیام به رایانامه');
		$languageService->addOrUpdateValue($langFaId, 'base', 'write_message', 'نوشتن پیام');
		$languageService->addOrUpdateValue($langFaId, 'base', 'save_and_approve', 'ذخیره و تایید');
		$languageService->addOrUpdateValue($langFaId, 'base', 'message_send', 'پیام فرستاده شد');
		$languageService->addOrUpdateValue($langFaId, 'base', 'invalid_user', 'کاربر موجود نیست');
		$languageService->addOrUpdateValue($langFaId, 'base', 'empty_subject', 'خالی کردن عنوان رایانامه');
		$languageService->addOrUpdateValue($langFaId, 'base', 'empty_message', 'خالی کردن پیام');
		$languageService->addOrUpdateValue($langFaId, 'base', 'delete_user_feedback', 'کاربر حذف شده است');
		$languageService->addOrUpdateValue($langFaId, 'base', 'message_invitation', 'پیام');
		$languageService->addOrUpdateValue($langFaId, 'base', 'authorization_role_bronze', 'افراد مهم');
		$languageService->addOrUpdateValue($langFaId, 'base', 'questions_question_presentation_fselect_label', 'انتخاب منفرد - منظم (نتایج کندتر، نامحدود)');
		$languageService->addOrUpdateValue($langFaId, 'base', 'themes_item_add_success_message', 'پوسته افزوده شد');
        $languageService->addOrUpdateValue($langFaId, 'base', 'copyright', '{$site_name} – تمام حقوق محفوظ است');
		$languageService->addOrUpdateValue($langFaId, 'base', 'questions_question_presentation_radio_label', 'تک انتخاب (دکمه رادیویی)');
		$languageService->addOrUpdateValue($langFaId, 'base', 'questions_question_presentation_select_label', 'تک انتخاب (منوی کرکره‌ای)');
		$languageService->addOrUpdateValue($langFaId, 'base', 'mark_email_unverified_btn', 'عدم تایید رایانشانی');
		$languageService->addOrUpdateValue($langFaId, 'base', 'mark_email_verified_btn', 'تایید رایانشانی');
		
		$languageService->addOrUpdateValue($langFaId, 'base', 'user_suspend_btn_lbl', 'تعلیق');
		$languageService->addOrUpdateValue($langFaId, 'base', 'user_unsuspend_btn_lbl', 'رفع تعلیق');
		$languageService->addOrUpdateValue($langFaId, 'base', 'suspend_user_btn', 'تعلیق');
		$languageService->addOrUpdateValue($langFaId, 'base', 'suspend_floatbox_title', 'تعلیق {$displayName}');
		
		$languageService->addOrUpdateValue($langFaId, 'admin', 'btn_label_delete', 'حذف');
		$languageService->addOrUpdateValue($langFaId, 'admin', 'confirm_delete', 'آیا از حذف این تصویر اطمینان دارید؟');
		$languageService->addOrUpdateValue($langFaId, 'admin', 'confirm_delete_images', 'آیا از حذف این تصاویر اطمینان دارید؟');
		$languageService->addOrUpdateValue($langFaId, 'admin', 'confirm_delete_users', 'آیا از حذف کاربرانی که انتخاب کرده‌اید اطمینان دارید؟');
		$languageService->addOrUpdateValue($langFaId, 'admin', 'delete', 'حذف');
		$languageService->addOrUpdateValue($langFaId, 'admin', 'delete_btn_label', 'حذف');
		$languageService->addOrUpdateValue($langFaId, 'admin', 'delete_selected', 'حذف انتخاب شده');
		$languageService->addOrUpdateValue($langFaId, 'admin', 'manage_plugins_delete_button_label', 'حذف');
		$languageService->addOrUpdateValue($langFaId, 'admin', 'manage_plugins_delete_confirm_message', 'آیا از حذف افزونه {$pluginName} به طور کامل اطمینان دارید؟');
		$languageService->addOrUpdateValue($langFaId, 'admin', 'manage_plugins_uninstall_confirm_message', 'آیا از حذف افزونه اطمینان دارید؟');
		$languageService->addOrUpdateValue($langFaId, 'admin', 'manage_plugins_uninstall_error_message', 'خطا در حذف افزونه');
		$languageService->addOrUpdateValue($langFaId, 'admin', 'manage_plugins_uninstall_request_box_cap_label', 'درخواست حذف افزونه');
		$languageService->addOrUpdateValue($langFaId, 'admin', 'permissions_delete_role', 'حذف');
		$languageService->addOrUpdateValue($langFaId, 'admin', 'permissions_go_to_role_management_page', 'برای ساختن، ویرایش و حذف نقش‌های کاربر به <a href="{$url}"> صفحه مدیریت نقش </a> بروید');
		$languageService->addOrUpdateValue($langFaId, 'admin', 'questions_delete_question_confirmation', 'آیا از حذف این پرسش اطمینان دارید؟');
		$languageService->addOrUpdateValue($langFaId, 'admin', 'questions_delete_question_parent_confirmation', 'آیا از حذف این پرسش اطمینان دارید؟&#13; توجه :این عمل باعث حذف همیشگی پاسخ‌ها در نمایه کاربران خواشد شد.همچنین این پرسش یک وابستگی به {$questions} دارد.این اطلاعات نیز همچنین حذف خواهند شد');
		$languageService->addOrUpdateValue($langFaId, 'admin', 'questions_delete_section_confirmation', 'آیا از حذف این بخش اطمینان دارید؟');
		$languageService->addOrUpdateValue($langFaId, 'admin', 'questions_delete_section_confirmation_with_move_questions', 'آیا از حذف این بخش اطمینان دارید؟ تمام سوالات به {$sectionName} منتقل خواهد شد');
		$languageService->addOrUpdateValue($langFaId, 'admin', 'themes_choose_delete_button_label', 'حذف');
		$languageService->addOrUpdateValue($langFaId, 'admin', 'themes_choose_delete_confirm_msg', 'آیا از حذف این پوسته اطمینان دارید؟');
		$languageService->addOrUpdateValue($langFaId, 'admin', 'theme_graphics_image_delete_confirm_message', 'آیا از حذف این تصویر اطمینان دارید؟');
		$languageService->addOrUpdateValue($langFaId, 'admin', 'theme_graphics_table_delete', 'حذف');
		$languageService->addOrUpdateValue($langFaId, 'admin', 'manage_plugins_uninstall_button_label', 'حذف');
		$languageService->addOrUpdateValue($langFaId, 'admin', 'plugin_uninstall_request_text', 'آیا از حذف افزونه {$name} اطمینان دارید؟');
		$languageService->addOrUpdateValue($langFaId, 'admin', 'remove_from_featured', 'حذف از برجسته');
		$languageService->addOrUpdateValue($langFaId, 'admin', 'input_settings_user_rich_media_disable_desc', 'اگر نمی‌خواهید به کاربران خود اجازه افزودن رسانه (مانند عکس و ویدیو) در نوشته (مانند بلاگ و انجمن) رابدهید، این گزینه را فعال کنید.');
		$languageService->addOrUpdateValue($langFaId, 'admin', 'sidebar_menu_item_plugin_blogs', 'بلاگ‌ها');
		
		$languageService->addOrUpdateValue($langFaId, 'base', 'admin_delete_user_text', 'آیا از حذف این کاربر اطمینان دارید؟');
		$languageService->addOrUpdateValue($langFaId, 'base', 'comment_delete_label', 'حذف');
		$languageService->addOrUpdateValue($langFaId, 'base', 'contex_action_comment_delete_label', 'حذف دیدگاه');
		$languageService->addOrUpdateValue($langFaId, 'base', 'contex_action_user_delete_label', 'حذف کاربر');
		$languageService->addOrUpdateValue($langFaId, 'base', 'comment_delete_confirm_message', 'آیا از حذف نظر اطمینان دارید؟');
		$languageService->addOrUpdateValue($langFaId, 'base', 'delete', 'حذف');
		$languageService->addOrUpdateValue($langFaId, 'base', 'delete_comment_by_content_owner', 'حذف نظر توسط نویسنده مطلب');
		$languageService->addOrUpdateValue($langFaId, 'base', 'delete_profile', 'حذف نمایه');
		$languageService->addOrUpdateValue($langFaId, 'base', 'delete_user_confirmation', 'آیا از حذف نمایه اطمینان دارید؟');
		$languageService->addOrUpdateValue($langFaId, 'base', 'delete_user_confirmation_label', 'تایید حذف');
		$languageService->addOrUpdateValue($langFaId, 'base', 'delete_user_content_label', 'حذف مطالب کاربر');
		$languageService->addOrUpdateValue($langFaId, 'base', 'delete_user_delete_button', 'حذف');
		$languageService->addOrUpdateValue($langFaId, 'base', 'delete_user_index', 'حذف نمایه');
		$languageService->addOrUpdateValue($langFaId, 'base', 'moderation_delete_confirmation', 'آیا از حذف {$content} اطمینان دارید؟');
		$languageService->addOrUpdateValue($langFaId, 'base', 'moderation_delete_multiple_confirmation', 'آیا از حذف {$count} {$content} اطمینان دارید؟');
		$languageService->addOrUpdateValue($langFaId, 'base', 'profile_toolbar_user_delete_label', 'حذف');
		$languageService->addOrUpdateValue($langFaId, 'base', 'questions_admin_delete_label', 'حذف');
		$languageService->addOrUpdateValue($langFaId, 'base', 'user_action_unmark_as_featured', 'حذف از برجسته‌ها');
		$languageService->addOrUpdateValue($langFaId, 'base', 'widgets_action_delete', 'حذف');
		$languageService->addOrUpdateValue($langFaId, 'base', 'widgets_delete_component_confirm', 'آیا از حذف این ابزارک اطمینان دارید؟');
		$languageService->addOrUpdateValue($langFaId, 'base', 'ws_button_label_more', 'انتهای پیش‌نمایش');
		$languageService->addOrUpdateValue($langFaId, 'base', 'html_error', 'متن وارد شده مجاز نیست.');
		$languageService->addOrUpdateValue($langFaId, 'base', 'widgets_allow_customize_locked_text', '            به دلیل فعال نبودن افزونه extension=php_tidy.dll - tidy در زبان برنامه‌نویسی PHP، امکان ارائه سفارشی‌سازی به کاربران به دلیل چالش‌های امنیتی وجود ندارد.');
		$languageService->addOrUpdateValue($langFaId, 'base', 'html_error_details', 'جزئیات خطا');
		$languageService->addOrUpdateValue($langFaId, 'base', 'authorization_group_blogs', 'بلاگ‌ها');
		$languageService->addOrUpdateValue($langFaId, 'base', 'join_activity_string', '<a href="{$userUrl}">{$user}</a> به {$site_name} پیوست !');
		$languageService->addOrUpdateValue($langFaId, 'base', 'widgets_default_settings_freeze', 'غیرقابل‌ حرکت‌ ساختن‌');
		$languageService->addOrUpdateValue($langFaId, 'base', 'ws_button_label_video', 'درج ویدیو‌');
		
				//replace profile picture instead of avatar
				
		$languageService->addOrUpdateValue($langFaId, 'base', 'not_writable_avatar_dir', 'پوشه تصاویر نمایه گم شده یا قابلیت نوشتن ندارد');
		
		$languageService->addOrUpdateValue($langFaId, 'base', 'avatar_avatar_is', 'تصویر نمایه تصویری با اندازه کوچک است که در نمایه شما نمایش داده می شود');
		
		$languageService->addOrUpdateValue($langFaId, 'base', 'avatar_current', 'تصویر نمایه شما');
		
		$languageService->addOrUpdateValue($langFaId, 'base', 'content_avatars_label', 'تصاویر نمایه');
		
		$languageService->addOrUpdateValue($langFaId, 'base', 'avatar_new', 'بارگذاری تصویر نمایه جدید');
		
		$languageService->addOrUpdateValue($langFaId, 'base', 'avatar_crop', 'برش تصویر نمایه');
		
		$languageService->addOrUpdateValue($langFaId, 'base', 'avatar_crop_instructions', 'با استفاده از موس خود قسمتی از تصویر نمایه را برای برش انتخاب کنید .  نتیجه قسمت برش داده شده در سمت راست نمایش داده می‌شود. زمانی که نتیجه خوشایند است بر روی دکمه "اعمال برش" کلیک کنید.' );
		
		$languageService->addOrUpdateValue($langFaId, 'base', 'avatar_picture', 'تصویر نمایه شما');
		
		$languageService->addOrUpdateValue($langFaId, 'base', 'avatar_upload_types', 'قالب‌های موجود برای بارگذاری تصویر نمایه<span class="ow_txt_value">JPG</span>/<span class="ow_txt_value">GIF</span>/<span class="ow_txt_value">PNG</span>');
		
		$languageService->addOrUpdateValue($langFaId, 'base', 'avatar_change_avatar', 'تغییر تصویر نمایه');
		
		$languageService->addOrUpdateValue($langFaId, 'base', 'avatar_change', 'تغییر تصویر نمایه');
		
		$languageService->addOrUpdateValue($langFaId, 'admin', 'user_avatar_settings', 'تنظیمات تصویر نمایه');
		
		$languageService->addOrUpdateValue($langFaId, 'admin', 'user_settings_avatar_size', 'اندازه تصویر نمایه');
		
		$languageService->addOrUpdateValue($langFaId, 'admin', 'user_settings_big_avatar_size', 'تصویر نمایه با اندازه بزرگ');
		
		$languageService->addOrUpdateValue($langFaId, 'base', 'avatar_widget', 'تصویر نمایه');
		
		$languageService->addOrUpdateValue($langFaId, 'base', 'avatar_activity_string', '<a href="{$userUrl}">{$user}</a>تصویر نمایه‌شان را تغییر دادند');
		
		$languageService->addOrUpdateValue($langFaId, 'base', 'avatar_update_string', 'تصویر نمایه خود را تغییر داده است');
		
		$languageService->addOrUpdateValue($langFaId, 'base', 'avatar_feed_string', 'تصویر نمایه‌اش را تغییر داده است');
		
		$languageService->addOrUpdateValue($langFaId, 'admin', 'avatar_feed_string', 'تصویر نمایه‌اش را تغییر داده است');
		
		$languageService->addOrUpdateValue($langFaId, 'admin', 'avatar_label', 'برچسب تصویر نمایه');
		
		$languageService->addOrUpdateValue($langFaId, 'admin', 'display_avatar_label', 'نمایش برچسب نقش بر روی تصویر نمایه');
		
		$languageService->addOrUpdateValue($langFaId, 'admin', 'default_avatar_deleted', 'تصویر نمایه پیش‌فرض پاک شد');
		
		$languageService->addOrUpdateValue($langFaId, 'admin', 'confirm_avatar_delete', 'آیا از حذف تصویر نمایه پیش‌فرض اطمینان دارید؟');
		
		$languageService->addOrUpdateValue($langFaId, 'admin', 'user_settings_avatar_image_desc', 'تغییر دادن تصویر نمایه پیش‌فرض پوسته');
		
		$languageService->addOrUpdateValue($langFaId, 'admin', 'user_settings_avatar_image', 'تصویر پیش‌فرض نمایه');
		
		$languageService->addOrUpdateValue($langFaId, 'base', 'input_settings_avatar_max_upload_size_label', 'بیشینه اندازه بارگذاری تصویر نمایه');
		
		$languageService->addOrUpdateValue($langFaId, 'base', 'avatar_changed', 'تصویر نمایه تغییر کرده است');
		
		$languageService->addOrUpdateValue($langFaId, 'base', 'avatar_has_been_approved', 'تصویر نمایه تایید شده است');
		
		$languageService->addOrUpdateValue($langFaId, 'base', 'avatar_image_too_small', 'این تصویر بسیار کوچک است و نمی‌توان آن‌را به عنوان تصویر نمایه انتخاب کرد <br />کوچکترین سایز  {$width}px x {$height}px');
		
		$languageService->addOrUpdateValue($langFaId, 'base', 'avatar_pending_approval', 'تصویر نمایه در انتظار تایید');
		
		$languageService->addOrUpdateValue($langFaId, 'admin', 'join_display_photo_upload_desc', 'به کاربران اجازه دهید هنگام ثبت‌نام تصویر نمایه خود را بارگذاری کنند');
		
		$languageService->addOrUpdateValue($langFaId, 'admin', 'join_display_photo_upload', 'بارگذاری تصویر نمایه');
		
		$languageService->addOrUpdateValue($langFaId, 'admin', 'user_settings_avatar_size_error', 'بیشینه اندازه تصویر نمایه قابل قبول {$max}px است');
		
		$languageService->addOrUpdateValue($langFaId, 'admin', 'user_settings_avatar_size_label', 'اندازه برش تصویر نمایه<br />');
		
		$languageService->addOrUpdateValue($langFaId, 'admin', 'user_settings_big_avatar_size_error', 'بیشینه اندازه تصویر نمایه بزرگ {$max}px است');
		
		$languageService->addOrUpdateValue($langFaId, 'admin', 'user_settings_big_avatar_size_label', 'ابعاد تصویر نمایه بزرگ');
		
		$languageService->addOrUpdateValue($langFaId, 'base', 'feed_activity_avatar_string', 'به روی تصویر نمایه جدید {$user} نظر داد');
		
		$languageService->addOrUpdateValue($langFaId, 'base', 'feed_activity_avatar_string_like', 'تصویر نمایه جدید {$user} را می‌پسندد');
		
		$languageService->addOrUpdateValue($langFaId, 'admin', 'feed_content_avatar_change', 'تصویر نمایه کاربری عوض شد');
		
		$languageService->addOrUpdateValue($langFaId, 'base', 'feed_activity_avatar_string_like_own', 'تصویر نمایه جدید را می‌پسندد');
		
		$languageService->addOrUpdateValue($langFaId, 'base', 'feed_activity_avatar_string_own', 'بر روی تصویر نمایه جدید نظر داده');
		
		$languageService->addOrUpdateValue($langFaId, 'base', 'content_avatar_label', 'تصویر نمایه');
		
		$languageService->addOrUpdateValue($langFaId, 'base', 'crop_avatar_failed', 'برش تصویر نمایه ناموفق بود');
		
		$languageService->addOrUpdateValue($langFaId, 'admin', 'input_settings_avatar_max_upload_size_label', 'محدودیت حجم فایل تصویر نمایه');
		
		$languageService->addOrUpdateValue($langFaId, 'admin', 'welcome_letter_template_html' , 'Welcome to <a href=\"{$site_url}\">{$site_name}</a>! Thanks for joining us. Here are some quick links that you need to find your way around:<br/><br/>\r\n- <a href=\"{$site_url}\">Main page</a><br/>\r\n- <a href=\"{$site_url}my-profile\">Change profile picture</a><br/>\r\n- <a href=\"{$site_url}photo/viewlist/latest\">Upload Photos</a><br/>\r\n- <a href=\"{$site_url}profile/edit\">Change profile details</a><br/>\r\n- <a href=\"{$site_url}users\">Look who\'s in</a><br/><br/>\r\nFeel free to participate in our community!<br/><br/>\r\n<a href=\"{$site_url}\">{$site_name}</a> administration<br/>');
		
		$languageService->addOrUpdateValue($langFaId, 'base', 'welcome_widget_legend', 'بارگذاری_تصویر - پیوند بارگذاری تصاویر

تغییر_تصویر نمایه - پیوند تغییر تصویر نمایه');
		
		$languageService->addOrUpdateValue($langFaId, 'base', 'welcome_letter_template_text', ' {$site_name} خوش آمدید! برای پیوستن به شبکه ممنونیم.در این‌جا چندین پیوند سریع برای صرفه جویی در وقت شما آورده‌ایم:<br/> - <a : - صفحه اصلی: {$site_url} - تغییر تصویر نمایه: {$site_url}profile/avatar - تغییر جزئیات نمایه: {$site_url}- دیدن/مرتب‌سازی دوباره نمایه: {$site_url}my-profile - مشاهده کاربران حاضر: {$site_url}- از اشتراک مطالب با یکدیگر لذت ببرید! ');
		
		$languageService->addOrUpdateValue($langFaId, 'base', 'welcome_widget_content', '<p> به شبکه ما خوش آمدید! در این‌جا چند پیوند سریع برای راحتی شما ارائه شده </p> <ul class="ow_regular"> <li><a href="profile/avatar" change_avatar>تغییر تصویر نمایه</a></li> <li><a href="javascript://" photo_upload>بارگذاری تصاویر</a></li> <li><a href="profile/edit">ویرایش نمایه</a></li> <li><a href="my-profile">پیش‌نمایش / بازتنظیم مشخصات</a></li> <li><a href="users">کاربرانی که هم‌اکنون حاضر هستند</a></li> </ul> <p> </p>');
		
		$languageService->addOrUpdateValue($langFaId, 'admin', 'permissions_index_no', 'خیر');
		
		$languageService->addOrUpdateValue($langFaId, 'admin', 'license_form_leave_label', 'بازگشت');
		
		$languageService->addOrUpdateValue($langFaId, 'admin', 'languages_values_updated', 'مقدارها به‌روزرسانی شد');
		
		$languageService->addOrUpdateValue($langFaId, 'admin', 'pages_and_menus_instructions', 'شما می‌توانید صفحات و بخش‌های منو که در وب‌گاه شما وجود دارد را مشاهده کنید. بعضی از صفحات را شما بوجود آوردید و بعضی دیگر صفحاتی هستند که به وسیله افزونه فعال شده است ,برای تغییر مکان منو‌ها شما باید<span class="ow_highlight">بکشید و رها</span> کنید');
		
		$languageService->addOrUpdateValue($langFaId, 'admin', 'massmailing_following_variables_text', 'شما می‌توانید از متغیرهای زیر استفاده کنید:');
		
		$languageService->addOrUpdateValue($langFaId, 'base', 'comment_box_cap_label', 'نظرات');
		
		$languageService->addOrUpdateValue($langFaId, 'base', 'flag_accepted', 'گزارش تخلف با موفقیت ثبت شد');
		
		$languageService->addOrUpdateValue($langFaId, 'base', 'email_verify_verify_mail_was_sent', 'رایانامه تایید حساب کاربری با موفقیت ارسال شد');
		
		$languageService->addOrUpdateValue($langFaId, 'base', 'base_sign_in_txt', 'به شبکه ما خوش آمدید !<br /><br /> اطلاعات ورود خود را وارد کنید یا عضو شوید');
		
		$languageService->addOrUpdateValue($langFaId, 'base', 'welcome_widget_content', '<p> به شبکه ما خوش آمدید! در این‌جا چند پیوند سریع برای راحتی شما ارائه شده </p> <ul class="ow_regular"> <li><a href="profile/avatar" change_avatar>تغییر تصویر نمایه</a></li> <li><a href="javascript://" photo_upload>بارگذاری تصاویر</a></li> <li><a href="profile/edit">ویرایش نمایه</a></li> <li><a href="my-profile">پیش‌نمایش / بازتنظیم مشخصات</a></li> <li><a href="users">مشاهده کاربران</a></li> </ul> <p> </p>');
		
		$languageService->addOrUpdateValue($langFaId, 'base', 'confirm_page_ok_label', 'تایید');
		
		$languageService->addOrUpdateValue($langFaId, 'admin', 'input_settings_user_custom_html_disable_desc', 'اگر نمی‌خواهید به کاربران خود اجازه نوشتن کد HTML را بدهید، این را گزینه را فعال کنید، در صورت فعال بودن این گزینه افزونه ویرایشگر پیشرفته متن قابل استفاده نخواهد بود');
		
		$languageService->addOrUpdateValue($langFaId, 'admin', 'warning_cron_is_not_active', 'اسکریپت کران جاب فعال نیست. لطفا آن را به فهرست کرون اضافه کنید ({$path})');
		
		$languageService->addOrUpdateValue($langFaId, 'admin', 'warning_url_fopen_disabled', 'موتوشاب نمی‌تواند با سرور به‌روزرسانی ارتباط برقرار کند.به نظر می‌رسد که شما نیاز دارید تا url_fopen را در تنظیمات PHP فعال کنید');
		
		$languageService->addOrUpdateValue($langFaId, 'base', 'sing_in_to_flag', 'برای گزارش تخلف باید وارد شوید');
		
		$languageService->addOrUpdateValue($langFaId, 'admin', 'delete_image', 'حذف');
		
		$languageService->addOrUpdateValue($langFaId, 'base', 'submit_attachment_not_loaded', 'تصویر در حال بارگذاری است...');
		
		$languageService->addOrUpdateValue($langFaId, 'base', 'edit_successfull_edit', 'اطلاعات کاربری شما به‌روز شد');
		
		$languageService->addOrUpdateValue($langFaId, 'base', 'ws_video_empty_field', 'کد ویدیو را وارد کنید');
		
		$languageService->addOrUpdateValue($langFaId, 'base', 'ow_ic_video', 'ویدیو');
		
		$languageService->addOrUpdateValue($langFaId, 'admin', 'cron_configuration_required_notice', 'شما باید یک کرون جاب بسازید.');
		
		$languageService->addOrUpdateValue($langFaId, 'admin', 'manage_plugins_core_update_request_text', 'آیا از به‌روزرسانی بستر نرم‌افزار ا از نسخه <b>{$oldVersion}</b> به نسخه <b>{$newVersion}</b> اطمینان دارید؟{$info}');
		
		$languageService->addOrUpdateValue($langFaId, 'admin', 'rating_total', '(تعداد امتیاز دهندگان: {$count})');
		
		$languageService->addOrUpdateValue($langFaId, 'admin', 'rating_your', '(تعداد امتیاز دهندگان: {$count} / امتیاز شما: {$score})');
		
		$languageService->addOrUpdateValue($langFaId, 'base', 'email_verify_template_html', 'کاربرگرامی ({$username})،<br />با سپاس از شما برای ثبت نام در {$site_name}، برای تکمیل فرآیند ثبت‌نام لطفا  <a href="{$url}"> اینجا را کلیک کنید</a><br /><br />همچنین شما می‌توانید با واردسازی کد زیر در <a href="{$verification_page_url}">صفحه تاییدسازی</a> ثبت‌نام خود را تکمیل کنید. <br />{$code}<br /><br />');
		
		$languageService->addOrUpdateValue($langFaId, 'base', 'edit_profile_warning', 'به خاطر داشته باشید که پس از ویرایش فیلدهای متنی، نمایه شما نیاز به تایید دارد. در طول فرآیند تایید، شما امکان استفاده از وب‌گاه را نخواهید داشت.');
		
		$languageService->addOrUpdateValue($langFaId, 'base', 'feed_activity_avatar_string', 'بر روی تصویر نمایه جدید {$user} نظر داد');
		
		$languageService->addOrUpdateValue($langFaId, 'base', 'flags_deleted', 'گزارش تخلف حذف شد');
		
		$languageService->addOrUpdateValue($langFaId, 'base', 'flag_already_flagged', 'این مورد قبلا به‌عنوان تخلف گزارش شده است');
		
		$languageService->addOrUpdateValue($langFaId, 'base', 'flag_own_content_not_accepted', 'شما نمی‌توانید مطلب خود را به‌عنوان تخلف گزارش کنید');
		
		$languageService->addOrUpdateValue($langFaId, 'base', 'moderation_feedback_delete', '{$content} حذف شد.');
		
		$languageService->addOrUpdateValue($langFaId, 'base', 'moderation_feedback_delete_multiple', '{$count} {$content} حذف شده است.');
		
		$languageService->addOrUpdateValue($langFaId, 'base', 'not_writable_avatar_dir', 'پوشه تصاویر نمایه در دسترس نیست یا قابلیت نوشتن ندارد');
		
		$languageService->addOrUpdateValue($langFaId, 'base', 'reset_password_mail_template_content_html', 'کاربر گرامی «{$username}»،<br />شما درخواست بازتنظیم گذرواژه خود را کرده‌اید. پیوند زیر را برای تغییر گذرواژه خود دنبال کنید<br /><a href="{$resetUrl}"> {$resetUrl}</a><br /><br />اگر در استفاده از پیوند بالا مشکلی بود، پیوند مقابل را دنبال کرده و کد زیر را در آن وارد کنید  <a href="{$requestUrl}"> {$requestUrl}</a>. <br />Code: {$code}<br />اگر شما در خواست کد تنظیم مجدد نکرده‌اید لطفا این رایانامه را نادیده بگیرید. <br /><br />');
		
		$languageService->addOrUpdateValue($langFaId, 'base', 'users_list_online_meta_description', 'چه کسانی برخط هستند');
		
		$languageService->addOrUpdateValue($langFaId, 'base', 'user_approved_mail_html', '<p>کاربر گرامی ({$user_name})،</p><p>ما بسیار خوشحالیم که به شما اطلاع می‌دهیم که حساب شما در<a href="{$site_url}">{$site_name}</a>فعال شده است . اکنون شما می‌توانید از این قسمت وارد سایت شوید <a href="{$site_url}">{$site_url}</a></p><p>امیدواریم که از شبکه ما لذت ببرید .');
		
		$languageService->addOrUpdateValue($langFaId, 'base', 'mail_template_admin_invite_user_content_html', '<p>سلام ,</p><p>ما از شما دعوت می کنیم تا در  شبکه ما عضو شوید.<br />به  بپیوندید<a href="{$url}">اینجا</a></p>');
		
		$languageService->addOrUpdateValue($langFaId, 'base', 'site_email_verify_template_html', '<p>سلام,</p><p>شخصی (احتمالا شما) این رایانامه را به عنوان آدرس رایانامه رسمی <a href="{$site_url}">{$site_name}</a> سایت انتخاب کرده است.</p><p>برای کامل کردن این فرآیند شما باید این رایانامه را با باز کردن این پیوند معتبر کنید : <a href="{$url}">{$url}</a></p><p>همچنین شما می‌توانید این پیوند را نیز باز کنید <a href="{$verification_page_url}">این پیوند را</a> و بچسبانید به ادامه ی کد: <b>{$code}</b></p><p>اگر شما برای این کار هیچ اقدامی نکردید می‌توانید به راحتی این رایانامه را نادیده بگیرید و پست الکتریکی شما استفاده نخواهد شد');
		
		$languageService->addOrUpdateValue($langFaId, 'base', 'site_email_verify_template_text', 'سلام 

شخصی (احتمالا شما) این رایانامه را به عنوان آدرس رایانامه رسمی  {$site_name} ({$site_url}) سایت انتخاب کرده است.



برای کامل کردن این فرآیند شما باید این رایانامه را با باز کردن این پیوند معتبر کنید: {$url}



همچنین شما می‌توانید این پیوند را نیز باز کنید: {$verification_page_url} و بچسبانید به ادامه ی کد: {$code}



اگر شما برای این کار هیچ اقدامی نکردید می‌توانید به راحتی این رایانامه را نادیده بگیرید و پست الکتریکی شما استفاده نخواهد شد');

			$languageService->addOrUpdateValue($langFaId, 'admin', 'check_updates_success_message', 'وب‌گاه شما از طریق درگاه موتوشاب بررسی شد.');
			
			$languageService->addOrUpdateValue($langFaId, 'admin', 'check_updates_fail_error_message', 'عدم امکان اتصال به درگاه موتوشاب');
			
			$languageService->addOrUpdateValue($langFaId, 'admin', 'check_license_invalid_server_responce_err_msg', 'امکان پاسخ‌گویی به پاسخ‌های نامعتبر وجود ندارد');
			
			$languageService->addOrUpdateValue($langFaId, 'admin', 'url_copied', 'نشانی اینترنتی رونوشت شد');
			
			$languageService->addOrUpdateValue($langFaId, 'admin', 'add_language_pack_empty_file_error_message', 'بسته زبان شما خالی یا غیر معتبر است، لطفا بسته زبان معتبر را انتخاب کنید و دوباره امتحان کنید');
			
			$languageService->addOrUpdateValue($langFaId, 'admin', 'available_languages', 'زبان‌های موجود');
			
			$languageService->addOrUpdateValue($langFaId, 'admin', 'clone_language', 'رونوشت زبان');
			
			$languageService->addOrUpdateValue($langFaId, 'admin', 'clone_language_cap_label', 'رونوشت زبان');
			
			$languageService->addOrUpdateValue($langFaId, 'admin', 'feed_content_user_comment', 'دیوار نظرات نمایه');
			
			$languageService->addOrUpdateValue($langFaId, 'admin', 'item_update_request_platform_update_warning', 'نسخه به‌روزرسانی هسته در دسترس است و قبل از انجام هر به‌روزرسانی موارد توصیه می‌شود');
			
			$languageService->addOrUpdateValue($langFaId, 'admin', 'manage_plugins_core_update_request_box_cap_label', 'درخواست به‌روزرسانی هسته');
			
			$languageService->addOrUpdateValue($langFaId, 'admin', 'manage_plugins_core_update_request_text', 'آیا از به‌روزرسانی هسته نرم‌افزار ا از نسخه <b>{$oldVersion}</b> به نسخه <b>{$newVersion}</b> اطمینان دارید؟{$info}');
			
			$languageService->addOrUpdateValue($langFaId, 'admin', 'manage_plugins_available_box_cap_label', 'افزونه‌های موجود برای نصب');
			
			$languageService->addOrUpdateValue($langFaId, 'admin', 'manage_plugin_cant_add_duplicate_key_error', 'افزونه افزوده نمی‌شود. خطای تکرار در کلید افزونه');
			
			$languageService->addOrUpdateValue($langFaId, 'admin', 'manage_themes_update_process_error', 'به‌روزرسانی پوسته امکان‌پذیر نیست');
			
			$languageService->addOrUpdateValue($langFaId, 'admin', 'msg_dublicate_key', 'با عرض پوزش، کلید از قبل وجود دارد');
			
			$languageService->addOrUpdateValue($langFaId, 'admin', 'msg_lang_cloned', 'رونوشت شد');
			
			$languageService->addOrUpdateValue($langFaId, 'admin', 'msg_lang_clone_failed', 'رونوشت از زبان ناموفق بود');
			
			$languageService->addOrUpdateValue($langFaId, 'admin', 'page_settings_no_favicon_label', 'هیچ فاوآیکن (شمایل وب‌گاه) نیست، گزینه را برای فعال‌سازی انتخاب کنید.');
			
			$languageService->addOrUpdateValue($langFaId, 'admin', 'permissions_feedback_cant_remove_moder', 'قبل از این‌که بتوانید ناظم‌ را به طور کامل پاک‌کنید، باید دسترسی‌ مدیر او را لغو کنید');
			
			$languageService->addOrUpdateValue($langFaId, 'admin', 'permissions_feedback_user_kicked_from_moders', 'کاربر از بخش ناظم‌های وب‌گاه حذف شد');
			
			$languageService->addOrUpdateValue($langFaId, 'admin', 'permissions_idex_if_not_yes_will_override_settings', 'درصورت انتخاب گزینه «بله»، تمامی تنظیمات دسترسی نادیده گرفته خواهند شد');
			
			$languageService->addOrUpdateValue($langFaId, 'admin', 'permissions_index_moders_approve_members_manually', 'قبل از این‌که کاربران اجازه ورود پیدا کنند، ناظم‌ها باید آن‌ها را تایید کنند');
			
			$languageService->addOrUpdateValue($langFaId, 'admin', 'permissions_roles_deleted_msg', 'نقش(ها) حذف شد');
			
			$languageService->addOrUpdateValue($langFaId, 'admin', 'plugins_manage_need_ftp_attrs_message', 'برای کامل شدن عملیات دسترسی، به FTP نیاز است');
			
			$languageService->addOrUpdateValue($langFaId, 'admin', 'questions_question_was_deleted', 'پرسش حذف شد');
			
			$languageService->addOrUpdateValue($langFaId, 'admin', 'questions_section_info_txt', ' اگر پرسش‌های شما در نمایه زیاد باشد، می‌تونید آن‌هارا بخش‌بندی کنید، مانند بخش‌های زیر "<b><i>اطلاعات اصلی</i></b>", "<b><i>تماس</i></b>", "<b><i>علایق</i></b>",و غیره');
			
			$languageService->addOrUpdateValue($langFaId, 'admin', 'site_email_verify_promo', 'شما باید رایانشانی را تایید کنید، یک رایانشانی فعال‌سازی ارسال شد اما امکان ارسال مجدد نیز وجود دارد');
			
			$languageService->addOrUpdateValue($langFaId, 'admin', 'site_relative_time_desc', 'بجای اول آبان 95 5:31، از دیروز 5:31 استفاده کنید');
			
			$languageService->addOrUpdateValue($langFaId, 'admin', 'site_tagline_desc', 'شرح مختصر وب‌گاه شما');
			
			$languageService->addOrUpdateValue($langFaId, 'admin', 'splash_intro_value', 'آیا از ورود به این وب‌گاه اطمینان دارید؟');
			
			$languageService->addOrUpdateValue($langFaId, 'admin', 'themes_cant_delete_active_theme', 'شما نمی‌توانید پوسته فعال را حذف کنید.');
			
			$languageService->addOrUpdateValue($langFaId, 'admin', 'themes_delete_success_message', 'پوسته حذف شد');
			
			$languageService->addOrUpdateValue($langFaId, 'admin', 'theme_add_duplicated_dir_error', 'خطا در بارگذاری پوسته، پوشه پوسته {$dir} قبلا ساخته شده');
			
			$languageService->addOrUpdateValue($langFaId, 'admin', 'theme_add_extract_error', 'نمی‌تواند فایل پوسته را از حالت فشرده خارج کند');
			
			$languageService->addOrUpdateValue($langFaId, 'admin', 'theme_settings_cap_label', 'شخصی‌سازی پوسته');
			
			$languageService->addOrUpdateValue($langFaId, 'admin', 'theme_update_not_available_error', 'به‌روزرسانی پوسته در دسترس نیست');
			
			$languageService->addOrUpdateValue($langFaId, 'admin', 'theme_update_download_error', 'بسته به‌روزرسانی بایگانی نامعتبر یا خالی است');
			
			$languageService->addOrUpdateValue($langFaId, 'admin', 'user_settings_confirm_email_desc', 'با انتخاب این گزینه، کاربران قبل از دسترسی به وب‌گاه باید رایانشانی‌های خودشان را تایید کنند.');
			
			$languageService->addOrUpdateValue($langFaId, 'admin', 'themes_delete_success_message', 'پوسته حذف شد');
			
			$languageService->addOrUpdateValue($langFaId, 'admin', 'warning_url_fopen_disabled', 'درگاه به‌روزرسانی موتوشاب امکان برقراری ارتباط با نرم‌افزار شما را ندارد. به نظر می‌رسد که شما نیاز دارید تا url_fopen را در تنظیمات PHP فعال کنید');
			
			$languageService->addOrUpdateValue($langFaId, 'base', 'seo_meta_choose_pages_label', 'انتخاب صفحات');
			
			$languageService->addOrUpdateValue($langFaId, 'base', 'seo_meta_section_base_pages', 'صفحات اصلی');
			
			$languageService->addOrUpdateValue($langFaId, 'base', 'seo_meta_section_users', 'کاربران');
			
			$languageService->addOrUpdateValue($langFaId, 'base', 'seo_meta_forgot_pass_label', 'صفحه فراموشی گذرواژه');
			
			$languageService->addOrUpdateValue($langFaId, 'base', 'seo_meta_form_element_title_label', 'عنوان');
			
			$languageService->addOrUpdateValue($langFaId, 'base', 'meta_title_forgot_pass', 'فراموشی گذروازه برای {$site_name}؟');
			
			$languageService->addOrUpdateValue($langFaId, 'base', 'seo_meta_form_element_desc_label', 'توضیحات اضافی');
			
			$languageService->addOrUpdateValue($langFaId, 'base', 'meta_desc_forgot_pass', 'برای دریافت گذرواژه‌ای جدید، رایانشانی را که در زمان ثبت‌نام در «{$site_name}» استفاده کرده‌اید وارد کنید.');
			
			$languageService->addOrUpdateValue($langFaId, 'base', 'seo_meta_form_element_keywords_label', 'کلمات کلیدی اضافی');
			
			$languageService->addOrUpdateValue($langFaId, 'base', 'meta_keywords_forgot_pass', ' ');
			
			$languageService->addOrUpdateValue($langFaId, 'base', 'seo_meta_form_element_index_label', 'اجازه برای شاخص‌گذاری');
			
			$languageService->addOrUpdateValue($langFaId, 'base', 'seo_meta_sign_in_label', 'صفحه ورود');
			
			$languageService->addOrUpdateValue($langFaId, 'base', 'meta_title_sign_in', 'ورود به {$site_name} – یافتن دوستان');
			
			$languageService->addOrUpdateValue($langFaId, 'base', 'meta_desc_sign_in', 'ورود به وب‌گاه {$site_name} و آشنایی با افراد جدید');
			
			$languageService->addOrUpdateValue($langFaId, 'base', 'meta_keywords_sign_in', '');
			
			$languageService->addOrUpdateValue($langFaId, 'base', 'seo_meta_join_label', 'عضویت');
			
			$languageService->addOrUpdateValue($langFaId, 'base', 'meta_title_join', 'عضویت در {$site_name} - یافتن دوستان ');
			
			$languageService->addOrUpdateValue($langFaId, 'base', 'meta_desc_join', 'عضویت در {$site_name} برای آشنایی با افراد جدید و گپ زدن. هم‌اکنون دوستان خود را بیابید!');
			
			$languageService->addOrUpdateValue($langFaId, 'base', 'meta_keywords_join', '');
			
			$languageService->addOrUpdateValue($langFaId, 'base', 'seo_meta_index_label', 'فهرست');
			
			$languageService->addOrUpdateValue($langFaId, 'base', 'meta_title_index', '{$site_name}');
			
			$languageService->addOrUpdateValue($langFaId, 'base', 'meta_desc_index', 'شبکه اجتماعی {$site_name}. هم‌اکنون با دوستانی جدید آشنا شوید!');
			
			$languageService->addOrUpdateValue($langFaId, 'base', 'meta_keywords_index', '');
			
			$languageService->addOrUpdateValue($langFaId, 'base', 'seo_upload_logo_label', 'بارگذاری لوگو');
			
			$languageService->addOrUpdateValue($langFaId, 'base', 'seo_social_meta_text', '<p>شما نیازی به تنظیم خاصی ندارید – همه موارد برای شما آماده شده است. تقریبا همه داده‌های مورد نیاز از صفحه تنظیمات SEO رونوشت شده و به برچسب‌های گراف باز (Open Graph Tags) منتقل می‌شوند. فهرست برچسب‌های استفاده شده عبارتند از:    </p>    <ul class=\"ow_stdmargin\">    <li><strong>og:عنوان</strong> - رونوشت از عنوان</li>    <li><strong>og:پیوند</strong> - رونوشت از نشانی اینترنتی</li>    <li><strong>og:نوع</strong> -«وب‌گاه» که به‌صورت پیش‌فرض تنظیم شده است مقدار</li>    <li><strong>og:توصیف</strong> - رونوشت از ابرتوصیف</li> <li><strong>og:site_name</strong> - {$site_name}</li>   <li><strong>twitter:عنوان</strong> - رونوشت از عنوان</li> <li><strong>twitter:توصیف</strong> - رونوشت از ابرتوصیف</li>    <li><strong>og:تصویر و توییتر:تصویر</strong> -برای هر دو برچسب تصویر یکسان تنظیم می‌شود. این انواع تصاویر به‌صورت خودکار بر اساس صفحه در سوال تنظیم می‌شوند: لوگو، پیش‌نمایش ویدویو، تصویر رویداد، تصویر کاربر، لوگو یا تصویر گروه، تصویر اول آلبوم تصویر،  تصویر. </li>    </ul>');
			
			$languageService->addOrUpdateValue($langFaId, 'base', 'form_social_meta_logo_label', 'لوگو با کیفیت خوب');
			
			$languageService->addOrUpdateValue($langFaId, 'base', 'social_meta_logo_desc', 'به‌منظور استفاده در نوشته‌های به اشتراک‌گذاری شده (عرض تصویر توصیه‌شده 1000px است؛ فرمت تصویر توصیه‌شده JPG است)');
			
			$languageService->addOrUpdateValue($langFaId, 'admin', 'seo_page', 'صفحه سئو');
			
			$languageService->addOrUpdateValue($langFaId, 'admin', 'seo_sitemap', 'نقشه وب‌گاه');
			
			$languageService->addOrUpdateValue($langFaId, 'admin', 'seo_social_meta', 'فرا اجتماعی');
			
			$languageService->addOrUpdateValue($langFaId, 'admin', 'seo_sitemap_settings', 'تنظیمات نقشه وب‌گاه');
			
			$languageService->addOrUpdateValue($langFaId, 'admin', 'seo_sitemap_schedule_updates', 'زمان‌بندی به‌روزرسانی‌ها');
			
			$languageService->addOrUpdateValue($langFaId, 'admin', 'seo_sitemap_schedule_updates_desc', 'بازه زمانی به‌روزرسانی نقشه وب‌گاه');
			
			$languageService->addOrUpdateValue($langFaId, 'admin', 'seo_sitemap_update_daily', 'روزانه');
			
			$languageService->addOrUpdateValue($langFaId, 'admin', 'seo_sitemap_update_weekly', 'هفتگی');
			
			$languageService->addOrUpdateValue($langFaId, 'admin', 'seo_sitemap_update_monthly', 'ماهانه');
			
			$languageService->addOrUpdateValue($langFaId, 'admin', 'seo_sitemap_page_types', 'انواع صفحه');
			
			$languageService->addOrUpdateValue($langFaId, 'admin', 'seo_sitemap_base_pages', 'صفحات پایه‌ای');
			
			$languageService->addOrUpdateValue($langFaId, 'admin', 'seo_sitemap_users', 'کاربران');
			
			$languageService->addOrUpdateValue($langFaId, 'admin', 'seo_page_heading', 'تنظیمات سئو');
			
			$languageService->addOrUpdateValue($langFaId, 'admin', 'seo_sitemap_note_desc', 'توجه: لطفا <b>"Sitemap: {$siteMapUrl}"</b>  به انتهای فایل robots.txt اضافه شود.');
			
			$languageService->addOrUpdateValue($langFaId, 'admin', 'sidebar_menu_item_seo_settings', 'سئو');
			
			$languageService->addOrUpdateValue($langFaId, 'admin', 'admin_suspend_floatbox_title', 'تعلیق کاربران');
			
			$languageService->addOrUpdateValue($langFaId, 'base', 'suspend_reason', 'دلیل تعلیق');
			
			$languageService->addOrUpdateValue($langFaId, 'base', 'seo_meta_form_element_title_desc', 'طول عنوان پیشنهادی تا 70 نشانه است');
			
			$languageService->addOrUpdateValue($langFaId, 'base', 'seo_meta_form_element_desc_desc', 'طول شرح پیشنهادی تا 150 نشانه است');
			
			$languageService->addOrUpdateValue($langFaId, 'base', 'ow_mail_information', 'این رایانامه از طریق وبگاه «{$site_name}» برای شما ارسال شده است.');
			
			$languageService->addOrUpdateValue($langFaId, 'base', 'component_sign_in_password_invitation', 'گذرواژه');
			
			$languageService->addOrUpdateValue($langFaId, 'base', 'flag_flag', 'گزارش تخلف');
			
			
			//mobile
			$languageService->addOrUpdateValue($langFaId, 'base', 'mobile_admin_navigation', 'منو موبایل');
			
			$languageService->addOrUpdateValue($langFaId, 'base', 'mobile_admin_pages_index', 'صفحه اصلی');
			
			$languageService->addOrUpdateValue($langFaId, 'base', 'mobile_admin_pages_dashboard', 'داشبورد');
			
			$languageService->addOrUpdateValue($langFaId, 'base', 'mobile_admin_settings', 'تنظیمات');
			
			$languageService->addOrUpdateValue($langFaId, 'base', 'admin_nav_top_section_label', 'منو بالا');
			
			$languageService->addOrUpdateValue($langFaId, 'base', 'admin_nav_bottom_section_label', 'منو پایین');
			
			$languageService->addOrUpdateValue($langFaId, 'base', 'admin_nav_new_item_label', 'صفحه جدید');
			
			$languageService->addOrUpdateValue($langFaId, 'base', 'admin_nav_hidden_section_label', 'پنهان');
			
			$languageService->addOrUpdateValue($langFaId, 'base', 'admin_widgets_main_section_label', 'محتوا');
			
			$languageService->addOrUpdateValue($langFaId, 'base', 'admin_widgets_hidden_section_label', 'پنهان');
			
			$languageService->addOrUpdateValue($langFaId, 'base', 'widgets_admin_index_heading', 'صفحه اصلی موبایل');
			
			$languageService->addOrUpdateValue($langFaId, 'base', 'widgets_admin_dashboard_heading', 'داشبورد کاربر');
			
			$languageService->addOrUpdateValue($langFaId, 'base', 'admin_widgets_main_section_label', 'محتوا');
			
			$languageService->addOrUpdateValue($langFaId, 'admin', 'input_settings_user_rich_media_disable_desc', 'اگر نمی‌خواهید به کاربران خود اجازه افزودن رسانه (مانند تصویر و ویدیو) در نوشته (مانند بلاگ و انجمن) را بدهید، این گزینه را فعال کنید.');
			
			$languageService->addOrUpdateValue($langFaId, 'admin', 'join_photo_upload_set_required', 'ضروری کردن گزینه بارگذاری تصویر');
			
			$languageService->addOrUpdateValue($langFaId, 'base', 'tf_img_gal', 'آلبوم تصویر');
			
			$languageService->addOrUpdateValue($langFaId, 'base', 'auth_action_add_comment', 'اجازه ارسال نوشته بر روی نمایه');
			
			$languageService->addOrUpdateValue($langFaId, 'base', 'email_verify_email_verify_fail', 'استفاده از این پیوند فعال‌سازی به دو دلیل زیر مجاز نیست: <br/>1-  این پیوند قبلا استفاده شده است یا نامعتبر است. <br/> 2-  به دلیل عدم استفاده از این پیوند در طول 5 روز، این پیوند نامعتبر شده است. <br/> به منظور دریافت پیوند فعال‌سازی، از طریق حساب کاربری وارد شوید.');
			
			$languageService->addOrUpdateValue($langFaId, 'base', 'avatar_user_list_select_count_label', '#count# کاربر انتخاب شده');
			
        $languageService->addOrUpdateValue($langFaId, 'mobile', 'mobile_pages_dashboard', 'داشبورد');
        $languageService->addOrUpdateValue($langFaId, 'base', 'upload_bad_request_error', 'فایل بارگذاری شده مجاز نیست. (ممکن است نام فایل شامل کاراکترهای غیرمجاز شناخته شده برای میزبان باشد.)');
		$languageService->addOrUpdateValue($langFaId, 'mobile', 'page_is_not_available', 'صفحه مورد نظر در نسخه موبایل قابل مشاهده نیست، لطفا نسخه رومیزی وب‌گاه را مشاهده کنید.');
		$languageService->addOrUpdateValue($langFaId, 'base', 'mobile_version_menu_item', 'نسخه تلفن همراه');
    }

    public static function updatePluginsOfFaLanguageValues($langFaId, $languageService, $activePlugins)
    {
        //iisblockingip plugin
        if (IISSecurityProvider::existPluginKeyInActivePlugins($activePlugins, 'iisblockingip')) {
            $languageService->addOrUpdateValue($langFaId, 'iisblockingip', 'admin_page_heading', 'تنظیمات افزونه مسدودکردن کاربران ناهنجار');
            $languageService->addOrUpdateValue($langFaId, 'iisblockingip', 'admin_page_title', 'تنظیمات افزونه مسدودکردن کاربران ناهنجار');
        }
        //iiscontrolkids plugin
        if (IISSecurityProvider::existPluginKeyInActivePlugins($activePlugins, 'iiscontrolkids')) {
            $languageService->addOrUpdateValue($langFaId, 'iiscontrolkids', 'admin_page_heading', 'تنظیمات افزونه پایش فرزندان');
            $languageService->addOrUpdateValue($langFaId, 'iiscontrolkids', 'admin_page_title', 'تنظیمات افزونه پایش فرزندان');
        }
        //iismutual plugin
        if (IISSecurityProvider::existPluginKeyInActivePlugins($activePlugins, 'iismutual')) {
            $languageService->addOrUpdateValue($langFaId, 'iismutual', 'admin_page_heading', 'تنظیمات افزونه دوستان مشترک');
            $languageService->addOrUpdateValue($langFaId, 'iismutual', 'admin_page_title', 'تنظیمات افزونه دوستان مشترک');
        }
        //iisimport plugin
        if (IISSecurityProvider::existPluginKeyInActivePlugins($activePlugins, 'iisimport ')) {
            $languageService->addOrUpdateValue($langFaId, 'iisimport ', 'admin_page_heading', 'تنظیمات افزونه پیدا کردن دوستان از حسابهای دیگر');
            $languageService->addOrUpdateValue($langFaId, 'iisimport ', 'admin_page_title', 'تنظیمات افزونه پیدا کردن دوستان از حسابهای دیگر');
        }
        //friends plugin
        if (IISSecurityProvider::existPluginKeyInActivePlugins($activePlugins, 'friends')) {
            $languageService->addOrUpdateValue($langFaId, 'friends', 'got_requests_tab', '{$count} درخواست دوستی');
			
			$languageService->addOrUpdateValue($langFaId, 'friends', 'newsfeed_action_string', 'و <a href="{$user_url}">{$name}</a> هم اکنون دوست هستند!');
			
			$languageService->addOrUpdateValue($langFaId, 'friends', 'user_widget_settings_count', 'تعداد');
			
			$languageService->addOrUpdateValue($langFaId, 'friends', 'remove_from_friends', 'حذف از دوستان');
			
			$languageService->addOrUpdateValue($langFaId, 'friends', 'remove_from_friendship_button', 'حذف از دوستان');
			
			$languageService->addOrUpdateValue($langFaId, 'friends', 'confirmed_mail_html', '<p>{$sender} گرامی</p><p>خیلی خوشحال هستیم که به شما خبر میدهیم هم اکنون شما و <a href="{$receiver_url}">{$receiver}</a> در <a href="{$site_url}">{$site_name}</a> دوست هستید</p>');
			
			$languageService->addOrUpdateValue($langFaId, 'friends', 'notify_request', '{$sender} می خواهد با شما دوست شود');
			
			$languageService->addOrUpdateValue($langFaId, 'friends', 'feedback_cancelled', 'کاربر مورد نظر از فهرست دوستان شما حذف شد');
			
        }

        //event plugin
        if (IISSecurityProvider::existPluginKeyInActivePlugins($activePlugins, 'event')) {
            $languageService->addOrUpdateValue($langFaId, 'event', 'view_page_date_label', 'تاریخ و زمان شروع');
		
			$languageService->addOrUpdateValue($langFaId, 'event', 'add_form_end_date_label', 'تاریخ و زمان اتمام');
			
			$languageService->addOrUpdateValue($langFaId, 'event', 'no_events_label', 'موردی وجود ندارد');
			
			//issue 966
			
			$languageService->addOrUpdateValue($langFaId, 'event', 'add_form_success_message', 'رویداد اضافه شد.');
			
			$languageService->addOrUpdateValue($langFaId, 'event', 'add_form_who_can_invite_option_creator', 'فقط سازنده رویداد');
			
			$languageService->addOrUpdateValue($langFaId, 'event', 'add_new_button_label', 'افزودن رویداد جدید');
			
			$languageService->addOrUpdateValue($langFaId, 'event', 'add_new_link_label', 'رویداد');
			
			$languageService->addOrUpdateValue($langFaId, 'event', 'add_page_heading', 'ساخت رویداد جدید');
			
			$languageService->addOrUpdateValue($langFaId, 'event', 'add_page_title', 'ساخت رویداد جدید - {$site_name}');
			
			$languageService->addOrUpdateValue($langFaId, 'event', 'auth_action_label_add_comment', 'اظهار نظر رویدادها');
			
			$languageService->addOrUpdateValue($langFaId, 'event', 'auth_action_label_add_event', 'افزودن رویدادها');
			
			$languageService->addOrUpdateValue($langFaId, 'event', 'auth_action_label_view_event', 'مشاهده رویدادها');
			
			$languageService->addOrUpdateValue($langFaId, 'event', 'auth_group_label', 'رویدادها');
			
			$languageService->addOrUpdateValue($langFaId, 'event', 'cmp_widget_events_count', 'تعداد رویدادها');
			
			$languageService->addOrUpdateValue($langFaId, 'event', 'common_list_type_joined_label', 'رویدادهای من');
			
			$languageService->addOrUpdateValue($langFaId, 'event', 'console_notification_label', 'رویداد دعوت‌نامه‌ها');
			
			$languageService->addOrUpdateValue($langFaId, 'event', 'content_events_label', 'رویدادها');
			
			$languageService->addOrUpdateValue($langFaId, 'event', 'content_event_label', 'رویداد');
			
			$languageService->addOrUpdateValue($langFaId, 'event', 'delete_confirm_message', 'آیا از حذف این رویداد اطمینان دارید؟');
			
			$languageService->addOrUpdateValue($langFaId, 'event', 'delete_success_message', 'رویداد حذف شد');
			
			$languageService->addOrUpdateValue($langFaId, 'event', 'edit_form_end_date_error_message', 'لطفا یک رویداد پایانی تاریخ/زمان را انتخاب کنید.');
			
			$languageService->addOrUpdateValue($langFaId, 'event', 'edit_form_success_message', 'رویداد ویرایش شد');
			
			$languageService->addOrUpdateValue($langFaId, 'event', 'edit_page_heading', 'ویرایش رویداد');
			
			$languageService->addOrUpdateValue($langFaId, 'event', 'edit_page_title', 'ویرایش رویداد – {$site_name}');
			
			$languageService->addOrUpdateValue($langFaId, 'event', 'email_notification_comment', '<a href="{$userUrl}">{$userName}</a> نظرخود را در رویداد <a href="{$url}">{$title}</a> بیان کرد.');
			
			$languageService->addOrUpdateValue($langFaId, 'event', 'email_notification_comment_setting', 'یک نفر بر دیوارهای رویداد پست ارسال کرد');
			
			$languageService->addOrUpdateValue($langFaId, 'event', 'email_notification_invite', '<a href="{$inviterUrl}">{$inviterName}</a> شما را دعوت کرده به رویداد "<a href="{$eventUrl}">{$eventName}</a>"');
			
			$languageService->addOrUpdateValue($langFaId, 'event', 'event_created_by_me_page_heading', 'رویدادهایی که ایجاد کرده‌ام');
			
			$languageService->addOrUpdateValue($langFaId, 'event', 'event_created_by_me_page_title', 'رویدادهایی که ایجاد کرده‌ام - {$site_name}');
			
			$languageService->addOrUpdateValue($langFaId, 'event', 'event_create_string', 'ایجاد رویداد جدید');
						
			$languageService->addOrUpdateValue($langFaId, 'event', 'event_edited_string', 'ویرایش رویداد');
			
			$languageService->addOrUpdateValue($langFaId, 'event', 'event_joined_by_me_page_heading', 'رویدادهایی که شرکت می کنم');
			
			$languageService->addOrUpdateValue($langFaId, 'event', 'event_joined_by_me_page_title', 'رویدادهایی که شرکت می‌کنم - {$site_name}');
			
			$languageService->addOrUpdateValue($langFaId, 'event', 'feed_actiovity_attend_string', 'در رویداد {$user} شرکت می‌کند');
			
			$languageService->addOrUpdateValue($langFaId, 'event', 'feed_activity_comment_string', 'بر رویداد {$user} نظر داد.');
			
			$languageService->addOrUpdateValue($langFaId, 'event', 'feed_activity_event_string_like', 'رویداد {$user} را می‌پسندد.');
			
			$languageService->addOrUpdateValue($langFaId, 'event', 'feed_activity_event_string_like_own', 'رویداد آنها را می‌پسندد.');
			
			$languageService->addOrUpdateValue($langFaId, 'event', 'feed_activity_own_comment_string', 'بر رویداد اظهارنظر کرد');
			
			$languageService->addOrUpdateValue($langFaId, 'event', 'feed_add_item_label', 'رویداد جدید ایجاد کرد');
			
			$languageService->addOrUpdateValue($langFaId, 'event', 'feed_content_label', 'رویدادها');
			
			$languageService->addOrUpdateValue($langFaId, 'event', 'feed_user_join_string', 'در رویداد شرکت می‌کند');
			
			$languageService->addOrUpdateValue($langFaId, 'event', 'invitation_join_string_1', '{$user1} شما را به رویداد {$event} دعوت می‌کند.');
			
			$languageService->addOrUpdateValue($langFaId, 'event', 'invitation_join_string_2', '{$user1} و {$user2} شما را به رویداد {$event} دعوت می‌کند.');
			
			$languageService->addOrUpdateValue($langFaId, 'event', 'invitation_join_string_many', '{$user1} و {$user2} و {$otherUsers} شما را به رویداد {$event} دعوت می‌کند.');
			
			$languageService->addOrUpdateValue($langFaId, 'event', 'invited_events_page_heading', 'دعوت‌نامه‌های رویداد');
			
			$languageService->addOrUpdateValue($langFaId, 'event', 'invited_events_page_title', 'دعوت‌نامه‌های رویداد - {$site_name}');
			
			$languageService->addOrUpdateValue($langFaId, 'event', 'latest_events_page_desc', 'لیست رویدادهای پیش‌رو برای اعضا {$site_name}.');
			
			$languageService->addOrUpdateValue($langFaId, 'event', 'latest_events_page_heading', 'رویداد‌ها');
			
			$languageService->addOrUpdateValue($langFaId, 'event', 'latest_events_page_title', 'رویدادها - {$site_name}');
			
			$languageService->addOrUpdateValue($langFaId, 'event', 'main_menu_item', 'رویدادها');
			
			$languageService->addOrUpdateValue($langFaId, 'event', 'my_events_widget_block_cap_label', 'رویدادهای من');
			
			$languageService->addOrUpdateValue($langFaId, 'event', 'notifications_new_message', 'شخصی مرا به یک رویداد دعوت می‌کند');
			
			$languageService->addOrUpdateValue($langFaId, 'event', 'notifications_section_label', 'رویدادها');
			
			$languageService->addOrUpdateValue($langFaId, 'event', 'no_index_events_label', 'رویدادی نیست, <a href="{$url}">افزودن جدید</a>');
			
			$languageService->addOrUpdateValue($langFaId, 'event', 'past_events_page_desc', 'لیست رویدادهای سابق برای اعضای {$site_name}.');
			
			$languageService->addOrUpdateValue($langFaId, 'event', 'past_events_page_heading', 'رویدادهای سابق');
			
			$languageService->addOrUpdateValue($langFaId, 'event', 'past_events_page_title', 'رویدادهای سابق - {$site_name}');
			
			$languageService->addOrUpdateValue($langFaId, 'event', 'privacy_action_view_attend_events', 'مشاهده رویدادهای من');
			
			$languageService->addOrUpdateValue($langFaId, 'event', 'private_event_text', 'پوزش می‌خواهیم. این رویداد خصوصی است.');
			
			$languageService->addOrUpdateValue($langFaId, 'event', 'private_page_heading', 'رویداد خصوصی');
			
			$languageService->addOrUpdateValue($langFaId, 'event', 'private_page_title', 'رویداد خصوصی');
			
			$languageService->addOrUpdateValue($langFaId, 'event', 'profile_events_widget_block_cap_label', 'رویدادهای من');
			
			$languageService->addOrUpdateValue($langFaId, 'event', 'up_events_widget_block_cap_label', 'رویدادهای پیش‌رو');
			
			$languageService->addOrUpdateValue($langFaId, 'event', 'usercredits_action_add_comment', 'نظر دادن رویداد');
			
			$languageService->addOrUpdateValue($langFaId, 'event', 'usercredits_action_add_event', 'افزودن رویداد');
			
			$languageService->addOrUpdateValue($langFaId, 'event', 'user_list_page_desc_1', 'لیست کاربران شرکت کننده در رویداد «{$eventTitle}».');
			
			$languageService->addOrUpdateValue($langFaId, 'event', 'user_list_page_desc_2', 'لیست کاربران شرکت کننده احتمالی در رویداد «{$eventTitle}».');
			
			$languageService->addOrUpdateValue($langFaId, 'event', 'user_list_page_desc_3', 'لیست کاربرانی که در رویداد «{$eventTitle}» شرکت نمی‌کنند.');
			
			$languageService->addOrUpdateValue($langFaId, 'event', 'user_list_page_heading_1', '«{$eventTitle}» شرکت کنندگان رویداد - {$site_name}');
			
			$languageService->addOrUpdateValue($langFaId, 'event', 'user_list_page_heading_2', 'رویداد «{$eventTitle}» – افرادای که ممکن است شرکت کنند - {$site_name}');
			
			$languageService->addOrUpdateValue($langFaId, 'event', 'user_list_page_heading_3', 'رویداد «{$eventTitle}» – افرادی که شرکت نمی‌کنند - {$site_name}');
			
			$languageService->addOrUpdateValue($langFaId, 'event', 'user_participated_events_page_desc', 'لیست رویدادهایی که {$display_name} در آن شرکت می‌کند.');
			
			$languageService->addOrUpdateValue($langFaId, 'event', 'user_participated_events_page_heading', 'رویدادهای {$display_name}');
			
			$languageService->addOrUpdateValue($langFaId, 'event', 'user_participated_events_page_title', 'رویدادهای {$display_name} - {$site_name}');
			
			$languageService->addOrUpdateValue($langFaId, 'event', 'user_status_author_cant_leave_error', 'با عرض پوزش، شما نمی‌توانید رویداد خود را ترک کنید');
			
			$languageService->addOrUpdateValue($langFaId, 'event', 'view_page_end_date_label', 'رویداد پایان می‌یابد');
		
			$languageService->addOrUpdateValue($langFaId, 'event', 'add_form_date_label', 'تاریخ و زمان شروع');
			
			$languageService->addOrUpdateValue($langFaId, 'event', 'feed_activity_event_string_like_own', 'رویداد را می‌پسندد.');
			
			// end of issue 966

            $languageService->addOrUpdateValue($langFaId, 'event', 'event_sitemap', 'رویدادها');
            $languageService->addOrUpdateValue($langFaId, 'event', 'seo_meta_section', 'رویدادها');
            $languageService->addOrUpdateValue($langFaId, 'event', 'seo_meta_events_list_label', 'صفحه فهرست رویدادها');
            $languageService->addOrUpdateValue($langFaId, 'event', 'meta_title_events_list', '{$event_list} رویدادها | {$site_name}');
            $languageService->addOrUpdateValue($langFaId, 'event', 'meta_desc_events_list', 'تمامی رویدادهای {$event_list} را در {$site_name} مشاهده کرده، نظرات خود را درج کرده و با دیگر کاربران گفت‌وگو کنید.');
            $languageService->addOrUpdateValue($langFaId, 'event', 'meta_keywords_events_list', '');
            $languageService->addOrUpdateValue($langFaId, 'event', 'created_events_page_title', 'ایجاد شده');
            $languageService->addOrUpdateValue($langFaId, 'event', 'joined_events_page_title', 'اضافه شده');
            $languageService->addOrUpdateValue($langFaId, 'event', 'seo_meta_event_view_label', 'صفحه رویداد جداگانه');
            $languageService->addOrUpdateValue($langFaId, 'event', 'meta_title_event_view', '{$event_title} | {$site_name}');
            $languageService->addOrUpdateValue($langFaId, 'event', 'meta_desc_event_view', '{$event_description}');
            $languageService->addOrUpdateValue($langFaId, 'event', 'meta_keywords_event_view', '');
            $languageService->addOrUpdateValue($langFaId, 'event', 'seo_meta_event_users_label', 'صفحه شرکت‌کنندگان رویداد');
            $languageService->addOrUpdateValue($langFaId, 'event', 'meta_title_event_users', 'همه کاربران "{$event_title}" | {$site_name}');
            $languageService->addOrUpdateValue($langFaId, 'event', 'meta_desc_event_users', 'فهرست تمامی کاربران {$site_name}، که در رویداد "{$event_title}" مشارکت می‌کنند.');
            $languageService->addOrUpdateValue($langFaId, 'event', 'meta_keywords_event_users', '');
			$languageService->addOrUpdateValue($langFaId, 'event', 'event_mobile', 'رویداد');
			$languageService->addOrUpdateValue($langFaId, 'event', 'back‌', 'بازگشت');
        }

        //video plugin
        if (IISSecurityProvider::existPluginKeyInActivePlugins($activePlugins, 'video')) {
            $languageService->addOrUpdateValue($langFaId, 'video', 'video_add_tip', 'ما اجازه داریم تا <b> ویدیوهای موجود </b>  را فقط از دیگر سایت‌های به اشتراک گذاری فیلم دیگر به اشتراک بگذاریم.');

            $languageService->addOrUpdateValue($langFaId, 'video', 'added', 'ایجاد شده در');
			
			$languageService->addOrUpdateValue($langFaId, 'video', 'confirm_delete', 'یا از حذف این ویدیو اطمینان دارید؟');
			
			$languageService->addOrUpdateValue($langFaId, 'video', 'remove_from_featured', 'حذف از حالت ویژه');
			
			$languageService->addOrUpdateValue($langFaId, 'video', 'tag_search', 'جستجوی برچسب');
			
			$languageService->addOrUpdateValue($langFaId, 'video', 'tag_search_result', 'نتایج جستجوی برچسب برای:');
			
			
			
			$languageService->addOrUpdateValue($langFaId, 'video', 'admin_config', 'تنظیمات افزونه ویدیو');
			
			$languageService->addOrUpdateValue($langFaId, 'video', 'admin_menu_general', 'عمومی');
			
			$languageService->addOrUpdateValue($langFaId, 'video', 'auth_action_label_delete_comment_by_content_owner', 'نویسنده مطلب می‌تواند نظرات را پاک کند.');
			
			$languageService->addOrUpdateValue($langFaId, 'video', 'auth_action_label_view', 'مشاهده ویدیو');
			
			$languageService->addOrUpdateValue($langFaId, 'video', 'auth_group_label', 'ویدیو');
			
			$languageService->addOrUpdateValue($langFaId, 'video', 'auth_view_permissions', 'شما مجاز به مشاهده ویدیو نیستید');
			
			$languageService->addOrUpdateValue($langFaId, 'video', 'clip_updated', 'ویدیو به‌روزرساني شد');
			
			$languageService->addOrUpdateValue($langFaId, 'video', 'cmp_widget_user_video_show_titles', 'نمايش عناوین ویدیو');
			
			$languageService->addOrUpdateValue($langFaId, 'video', 'description_desc', 'شرح کوتاه از این ویدیو');
			
			$languageService->addOrUpdateValue($langFaId, 'video', 'feed_activity_video_string', 'بر روی ویدیو {$user} نظر داد');
			
			$languageService->addOrUpdateValue($langFaId, 'video', 'flags', 'ویدیو');
			
			$languageService->addOrUpdateValue($langFaId, 'video', 'mark_featured', 'نشانه گذاری به عنوان ویژه');
			
			$languageService->addOrUpdateValue($langFaId, 'video', 'menu_featured', 'ویژه‌ها');
			
			$languageService->addOrUpdateValue($langFaId, 'video', 'menu_latest', 'آخرین‌های عمومی');
			
			$languageService->addOrUpdateValue($langFaId, 'video', 'menu_tagged', 'مرور به‌وسیله برچسب');
			
			$languageService->addOrUpdateValue($langFaId, 'video', 'menu_toprated', 'بیشترین امتیاز');
			
			$languageService->addOrUpdateValue($langFaId, 'video', 'meta_description_user_video', 'ویدیوهای ارسال شده به وسیله کاربر {$displayName}');
			
			$languageService->addOrUpdateValue($langFaId, 'video', 'meta_description_video_featured', 'بهترین ویدیوها در {$site_name} انتخاب کارکنان!');
			
			$languageService->addOrUpdateValue($langFaId, 'video', 'meta_description_video_latest', 'ویدیوهای ارسال شده اخیر در {$site_name}.');
			
			$languageService->addOrUpdateValue($langFaId, 'video', 'meta_description_video_tagged', 'فیلم‌ها توسط کلمات کلیدی : {$topTags}, و غیره.');
			
			$languageService->addOrUpdateValue($langFaId, 'video', 'meta_description_video_tagged_as', 'مرور فیلم‌های برچسب گذاشته شده به عنوان {$tag}.');
			
			$languageService->addOrUpdateValue($langFaId, 'video', 'meta_description_video_toprated', 'ویدیوهای ارسال شده توسط کاربران در{$site_name} با بالاترین امتیازدهی.');
			
			$languageService->addOrUpdateValue($langFaId, 'video', 'meta_description_video_view', 'ویدیو نام‌گذاری شده «{$title}»، منطبق با برچسب {$tags}.');
			
			$languageService->addOrUpdateValue($langFaId, 'video', 'my_video', 'ويدیو من');
			
			$languageService->addOrUpdateValue($langFaId, 'video', 'no_video_found', 'موردی موجود نیست');
            $languageService->addOrUpdateValue($langFaId, 'video', 'admin_page_heading', 'تنظیمات افزونه ویدیو');
            $languageService->addOrUpdateValue($langFaId, 'video', 'admin_page_title', 'تنظیمات افزونه ویدیو');
			$languageService->addOrUpdateValue($langFaId, 'video', 'video_add_tip', 'شما فقط مجاز به بارگذاری ویدیو از طریق وب‌گاه‌های اشتراک‌گذاری ویدیو هستید.');
			$languageService->addOrUpdateValue($langFaId, 'video', 'code_desc', 'از کد نمایش وب‌گاه‌های منبع فیلم مانند آپارات، یوتیوب، گوگل ویدیو و غیره استفاده کنید.');
			
			$languageService->addOrUpdateValue($langFaId, 'video', 'auth_action_label_add_comment', 'نظردادن برروی ویدیو');
			
			$languageService->addOrUpdateValue($langFaId, 'video', 'auth_add_permissions', 'شما به دلیل محدودیت‌های دسترسی، مجاز به افزودن ویدیو نیستید');
			
			$languageService->addOrUpdateValue($langFaId, 'video', 'clips_by', 'ویدیو توسط');
			
			$languageService->addOrUpdateValue($langFaId, 'video', 'clip_not_deleted', 'ویدیو حذف نشده');
			
			$languageService->addOrUpdateValue($langFaId, 'video', 'cmp_widget_video_count', 'تعداد ویدیوها برای نمایش');
			
			$languageService->addOrUpdateValue($langFaId, 'video', 'quota_desc', 'این عدد به یک اندازه منطقی بزرگ نگه‌داشته شود');

            $languageService->addOrUpdateValue($langFaId, 'video', 'video_sitemap', 'ویدیو');
            $languageService->addOrUpdateValue($langFaId, 'video', 'seo_meta_section', 'ویدیو');
            $languageService->addOrUpdateValue($langFaId, 'video', 'seo_meta_tagged_list_label', 'صفحه تمامی ویدیوها');
            $languageService->addOrUpdateValue($langFaId, 'video', 'meta_title_tagged_list', 'برچسب ویدیو | {$site_name}');
            $languageService->addOrUpdateValue($langFaId, 'video', 'meta_desc_tagged_list', 'مشاهده تمامی ویدیوها در {$site_name}');
            $languageService->addOrUpdateValue($langFaId, 'video', 'meta_keywords_tagged_list', '');
            $languageService->addOrUpdateValue($langFaId, 'video', 'seo_meta_view_list_label', 'صفحه ویدیوها بر اساس نوع (آخرین‌ها، بیشترین امتیازها، بیشترین بحث‌شده‌ها)');
            $languageService->addOrUpdateValue($langFaId, 'video', 'meta_title_view_list', 'ویدیوهای {$video_list} | {$site_name}');
            $languageService->addOrUpdateValue($langFaId, 'video', 'meta_desc_view_list', 'مشاهده تمامی ویدیوهای {$video_list} در {$site_name}.');
            $languageService->addOrUpdateValue($langFaId, 'video', 'meta_keywords_view_list', '');
            $languageService->addOrUpdateValue($langFaId, 'video', 'featured_list_label', 'ویژه');
            $languageService->addOrUpdateValue($langFaId, 'video', 'latest_list_label', 'آخرین‌ها');
            $languageService->addOrUpdateValue($langFaId, 'video', 'toprated_list_label', 'بیشترین امتیازدهی‌شده‌ها');
            $languageService->addOrUpdateValue($langFaId, 'video', 'tagged_list_label', 'برچسب زده شده');
            $languageService->addOrUpdateValue($langFaId, 'video', 'seo_meta_view_clip_label', 'مشاهده صفحه ویدیو');
            $languageService->addOrUpdateValue($langFaId, 'video', 'meta_title_view_clip', 'ویدیو "{$video_title}"  توسط {$user_name} | {$site_name}');
            $languageService->addOrUpdateValue($langFaId, 'video', 'meta_desc_view_clip', 'مشاهده ویدیو "{$video_title}"  در {$site_name} توسط {$user_name}');
            $languageService->addOrUpdateValue($langFaId, 'video', 'meta_keywords_view_clip', '');
            $languageService->addOrUpdateValue($langFaId, 'video', 'seo_meta_tag_list_label', 'صفحه ویدیوهای دارای برچسب');
            $languageService->addOrUpdateValue($langFaId, 'video', 'meta_title_tag_list', 'ویدیوهای دارای برچسب "{$video_tag_name}" | {$site_name}');
            $languageService->addOrUpdateValue($langFaId, 'video', 'meta_desc_tag_list', 'مشاهده تمامی ویدیوهای دارای برچسب "{$video_tag_name}" در {$site_name}');
            $languageService->addOrUpdateValue($langFaId, 'video', 'meta_keywords_tag_list', '');
            $languageService->addOrUpdateValue($langFaId, 'video', 'seo_meta_user_video_list_label', 'صفحه ویدیو فرد');
            $languageService->addOrUpdateValue($langFaId, 'video', 'meta_title_user_video_list', 'ویدیوهای ایجاد شده توسط {$user_name}، {$user_age} | {$site_name}');
            $languageService->addOrUpdateValue($langFaId, 'video', 'meta_desc_user_video_list', 'مشاهده ویدیوهای بارگذاری شده توسط {$user_name}، {$user_age} در {$site_name}');
            $languageService->addOrUpdateValue($langFaId, 'video', 'meta_keywords_user_video_list', '');
			$languageService->addOrUpdateValue($langFaId, 'video', 'latest_friends_list_label', 'دوستان');
			$languageService->addOrUpdateValue($langFaId, 'video', 'video_mobile', 'ویدیو');
			$languageService->addOrUpdateValue($langFaId, 'video', 'latest_myvideo_list_label', 'ویدیوهای من - {$site_name}');
			$languageService->addOrUpdateValue($langFaId, 'video', 'other_video', 'سایر ویدیوهای کاربر');
        }

        //notifications plugin
        if (IISSecurityProvider::existPluginKeyInActivePlugins($activePlugins, 'notifications')) {
            $languageService->addOrUpdateValue($langFaId, 'notifications', 'email_html_head', 'کاربر گرامی ({$userName})،');
			
			$languageService->addOrUpdateValue($langFaId, 'notifications', 'config_schedule_title', 'تنظیم زمان‌بندی ارسال رایانامه');
			
			$languageService->addOrUpdateValue($langFaId, 'notifications', 'email_txt_bottom', '');
			
			$languageService->addOrUpdateValue($langFaId, 'notifications', 'email_txt_head', 'کاربر گرامی {$userName} سلام

آخرین فعالیت‌های شما در {$site_name} که قصد اطلاع از وضعیت آن‌ها را دارید:');

			$languageService->addOrUpdateValue($langFaId, 'notifications', 'settings_not_changed', 'تنظیمات تغییر نکرد');
			
			$languageService->addOrUpdateValue($langFaId, 'notifications', 'preferences_legend', 'امکان انتخاب فعالیت‌هایی که در صورت عدم مراجعه به وب‌گاه در مدت زمان بیشینه 2 روز، به رایانشانی شما اطلاع رایانامه‌ای ارسال شود.');
        }

        //iissecurityessentials plugin
        if (IISSecurityProvider::existPluginKeyInActivePlugins($activePlugins, 'iissecurityessentials')) {
            $languageService->addOrUpdateValue($langFaId, 'iissecurityessentials', 'verify_using_code', 'فعال‌سازی حساب کاربری از طریق کد ارسال شده به شما');
            $languageService->addOrUpdateValue($langFaId, 'iissecurityessentials', 'admin_page_heading', 'تنظیمات مواردامنیتی ضروری');
            $languageService->addOrUpdateValue($langFaId, 'iissecurityessentials', 'admin_page_title', 'تنظیمات مواردامنیتی ضروری');
			$languageService->addOrUpdateValue($langFaId, 'iissecurityessentials', 'view_user_comment_widget', 'نمایش ابزارک نظر در نمایه (به دلیل مسائل حریم خصوصی توصیه می‌شود آن را فعال نکنید.)');

        }

        //coverphoto plugin
        if (IISSecurityProvider::existPluginKeyInActivePlugins($activePlugins, 'coverphoto')) {
            $languageService->addOrUpdateValue($langFaId, 'coverphoto', 'reposition_label', 'تغییر موقعیت سرصفحه');
			
			$languageService->addOrUpdateValue($langFaId, 'coverphoto', 'main_menu_item', 'تصویر سرصفحه');
			
			$languageService->addOrUpdateValue($langFaId, 'coverphoto', 'list_is_empty', 'شما تصویر سرصفحه‏ای ندارید.');
			
			$languageService->addOrUpdateValue($langFaId, 'coverphoto', 'edit_for_select_cover', 'ویرایش تصاویر سرصفحه');
			
			$languageService->addOrUpdateValue($langFaId, 'coverphoto', 'empty_image', 'شما باید یک تصویر سرصفحه بارگذاری نمایید.');
			
			$languageService->addOrUpdateValue($langFaId, 'coverphoto', 'not_valid_image', 'این تصویر پشتیبانی نمی‏شود');
			
			$languageService->addOrUpdateValue($langFaId, 'coverphoto', 'description_coverphoto_float_page', 'شما می‌توانید از تصاویر سرصفحه قدیمی خود به عنوان تصویر سرصفحه فعلی استفاده کنید و یا آن‏ها را حذف کنید.');
			
			$languageService->addOrUpdateValue($langFaId, 'coverphoto', 'description_coverphoto_page', 'شما می‌توانید تصویر سرصفحه خود را بارگذاری کرده و آخرین تصویر بارگذاری شده به عنوان تصویر سرصفحه انتخاب خواهد شد. اگر می‌خواهید تصویر سرصفحه خود را تغییر دهید، می‌توانید از تصاویر سرصفحه قدیمی خود استفاده کنید و یا یک تصویر جدید بارگذاری نمایید.');
			
			$languageService->addOrUpdateValue($langFaId, 'coverphoto', 'errors_image_invalid', 'این نوع تصویر پشتیبانی نمی‏شود');
			
			$languageService->addOrUpdateValue($langFaId, 'coverphoto', 'forms_page_heading', 'تصاویر سرصفحه');
			
			$languageService->addOrUpdateValue($langFaId, 'coverphoto', 'are_you_sure_to_remove', 'آیا شما از حذف این تصویر سرصفحه اطمینان دارید؟');
			
			$languageService->addOrUpdateValue($langFaId, 'coverphoto', 'are_you_sure_to_use_this', 'آیا شما از استفاده از این تصویر به عنوان تصویر سرصفحه اطمینان دارید؟');
			
			$languageService->addOrUpdateValue($langFaId, 'coverphoto', 'covers', 'لیست تصاویر سرصفحه');
			
			$languageService->addOrUpdateValue($langFaId, 'coverphoto', 'forms_title_field_description', 'شما باید عنوان تصویر سرصفحه را وارد نمایید.');
			
			$languageService->addOrUpdateValue($langFaId, 'coverphoto', 'forms_title_field_label', 'عنوان تصویر سرصفحه');
			
			$languageService->addOrUpdateValue($langFaId, 'coverphoto', 'index_page_heading', 'تصویر سرصفحه');
			
			$languageService->addOrUpdateValue($langFaId, 'coverphoto', 'index_page_title', 'تصویر سرصفحه');
			
			$languageService->addOrUpdateValue($langFaId, 'coverphoto', 'database_record_saved_info', 'تصویر سرصفحه با موفقیت ذخیره شد.');
			
			$languageService->addOrUpdateValue($langFaId, 'coverphoto', 'database_record_used', 'تصویر سرصفحه با موفقیت تغییر کرد.');
			
			$languageService->addOrUpdateValue($langFaId, 'coverphoto', 'database_record_deleted', 'تصویر سرصفحه با موفقیت حذف شد.');
			
			$languageService->addOrUpdateValue($langFaId, 'coverphoto', 'upload_image', 'بارگذاری تصویر');
			
        }

        //blogs plugin
        if (IISSecurityProvider::existPluginKeyInActivePlugins($activePlugins, 'blogs')) {
            $languageService->addOrUpdateValue($langFaId, 'blogs', 'added', 'ایجاد شده در');
			
			$languageService->addOrUpdateValue($langFaId, 'blogs', 'latest_title', 'بلاگ‌های کاربر - {$site_name}');
			
			$languageService->addOrUpdateValue($langFaId, 'blogs', 'list_page_heading', 'بلاگ‌های کاربر');
			
			$languageService->addOrUpdateValue($langFaId, 'blogs', 'view_page_heading', '{$postTitle} <span class="ow_small">از<a href="{$url}"> وبلاگ {$name}</a></span>');
			
			$languageService->addOrUpdateValue($langFaId, 'blogs', 'activity_string', '<a href="{$userUrl}">{$user}</a> نوشته جدید بلاگ ارسال کرد <a href="{$url}">{$title}</a>');
			
			$languageService->addOrUpdateValue($langFaId, 'blogs', 'authorization_failed_view_blog', 'با عرض پوزش، شما مجاز به مشاهده اين بلاگ نيستيد .');
			
			$languageService->addOrUpdateValue($langFaId, 'blogs', 'auth_action_label_add', 'افزودن نوشته‌های بلاگ');
			
			$languageService->addOrUpdateValue($langFaId, 'blogs', 'auth_action_label_add_comment', 'نظرات بلاگ');
			
			$languageService->addOrUpdateValue($langFaId, 'blogs', 'auth_action_label_view', 'مشاهده نوشته‌های بلاگ');
			
			$languageService->addOrUpdateValue($langFaId, 'blogs', 'auth_group_label', 'بلاگ‌ها');
			
			$languageService->addOrUpdateValue($langFaId, 'blogs', 'blog', 'بلاگ');
			
			$languageService->addOrUpdateValue($langFaId, 'blogs', 'blog_index', 'خانه بلاگ');
			
			$languageService->addOrUpdateValue($langFaId, 'blogs', 'browse_by_tag_description', 'مرور نوشته‌های بلاگ‌ها بر اساس بر چسب‌ها : {$tags} و دیگران.');
			
			$languageService->addOrUpdateValue($langFaId, 'blogs', 'browse_by_tag_item_description', 'مرور برچسب‌هاي نوشته‌هاي بلاگ به عنوان {$tag}.');
			
			$languageService->addOrUpdateValue($langFaId, 'blogs', 'browse_by_tag_item_title', '{$tag}نوشته‌هاي مرتبط بلاگ {$site_name}');
			
			$languageService->addOrUpdateValue($langFaId, 'blogs', 'browse_by_tag_title', 'مرور نوشته‌های بلاگ به وسيله برچسب‌ها {$site_name}');
			
			$languageService->addOrUpdateValue($langFaId, 'blogs', 'feed_add_item_label', 'ایجاد یک نوشته جدید در بلاگ');
			
			$languageService->addOrUpdateValue($langFaId, 'blogs', 'feed_edit_item_label', 'ویرایش نوشته بلاگ خود');
			
			$languageService->addOrUpdateValue($langFaId, 'blogs', 'go_to_blog', 'برو به بلاگ‌ها');
			
			$languageService->addOrUpdateValue($langFaId, 'blogs', 'meta_title_new_blog_post', 'نوشته جدید بلاگ - {$site_name}');
			
			$languageService->addOrUpdateValue($langFaId, 'blogs', 'most_discussed_description', 'بيشترين نوشته‌هاي بحث شده بلاگ کاربر در {$site_name}.');
			
			$languageService->addOrUpdateValue($langFaId, 'blogs', 'most_discussed_title', 'بيشترین بلاگ‌هاي بحث شده  - {$site_name}');
			
			$languageService->addOrUpdateValue($langFaId, 'blogs', 'new_post', 'نوشته جدید بلاگ');
			
			$languageService->addOrUpdateValue($langFaId, 'blogs', 'save_page_heading', 'ایجاد نوشته جدید بلاگ');
			
			$languageService->addOrUpdateValue($langFaId, 'blogs', 'top_rated_title', 'برترین بلاگ‌ها - {$site_name}');
			
			$languageService->addOrUpdateValue($langFaId, 'blogs', 'user_blog_page_heading', '{$name} بلاگ');
			
			$languageService->addOrUpdateValue($langFaId, 'blogs', 'user_blog_title', '{$display_name} بلاگ - {$site_name}');
			
			$languageService->addOrUpdateValue($langFaId, 'blogs', 'view_page_heading', '{$postTitle} <span class="ow_small">از<a href="{$url}"> بلاگ {$name}</a></span>');
			
			
			
			
			$languageService->addOrUpdateValue($langFaId, 'blogs', 'admin_blogs_settings_heading', 'تنظیمات افزونه بلاگ‌ها');
			
			$languageService->addOrUpdateValue($langFaId, 'blogs', 'admin_settings_results_per_page', 'نوشته‌های این صفحه');
			
			$languageService->addOrUpdateValue($langFaId, 'blogs', 'authorization_failed_view_blog', 'با عرض پوزش، شما مجاز به مشاهده این بلاگ نیستید .');
			
			$languageService->addOrUpdateValue($langFaId, 'blogs', 'auth_action_label_delete_comment_by_content_owner', 'صاحب مطلب می تواند نظرات را پاک کند');
			
			$languageService->addOrUpdateValue($langFaId, 'blogs', 'blog_archive_lbl_archives', 'بایگانی');
			
			$languageService->addOrUpdateValue($langFaId, 'blogs', 'blog_post_title', '{$post_title} نوشته شده به وسیله : {$display_name} در {$site_name}');
			
			$languageService->addOrUpdateValue($langFaId, 'blogs', 'blog_widget_preview_length_lbl', 'طول پیش نمایش');
			
			$languageService->addOrUpdateValue($langFaId, 'blogs', 'browse_by_tag_item_description', 'مرور برچسب‌های نوشته‌های بلاگ به عنوان {$tag}.');
			
			$languageService->addOrUpdateValue($langFaId, 'blogs', 'browse_by_tag_item_title', '{$tag}نوشته‌های مرتبط بلاگ {$site_name}');
			
			$languageService->addOrUpdateValue($langFaId, 'blogs', 'browse_by_tag_title', 'مرور نوشته‌های بلاگ به وسیله برچسب‌ها {$site_name}');
			
			$languageService->addOrUpdateValue($langFaId, 'blogs', 'by', 'به وسیله');
			
			$languageService->addOrUpdateValue($langFaId, 'blogs', 'cmp_widget_post_count', 'تعداد نوشته‌ها برای نمایش');
			
			$languageService->addOrUpdateValue($langFaId, 'blogs', 'latest_post', 'آخرین نوشته‌ها');
			
			$languageService->addOrUpdateValue($langFaId, 'blogs', 'manage_page_last_updated', 'آخرین به‌روزرسانی');
			
			$languageService->addOrUpdateValue($langFaId, 'blogs', 'manage_page_menu_drafts', 'پیش نویس ها');
			
			$languageService->addOrUpdateValue($langFaId, 'blogs', 'manage_page_menu_published', 'پست‌های منتشر شده');
			
			$languageService->addOrUpdateValue($langFaId, 'blogs', 'manage_page_status', 'وضعیت');
			
			$languageService->addOrUpdateValue($langFaId, 'blogs', 'menuItemMostDiscussed', 'بیشترین بحث شده‌ها');
			
			$languageService->addOrUpdateValue($langFaId, 'blogs', 'more', 'بیشتر');
			
			$languageService->addOrUpdateValue($langFaId, 'blogs', 'most_discussed_description', 'بیشترین نوشته‌های بحث شده بلاگ کاربر در {$site_name}.');
			
			$languageService->addOrUpdateValue($langFaId, 'blogs', 'most_discussed_title', 'بیشترین بلاگ‌های بحث شده  - {$site_name}');
			
			$languageService->addOrUpdateValue($langFaId, 'blogs', 'next_post', 'نوشته بعدی');
			
			$languageService->addOrUpdateValue($langFaId, 'blogs', 'on', 'روی');
			
			$languageService->addOrUpdateValue($langFaId, 'blogs', 'sava_draft', 'ذخیره به عنوان پیش نویس');
			
			$languageService->addOrUpdateValue($langFaId, 'blogs', 'save_btn_label', 'ذخیره');
			
			$languageService->addOrUpdateValue($langFaId, 'blogs', 'settings', 'تنظیمات');
			
			$languageService->addOrUpdateValue($langFaId, 'blogs', 'comments', 'تعداد نظرات:');
			
			$languageService->addOrUpdateValue($langFaId, 'blogs', 'browse_by_tag_item_title', 'نوشته‌های مرتبط بلاگ با برچسب «{$tag}» - {$site_name}');

            $languageService->addOrUpdateValue($langFaId, 'blogs', 'blogs_sitemap', 'بلاگ‌ها');
            $languageService->addOrUpdateValue($langFaId, 'blogs', 'blogs_sitemap_desc', 'بلاگ‌ها و فهرست‌ها');
            $languageService->addOrUpdateValue($langFaId, 'blogs', 'seo_meta_section', 'بلاگ‌ها');
            $languageService->addOrUpdateValue($langFaId, 'blogs', 'seo_meta_blogs_list_label', 'صفحه فهرست بلاگ‌ها');
            $languageService->addOrUpdateValue($langFaId, 'blogs', 'meta_title_blogs_list', '{$blog_list} بلاگ‌ها | {$site_name}');
            $languageService->addOrUpdateValue($langFaId, 'blogs', 'meta_desc_blogs_list', 'تمامی نوشته‌های بلاگ {$blog_list} را در {$site_name} بخوانید، نظرات خود را وارد کرده و در مورد موضوعات، با دیگر کاربران گفت‌وگو کنید.');
            $languageService->addOrUpdateValue($langFaId, 'blogs', 'meta_keywords_blogs_list', '');
            $languageService->addOrUpdateValue($langFaId, 'blogs', 'seo_meta_user_blog_label', 'صفحه بلاگ فرد');
            $languageService->addOrUpdateValue($langFaId, 'blogs', 'meta_title_user_blog', 'بلاگ نوشته شده توسط {$user_name}, {$user_age} | {$site_name}');
            $languageService->addOrUpdateValue($langFaId, 'blogs', 'meta_desc_user_blog', 'تمامی نوشته‌های کاربر {$user_name} را در {$site_name} بخوانید و نظرات خود را وارد کنید.');
            $languageService->addOrUpdateValue($langFaId, 'blogs', 'meta_keywords_user_blog', '');
            $languageService->addOrUpdateValue($langFaId, 'blogs', 'seo_meta_blog_post_label', 'صفحه نوشته‌های بلاگ فرد');
            $languageService->addOrUpdateValue($langFaId, 'blogs', 'meta_title_blog_post', 'بلاگ {$post_subject} | {$site_name}');
            $languageService->addOrUpdateValue($langFaId, 'blogs', 'meta_desc_blog_post', '{$post_body}');
            $languageService->addOrUpdateValue($langFaId, 'blogs', 'meta_keywords_blog_post', '');
			$languageService->addOrUpdateValue($langFaId, 'blogs', 'comment_notification_string', '<a href="{$actorUrl}">{$actor}</a> بر روی نوشته شما نظر گذاشته: <a href="{$url}">"{$title}"</a>');
        }
		
		//iisterms plugin
        if (IISSecurityProvider::existPluginKeyInActivePlugins($activePlugins, 'iisterms')) {
			$languageService->addOrUpdateValue($langFaId, 'iisterms', 'notification_content', 'یک نسخه جدید از {$value1} منتشر شد. {$value2} بند مهم افزوده، ویرایش یا حذف شدند.');
			$languageService->addOrUpdateValue($langFaId, 'iisterms', 'send_notification_description', 'ارسال پیام جهت اطلاع از تغییرات در شرایط استفاده از خدمات و سیاست حریم خصوصی');
			$languageService->addOrUpdateValue($langFaId, 'iisterms', 'delete_item', 'حذف');
			$languageService->addOrUpdateValue($langFaId, 'iisterms', 'delete_section', 'حذف');
			$languageService->addOrUpdateValue($langFaId, 'iisterms', 'delete_item_warning', 'آیا از حذف این آیتم اطمینان دارید؟');
			$languageService->addOrUpdateValue($langFaId, 'iisterms', 'delete_section_warning', 'آیا از حذف این نسخه اطمینان دارید؟');
            $languageService->addOrUpdateValue($langFaId, 'iisterms', 'admin_page_heading', 'تنظیمات افزونه شرایط');
            $languageService->addOrUpdateValue($langFaId, 'iisterms', 'admin_page_title', 'تنظیمات افزونه شرایط');			
			$languageService->addOrUpdateValue($langFaId, 'iisterms', 'mobile_notification_content', '<a href="{$url}">یک نسخه جدید از {$value1} منتشر شد. {$value2} بند مهم افزوده، ویرایش یا حذف شدند.</a>');
			$languageService->addOrUpdateValue($langFaId, 'iisterms', 'mobile_bottom_menu_item', 'شرایط');	
        }
		
				//iispasswordstrengthmeter plugin
        if (IISSecurityProvider::existPluginKeyInActivePlugins($activePlugins, 'iispasswordstrengthmeter')) {
		
			$languageService->addOrUpdateValue($langFaId, 'iispasswordstrengthmeter', 'strength_password_validate_error', 'گذرواژه وارد شده، حداقل استحکام مورد نیاز را ندارد. حداقل استحکام قابل قبول، سطح {$value} است.');
			
			$languageService->addOrUpdateValue($langFaId, 'iispasswordstrengthmeter', 'minimum_requirement_password_strength_label', 'انتخاب حداقل معیار قبولی برای گذرواژه ');

            $languageService->addOrUpdateValue($langFaId, 'iispasswordstrengthmeter', 'admin_page_heading', 'تنظیمات افزونه نمایشگر میزان قدرت گذرواژه');
            $languageService->addOrUpdateValue($langFaId, 'iispasswordstrengthmeter', 'admin_page_title', 'تنظیمات افزونه نمایشگر میزان قدرت گذرواژه');

        }
		
		//groups plugin
        if (IISSecurityProvider::existPluginKeyInActivePlugins($activePlugins, 'groups')) {
			$languageService->addOrUpdateValue($langFaId, 'groups', 'email_notification_comment_setting', 'کسی بر روی دیوار گروهی که در آن شرکت دارم نظر داده است');
			
			$languageService->addOrUpdateValue($langFaId, 'groups', 'confirm_delete_groups', 'آیا از حذف همه گروه‌ها اطمینان دارید؟');
			
			$languageService->addOrUpdateValue($langFaId, 'groups', 'delete_confirm_msg', 'آیا از حذف این گروه اطمینان دارید؟ این کار همه محتوای آن‌را نیز پاک می‌کند.');
			
			$languageService->addOrUpdateValue($langFaId, 'groups', 'delete_content', 'حذف محتوا و افزونه');
			
			$languageService->addOrUpdateValue($langFaId, 'groups', 'delete_feed_item_label', 'حذف پست');
			
			$languageService->addOrUpdateValue($langFaId, 'groups', 'delete_group_label', 'حذف');
			
			$languageService->addOrUpdateValue($langFaId, 'groups', 'delete_group_user_confirmation', 'آیا از حذف کاربر از این گروه اطمینان دارید؟');
			
			$languageService->addOrUpdateValue($langFaId, 'groups', 'delete_group_user_label', 'حذف کاربر');
			
			$languageService->addOrUpdateValue($langFaId, 'groups', 'page_title_uninstall', 'حذف افزونه گروه‌ها');
			
			$languageService->addOrUpdateValue($langFaId, 'groups', 'plugin_set_for_uninstall', 'حذف افزونه انجمن آغاز شد.');
			
			$languageService->addOrUpdateValue($langFaId, 'groups', 'invite_list_page_title', 'دعوت‌نامه‌های  گروه - {$site_name}');
			
			$languageService->addOrUpdateValue($langFaId, 'groups', 'feed_create_string', 'گروه جدید ایجاد کرد');
			
			$languageService->addOrUpdateValue($langFaId, 'groups', 'auth_action_label_delete_comment_by_content_owner', 'نویسنده می‌تواند نظرهای روی دیوار را حذف کند');
			
			$languageService->addOrUpdateValue($langFaId, 'groups', 'delete_complete_msg', 'گروه حذف شد');
			
			$languageService->addOrUpdateValue($langFaId, 'groups', 'delete_confirm_msg', 'آیا از حذف این گروه اطمینان دارید؟ این کار همه محتوای آن‌را نیز حذف می‌کند.');
			
			$languageService->addOrUpdateValue($langFaId, 'groups', 'delete_user_success_message', 'کاربر حذف شده است');
			
			$languageService->addOrUpdateValue($langFaId, 'groups', 'delete_content_desc', 'قبل از حذف افزونه گروه‌ها باید همه گروه‌های جدید حذف شوند. این کار ممکن است اندکی زمان‌بر باشد. بنابر همین دلیل وب‌گاه به "حالت نگهداری" خواهد رفت و به‌محض کامل شدن عملیات حذف، فعال می‌شود.');
			
			$languageService->addOrUpdateValue($langFaId, 'groups', 'errors_image_upload', 'بارگذاری فایل ناموفق بود.');
			
			$languageService->addOrUpdateValue($langFaId, 'groups', 'feed_follow_complete_msg', 'شما اکنون {$groupTitle} را دنبال می‌کنید');
			
			$languageService->addOrUpdateValue($langFaId, 'groups', 'group_owner_delete_error', 'شما نمی‌توانید مالک گروه را حذف کنید');
			
			$languageService->addOrUpdateValue($langFaId, 'groups', 'post_reply_permission_error', 'برای ایجاد پست، نیاز است تا شما عضوی از گروه باشید.');
			
			$languageService->addOrUpdateValue($langFaId, 'groups', 'widget_groups_count_setting', 'تعداد');
			
			$languageService->addOrUpdateValue($langFaId, 'groups', 'widget_users_settings_count', 'تعداد');

            $languageService->addOrUpdateValue($langFaId, 'groups', 'groups_sitemap', 'گروه‌ها');
            $languageService->addOrUpdateValue($langFaId, 'groups', 'groups_sitemap_desc', 'گروه‌ها و فهرست‌ها');
            $languageService->addOrUpdateValue($langFaId, 'groups', 'seo_meta_section', 'گروه‌ها');
            $languageService->addOrUpdateValue($langFaId, 'groups', 'seo_meta_most_popular_label', 'صفحه مشهورترین گروه‌ها');
            $languageService->addOrUpdateValue($langFaId, 'groups', 'meta_title_most_popular', 'صفحه مشهورترین گروه‌ها | {$site_name}');
            $languageService->addOrUpdateValue($langFaId, 'groups', 'meta_desc_most_popular', 'فهرست مشهورترین گروه‌ها در{$site_name}. عضو شوید.');
            $languageService->addOrUpdateValue($langFaId, 'groups', 'meta_keywords_most_popular', '');
            $languageService->addOrUpdateValue($langFaId, 'groups', 'seo_meta_latest_label', 'صفحه آخرین گروه‌ها');
            $languageService->addOrUpdateValue($langFaId, 'groups', 'meta_title_latest', 'آخرین گروه‌ها | {$site_name}');
            $languageService->addOrUpdateValue($langFaId, 'groups', 'meta_desc_latest', 'لیست آخرین گروه‌های ایجاد شده در {$site_name}. عضو شوید.');
            $languageService->addOrUpdateValue($langFaId, 'groups', 'meta_keywords_latest', '');
            $languageService->addOrUpdateValue($langFaId, 'groups', 'seo_meta_user_groups_label', 'صفحه گروه');
            $languageService->addOrUpdateValue($langFaId, 'groups', 'meta_title_user_groups', 'فرد {$user_name}،{$user_age} عضو گروه شد | {$site_name}');
            $languageService->addOrUpdateValue($langFaId, 'groups', 'meta_desc_user_groups', 'تمام گروه‌های {$user_name}، {$user_age} در {$site_name}.');
            $languageService->addOrUpdateValue($langFaId, 'groups', 'meta_keywords_user_groups', '');
            $languageService->addOrUpdateValue($langFaId, 'groups', 'seo_meta_groups_page_label', 'صفحه گروه');
            $languageService->addOrUpdateValue($langFaId, 'groups', 'meta_title_groups_page', '{$group_title} | {$site_name}');
            $languageService->addOrUpdateValue($langFaId, 'groups', 'meta_desc_groups_page', '{$group_description}');
            $languageService->addOrUpdateValue($langFaId, 'groups', 'meta_keywords_groups_page', '');
            $languageService->addOrUpdateValue($langFaId, 'groups', 'seo_meta_group_users_label', 'صفحه شرکت‌گنندگان گروه');
            $languageService->addOrUpdateValue($langFaId, 'groups', 'meta_title_group_users', 'تمامی اعضای {$group_title} | {$site_name}');
            $languageService->addOrUpdateValue($langFaId, 'groups', 'meta_desc_group_users', 'فهرست اعضای "{$group_title}" در {$site_name}');
            $languageService->addOrUpdateValue($langFaId, 'groups', 'meta_keywords_group_users', '');
			$languageService->addOrUpdateValue($langFaId, 'groups', 'auth_action_label_wall_post', 'اجازه برای ارسال نوشته‌های دیوار گروه');
			$languageService->addOrUpdateValue($langFaId, 'groups', 'delete_feed_item_label', 'حذف نوشته');
			$languageService->addOrUpdateValue($langFaId, 'groups', 'post_reply_permission_error', 'برای ایجاد نوشته، نیاز است تا شما عضوی از گروه باشید.');
			$languageService->addOrUpdateValue($langFaId, 'groups', 'seo_meta_most_popular_label', 'صفحه محبوب‌ترین گروه‌ها');
			$languageService->addOrUpdateValue($langFaId, 'groups', 'meta_title_most_popular', 'صفحه محبوب‌ترین گروه‌ها | {$site_name}');
			$languageService->addOrUpdateValue($langFaId, 'groups', 'mobile_main_menu_list', 'گروه');
			$languageService->addOrUpdateValue($langFaId, 'groups', 'users_invite_success_message', 'دعوت‌نامه به {$count} کاربر ارسال شد.');
        }
		
		//iispasswordchangeinterval plugin
		if (IISSecurityProvider::existPluginKeyInActivePlugins($activePlugins, 'iispasswordchangeinterval')) {
            $languageService->addOrUpdateValue($langFaId, 'iispasswordchangeinterval', 'admin_page_heading', 'تنظیمات افزونه تغییر رمز دوره ای');
            $languageService->addOrUpdateValue($langFaId, 'iispasswordchangeinterval', 'admin_page_title', 'تنظیمات افزونه تغییر رمز دوره ای');

            $languageService->addOrUpdateValue($langFaId, 'iispasswordchangeinterval', 'description_change_password', 'شما باید گذرواژه خود را به دلیل چالش امنیتی تغییر دهید');
			
			$languageService->addOrUpdateValue($langFaId, 'iispasswordchangeinterval', 'deal_with_expired_password_normal_with_notif', 'کاربران با آگاه شدن از منقضی بودن گذرواژه خود، قادر هستند هر کاری انجام دهند');
			
			$languageService->addOrUpdateValue($langFaId, 'iispasswordchangeinterval', 'set_all_password_valid_description', 'شما می‏توانید گذرواژه‌ها تمامی کاربران را معتبر کنید تا آن‏ها نیاز به تغییر گذرواژه نداشته باشند.');
			
			$languageService->addOrUpdateValue($langFaId, 'iispasswordchangeinterval', 'change_password_per_day_label', 'تغییر گذرواژه بصورت بازه ای (بر حسب روز)');
			
			$languageService->addOrUpdateValue($langFaId, 'iispasswordchangeinterval', 'deal_with_expired_password', 'نحوه مقابله با گذرواژه‌های منقضی شده');
			
			$languageService->addOrUpdateValue($langFaId, 'iispasswordchangeinterval', 'set_all_password_invalid', 'نامعتبر سازی تمامی گذرواژه‌ها');
			
			$languageService->addOrUpdateValue($langFaId, 'iispasswordchangeinterval', 'set_all_password_valid', 'معتبرسازی تمامی گذرواژه‌ها');
			
			$languageService->addOrUpdateValue($langFaId, 'iispasswordchangeinterval', 'set_all_password_invalid_description', ' شما می‏توانید گذرواژه‌های تمامی کاربران را نامعتبر کنید.');
			
			$languageService->addOrUpdateValue($langFaId, 'iispasswordchangeinterval', 'password_is_invalid_description', 'گذرواژه قبلی شما به دلیل چالش امنیتی نامعتبر است. نیاز است تا شما از پیوندی که به رایانشانی شما فرستاده شده وارد شوید و گذرواژه خود را تغییر دهید.');
			
        }
		//photo plugin
        if (IISSecurityProvider::existPluginKeyInActivePlugins($activePlugins, 'photo')) {
            $languageService->addOrUpdateValue($langFaId, 'photo', 'of', 'از');
			$languageService->addOrUpdateValue($langFaId, 'photo', 'feed_multiple_descriptions', '<span dir="auto" > {$number} تصویر جدید به آلبوم <a href="{$albumUrl}">{$albumName}</a> بارگذاری کرد. </span>');
			
			$languageService->addOrUpdateValue($langFaId, 'photo', 'delete_content_desc', 'قبل از حذف افزونه تصویر، باید تمام تصاویر کاربران را پاک کنیم . این کار اندکی زمان‌بر است. در این زمان سایت را به حالت تعمیرات و نگهداری می‌بریم.');
			
			$languageService->addOrUpdateValue($langFaId, 'photo', 'delete_selected', 'حذف انتخاب شده‌ها');
			
			$languageService->addOrUpdateValue($langFaId, 'photo', 'plugin_set_for_uninstall', 'حذف افزونه تصویر راه‌اندازی شد.');
			
			$languageService->addOrUpdateValue($langFaId, 'photo', 'confirm_delete', 'آیا از حذف این تصویر اطمینان دارید؟');
			
			$languageService->addOrUpdateValue($langFaId, 'photo', 'confirm_delete_album', 'آیا از حذف این آلبوم تصویر اطمینان دارید؟');
			
			$languageService->addOrUpdateValue($langFaId, 'photo', 'confirm_delete_photos', 'آیا از حذف تمام تصاویر کاربران اطمینان دارید؟');
			
			$languageService->addOrUpdateValue($langFaId, 'photo', 'delete_album', 'حذف آلبوم');
			
			$languageService->addOrUpdateValue($langFaId, 'photo', 'delete_content', 'حذف مطالب و افزونه');
			
			$languageService->addOrUpdateValue($langFaId, 'photo', 'delete_fullsize_confirm', 'آیا از حذف تصاویر با اندازه کامل اطمینان دارید؟');
			
			$languageService->addOrUpdateValue($langFaId, 'photo', 'delete_photo', 'حذف');
			
			$languageService->addOrUpdateValue($langFaId, 'photo', 'remove_from_featured', 'حذف از حالت ویژه');
			
			$languageService->addOrUpdateValue($langFaId, 'photo', 'album_delete_not_allowed', 'شما مجاز به حذف آلبوم‌های تصویر نیستید');
			
			$languageService->addOrUpdateValue($langFaId, 'photo', 'album_updated', 'به‌روزرسانی');
			
			$languageService->addOrUpdateValue($langFaId, 'photo', 'auth_action_label_add_comment', 'نظر تصویر');
			
			$languageService->addOrUpdateValue($langFaId, 'photo', 'btn_edit', 'ذخیره');
			
			$languageService->addOrUpdateValue($langFaId, 'photo', 'cmp_widget_photo_albums_count', 'تعداد آلبوم‌های تصاویر');
			
			$languageService->addOrUpdateValue($langFaId, 'photo', 'cmp_widget_photo_albums_show_titles', 'نمایش عنوان‌های آلبوم تصویر');
			
			$languageService->addOrUpdateValue($langFaId, 'photo', 'cmp_widget_photo_count', 'تعداد تصاویر برای نمایش');
			
			$languageService->addOrUpdateValue($langFaId, 'photo', 'tb_edit_photo', 'ویرایش تصویر');
			
			$languageService->addOrUpdateValue($langFaId, 'photo', 'upload_ini_value', '(محدودیت سرور : {$value})');
			
			$languageService->addOrUpdateValue($langFaId, 'photo', 'view_fullsize', 'مشاهده سایز کامل');
			
			$languageService->addOrUpdateValue($langFaId, 'photo', 'auth_action_label_view', 'مشاهده تصویر');
			
			$languageService->addOrUpdateValue($langFaId, 'photo', 'admin_menu_view', 'تنظیمات نمایش');
			
			$languageService->addOrUpdateValue($langFaId, 'photo', 'accepted_filesize_desc', 'بیشینه اندازه قابل قبول برای بارگذاری تصویر. کمینه مقدار قابل قبول <b><i>0.5</i></b> مگابایت.');

			$languageService->addOrUpdateValue($langFaId, 'photo', 'admin_config', 'تنظیمات افزونه تصویر');
			
			$languageService->addOrUpdateValue($langFaId, 'photo', 'rating_total', '(تعداد امتیاز دهندگان: {$count})');
		
			$languageService->addOrUpdateValue($langFaId, 'photo', 'rating_your', '(تعداد امتیاز دهندگان: {$count} / امتیاز شما: {$score})');
			
			$languageService->addOrUpdateValue($langFaId, 'photo', 'advanced_upload_desc', 'فعال‌سازی بارگذار پیشرفته نرم‌افزار فلش تا به کاربران اجازه داده شود تعداد اسناد و امکاناتی مانند تغییر اندازه، و چرخاندن تصاویر را قبل از بارگذاری انتخاب کنند.');
			
			$languageService->addOrUpdateValue($langFaId, 'photo', 'album_deleted', 'آلبوم حذف شد');
			
			$languageService->addOrUpdateValue($langFaId, 'photo', 'auth_action_label_delete_comment_by_content_owner', 'مالک محتوا می‌تواند نظرات را حذف کند');
			
			$languageService->addOrUpdateValue($langFaId, 'photo', 'choose_existing_or_create', 'انتخاب آلبوم موجود یا ایجاد');
			
			$languageService->addOrUpdateValue($langFaId, 'photo', 'delete_fullsize_photos', 'تصاویر اندازه کامل حذف شد ({$count})');
			
			$languageService->addOrUpdateValue($langFaId, 'photo', 'dnd_support', 'تصاویر را بکشید و در این‌جا رها کنید یا برای مرور آن‌ها کلیک کنید');
			
			$languageService->addOrUpdateValue($langFaId, 'photo', 'fullsize_resolution_desc', 'تمام تصاویر با اندازه‌های بزرگ‌تر از (X  یا Y) با این محدودیت‌های اندازه برای ذخیره تغییر اندازه پیدا خواهند کرد.');
			
			$languageService->addOrUpdateValue($langFaId, 'photo', 'photos_deleted', 'تصاویر حذف شدند');
			
			$languageService->addOrUpdateValue($langFaId, 'photo', 'photo_not_deleted', 'تصویر حذف نشده است');
			
			$languageService->addOrUpdateValue($langFaId, 'photo', 'photo_uploaded_pending_approval', 'تصاویر در مدت کوتاهی در دسترس خواهد بود، در انتظار تایید.');
			
			$languageService->addOrUpdateValue($langFaId, 'photo', 'plugin_set_for_uninstall', 'حذف افزونه تصویر آغاز شد.');
			
			$languageService->addOrUpdateValue($langFaId, 'photo', 'view_fullsize', 'مشاهده اندازه کامل');
			
			$languageService->addOrUpdateValue($langFaId, 'photo', 'user_quota_desc', 'این عدد باید به‌اندازه معقولی بزرگ نگه‌داشته شود.');
										
			$languageService->addOrUpdateValue($langFaId, 'photo', 'photo_list', 'فهرست تصاویر');
					
			$languageService->addOrUpdateValue($langFaId, 'photo', 'choose_type_of_photo_list', 'انتخاب نوع فهرست تصاویر');
					
			$languageService->addOrUpdateValue($langFaId, 'photo', 'album_list', 'فهرست آلبوم');
					
			$languageService->addOrUpdateValue($langFaId, 'photo', 'choose_type_of_album_list', 'انتخاب نوع فهرست آلبوم');
					
			$languageService->addOrUpdateValue($langFaId, 'photo', 'photo_view', 'نمایش تصویر');
					
			$languageService->addOrUpdateValue($langFaId, 'photo', 'choose_type_of_photo_view', 'انتخاب نوع نمایش تصاویر');


            $languageService->addOrUpdateValue($langFaId, 'photo', 'photo_sitemap', 'تصاویر');
            $languageService->addOrUpdateValue($langFaId, 'photo', 'seo_meta_section', 'تصاویر');
            $languageService->addOrUpdateValue($langFaId, 'photo', 'seo_meta_tagged_list_label', 'صفحه تمامی تصاویر برچسب‌زده‌شده');
            $languageService->addOrUpdateValue($langFaId, 'photo', 'meta_title_tagged_list', '{$tag} photos | {$site_name}');
            $languageService->addOrUpdateValue($langFaId, 'photo', 'meta_desc_tagged_list', 'فهرست تمام تصاویر برچسب زده شده "{$tag}" در {$site_name}');
            $languageService->addOrUpdateValue($langFaId, 'photo', 'meta_keywords_tagged_list', '');
            $languageService->addOrUpdateValue($langFaId, 'photo', 'seo_meta_photo_list_label', 'صفحه انواع تصاویر  (آخرین‌ها، بیشترین امتیازدهی شده‌ها، بیشترین بحث‌شده‌ها)');
            $languageService->addOrUpdateValue($langFaId, 'photo', 'meta_title_photo_list', 'تصاویر {$list_type} | {$site_name}');
            $languageService->addOrUpdateValue($langFaId, 'photo', 'meta_desc_photo_list', 'تمامی تصاویر {$list_type} در {$site_name}');
            $languageService->addOrUpdateValue($langFaId, 'photo', 'meta_keywords_photo_list', '');
            $languageService->addOrUpdateValue($langFaId, 'photo', 'list_type_label_featured', 'ویژه');
            $languageService->addOrUpdateValue($langFaId, 'photo', 'list_type_label_latest', 'آخرین‌ها');
            $languageService->addOrUpdateValue($langFaId, 'photo', 'list_type_label_toprated', 'بیشترین امتیازدهی شده');
            $languageService->addOrUpdateValue($langFaId, 'photo', 'list_type_label_tagged', 'برچسب‌زده‌شده');
            $languageService->addOrUpdateValue($langFaId, 'photo', 'list_type_label_most_discussed', 'بیشتر بحث‌شده‌ها');
            $languageService->addOrUpdateValue($langFaId, 'photo', 'seo_meta_user_albums_label', 'صفحه آلبوم‌های تصاویر فرد');
            $languageService->addOrUpdateValue($langFaId, 'photo', 'meta_title_user_albums', 'آلبوم تصاویر {$user_name}،{$user_age} | {$site_name}');
            $languageService->addOrUpdateValue($langFaId, 'photo', 'meta_desc_user_albums', 'مشاهده تمامی تصاویر آلبوم که توسط {$user_name}،{$user_age} در {$site_name} بارگذاری شده است.');
            $languageService->addOrUpdateValue($langFaId, 'photo', 'meta_keywords_user_albums', '');
            $languageService->addOrUpdateValue($langFaId, 'photo', 'seo_meta_user_album_label', 'صفحه آلبوم تصاویر فرد');
            $languageService->addOrUpdateValue($langFaId, 'photo', 'meta_title_user_album', 'مشاهده تمامی تصاویر در "{$album_name}" که توسط {$user_name}،{$user_age} در {$site_name} بارگذاری شده است.');
            $languageService->addOrUpdateValue($langFaId, 'photo', 'meta_desc_user_album', 'مشاهده تمامی تصاویر در "{$album_name}" که توسط {$user_name}،{$user_age} در {$site_name} بارگذاری شده است.');
            $languageService->addOrUpdateValue($langFaId, 'photo', 'meta_keywords_user_album', '');
            $languageService->addOrUpdateValue($langFaId, 'photo', 'seo_meta_user_photos_label', 'صفحه آلبوم فرد');
            $languageService->addOrUpdateValue($langFaId, 'photo', 'meta_title_user_photos', 'تمامی تصاویر بارگذاری شده توسط {$user_name}،{$user_age} در {$site_name}.');
            $languageService->addOrUpdateValue($langFaId, 'photo', 'meta_desc_user_photos', 'مشاهده تمامی تصاویر بارگذاری شده توسط {$user_name}،{$user_age} در {$site_name}.');
            $languageService->addOrUpdateValue($langFaId, 'photo', 'meta_keywords_user_photos', '');
            $languageService->addOrUpdateValue($langFaId, 'photo', 'seo_meta_photo_view_label', 'صفحه تصویر');
            $languageService->addOrUpdateValue($langFaId, 'photo', 'meta_title_photo_view', 'مشاهده تصویر "{$photo_id}" که توسط {$user_name} در {$site_name} بارگذاری شده است.');
            $languageService->addOrUpdateValue($langFaId, 'photo', 'meta_desc_photo_view', 'مشاهده تصویر "{$photo_id}" که توسط {$user_name} در {$site_name} بارگذاری شده است.');
            $languageService->addOrUpdateValue($langFaId, 'photo', 'meta_keywords_photo_view', '');
			$languageService->addOrUpdateValue($langFaId, 'photo', 'list_type_label_photo_friends', 'دوستان');
			$languageService->addOrUpdateValue($langFaId, 'photo', 'search_invitation', 'توضیح تصویر، @نام کاربری یا #برچسب تصویر');
			$languageService->addOrUpdateValue($langFaId, 'photo', 'meta_title_photo_photo_friends', 'تصاویر دوستان - {$site_name}');
        }
		
		//forum plugin
        if (IISSecurityProvider::existPluginKeyInActivePlugins($activePlugins, 'forum')) {
            $languageService->addOrUpdateValue($langFaId, 'forum', 'admin_forum_settings_heading', 'تنظیمات افزونه انجمن');

            $languageService->addOrUpdateValue($langFaId, 'forum', 'feed_activity_topic_string', 'یک موضوع در انجمن ایجاد کرد.');
			
			$languageService->addOrUpdateValue($langFaId, 'forum', 'advanced_search', 'جستجو پیشرفته');
			
			$languageService->addOrUpdateValue($langFaId, 'forum', 'search_advanced_heading', 'جستجو پیشرفته');
			
			$languageService->addOrUpdateValue($langFaId, 'forum', 'this_topic', 'تنظیمات موضوع');
			
			$languageService->addOrUpdateValue($langFaId, 'forum', 'feed_activity_topic_reply_string', 'پاسخی برای یک موضوع در انجمن ارائه داد.');
			
			$languageService->addOrUpdateValue($langFaId, 'forum', 'email_notification_post', '<a href="{$userUrl}">{$userName}</a> به یک موضوع در انجمن <a href="{$postUrl}">{$title}</a> پاسخ داد.');
			
			$languageService->addOrUpdateValue($langFaId, 'forum', 'add_post_title', 'درج پاسخ');
			
			$languageService->addOrUpdateValue($langFaId, 'forum', 'flag', 'گزارش تخلف');

			$languageService->addOrUpdateValue($langFaId, 'forum', 'confirm_delete_attachment', 'آیا از حذف این سند اطمینان دارید؟');			
			
			$languageService->addOrUpdateValue($langFaId, 'forum', 'confirm_delete_forum', 'آیا از حذف همه موضوعات انجمن و بخش‌ها اطمینان دارید؟');
			
			$languageService->addOrUpdateValue($langFaId, 'forum', 'delete', 'حذف');
			
			$languageService->addOrUpdateValue($langFaId, 'forum', 'delete_content', 'حذف محتوا و افزونه');
			
			$languageService->addOrUpdateValue($langFaId, 'forum', 'delete_content_desc', 'قبل از حذف افزونه انجمن تمام محتوای موجود در آن باید پاک شود. این کار ممکن است اندکی زمان‌بر باشد. به همین دلیل سایت را به "حالت نگهداری" برده و پس از پایان عملیات پاکسازی دوباره فعال‌سازی می‌کنیم.');
			
			$languageService->addOrUpdateValue($langFaId, 'forum', 'delete_group', 'حذف');
			
			$languageService->addOrUpdateValue($langFaId, 'forum', 'delete_group_confirm', 'آیا از حذف این انجمن گروه اطمینان دارید؟');
			
			$languageService->addOrUpdateValue($langFaId, 'forum', 'delete_post_confirm', 'آیا از حذف این نوشته اطمینان دارید؟');
			
			$languageService->addOrUpdateValue($langFaId, 'forum', 'delete_section', 'حذف');
			
			$languageService->addOrUpdateValue($langFaId, 'forum', 'delete_section_confirm', 'آیا از حذف این بخش انجمن اطمینان دارید؟');
			
			$languageService->addOrUpdateValue($langFaId, 'forum', 'delete_topic', 'حذف');
			
			$languageService->addOrUpdateValue($langFaId, 'forum', 'delete_topic_confirm', 'آیا از حذف این موضوع اطمینان دارید؟');
			
			$languageService->addOrUpdateValue($langFaId, 'forum', 'page_title_uninstall', 'حذف افزونه انجمن');
			
			$languageService->addOrUpdateValue($langFaId, 'forum', 'plugin_set_for_uninstall', 'حذف افزونه انجمن آغاز شد.');
			
			$languageService->addOrUpdateValue($langFaId, 'forum', 'unsticky_topic', 'حذف از بخش مهم‌ها');
			
			$languageService->addOrUpdateValue($langFaId, 'forum', 'unsticky_topic_confirm', 'این موضوع مهم نخواهد بود، آیا از حذف این موضوع از بخش مهم‌ها اطمینان دارید؟');
			
			$languageService->addOrUpdateValue($langFaId, 'forum', 'clear_all', 'حذف همه');
			
			$languageService->addOrUpdateValue($langFaId, 'forum', 'confirm_delete_all_attachments', 'آیا از حذف این اسناد اطمینان دارید؟');
			
			$languageService->addOrUpdateValue($langFaId, 'forum', 'delete_quote_confirm', 'آیا از حذف این نقل قول اطمینان دارید؟');
			
			$languageService->addOrUpdateValue($langFaId, 'forum', 'clear_all', 'حذف همه');

            $languageService->addOrUpdateValue($langFaId, 'forum', 'forum_sitemap', 'انجمن');
            $languageService->addOrUpdateValue($langFaId, 'forum', 'seo_meta_section', 'انجمن');
            $languageService->addOrUpdateValue($langFaId, 'forum', 'seo_meta_home_label', 'صفحه اصلی انجمن');
            $languageService->addOrUpdateValue($langFaId, 'forum', 'meta_title_home', 'انجمن | {$site_name}');
            $languageService->addOrUpdateValue($langFaId, 'forum', 'meta_desc_home', 'به انجمن {$site_name} خوش آمدید. موضوع جدید ایجاد کنید، نوشته‌های دیگران را مطالعه کنید و با دیگر کاربران گفت‌وگو کنید.');
            $languageService->addOrUpdateValue($langFaId, 'forum', 'meta_keywords_home', '');
            $languageService->addOrUpdateValue($langFaId, 'forum', 'seo_meta_adv_search_label', 'صفحه جستجوی پیشرفته انجمن');
            $languageService->addOrUpdateValue($langFaId, 'forum', 'meta_title_adv_search', 'جستجوی پیشرفته انجمن {$site_name}');
            $languageService->addOrUpdateValue($langFaId, 'forum', 'meta_desc_adv_search', 'از جستجوگر پیشرفته برای شناسایی اطلاعات مورد نیاز در انجمن {$site_name} استفاده کنید.');
            $languageService->addOrUpdateValue($langFaId, 'forum', 'meta_keywords_adv_searche', '');
            $languageService->addOrUpdateValue($langFaId, 'forum', 'seo_meta_adv_search_result_label', 'صفحه پیشرفته نتایج جستجوی انجمن');
            $languageService->addOrUpdateValue($langFaId, 'forum', 'meta_title_adv_search_result', 'نتایج جستجوی برای انجمن {$site_name}');
            $languageService->addOrUpdateValue($langFaId, 'forum', 'meta_desc_adv_search_result', 'مشاهده نتایج جستجوی انجمن در {$site_name}');
            $languageService->addOrUpdateValue($langFaId, 'forum', 'meta_keywords_adv_searche_result', '');
            $languageService->addOrUpdateValue($langFaId, 'forum', 'seo_meta_section_label', 'صفحه زیربخش‌ها');
            $languageService->addOrUpdateValue($langFaId, 'forum', 'meta_title_section', '{$section_name} در انجمن {$site_name}');
            $languageService->addOrUpdateValue($langFaId, 'forum', 'meta_desc_section', '{$section_name}');
            $languageService->addOrUpdateValue($langFaId, 'forum', 'meta_keywords_section', '');
            $languageService->addOrUpdateValue($langFaId, 'forum', 'seo_meta_group_label', 'صفحه دسته‌های زیربخش‌ها');
            $languageService->addOrUpdateValue($langFaId, 'forum', 'meta_title_group', '{$group_name} در انجمن {$site_name}');
            $languageService->addOrUpdateValue($langFaId, 'forum', 'meta_desc_group', '{$group_description}');
            $languageService->addOrUpdateValue($langFaId, 'forum', 'meta_keywords_group', '');
            $languageService->addOrUpdateValue($langFaId, 'forum', 'seo_meta_topic_label', 'صفحه ریسمان انجمن');
            $languageService->addOrUpdateValue($langFaId, 'forum', 'meta_title_topic', '{$topic_name} در انجمن {$site_name}');
            $languageService->addOrUpdateValue($langFaId, 'forum', 'meta_desc_topic', '{$topic_description}');
            $languageService->addOrUpdateValue($langFaId, 'forum', 'meta_keywords_topic', '');
            $languageService->addOrUpdateValue($langFaId, 'forum', 'seo_meta_section_search_label', 'صفحه جستجوی زیربخش‌ها');
            $languageService->addOrUpdateValue($langFaId, 'forum', 'meta_title_section_search', 'جستجوی {$section_name} در انجمن {$site_name}');
            $languageService->addOrUpdateValue($langFaId, 'forum', 'meta_desc_section_search', 'نتایج جستجو برای زیربخش {$section_name} در انجمن {$site_name}');
            $languageService->addOrUpdateValue($langFaId, 'forum', 'meta_keywords_section_search', '');
            $languageService->addOrUpdateValue($langFaId, 'forum', 'seo_meta_group_search_label', 'صفحه جستحوی زیربخش‌های انجمن');
            $languageService->addOrUpdateValue($langFaId, 'forum', 'meta_title_group_search', 'جستجوی {$group_name} در انجمن {$site_name}');
            $languageService->addOrUpdateValue($langFaId, 'forum', 'meta_desc_group_search', 'نتیج جستجو برای ریسمان {$group_name} در انجمن {$site_name}');
            $languageService->addOrUpdateValue($langFaId, 'forum', 'meta_keywords_group_search', '');
            $languageService->addOrUpdateValue($langFaId, 'forum', 'seo_meta_topic_search_label', 'جستجوی ریسمان انجمن');
            $languageService->addOrUpdateValue($langFaId, 'forum', 'meta_title_topic_search', 'جستجوی موضوع {$topic_name} در {$site_name}');
            $languageService->addOrUpdateValue($langFaId, 'forum', 'meta_desc_topic_search', 'نتایج جستجو برای موضوع {$topic_name} در انجمن {$site_name}.');
            $languageService->addOrUpdateValue($langFaId, 'forum', 'meta_keywords_topic_search', '');
			$languageService->addOrUpdateValue($langFaId, 'forum', 'forms_search_sort_direction_field_value_increase', 'افزایشی');
			$languageService->addOrUpdateValue($langFaId, 'forum', 'forms_search_sort_direction_field_value_decrease', 'کاهشی');
			
			
        }
		
		//iisnews plugin
        if (IISSecurityProvider::existPluginKeyInActivePlugins($activePlugins, 'iisnews')) {
            $languageService->addOrUpdateValue($langFaId, 'iisnews', 'feed_add_item_label', 'یک خبر جدید ایجاد کرد.');
			
			$languageService->addOrUpdateValue($langFaId, 'iisnews', 'news_manage_delete', 'حذف');
			
			$languageService->addOrUpdateValue($langFaId, 'iisnews', 'confirm_delete_photos', 'آیا از حذف تصاویر همه کاربران اطمینان دارید؟');
			
			$languageService->addOrUpdateValue($langFaId, 'iisnews', 'delete_content', 'حذف مطالب و حذف افزونه');
			
			$languageService->addOrUpdateValue($langFaId, 'iisnews', 'delete_content_desc', 'قبل از حذف افزونه خبر ما باید تمام محتوای موجود کاربران را حذف کنیم . این ممکن است اندکی زمان‌بر باشد. در این زمان ما سایت را در حالت تعمیر و نگهداری قرار می دهیم و پس از پایان عملیات دوباره آن‌را فعال می‌کنیم.');
			
			$languageService->addOrUpdateValue($langFaId, 'iisnews', 'page_title_uninstall', 'حذف افزونه خبر');
			
			$languageService->addOrUpdateValue($langFaId, 'iisnews', 'plugin_set_for_uninstall', 'حذف افزونه اخبار آغاز شد');
			
			$languageService->addOrUpdateValue($langFaId, 'iisnews', 'toolbar_delete', 'حذف');
			
			
			
			$languageService->addOrUpdateValue($langFaId, 'iisnews', 'admin_news_settings_heading', 'تنظیمات افزونه اخبار');
			
			$languageService->addOrUpdateValue($langFaId, 'iisnews', 'admin_settings_results_per_page', 'نوشته‌های این صفحه');
			
			$languageService->addOrUpdateValue($langFaId, 'iisnews', 'authorization_failed_view_news', 'با عرض پوزش، شما مجاز به مشاهده این خبر نیستید .');
			
			$languageService->addOrUpdateValue($langFaId, 'iisnews', 'auth_action_label_delete_comment_by_content_owner', 'صاحب مطلب می تواند نظرات را پاک کند');
			
			$languageService->addOrUpdateValue($langFaId, 'iisnews', 'news_archive_lbl_archives', 'بایگانی');
			
			$languageService->addOrUpdateValue($langFaId, 'iisnews', 'news_entry_title', '{$entry_title} نوشته شده به وسیله : {$display_name} در {$site_name}');
			
			$languageService->addOrUpdateValue($langFaId, 'iisnews', 'news_widget_preview_length_lbl', 'طول پیش نمایش');
			
			$languageService->addOrUpdateValue($langFaId, 'iisnews', 'browse_by_tag_item_description', 'مرور برچسب‌های نوشته‌های خبر به عنوان {$tag}.');
			
			$languageService->addOrUpdateValue($langFaId, 'iisnews', 'browse_by_tag_item_title', '{$tag}نوشته‌های مرتبط خبر {$site_name}');
			
			$languageService->addOrUpdateValue($langFaId, 'iisnews', 'browse_by_tag_title', 'مرور نوشته‌های خبر به وسیله برچسب‌ها {$site_name}');
			
			$languageService->addOrUpdateValue($langFaId, 'iisnews', 'by', 'به وسیله');
			
			$languageService->addOrUpdateValue($langFaId, 'iisnews', 'cmp_widget_entry_count', 'تعداد نوشته‌ها برای نمایش');
			
			$languageService->addOrUpdateValue($langFaId, 'iisnews', 'latest_entry', 'آخرین نوشته‌ها');
			
			$languageService->addOrUpdateValue($langFaId, 'iisnews', 'manage_page_last_updated', 'آخرین به‌روزرسانی');
			
			$languageService->addOrUpdateValue($langFaId, 'iisnews', 'manage_page_menu_drafts', 'پیش نویس ها');
			
			$languageService->addOrUpdateValue($langFaId, 'iisnews', 'manage_page_menu_published', 'پست‌های منتشر شده');
			
			$languageService->addOrUpdateValue($langFaId, 'iisnews', 'manage_page_status', 'وضعیت');
			
			$languageService->addOrUpdateValue($langFaId, 'iisnews', 'menuItemMostDiscussed', 'بیشترین بحث شده‌ها');
			
			$languageService->addOrUpdateValue($langFaId, 'iisnews', 'more', 'بیشتر');
			
			$languageService->addOrUpdateValue($langFaId, 'iisnews', 'most_discussed_description', 'بیشترین نوشته‌های بحث شده خبر کاربر در {$site_name}.');
			
			$languageService->addOrUpdateValue($langFaId, 'iisnews', 'most_discussed_title', 'بیشترین اخباری بحث شده  - {$site_name}');
			
			$languageService->addOrUpdateValue($langFaId, 'iisnews', 'next_entry', 'نوشته بعدی');
			
			$languageService->addOrUpdateValue($langFaId, 'iisnews', 'on', 'روی');
			
			$languageService->addOrUpdateValue($langFaId, 'iisnews', 'sava_draft', 'ذخیره به عنوان پیش نویس');
			
			$languageService->addOrUpdateValue($langFaId, 'iisnews', 'save_btn_label', 'ذخیره');
			
			$languageService->addOrUpdateValue($langFaId, 'iisnews', 'settings', 'تنظیمات');
			
			$languageService->addOrUpdateValue($langFaId, 'iisnews', 'top_rated_title', 'برترین خبرها - {$site_name}');
			
			$languageService->addOrUpdateValue($langFaId, 'iisnews', 'most_discussed_title', 'بیشترین اخبار بحث شده  - {$site_name}');
			
			$languageService->addOrUpdateValue($langFaId, 'iisnews', 'browse_by_tag_item_title', 'نوشته‌های مرتبط خبر با برچسب «{$tag}» - {$site_name}');
			
			$languageService->addOrUpdateValue($langFaId, 'iisnews', 'save_form_lbl_date_enable', 'فعال‌سازی ویرایش تاریخ انتشار');
			
			$languageService->addOrUpdateValue($langFaId, 'iisnews', 'save_form_lbl_date', 'تاریخ انتشار');
			
			$languageService->addOrUpdateValue($langFaId, 'iisnews', 'news_notification_string', '<a href="{$actorUrl}">{$actor}</a> خبری منتشر کرده است:<a href="{$url}">"{$title}"</a> ');
			
			$languageService->addOrUpdateValue($langFaId, 'iisnews', 'notification_form_lbl_published', 'اعلان انتشار خبر');
			
			$languageService->addOrUpdateValue($langFaId, 'iisnews', 'email_notifications_setting_news', 'خبری منتشر شد');
			
			$languageService->addOrUpdateValue($langFaId, 'iisnews', 'manage_page_menu_drafts', 'پیش‌نویس‌ها');
			
			$languageService->addOrUpdateValue($langFaId, 'iisnews', 'manage_page_menu_published', 'اخبار منتشر شده');
			
			$languageService->addOrUpdateValue($langFaId, 'iisnews', 'comment_notification_string', '<a href="{$actorUrl}">{$actor}</a> بر روی نوشته شما نظر گذاشته: <a href="{$url}">"{$title}"</a>');
			$languageService->addOrUpdateValue($langFaId, 'iisnews', 'iisnews_mobile', 'اخبار');
			$languageService->addOrUpdateValue($langFaId, 'iisnews', 'index_page_title', 'اخبار');
			$languageService->addOrUpdateValue($langFaId, 'iisnews', 'index_page_heading', 'اخبار');
			$languageService->addOrUpdateValue($langFaId, 'iisnews', 'auth_group_label', 'اخبار');
        }
		
		//newsfeed plugin
        if (IISSecurityProvider::existPluginKeyInActivePlugins($activePlugins, 'newsfeed')) {
            $languageService->addOrUpdateValue($langFaId, 'newsfeed', 'email_notifications_setting_status_like', 'کسی وضعیت من را می‌پسندد');
			
			$languageService->addOrUpdateValue($langFaId, 'newsfeed', 'unfollow_button', 'دنبال نکردن');
			
			$languageService->addOrUpdateValue($langFaId, 'newsfeed', 'admin_features_expanded_label', 'حالت گسترده به صورت پیش‌فرض برای پسندیدن و نظرات');
			
			$languageService->addOrUpdateValue($langFaId, 'newsfeed', 'admin_save_btn', 'ذخیره');
			
			$languageService->addOrUpdateValue($langFaId, 'newsfeed', 'admin_settings_title', 'تنظیمات');
			
			$languageService->addOrUpdateValue($langFaId, 'newsfeed', 'feed_view_more_btn', 'نمایش بیشتر');
			
			$languageService->addOrUpdateValue($langFaId, 'newsfeed', 'settings_updated', 'تنظیمات به‌روزرسانی شد');
			
			$languageService->addOrUpdateValue($langFaId, 'newsfeed', 'widget_settings_count', 'گزینه‌ها برای نمایش');
			
			$languageService->addOrUpdateValue($langFaId, 'newsfeed', 'widget_settings_view_more', 'نمایش بیشتر');
			
			$languageService->addOrUpdateValue($langFaId, 'newsfeed', 'admin_index_status_label', 'فعال‌سازی فیلد «چه خبر» در صفحه اصلی');
			
			$languageService->addOrUpdateValue($langFaId, 'newsfeed', 'admin_comments_count_label', 'تعداد نظرات پیش‌فرض برای نمایش');
			
			$languageService->addOrUpdateValue($langFaId, 'newsfeed', 'admin_page_heading', 'تنظیمات افزونه تازه‌ها');
			
			$languageService->addOrUpdateValue($langFaId, 'newsfeed', 'admin_page_title', 'تنظیمات افزونه تازه‌ها');
			
			$languageService->addOrUpdateValue($langFaId, 'newsfeed', 'follow_complete_message', 'شما اکنون {$username} را دنبال می‌کنید');
			
			$languageService->addOrUpdateValue($langFaId, 'newsfeed', 'activity_string_self_status_like', 'نوشته وضعیت خودش را دوست دارد');
			
			$languageService->addOrUpdateValue($langFaId, 'newsfeed', 'admin_customization_legend', 'امکان کنترل این‌که چه نوع مطالبی در تازه‌ها نشان داده شود، لطفا توجه کنید که تغییرات تنها بر روی پست‌های جدید اثر خواهد گذاشت.');
			
			$languageService->addOrUpdateValue($langFaId, 'newsfeed', 'delete_feed_item_label', 'حذف نوشته');
			
			$languageService->addOrUpdateValue($langFaId, 'newsfeed', 'delete_feed_item_user_label', 'حذف کاربر');
			
			$languageService->addOrUpdateValue($langFaId, 'newsfeed', 'feed_delete_item_label', 'حذف نوشته');
			
			$languageService->addOrUpdateValue($langFaId, 'newsfeed', 'feed_likes_1_label', '{$user1} پسندید');
			
			$languageService->addOrUpdateValue($langFaId, 'newsfeed', 'feed_likes_2_label', '{$user1} و {$user2} پسندیدند');
			
			$languageService->addOrUpdateValue($langFaId, 'newsfeed', 'feed_likes_3_label', '{$user1}, {$user2} و {$user3} پسندیدند');
			
			$languageService->addOrUpdateValue($langFaId, 'newsfeed', 'feed_likes_list_label', '<a href="{$url}">{$count} کاربر</a> پسندیدند');
			
			$languageService->addOrUpdateValue($langFaId, 'newsfeed', 'follow_complete_message', 'شما اکنون فعالیت‌های {$username} را دنبال می‌کنید');
			
			$languageService->addOrUpdateValue($langFaId, 'newsfeed', 'unfollow_complete_message', 'شما دیگر فعالیت‌های {$username} را دنبال نمی‌کنید');
			
			$languageService->addOrUpdateValue($langFaId, 'newsfeed', 'item_deleted_feedback', 'گزینه حذف شد');
			
			$languageService->addOrUpdateValue($langFaId, 'newsfeed', 'email_notifications_setting_user_status', 'کسی بر روی نمایه من چیزی نوشت');
        }
		
		//privacy plugin
        if (IISSecurityProvider::existPluginKeyInActivePlugins($activePlugins, 'privacy')) {
            $languageService->addOrUpdateValue($langFaId, 'privacy', 'privacy_description', 'شما در اینجا می‌توانید حق دسترسی به محتوای خود برای دیگر کاربران را تنظیم کنید');
			
			$languageService->addOrUpdateValue($langFaId, 'privacy', 'privacy_no_permission_message', '{$display_name} اجازه نداده است تا این محتوا به اشتراک گذاشته شود.');
			
			$languageService->addOrUpdateValue($langFaId, 'privacy', 'no_items', 'موردی وجود ندارد.');
			
			$languageService->addOrUpdateValue($langFaId, 'privacy', 'action_action_data_was_saved', 'سامانه در حال اعمال تنظیماتی است که شما تغییر داده‌اید. این مسئله می‌تواند مقداری زمان‌بر باشد.');
			$languageService->addOrUpdateValue($langFaId, 'privacy', 'no_permission_message', 'شما اجازه مشاهده این صفحه را ندارید');
        }
		
		//iisuserlogin plugin
        if (IISSecurityProvider::existPluginKeyInActivePlugins($activePlugins, 'iisuserlogin')) {
            $languageService->addOrUpdateValue($langFaId, 'iisuserlogin', 'email_login_details', 'سلام {$username}، یک ورود از حساب کاربری شما با رایانامه {$email} در شبکه ثبت شد. اطلاعات مرورگر، آی‌پی و زمان ورود عبارتند از: <br />اطلاعات مرورگر: {$browser}  <br /> آی‌پی: {$ip} <br /> زمان: {$time} <br />');
			$languageService->addOrUpdateValue($langFaId, 'iisuserlogin', 'preference_login_detail_subscribe_description', 'در هنگام ورود به شبکه اجتماعی، یک رایانامه به صورت خودکار، حاوی اطلاعات ورود برای شما ارسال خواهد شد');
            $languageService->addOrUpdateValue($langFaId, 'iisuserlogin', 'admin_page_heading', 'تنظیمات افزونه نمایش جزییات ورود کاربران');
            $languageService->addOrUpdateValue($langFaId, 'iisuserlogin', 'admin_page_title', 'تنظیمات افزونه نمایش جزییات ورود کاربران');

        }
		
		//iisvideoplus
		if (IISSecurityProvider::existPluginKeyInActivePlugins($activePlugins, 'iisvideoplus')) {
            $languageService->addOrUpdateValue($langFaId, 'iisvideoplus', 'latest_friends', 'آخرین ویدیوهای دوستان');
			
			$languageService->addOrUpdateValue($langFaId, 'iisvideoplus', 'meta_description_video_latest_friends', 'میتوانید آخرین ویدیوهای دوستان خود را در این صفحه مشاهده کنید');
			 
			$languageService->addOrUpdateValue($langFaId, 'iisvideoplus', 'meta_title_video_add_latest_friends', 'آخرین ویدیوهای دوستان');
			  
			$languageService->addOrUpdateValue($langFaId, 'iisvideoplus', 'latest_myvideo', 'ویدیوهای من');
			   
			$languageService->addOrUpdateValue($langFaId, 'iisvideoplus', 'meta_title_video_add_latest_myvideo', 'ویدیوهای من');
			
			$languageService->addOrUpdateValue($langFaId, 'iisvideoplus', 'meta_description_video_latest_myvideo', 'می‌توانید ویدیوهای خود را در این صفحه مشاهده کنید.');
        }

        //iisrules
        if (IISSecurityProvider::existPluginKeyInActivePlugins($activePlugins, 'iisrules')) {
            $languageService->addOrUpdateValue($langFaId, 'iisrules', 'security', 'توصیه‌های امنیتی');
            $languageService->addOrUpdateValue($langFaId, 'iisrules', 'privacy', 'توصیه‌های حریم خصوصی');
            $languageService->addOrUpdateValue($langFaId, 'iisrules', 'security_header', 'فهرست توصیه‌های امنیت نرم‌افزار در شبکه‌های اجتماعی');
            $languageService->addOrUpdateValue($langFaId, 'iisrules', 'privacy_header', 'فهرست توصیه‌های حریم خصوصی کاربران در شبکه‌های اجتماعی');
            $languageService->addOrUpdateValue($langFaId, 'iisrules', 'numberingLabel', 'توصیه شماره {$value}:');
            $languageService->addOrUpdateValue($langFaId, 'iisrules', 'bottom_menu_item', 'توصیه به متولیان');
            $languageService->addOrUpdateValue($langFaId, 'iisrules', 'country_header', 'فهرست قوانین و ضوابط اجرایی جمهوری اسلامی ایران در حوزه فضای مجازی');
            $languageService->addOrUpdateValue($langFaId, 'iisrules', 'guideline', 'راهنمای مطالعه توصیه‌ها');
			$languageService->addOrUpdateValue($langFaId, 'iisrules', 'delete_item_warning', 'آیا از حذف این آیتم اطمینان دارید؟');
            $languageService->addOrUpdateValue($langFaId, 'iisrules', 'admin_page_heading', 'تنظیمات افزونه توصیه ها');
            $languageService->addOrUpdateValue($langFaId, 'iisrules', 'admin_page_title', 'تنظیمات افزونه توصیه ها');
            $languageService->addOrUpdateValue($langFaId, 'iisrules', 'guidelineFieldLabel', 'متن راهنمای توصیه‌ها');
            $languageService->addOrUpdateValue($langFaId, 'iisrules', 'filer_by_category', 'دسته‌ها');

        }

        //iisupdateserver
        if (IISSecurityProvider::existPluginKeyInActivePlugins($activePlugins, 'iisupdateserver')) {
            $languageService->addOrUpdateValue($langFaId, 'iisupdateserver', 'plugins_sample', 'افزونه‌ها');
            $languageService->addOrUpdateValue($langFaId, 'iisupdateserver', 'themes_sample', 'پوسته‌ها');
            $languageService->addOrUpdateValue($langFaId, 'iisupdateserver', 'view_versions', 'مشاهده تمامی نسخه‌ها');
            $languageService->addOrUpdateValue($langFaId, 'iisupdateserver', 'download_plugins_description', 'برای بارگیری افزونه‌ها می‌توانید وارد صفحه <a href="{$url}" target="_blank">بارگیری افزونه‌ها</a> شده و افزونه را پیدا کرده و نسخه مورد نظر را بارگیری کنید. همچنین می‌توانید افزونه مورد نظر خود را در لیست زیر پیدا کنید.');
            $languageService->addOrUpdateValue($langFaId, 'iisupdateserver', 'download_themes_description', 'برای بارگیری پوسته‌ها می‌توانید وارد صفحه <a href="{$url}" target="_blank">بارگیری پوسته‌ها</a> شده و پوسته را پیدا کرده و نسخه مورد نظر را بارگیری کنید. همچنین می‌توانید پوسته مورد نظر خود را در لیست زیر پیدا کنید.');
            $languageService->addOrUpdateValue($langFaId, 'iisupdateserver', 'download_core_update_description','با استفاده از نسخه به‌روزرسانی، شما می‌توانید به صورت دستی، شبکه اجتماعی خود را از هر نسخه‌ای به نسخه {$version} به‌روزرسانی کنید.');
            $languageService->addOrUpdateValue($langFaId, 'iisupdateserver', 'view_guideline', 'راهنمای کاربری');
            $languageService->addOrUpdateValue($langFaId, 'iisupdateserver', 'guidelineurl_label', 'نشانی اینترنتی راهنمای کاربری');
			$languageService->addOrUpdateValue($langFaId, 'iisupdateserver', 'check_update_version', 'بررسی به‌روزرسانی');
			$languageService->addOrUpdateValue($langFaId, 'iisupdateserver', 'sha256_label', 'صحت اطلاعات (Sha256)');
            $languageService->addOrUpdateValue($langFaId, 'iisupdateserver', 'admin_page_heading', 'تنظیمات افزونه سرور بروزرسانی افزونه ها و پوسته ها');
            $languageService->addOrUpdateValue($langFaId, 'iisupdateserver', 'admin_page_title', 'تنظیمات افزونه سرور بروزرسانی افزونه ها و پوسته ها');
            $languageService->addOrUpdateValue($langFaId, 'iisupdateserver', 'return', 'بازگشت');
            $languageService->addOrUpdateValue($langFaId, 'iisupdateserver', 'update_guideline', 'نحوه به‌روزرسانی');
            $languageService->addOrUpdateValue($langFaId, 'iisupdateserver', 'download_core_main_description', 'دریافت مجموعه فایل‌های لازم راه‌اندازی کامل یک شبکه اجتماعی (در داخل این مجموعه، افزونه‌های مورد نیاز نیز قرار دارند.)');
			$languageService->addOrUpdateValue($langFaId, 'iisupdateserver', 'delete_item', 'حذف کردن مورد');
			$languageService->addOrUpdateValue($langFaId, 'iisupdateserver', 'buildNumber', 'نسخه');
			$languageService->addOrUpdateValue($langFaId, 'iisupdateserver', 'item_deleted_successfully', 'نسخه مورد نظر با موفقیت حذف شد.');
			$languageService->addOrUpdateValue($langFaId, 'iisupdateserver', 'admin_delete_item_title', 'تنظیمات افزونه سرور به‌روزرسانی افزونه‌ها و پوسته‌ها');
			$languageService->addOrUpdateValue($langFaId, 'iisupdateserver', 'last_version_buildNumber', 'شماره آخرین نسخه:');
			$languageService->addOrUpdateValue($langFaId, 'iisupdateserver', 'admin_check_item_title', 'بررسی به روز رسانی مورد');
			$languageService->addOrUpdateValue($langFaId, 'iisupdateserver', 'check_item', 'به روز رسانی مورد');
        }

        //iisevaluation
        if (IISSecurityProvider::existPluginKeyInActivePlugins($activePlugins, 'iisevaluation')) {
            $languageService->addOrUpdateValue($langFaId, 'iisevaluation', 'degree_header', 'رده‌بندی فعلی: ');
            $languageService->addOrUpdateValue($langFaId, 'iisevaluation', 'results_header', 'نتایج ارزیابی');
            $languageService->addOrUpdateValue($langFaId, 'iisevaluation', 'requirement_suggest', 'ارزیابی موارد پیشنهادی به تفکیک حوزه‌ها');
            $languageService->addOrUpdateValue($langFaId, 'iisevaluation', 'requirement_normal', 'ارزیابی الزامات عادی به تفکیک حوزه‌ها');
            $languageService->addOrUpdateValue($langFaId, 'iisevaluation', 'requirement_important', 'ارزیابی الزامات مهم به تفکیک حوزه‌ها');
            $languageService->addOrUpdateValue($langFaId, 'iisevaluation', 'requirement_fundamental', 'ارزیابی الزامات اساسی به تفکیک حوزه‌ها');
            $languageService->addOrUpdateValue($langFaId, 'iisevaluation', 'user_value', 'امتیاز کسب شده');
            $languageService->addOrUpdateValue($langFaId, 'iisevaluation', 'remaining_value', 'امتیاز باقی‌مانده');
            $languageService->addOrUpdateValue($langFaId, 'iisevaluation', 'total_value', 'حداکثر امتیاز');
            $languageService->addOrUpdateValue($langFaId, 'iisevaluation', 'questions_without_values', 'برای این سوال، پاسخی ایجاد نشده است.');
            $languageService->addOrUpdateValue($langFaId, 'iisevaluation', 'category_questions_header', 'فهرست سوالات');
            $languageService->addOrUpdateValue($langFaId, 'iisevaluation', 'admin_evaluation_settings_heading', 'تنظیمات افزونه ارزیابی');
			$languageService->addOrUpdateValue($langFaId, 'iisevaluation', 'delete_item_warning', 'آیا از حذف این مورد اطمینان دارید؟');
			$languageService->addOrUpdateValue($langFaId, 'iisevaluation', 'active_item_warning', 'آیا از فعال‌سازی این مورد اطمینان دارید؟');
			$languageService->addOrUpdateValue($langFaId, 'iisevaluation', 'lock_item_warning', 'آیا از قفل‌گذاری روی این مورد اطمینان دارید؟');
        }

		
		//mailbox
		if (IISSecurityProvider::existPluginKeyInActivePlugins($activePlugins, 'mailbox')) {
			
			$languageService->addOrUpdateValue($langFaId, 'mailbox', 'settings_desc_show_all_members', 'اندیشه‌ای واقعا بد برای وب‌گاه‌های بزرگ');
			
			$languageService->addOrUpdateValue($langFaId, 'mailbox', 'attache_file_delete_button', 'حذف');
			
			$languageService->addOrUpdateValue($langFaId, 'mailbox', 'bulk_delete_conversations_btn', 'حذف');
			
			$languageService->addOrUpdateValue($langFaId, 'mailbox', 'confirm_delete_im', 'آیا از حذف پیام‌ها اطمینان دارید؟');
			
			$languageService->addOrUpdateValue($langFaId, 'mailbox', 'delete_chat', 'حذف گپ');
			
			$languageService->addOrUpdateValue($langFaId, 'mailbox', 'delete_chat_desc', 'قبل از حذف افزونه پیام‌ها باید کل اطلاعات گپ کاربران حذف شود. این کار ممکن است اندکی زمان‌بر باشد.');
			
			$languageService->addOrUpdateValue($langFaId, 'mailbox', 'delete_confirm_message', 'آیا از حذف گفت‌وگو(ها)ی انتخاب شده اطمینان دارید؟');
			
			$languageService->addOrUpdateValue($langFaId, 'mailbox', 'delete_conversation_button', 'حذف گفت‌وگو');
			
			$languageService->addOrUpdateValue($langFaId, 'mailbox', 'delete_conversation_link', 'حذف');
			
			$languageService->addOrUpdateValue($langFaId, 'mailbox', 'delete_conversation_title', 'حذف تاریخچه');
			
			$languageService->addOrUpdateValue($langFaId, 'mailbox', 'page_title_uninstall', 'حذف افزونه صندوق پیام');
			
			$languageService->addOrUpdateValue($langFaId, 'mailbox', 'plugin_set_for_uninstall', 'حذف افزونه پیام‌ها آغاز شد.');
			
			$languageService->addOrUpdateValue($langFaId, 'mailbox', 'upload_file_delete_fail', 'حذف فایل ناموفق!');
			
			$languageService->addOrUpdateValue($langFaId, 'mailbox', 'upload_file_delete_success', 'حذف فایل موفق!');
			
			$languageService->addOrUpdateValue($langFaId, 'mailbox', 'create_conversation_fail_message', 'پیام ارسال نشد');
			
			$languageService->addOrUpdateValue($langFaId, 'mailbox', 'create_conversation_message', 'پیام ارسال شده');
			
			$languageService->addOrUpdateValue($langFaId, 'mailbox', 'email_notifications_section_label', 'صندوق پستی');
			
			$languageService->addOrUpdateValue($langFaId, 'mailbox', 'new_conversation_link', 'جدید');
			
			$languageService->addOrUpdateValue($langFaId, 'mailbox', 'upload_file_cant_write_file_error', 'نوشتن فایل روی دیسک ناموفق بود.');
			
			$languageService->addOrUpdateValue($langFaId, 'mailbox', 'conversation_label', 'مکالمه');
			
			$languageService->addOrUpdateValue($langFaId, 'mailbox', 'notification_mail_template_html', 'سلام {$username},<br/><br/>شما پیام جدیدی دریافت کردید از {$sendername} در{$site_name}.<br/><br/>برای پاسخ به پیام به <a href="{$replyUrl}">{$replyUrl}</a> بروید.<br/>');
			
			$languageService->addOrUpdateValue($langFaId, 'mailbox', 'notification_mail_template_text', 'سلام {$username},



شما پیام جدیدی دریافت کردید از {$sendername} در {$site_name}.



برای پاسخ دادن به پیام به {$replyUrl} ببروید. ');
			
			$languageService->addOrUpdateValue($langFaId, 'mailbox', 'conversations', 'گفت‌و‌گو');
			
			$languageService->addOrUpdateValue($langFaId, 'mailbox', 'conversation_empty', 'شما تاکنون گفت‌وگویی نداشته‌اید');
			
			$languageService->addOrUpdateValue($langFaId, 'mailbox', 'conversation_item_list_empty', 'فهرست گفت‌وگو شما خالی است');
			
			$languageService->addOrUpdateValue($langFaId, 'mailbox', 'conversation_not_found', 'گفت‌وگو پیدا نشد');
			
			$languageService->addOrUpdateValue($langFaId, 'mailbox', 'delete_conversation_message', 'مکالمه حذف شد.');
			
			$languageService->addOrUpdateValue($langFaId, 'mailbox', 'delete_message', '{$count} مکالمه حذف شد');
			
			$languageService->addOrUpdateValue($langFaId, 'mailbox', 'message_too_long_error', 'متن شما بیش از حد طولانی است. آن‌را تا بیشینه اندازه {$maxLength} کاراکتر فشرده کنید.');
			
			$languageService->addOrUpdateValue($langFaId, 'mailbox', 'message_was_not_authorized', 'پیام نامعتبر بود');
			
			$languageService->addOrUpdateValue($langFaId, 'mailbox', 'settings_desc_show_all_members', 'گزینه‌ای نامناسب برای وب‌گاه‌های بزرگ');
			
			$languageService->addOrUpdateValue($langFaId, 'mailbox', 'status_invisible_label', 'پنهان از دید همه');
			
			$languageService->addOrUpdateValue($langFaId, 'mailbox', 'uninstall_inprogress_desc', '<i>افزونه پیامها</i> در حال حذف شدن است. زمان تقریبی بستگی به تعداد کاربران شما دارد. <br /> حذف شده تا کنون: {$percents}%');
			
			$languageService->addOrUpdateValue($langFaId, 'mailbox', 'upload_file_extension_is_not_allowed', 'این پسوند فایل مجاز نیست.');
			
			$languageService->addOrUpdateValue($langFaId, 'mailbox', 'usercredits_action_read_chat_message', 'خواندن پیام‌های گپ‌');
			
			$languageService->addOrUpdateValue($langFaId, 'mailbox', 'user_is_deleted', 'کاربری که می‌خواهید با او تماس بگیرید حذف شده است');
			
			$languageService->addOrUpdateValue($langFaId, 'mailbox', 'user_is_not_approved', 'کاربری که می‌خواهید با او تماس بگیرید تایید نشده است');
			
			$languageService->addOrUpdateValue($langFaId, 'mailbox', 'user_is_not_verified', 'کاربری که می‌خواهید با او تماس بگیرید تایید نشده است');
			
			$languageService->addOrUpdateValue($langFaId, 'mailbox', 'user_is_suspended', 'کاربری که می‌خواهید با او تماس بگیرید تعلیق شده است');
			
			$languageService->addOrUpdateValue($langFaId, 'mailbox', 'user_is_offline', '[username] برخط نیست');
			
			$languageService->addOrUpdateValue($langFaId, 'mailbox', 'user_went_offline', '{$displayname} از دسترس خارج شده است.');
			
			$languageService->addOrUpdateValue($langFaId, 'mailbox', 'settings_label_send_message_interval_seconds', 'ثانیه');
		}

		//iisdatabackup plugin
		if (IISSecurityProvider::existPluginKeyInActivePlugins($activePlugins, 'iisdatabackup')) {
			$languageService->addOrUpdateValue($langFaId, 'iisdatabackup', 'newsfeed_status', 'وضعیت‌های کاربران (به جر آخرین وضعیت)');
			$languageService->addOrUpdateValue($langFaId, 'iisdatabackup', 'newsfeed_action', 'اعمال انجام شده کاربران در تازه‌ها');
            $languageService->addOrUpdateValue($langFaId, 'iisdatabackup', 'admin_page_heading', 'تنظیمات افزونه پشتیبانی از داده ها');
            $languageService->addOrUpdateValue($langFaId, 'iisdatabackup', 'admin_page_title', 'تنظیمات افزونه پشتیبانی از داده ها');

        }
		
				//birthdays plugin
		if (IISSecurityProvider::existPluginKeyInActivePlugins($activePlugins, 'birthdays')) {
		
			$languageService->addOrUpdateValue($langFaId, 'birthdays', 'my_widget_title', 'تاریخ تولد من');
			
		}
		
			//iissmartscroll plugin
		if (IISSecurityProvider::existPluginKeyInActivePlugins($activePlugins, 'iissmartscroll')) {

            $languageService->addOrUpdateValue($langFaId, 'iissmartscroll', 'admin_settings_title', 'تنظیمات افزونه اسکرول پیشرفته');
			
		}
		
		//iiscontactus plugin
		if (IISSecurityProvider::existPluginKeyInActivePlugins($activePlugins, 'iiscontactus')) {
		
			$languageService->addOrUpdateValue($langFaId, 'iiscontactus', 'admin_dept_title', 'تنظیمات افزونه ارتباط با ما');
			
			$languageService->addOrUpdateValue($langFaId, 'iiscontactus', 'admin_dept_heading', 'تنظیمات افزونه ارتباط با ما');
			
			$languageService->addOrUpdateValue($langFaId, 'iiscontactus', 'after_install_notification', 'لطفا برای ارسال رایانامه یک گروه <a href=\'{$url}\'>ایجاد</a> کنید');
			
			$languageService->addOrUpdateValue($langFaId, 'iiscontactus', 'form_label_from', 'رایانشانی');
			
			$languageService->addOrUpdateValue($langFaId, 'iiscontactus', 'message_sent', 'پیام شما به {$dept} ارسال شد. پاسخ در اسرع وقت ارسال خواهد شد.');
			
			$languageService->addOrUpdateValue($langFaId, 'iiscontactus', 'modified_successfully', 'تغییرات با موفقیت ذخیره شد');
			
		}
		
		//iiscontrolkids plugin
		if (IISSecurityProvider::existPluginKeyInActivePlugins($activePlugins, 'iiscontrolkids')) {
		
			$languageService->addOrUpdateValue($langFaId, 'iiscontrolkids', 'marginTimeLabel', 'مدت زمان باقی‌مانده برای خروج از سن کودکی (بر حسب هفته)');
		
		}
    }

}