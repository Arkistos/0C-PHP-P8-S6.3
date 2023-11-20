<?php

namespace App\Tests\Controller;

use App\DataFixtures\TaskFixtures;
use App\DataFixtures\UserFixtures;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Liip\TestFixturesBundle\Services\DatabaseTools\AbstractDatabaseTool;
use Monolog\Logger;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpFoundation\Request;

class SecurityControllerTest extends WebTestCase
{
    private KernelBrowser|null $client = null;
    protected ?AbstractDatabaseTool $databaseTool=null;


    public function setUp(): void
    {
        $this->client = static::createClient();

        $this->databaseTool = static::getContainer()->get(DatabaseToolCollection::class)->get();
        $this->databaseTool->loadFixtures([
            UserFixtures::class,
            TaskFixtures::class
        ]);
    }

    public function testLogin()
    {
        $crawler = $this->client->request('GET', '/login');
        $form = $crawler->selectButton('Se connecter')->form();
        $form['_username']= 'Admin';
        $form['_password']= 'password';
        $this->client->submit($form);
        $this->assertResponseStatusCodeSame(302);
        $this->client->followRedirect();
        $this->assertResponseStatusCodeSame(200);
        $this->assertEquals($this->client->getRequest()->getPathInfo(), '/');
    }

    public function testLoginFailWithBadCredentials()
    {
        $crawler = $this->client->request('GET', '/login');
        $form = $crawler->selectButton('Se connecter')->form();
        $form['_username']= 'Admin';
        $form['_password']= 'wrong password';
        $this->client->submit($form);

        $this->client->followRedirect();
        $this->assertSelectorTextContains('div.alert.alert-danger', 'Invalid credentials.');
        $this->assertEquals($this->client->getRequest()->getPathInfo(), '/login');
    }

    public function testLogout(){
        $this->client->request('GET', '/logout');
        $this->assertResponseStatusCodeSame(302);
        $this->client->followRedirect();
        $this->assertResponseStatusCodeSame(200);
        $this->assertEquals($this->client->getRequest()->getPathInfo(), '/');
    }


    protected function tearDown(): void
    {
        parent::tearDown();
        unset($this->databaseTool);
        unset($this->client);
    }
}
