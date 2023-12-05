<?php

namespace App\Tests\Controller;

use App\DataFixtures\TaskFixtures;
use App\DataFixtures\UserFixtures;
use App\Entity\User;
use App\Repository\TaskRepository;
use App\Repository\UserRepository;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Liip\TestFixturesBundle\Services\DatabaseTools\AbstractDatabaseTool;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Exception\LogicException;
use Symfony\Component\DomCrawler\Crawler;

use function PHPUnit\Framework\assertEquals;

class TaskControllerTest extends WebTestCase
{
    private KernelBrowser|null $client = null;
    private Crawler|null $crawler = null;
    private UserRepository|null $userRepository = null;
    private TaskRepository|null $taskRepository = null;
    protected ?AbstractDatabaseTool $databaseTool = null;
    private User|null $roleAdmin = null;
    private User|null $roleUser = null;

    public function setUp(): void
    {
        $this->client = static::createClient();
        $this->userRepository = static::getContainer()->get(UserRepository::class);
        $this->taskRepository = static::getContainer()->get(TaskRepository::class);
        $this->databaseTool = static::getContainer()->get(DatabaseToolCollection::class)->get();
        $this->databaseTool->loadFixtures([
            UserFixtures::class,
            TaskFixtures::class,
        ]);
        $this->roleAdmin = $this->userRepository->findOneByEmail('email@admin.com');
        $this->roleUser = $this->userRepository->findOneByEmail('email@user.com');
    }

    public function testDisplayTaskList()
    {
        $this->client->request('GET', '/tasks');
        $this->assertResponseStatusCodeSame(200);
    }

    public function testCreate()
    {
        $this->client->loginUser($this->roleAdmin);
        $crawler = $this->client->request('GET', '/tasks/create');
        $this->assertResponseStatusCodeSame(200);
        $form = $crawler->selectButton('Ajouter')->form();
        $form['task[title]'] = 'testTitle';
        $form['task[content]'] = 'testContent';
        $this->client->submit($form);
        $this->client->followRedirect();
        $this->assertSelectorTextContains('div.alert.alert-success', 'La tâche a été bien été ajoutée.');
    }

    public function testCreateNotLogged()
    {
        $this->client->request('GET', '/tasks/create');
        $this->assertResponseStatusCodeSame(302);
        $this->client->followRedirect();
        $this->assertResponseStatusCodeSame(200);
        $this->assertEquals($this->client->getRequest()->getPathInfo(), '/login');
    }

    public function testCreateWithoutTitle()
    {
        $this->client->loginUser($this->roleAdmin);
        $crawler = $this->client->request('GET', '/tasks/create');
        $this->assertResponseStatusCodeSame(200);
        $form = $crawler->selectButton('Ajouter')->form();
        $form['task[content]'] = 'testContent';
        $this->client->submit($form);
        $this->expectException(LogicException::class);
        $this->client->followRedirect();
        $this->assertEquals($this->client->getRequest()->getPathInfo(), '/tasks/create');
    }

    public function testCreateWithoutContent()
    {
        $this->client->loginUser($this->roleAdmin);
        $crawler = $this->client->request('GET', '/tasks/create');
        $this->assertResponseStatusCodeSame(200);
        $form = $crawler->selectButton('Ajouter')->form();
        $form['task[title]'] = 'testTitle';
        $this->client->submit($form);
        $this->expectException(LogicException::class);
        $this->client->followRedirect();
        $this->assertEquals($this->client->getRequest()->getPathInfo(), '/tasks/create');
    }

    public function testEdit()
    {
        $testTask = $this->taskRepository->findOneByTitle('Task 1');
        $this->client->loginUser($this->roleAdmin);
        $crawler = $this->client->request('GET', '/tasks/'.$testTask->getId().'/edit');

        $form = $crawler->selectButton('Modifier')->form();
        $form['task[title]'] = 'testNewTitle';
        $form['task[content]'] = 'testNewContent';
        $this->client->submit($form);
        $this->client->followRedirect();
        $this->assertSelectorTextContains('div.alert.alert-success', 'La tâche a bien été modifiée.');

        $this->assertResponseStatusCodeSame(200);
    }

    public function testEditWontChangeUserRelated()
    {
        $testTask = $this->taskRepository->findOneByTitle('Task 1');
        $this->client->loginUser($this->roleUser);
        $crawler = $this->client->request('GET', '/tasks/'.$testTask->getId().'/edit');
        $form = $crawler->selectButton('Modifier')->form();
        $form['task[title]'] = 'testNewTitle';
        $form['task[content]'] = 'testNewContent';
        $this->client->submit($form);
        $this->client->followRedirect();

        $testTaskUpdate = $this->taskRepository->findOneByTitle('testNewTitle');

        assertEquals('testNewTitle', $testTaskUpdate->getTitle());
        assertEquals($this->roleAdmin->getUsername(), $testTaskUpdate->getUser()->getUsername());
    }

    public function testToggle()
    {
        $testTask = $this->taskRepository->findOneByTitle('Task 1');
        $this->client->loginUser($this->roleAdmin);
        $this->client->request('GET', '/tasks/'.$testTask->getId().'/toggle');
        $this->client->followRedirect();
        $this->assertResponseStatusCodeSame(200);
    }

    public function testDelete()
    {
        $testTask = $this->taskRepository->findOneByTitle('Task 1');
        $this->client->loginUser($this->roleAdmin);
        $this->client->request('GET', '/tasks/'.$testTask->getId().'/delete');
        $this->client->followRedirect();
        $this->assertResponseStatusCodeSame(200);
    }

    public function testUsersCanOnlyDeleteTheirOwnTasks()
    {
        $testTask = $this->taskRepository->findOneByTitle('Task by User');
        $this->client->loginUser($this->roleAdmin);
        $this->client->request('GET', '/tasks/'.$testTask->getId().'/delete');

        $this->assertResponseStatusCodeSame(302);
        $this->client->followRedirect();
        $this->assertEquals($this->client->getRequest()->getPathInfo(), '/login');
        $testTaskNotDelete = $this->taskRepository->findOneByTitle('Task by User');
        $this->assertEquals($testTaskNotDelete->getTitle(), $testTask->getTitle());

        $this->client->loginUser($this->roleUser);
        $this->client->request('GET', '/tasks/'.$testTask->getId().'/delete');

        $this->assertResponseStatusCodeSame(302);
        $this->client->followRedirect();
        $this->assertEquals($this->client->getRequest()->getPathInfo(), '/tasks');
        $testTaskDelete = $this->taskRepository->findOneByTitle('Task by User');
        $this->assertNull($testTaskDelete);
    }
}
