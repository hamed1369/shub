<?php

/**
 *
 * @author Yaser Alimardany <yaser.alimardany@gmail.com>
 * @package ow_plugins.iisterms.classes
 * @since 1.0
 */
class IISTERMS_MCLASS_EventHandler
{
    /**
     * Singleton instance.
     *
     * @var IISTERMS_MCLASS_EventHandler
     */
    private static $classInstance;

    /**
     * Returns an instance of class (singleton pattern implementation).
     *
     * @return IISTERMS_MCLASS_EventHandler
     */
    public static function getInstance()
    {
        if ( self::$classInstance === null )
        {
            self::$classInstance = new self();
        }

        return self::$classInstance;
    }

    /**
     *
     * @var IISTERMS_BOL_Service
     */
    private $service;

    private function __construct()
    {
        $this->service = IISTERMS_BOL_Service::getInstance();
    }
    
    public function genericInit()
    {
        $service = IISTERMS_BOL_Service::getInstance();
        OW::getEventManager()->bind('notifications.collect_actions', array($service, 'on_notify_actions'));
        OW::getEventManager()->bind(IISEventManager::ON_RENDER_JOIN_FORM, array($service, 'on_render_join_form'));
        OW::getEventManager()->bind('mobile.notifications.on_item_render', array($this, 'onNotificationRender'));
    }

    public function onNotificationRender( OW_Event $e )
    {
        $params = $e->getParams();
        if ( $params['pluginKey'] != 'iisterms' || $params['entityType'] != 'iisterms-terms' )
        {
            return;
        }

        if(!isset($params['data']['string']['vars']['value1']) || !isset($params['data']['string']['vars']['value2']) ||
        !isset($params['data']['url']))
        {
            return;
        }
        else{
            $title =$params['data']['string']['vars']['value1'];
            $size = $params['data']['string']['vars']['value2'];
        }
        $langVars = array(
            'value1' => $title,
            'url' => $params['data']['url'],
            'value2' => $size
        );

        $data['string'] = array('key' => 'iisterms+mobile_notification_content', 'vars' => $langVars);
        $e->setData($data);



    }

}