<?php


OW::getRouter()->addRoute(new OW_Route('iiscontactus.index', 'iiscontact', "IISCONTACTUS_CTRL_Contact", 'index'));
OW::getRouter()->addRoute(new OW_Route('iiscontactus.admin', 'admin/plugins/iiscontactus', "IISCONTACTUS_CTRL_Admin", 'dept'));
OW::getRouter()->addRoute(new OW_Route('iiscontactus.admin.section-id', 'iiscontactus/admin/:sectionId', 'IISCONTACTUS_CTRL_Admin', 'dept'));
OW::getRouter()->addRoute(new OW_Route('iiscontactus.admin.data', 'iiscontactus/admin/data', 'IISCONTACTUS_CTRL_Admin', 'data'));
function iiscontactus_handler_after_install( BASE_CLASS_EventCollector $event )
{
    if ( count(IISCONTACTUS_BOL_Service::getInstance()->getDepartmentList()) < 1 )
    {
        $url = OW::getRouter()->urlForRoute('iiscontactus.admin');
        $event->add(OW::getLanguage()->text('iiscontactus', 'after_install_notification', array('url' => $url)));
    }
}

OW::getEventManager()->bind('admin.add_admin_notification', 'iiscontactus_handler_after_install');


function iiscontactus_ads_enabled( BASE_CLASS_EventCollector $event )
{
    $event->add('iiscontactus');
}

OW::getEventManager()->bind('ads.enabled_plugins', 'iiscontactus_ads_enabled');

OW::getRequestHandler()->addCatchAllRequestsExclude('base.suspended_user', 'IISCONTACTUS_CTRL_Contact');