<?php

class iiscontrolkidsTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        IISSecurityProvider::createUser('kidtest', 'kid@test.com', '12345678', '1987/3/21', '1');
        IISSecurityProvider::createUser('parenttest', 'parent@test.com', '12345678', '1987/3/21', '1');
    }

    /**
     * Test of blocking users by ip in iisblockingip plugin
     */
    public function testControlKids()
    {
        $service = IISCONTROLKIDS_BOL_Service::getInstance();
        $oldValue = OW::getConfig()->getValue('iiscontrolkids','kidsAge');
        $currentYear = date("Y");
        $age = $currentYear - $oldValue;
        $year = $age-1;
        $this->assertEquals(false, $service->isInChildhood(date_create('13-02-'.$year)));
        $year = $age-2;
        $this->assertEquals(false, $service->isInChildhood(date_create('13-02-'.$year)));
        $year = $age+1;
        $this->assertEquals(true, $service->isInChildhood('13-02-'.$year));
        $year = $age+2;
        $this->assertEquals(true, $service->isInChildhood('13-02-'.$year));

        $kid = BOL_UserService::getInstance()->findByUsername('kidtest');
        $parent = BOL_UserService::getInstance()->findByUsername('parenttest');

        $service->addRelationship($kid->getId(), 'parent@test.com', false);
        $this->assertEquals(true, $service->isParentExist($kid->getId(), $parent->getId()));

        $service->deleteRelationship($kid->getId());
        $this->assertEquals(false, $service->isParentExist($kid->getId(), $parent->getId()));
    }

    public function tearDown()
    {
        IISSecurityProvider::deleteUser('kidtest');
        IISSecurityProvider::deleteUser('parenttest');
    }
}