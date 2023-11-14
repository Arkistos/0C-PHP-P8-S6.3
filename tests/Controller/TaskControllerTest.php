<?php

namespace App\Tests\Controller;

use App\DataFixtures\TaskFixtures;
use App\DataFixtures\UserFixtures;
use App\Repository\TaskRepository;
use App\Repository\UserRepository;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Liip\TestFixturesBundle\Services\DatabaseTools\AbstractDatabaseTool;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpFoundation\Request;

class TaskControllerTest extends WebTestCase
{
    private KernelBrowser|null $client = null;
    private Crawler|null $crawler = null;
    private UserRepository|null $userRepository = null;
    private TaskRepository|null $taskRepository =null;
    protected ?AbstractDatabaseTool $databaseTool=null;

    public function setUp(): void
    {
        $this->client = static::createClient();
        $this->userRepository = static::getContainer()->get(UserRepository::class);
        $this->taskRepository = static::getContainer()->get(TaskRepository::class);
        $this->databaseTool = static::getContainer()->get(DatabaseToolCollection::class)->get();
        $this->databaseTool->loadFixtures([
            UserFixtures::class,
            TaskFixtures::class
        ]);
    }

    public function testDisplayTaskList()
    {
        $this->client->request('GET', '/tasks');
        $this->assertResponseStatusCodeSame(200);
    }

    public function testCreate()
    {
        $testUser = $this->userRepository->findOneByEmail('email@admin.com');
        $this->client->loginUser($testUser);
        $crawler  = $this->client->request('GET', '/tasks/create');
        $this->assertResponseStatusCodeSame(200);
        $form = $crawler->selectButton('Ajouter')->form();
        $form['task[title]']='testTitle';
        $form['task[content]'] = 'testContent';
        $this->client->submit($form);
        $this->client->followRedirect();
        $this->assertSelectorTextContains('div.alert.alert-success', 'La tâche a été bien été ajoutée.');
    }

    public function testEdit(){
        $testUser = $this->userRepository->findOneByEmail('email@admin.com');
        $testTask = $this->taskRepository->findOneByTitle('Task 1');
        $this->client->loginUser($testUser);
        $crawler = $this->client->request('GET', '/tasks/'.$testTask->getId().'/edit');

        $form = $crawler->selectButton('Modifier')->form();
        $form['task[title]']='testNewTitle';
        $form['task[content]'] = 'testNewContent';
        $this->client->submit($form);
        $this->client->followRedirect();
        $this->assertSelectorTextContains('div.alert.alert-success', 'La tâche a bien été modifiée.');

        $this->assertResponseStatusCodeSame(200);
    }

    public function testToggle(){
        $testUser = $this->userRepository->findOneByEmail('email@admin.com');
        $testTask = $this->taskRepository->findOneByTitle('Task 1');
        $this->client->loginUser($testUser);
        $this->client->request('GET', '/tasks/'.$testTask->getId().'/toggle');
        $this->client->followRedirect();
        $this->assertResponseStatusCodeSame(200);
    }

    public function testDelete(){
        $testUser = $this->userRepository->findOneByEmail('email@admin.com');
        $testTask = $this->taskRepository->findOneByTitle('Task 1');
        $this->client->loginUser($testUser);
        $this->client->request('GET', '/tasks/'.$testTask->getId().'/delete');
        $this->client->followRedirect();
        $this->assertResponseStatusCodeSame(200);
    }
}
