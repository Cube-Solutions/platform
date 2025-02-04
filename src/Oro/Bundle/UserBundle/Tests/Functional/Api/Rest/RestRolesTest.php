<?php

namespace Oro\Bundle\UserBundle\Tests\Functional\Api\Rest;

use Oro\Bundle\TestFrameworkBundle\Test\WebTestCase;

class RestRolesTest extends WebTestCase
{
    protected function setUp(): void
    {
        $this->initClient(array(), $this->generateWsseAuthHeader());
    }

    /**
     * @return array $request
     */
    public function testCreateRole()
    {
        $roleName = 'Role_' . mt_rand(100, 500);
        $request  = array(
            "role" => array(
                "label" => $roleName,
            )
        );
        $this->client->jsonRequest('POST', $this->getUrl('oro_api_post_role'), $request);
        $result = $this->client->getResponse();
        $this->assertJsonResponseStatusCodeEquals($result, 201);

        return $request;
    }

    /**
     * @depends testCreateRole
     *
     * @param array $request
     */
    public function testGetRoleByName($request)
    {
        $this->client->jsonRequest(
            'GET',
            $this->getUrl('oro_api_get_role_byname', array('name' => $request['role']['label']))
        );
        $result = $this->client->getResponse();
        $this->assertJsonResponseStatusCodeEquals($result, 200);
    }

    /**
     * @depends testCreateRole
     *
     * @param  array $request
     *
     * @return int   $roleId
     */
    public function testGetRoleById($request)
    {
        $this->client->jsonRequest(
            'GET',
            $this->getUrl('oro_api_get_roles', ['limit' => 20])
        );

        $result = $this->getJsonResponseContent($this->client->getResponse(), 200);

        $role = array_filter(
            $result,
            function ($a) use ($request) {
                return $a['label'] === $request['role']['label'];
            }
        );
        $this->assertNotEmpty($role, 'Created role is not in roles list');

        $roleId = reset($role)['id'];

        $this->client->jsonRequest(
            'GET',
            $this->getUrl('oro_api_get_role', array('id' => $roleId))
        );
        $result = $this->client->getResponse();
        $this->assertJsonResponseStatusCodeEquals($result, 200);

        return $roleId;
    }

    /**
     * @depends testGetRoleById
     * @depends testCreateRole
     *
     * @param int $roleId
     * @param array $request
     */
    public function testUpdateRole($roleId, $request)
    {
        $request['role']['label'] .= '_Update';
        $this->client->jsonRequest(
            'PUT',
            $this->getUrl('oro_api_put_role', array('id' => $roleId)),
            $request
        );
        $result = $this->client->getResponse();
        $this->assertEmptyResponseStatusCodeEquals($result, 204);

        $this->client->jsonRequest(
            'GET',
            $this->getUrl('oro_api_get_role', array('id' => $roleId))
        );

        $result = $this->getJsonResponseContent($this->client->getResponse(), 200);

        $this->assertEquals($result['label'], $request['role']['label'], 'Role does not updated');
    }

    /**
     * @depends testGetRoleById
     */
    public function testDeleteRole($roleId)
    {
        $this->client->jsonRequest(
            'DELETE',
            $this->getUrl('oro_api_delete_role', array('id' => $roleId))
        );
        $result = $this->client->getResponse();
        $this->assertEmptyResponseStatusCodeEquals($result, 204);

        $this->client->jsonRequest(
            'GET',
            $this->getUrl('oro_api_get_role', array('id' => $roleId))
        );
        $result = $this->client->getResponse();
        $this->assertJsonResponseStatusCodeEquals($result, 404);
    }
}
