<?php
namespace App\Tests\Controller;

use App\DataFixtures\TaskFixtures;
use App\DataFixtures\UserFixtures;
use Doctrine\ORM\EntityManagerInterface;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Liip\TestFixturesBundle\Services\DatabaseTools\AbstractDatabaseTool;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class SecurityControllerTest extends WebTestCase
{
    private KernelBrowser|null $client = null;
    private Crawler|null $crawler = null;
    protected AbstractDatabaseTool $databaseTool;
    protected EntityManagerInterface $entityManager;
   

    public function setUp():void
    {
        $this->client = static::createClient();
        $urlGenerator = $this->client->getContainer()->get('router.default');
        $this->crawler = $this->client->request(Request::METHOD_GET, $urlGenerator->generate('login'));

        $this->databaseTool = self::getContainer()->get(DatabaseToolCollection::class)->get();
        $this->entityManager = self::getContainer()->get(EntityManagerInterface::class);
        $this->databaseTool->loadFixtures([
            UserFixtures::class,
            TaskFixtures::class
        ]);
    }

    public function testLogin(){

        $form = $this->crawler->selectButton('Se connecter')->form();
        $form['_username']= 'Admin';
        $form['_password']= 'password';
        $this->client->submit($form);
        

        $this->assertResponseStatusCodeSame(302);
        $this->client->followRedirect();
        $this->assertResponseStatusCodeSame(200);
    }

    public function testLoginFailWithBadCredentials(){
        $form = $this->crawler->selectButton('Se connecter')->form();
        $form['_username']= 'Admin';
        $form['_password']= 'wrong password';
        $this->client->submit($form);
        
        $this->client->followRedirect();
        $this->assertSelectorTextContains('div.alert.alert-danger','Invalid credentials.');
        
    }
}