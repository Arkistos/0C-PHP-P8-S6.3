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
    private Crawler|null $crawler = null;
    protected ?AbstractDatabaseTool $databaseTool=null;


    public function setUp(): void
    {
        $this->client = static::createClient();
        $urlGenerator = $this->client->getContainer()->get('router.default');
        $this->crawler = $this->client->request(Request::METHOD_GET, $urlGenerator->generate('login'));

        $this->databaseTool = static::getContainer()->get(DatabaseToolCollection::class)->get();
        $this->databaseTool->loadFixtures([
            UserFixtures::class,
            TaskFixtures::class
        ]);
    }

    public function testLogin()
    {
        $form = $this->crawler->selectButton('Se connecter')->form();
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
        $form = $this->crawler->selectButton('Se connecter')->form();
        $form['_username']= 'Admin';
        $form['_password']= 'wrong password';
        $this->client->submit($form);

        $this->client->followRedirect();
        $this->assertSelectorTextContains('div.alert.alert-danger', 'Invalid credentials.');
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
    }
}
