<?php

class iisPasswordChangeIntervalTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
    }

    /**
     * Test of validating and invalidating of user's password
     */
    public function testInvalidateAndValidateUsers()
    {
        //Invalidate all user's password
        $service = IISPASSWORDCHANGEINTERVAL_BOL_Service::getInstance();
        $service->setAllUsersPasswordInvalid(false);
        $invalidUsers = $service->getAllUsersInvalid();
        $numberOfUsers = BOL_UserService::getInstance()->count(true);
        $users = BOL_UserService::getInstance()->findList(0, $numberOfUsers, true);
        $this->assertEquals(count($invalidUsers), count($users));

        //Check non of users should be invalid
        foreach ($invalidUsers as $invalidUser)
        {
            $service->setUserPasswordValid($invalidUser->username);
        }
        $invalidUsers = $service->getAllUsersInvalid();
        $this->assertEquals(count($invalidUsers), 0);

        //Check all users should be valid
        $validUsers = $service->getAllUsersValid();
        $this->assertEquals(count($validUsers), count($users));

        $service->deleteAllUsersFromPasswordValidation();
    }
}