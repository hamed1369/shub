<?php

class iisuserloginTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        IISSecurityProvider::createUser('iisuserlogintest', 'iisuserlogintest@test.com', '12345678', '1987/3/21', '1');
    }

    /**
     * Test of iisevaluation plugin
     */
    public function testLogin()
    {
        $service = IISUSERLOGIN_BOL_Service::getInstance();
        $user = BOL_UserService::getInstance()->findByUsername('iisuserlogintest');
        $service->addLoginDetails($user->getId(), false);
        $userLoginDetails = $service->getUserLoginDetails($user->getId(), false);
        $this->assertEquals(1, sizeof($userLoginDetails));

        $service->addLoginDetails($user->getId(), false);
        $userLoginDetails = $service->getUserLoginDetails($user->getId(), false);
        $this->assertEquals(2, sizeof($userLoginDetails));

        $service->deleteUserLoginDetails($user->getId(), false);
        $userLoginDetails = $service->getUserLoginDetails($user->getId(), false);
        $this->assertEquals(0, sizeof($userLoginDetails));
    }

    public function tearDown()
    {
        IISSecurityProvider::deleteUser('iisuserlogintest');
    }
}