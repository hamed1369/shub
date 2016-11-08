<?php

/**
 * Copyright (c) 2016, Yaser Alimardany
 * All rights reserved.
 */

/**
 *
 *
 * @author Yaser Alimardany <yaser.alimardany@gmail.com>
 * @package ow_plugins.iisadvancesearch.classes
 * @since 1.0
 */
class IISADVANCESEARCH_CMP_ConsoleSearch extends BASE_CMP_ConsoleDropdownList
{
    public function __construct()
    {
        parent::__construct( OW::getLanguage()->text('iisadvancesearch', 'search_title'), 'search' );
        $plugin = OW::getPluginManager()->getPlugin('iisadvancesearch');
        $this->setTemplate($plugin->getCmpViewDir() . 'console_dropdown_list.html');
        $this->assign('searchUrl', 'createSearchElements();');

    }

    protected function initJs()
    {
        return $this->consoleItem->getUniqId();
    }

}