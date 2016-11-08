<?php

$logger = Updater::getLogger();

try
{
    //Remove default widget in mobile
    IISSecurityProvider::deleteWidgetUsingComponentPlaceUniqueName("admin-5295f2e03ec8a");

    //Remove default widget in mobile
    IISSecurityProvider::deleteWidgetUsingComponentPlaceUniqueName("admin-5295f2e40db5c");

    BOL_ComponentAdminService::getInstance()->clearAllCache();
}
catch (Exception $e)
{
    $logger->addEntry(json_encode($e));
}