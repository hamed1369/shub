<?php

/**
 * Copyright (c) 2016, Yaser Alimardany
 * All rights reserved.
 */

/**
 *
 * @author Yaser Alimardany <yaser.alimardany@gmail.com>
 * @package ow_plugins.iisevaluation.bol
 * @since 1.0
 */
class IISEVALUATION_BOL_Question extends OW_Entity
{

    public $title;
    public $description;
    public $hasDescribe;
    public $hasFile;
    public $hasVerification;
    public $categoryId;
    public $weight;
    public $level;
    public $order;
}
