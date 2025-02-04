<?php

namespace Oro\Bundle\UserBundle\Tests\Functional\Api\Rest;

use Oro\Bundle\TestFrameworkBundle\Test\WebTestCase;
use Oro\Bundle\UserBundle\Tests\Functional\Api\DataFixtures\LoadUserData;

class RestUsersACLTest extends WebTestCase
{
    const DEFAULT_USER_ID = '1';

    protected function setUp(): void
    {
        $this->initClient();
        $this->loadFixtures(array('Oro\Bundle\UserBundle\Tests\Functional\Api\DataFixtures\LoadUserData'));
    }

    public function testCreateUser()
    {
        $request = array(
            "user" => array(
                "username" => 'user_' . mt_rand(),
                "email" => 'test_'  . mt_rand() . '@test.com',
                "enabled" => '1',
                "plainPassword" => '1231231q',
                "firstName" => "firstName",
                "lastName" => "lastName",
                "userRoles" => array("1")
            )
        );

        $this->client->jsonRequest(
            'POST',
            $this->getUrl('oro_api_post_user'),
            $request,
            $this->generateWsseAuthHeader(LoadUserData::USER_NAME, LoadUserData::USER_PASSWORD)
        );
        $result = $this->client->getResponse();
        $this->assertJsonResponseStatusCodeEquals($result, 403);
    }

    public function testGetUsers()
    {
        //get user id
        $this->client->jsonRequest(
            'GET',
            $this->getUrl('oro_api_get_users', ['limit' => 100]),
            [],
            $this->generateWsseAuthHeader(LoadUserData::USER_NAME, LoadUserData::USER_PASSWORD)
        );
        $result = $this->client->getResponse();
        $this->assertJsonResponseStatusCodeEquals($result, 403);
    }

    public function testGetUser()
    {
        //open user by id
        $this->client->jsonRequest(
            'GET',
            $this->getUrl('oro_api_get_user', array('id' => self::DEFAULT_USER_ID)),
            array(),
            $this->generateWsseAuthHeader(LoadUserData::USER_NAME, LoadUserData::USER_PASSWORD)
        );
        $result = $this->client->getResponse();
        $this->assertJsonResponseStatusCodeEquals($result, 403);
    }

    public function testUpdateUser()
    {
        $request = array(
            "user" => array(
                "username" => 'user_' . mt_rand(),
                "email" => 'test_'  . mt_rand() . '@test.com',
                "enabled" => '1',
                "firstName" => "firstName",
                "lastName" => "lastName",
                "roles" => array("1")
            )
        );

        $this->client->jsonRequest(
            'PUT',
            $this->getUrl('oro_api_put_user', array('id' => self::DEFAULT_USER_ID)),
            $request,
            $this->generateWsseAuthHeader(LoadUserData::USER_NAME, LoadUserData::USER_PASSWORD)
        );
        $result = $this->client->getResponse();
        $this->assertJsonResponseStatusCodeEquals($result, 403);
    }

    public function testDeleteUser()
    {
        $this->client->jsonRequest(
            'DELETE',
            $this->getUrl('oro_api_delete_user', array('id' => self::DEFAULT_USER_ID)),
            array(),
            $this->generateWsseAuthHeader(LoadUserData::USER_NAME, LoadUserData::USER_PASSWORD)
        );
        $result = $this->client->getResponse();
        $this->assertJsonResponseStatusCodeEquals($result, 403);
    }
}
