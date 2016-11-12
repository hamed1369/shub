<?php


class IISTERMS_CLASS_ActionController extends OW_ActionController
{
    /**
     *
     * @var IISTERMS_BOL_Service
     */
    protected $service;

    public function init()
    {
        $this->service = IISTERMS_BOL_Service::getInstance();
    }
}

