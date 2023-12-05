<?php

namespace App\Tests\Controller;

use App\DataFixtures\TaskFixtures;
use App\DataFixtures\UserFixtures;
use App\Entity\User;
use App\Repository\UserRepository;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Liip\TestFixturesBundle\Services\DatabaseTools\AbstractDatabaseTool;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserControllerTest extends WebTestCase
{
    private KernelBrowser|null $client = null;
    private UserRepository|null $userRepository = null;
    protected ?AbstractDatabaseTool $databaseTool = null;
    private User|null $roleAdmin = null;
    private User|null $roleUser = null;

    public function setUp(): void
    {
        $this->client = static::createClient();
        $this->userRepository = static::getContainer()->get(UserRepository::class);

        $this->databaseTool = static::getContainer()->get(DatabaseToolCollection::class)->get();
        $this->databaseTool->loadFixtures([
            UserFixtures::class,
            TaskFixtures::class,
        ]);
        $this->roleAdmin = $this->userRepository->findOneByEmail('email@admin.com');
        $this->roleUser = $this->userRepository->findOneByEmail('email@user.com');
    }

    public function testListUsers(): void
    {
        $this->client->loginUser($this->roleAdmin);
        $this->client->request('GET', '/users');
        $this->assertResponseStatusCodeSame(200);
    }

    public function testListUsersCantBeAccessByRoleUser(): void
    {
        $this->client->loginUser($this->roleUser);
        $this->client->request('GET', '/users');
        $this->assertResponseStatusCodeSame(302);
        $this->client->followRedirect();
        $this->assertEquals($this->client->getRequest()->getPathInfo(), '/login');
    }

    public function testUserCreate(): void
    {
        $this->client->loginUser($this->roleAdmin);
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
        $this->client->loginUser($this->roleAdmin);
        $crawler = $this->client->request('GET', '/users/'.$this->roleAdmin->getId().'/edit');
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
