<?php

try
{
    define('_OW_', true);
    define('DS', DIRECTORY_SEPARATOR);
    define('OW_DIR_ROOT', dirname(__FILE__) . DS . '..' . DS . '..' . DS);
    define('OW_CRON', true);

    require_once(OW_DIR_ROOT . 'ow_includes' . DS . 'init.php');

    OW::getRouter()->setBaseUrl(OW_URL_HOME);

    date_default_timezone_set(OW::getConfig()->getValue('base', 'site_timezone'));
    OW_Auth::getInstance()->setAuthenticator(new OW_SessionAuthenticator());

    OW::getPluginManager()->initPlugins();
    $event = new OW_Event(OW_EventManager::ON_PLUGINS_INIT);
    OW::getEventManager()->trigger($event);

    IISSecurityProvider::updateLanguages();
}
catch (Exception $ex)
{
    echo "Error in translation (update.php):\n".$ex."\nSolve the problem and run again\n";
    throw $ex;
}
