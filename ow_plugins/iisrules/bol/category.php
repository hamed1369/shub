<?php

/**
 * IIS Rules
 */

/**
 * Data Transfer Object for `iisrules` table.
 *
 * @author Yaser Alimardany <yaser.alimardany@gmail.com>
 * @package ow_plugins.iisrules.bol
 * @since 1.0
 */
class IISRULES_BOL_Category extends OW_Entity
{
    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $icon;

    /**
     * @var integer
     */
    public $sectionId;
}