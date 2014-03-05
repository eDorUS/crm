<?php

namespace OroCRM\Bundle\TaskBundle\Tests\Functional\Controller\Api\Rest;

use Oro\Bundle\TestFrameworkBundle\Test\WebTestCase;
use Oro\Bundle\TestFrameworkBundle\Test\ToolsAPI;
use Oro\Bundle\TestFrameworkBundle\Test\Client;

/**
 * @outputBuffering enabled
 * @db_isolation
 * @db_reindex
 */
class RestTaskTest extends WebTestCase
{
    /** @var Client */
    protected $client;

    public function setUp()
    {
        $this->client = static::createClient(array(), ToolsAPI::generateWsseHeader());
    }

    public function testCreate()
    {
        $request = array(
            'task' => array(
                'subject' => 'New task',
                'description' => 'New description',
                'dueDate' => '2014-03-04T20:00:00+0000',
                'taskPriority' => 'high',
                'assignedTo' => '1',
                'owner' => '1'
            )
        );

        $this->client->request('POST', $this->client->generate('oro_api_post_task'), $request);
        $result = $this->client->getResponse();
        ToolsAPI::assertJsonResponse($result, 201);

        $task = json_decode($result->getContent(), true);
        $this->assertEquals($task['subject'], $request['task']['subject']);
    }

    /**
     * @depends testCreate
     * @return array
     */
    public function testCget()
    {
        $this->markTestSkipped();

        $this->markTestSkipped();

        $this->client->request('GET', $this->client->generate('oro_api_get_tasks'));
        $result = $this->client->getResponse();
        ToolsAPI::assertJsonResponse($result, 200);

        $tasks = ToolsAPI::jsonToArray($result->getContent());
        $this->assertCount(1, $tasks);

        return reset($tasks);
    }

    /**
     * @depends testCget
     * @param array $expectedTask
     */
    public function testGet($expectedTask)
    {
        $this->client->request(
            'GET',
            $this->client->generate('oro_api_get_task', array('id' => $expectedTask['id']))
        );
        $result = $this->client->getResponse();
        ToolsAPI::assertJsonResponse($result, 200);

        $task = json_decode($result->getContent(), true);
        $this->assertEquals($expectedTask, $task);
    }

    /**
     * @depends testCget
     * @param array $task
     */
    public function testPut($task)
    {
        $updatedTask = array('title' => 'Updated title');
        $this->client->request(
            'PUT',
            $this->client->generate('oro_api_put_task', array('id' => $task['id'])),
            array('task' => $updatedTask)
        );
        $result = $this->client->getResponse();
        ToolsAPI::assertJsonResponse($result, 200);

        $task = json_decode($result->getContent(), true);
        $this->assertEquals($task['title'], $updatedTask['title']);
    }

    /**
     * @depends testCget
     * @param array $task
     */
    public function testDelete($task)
    {
        $this->client->request(
            'DELETE',
            $this->client->generate('oro_api_delete_task', array('id' => $task['id']))
        );
        $result = $this->client->getResponse();
        ToolsAPI::assertJsonResponse($result, 204);
        $this->client->request(
            'GET',
            $this->client->generate('oro_api_get_task', array('id' => $task['id']))
        );
        $result = $this->client->getResponse();
        ToolsAPI::assertJsonResponse($result, 404);
    }
}
