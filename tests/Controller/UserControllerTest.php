<?php

namespace App\Tests\Controller;

use App\DataFixtures\TaskFixtures;
use App\DataFixtures\UserFixtures;
use App\Repository\UserRepository;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Liip\TestFixturesBundle\Services\DatabaseTools\AbstractDatabaseTool;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserControllerTest extends WebTestCase
{
    private KernelBrowser|null $client = null;
    private UserRepository|null $userRepository = null;
    protected ?AbstractDatabaseTool $databaseTool=null;

    public function setUp():void{
        $this->client = static::createClient();
        $this->userRepository = static::getContainer()->get(UserRepository::class);

        $this->databaseTool = static::getContainer()->get(DatabaseToolCollection::class)->get();
        $this->databaseTool->loadFixtures([
            UserFixtures::class,
            TaskFixtures::class
        ]);
    }

    public function testListUsers(): void
    {
        $testUser = $this->userRepository->findOneByEmail('email@admin.com');
        $this->client->loginUser($testUser);
        $this->client->request('GET', '/users');
        $this->assertResponseStatusCodeSame(200);
    }

    public function testUserCreate(): void
    {
        $testUser = $this->userRepository->findOneByEmail('email@admin.com');
        $this->client->loginUser($testUser);
        $crawler = $this->client->request('GET', '/users/create');
        $this->assertResponseStatusCodeSame(200);
        $form = $crawler->selectButton('Ajouter')->form();
        $form['user[username]'] = 'testUsername';
        $form['user[roles]'] = ['ROLE_USER'];
        $form['user[password][first]'] = 'testPassword';
        $form['user[password][second]'] = 'testPassword';
        $form['user[email]'] = 'testEmail';
        $this->client->submit($form);
        $this->client->followRedirect();
        $this->assertSelectorTextContains('div.alert.alert-success', 'L\'utilisateur a bien été ajouté.');
        $this->assertEquals($this->client->getRequest()->getPathInfo(), '/users');
    }

    public function testUserEdit(): void
    {
        $testUser = $this->userRepository->findOneByEmail('email@admin.com');
        $this->client->loginUser($testUser);
        $crawler = $this->client->request('GET', '/users/'.$testUser->getId().'/edit');
        $this->assertResponseStatusCodeSame(200);
        $form = $crawler->selectButton('Modifier')->form();
        $form['user[username]'] = 'testNewUsername';
        $form['user[roles]'] = ['ROLE_ADMIN'];
        $form['user[password][first]'] = 'testNewPassword';
        $form['user[password][second]'] = 'testNewPassword';
        $form['user[email]'] = 'testNewEmail';
        $this->client->submit($form);
        $this->client->followRedirect();
        $this->assertSelectorTextContains('div.alert.alert-success', 'L\'utilisateur a bien été modifié');
        $this->assertEquals($this->client->getRequest()->getPathInfo(), '/users');
    }
}
