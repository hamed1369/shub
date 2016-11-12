<?php

class iisblockingipTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
    }

    /**
     * Test of blocking users by ip in iisblockingip plugin
     */
    public function testBlocking()
    {
        $oldValue = OW::getConfig()->getValue('iisblockingip', IISBLOCKINGIP_BOL_Service::TRY_COUNT_BLOCK);
        OW::getConfig()->saveConfig('iisblockingip', IISBLOCKINGIP_BOL_Service::TRY_COUNT_BLOCK, 3);

        $service = IISBLOCKINGIP_BOL_Service::getInstance();
        $service->deleteBlockCurrentIp();

        $this->assertEquals(0, $service->getUserTryCount());
        $this->assertEquals(false, $service->isLocked());

        $service->bruteforceTrack();
        $this->assertEquals(1, $service->getUserTryCount());
        $this->assertEquals(false, $service->isLocked());

        $service->bruteforceTrack();
        $this->assertEquals(2, $service->getUserTryCount());
        $this->assertEquals(false, $service->isLocked());

        $service->bruteforceTrack();
        $this->assertEquals(3, $service->getUserTryCount());
        $this->assertEquals(false, $service->isLocked());

        $service->bruteforceTrack();
        $this->assertEquals(4, $service->getUserTryCount());
        $this->assertEquals(true, $service->isLocked());

        $service->deleteBlockCurrentIp();
        $this->assertEquals(0, $service->getUserTryCount());
        $this->assertEquals(false, $service->isLocked());

        OW::getConfig()->saveConfig('iisblockingip', IISBLOCKINGIP_BOL_Service::TRY_COUNT_BLOCK, $oldValue);
    }
    public function tearDown()
    {
        echo "iisBlockIPTest Abbasloo tearDown \n";
        parent::tearDown(); // TODO: Change the autogenerated stub
    }
}