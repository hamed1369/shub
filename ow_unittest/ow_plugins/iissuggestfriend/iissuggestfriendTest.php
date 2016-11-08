<?php

class iissuggestfriendTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        IISSecurityProvider::createUser('iissuggestfriend_user1', 'iissuggestfriend_user1@test.com', '12345678', '1987/3/21', '1');
        IISSecurityProvider::createUser('iissuggestfriend_user2', 'iissuggestfriend_user2@test.com', '12345678', '1987/3/21', '1');
        IISSecurityProvider::createUser('iissuggestfriend_user3', 'iissuggestfriend_user3@test.com', '12345678', '1987/3/21', '1');
    }

    /**
     * Test of iissuggestfriend plugin
     */
    public function testSuggestFriend()
    {

        $user1 = BOL_UserService::getInstance()->findByUsername('iissuggestfriend_user1');
        $user2 = BOL_UserService::getInstance()->findByUsername('iissuggestfriend_user2');
        $user3 = BOL_UserService::getInstance()->findByUsername('iissuggestfriend_user3');

        FRIENDS_BOL_Service::getInstance()->request($user1->getId(), $user2->getId());
        FRIENDS_BOL_Service::getInstance()->accept($user2->getId(), $user1->getId());

        FRIENDS_BOL_Service::getInstance()->request($user1->getId(), $user3->getId());
        FRIENDS_BOL_Service::getInstance()->accept($user3->getId(), $user1->getId());

        $suggestedFriendsToUser2 = IISSUGGESTFRIEND_CLASS_Suggest::getInstance()->getSuggestedFriends($user2->getId());
        $this->assertEquals(true, in_array($user3->getId(), $suggestedFriendsToUser2));

        $suggestedFriendsToUser3 = IISSUGGESTFRIEND_CLASS_Suggest::getInstance()->getSuggestedFriends($user3->getId());
        $this->assertEquals(true, in_array($user2->getId(), $suggestedFriendsToUser3));
    }

    public function tearDown()
    {
        IISSecurityProvider::deleteUser('iissuggestfriend_user1');
        IISSecurityProvider::deleteUser('iissuggestfriend_user2');
        IISSecurityProvider::deleteUser('iissuggestfriend_user3');
    }
}