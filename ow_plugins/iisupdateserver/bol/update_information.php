<?php

/**
 * IIS Terms
 */

/**
 * Data Transfer Object for `iisupdateserver` table.
 *
 * @author Yaser Alimardany <yaser.alimardany@gmail.com>
 * @package ow_plugins.iisupdateserver.bol
 * @since 1.0
 */
class IISUPDATESERVER_BOL_UpdateInformation extends OW_Entity
{
    /**
     * @var integer
     */
    public $time;

    /**
     * @var string
     */
    public $buildNumber;

    /**
     * @var string
     */
    public $key;

    /**
     * @var string
     */
    public $version;

}