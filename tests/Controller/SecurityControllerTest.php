<?php
namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityControllerTest extends WebTestCase
{
    private KernelBrowser|null $client = null;
    private Crawler|null $crawler = null;

    public function setUp():void
    {
        $this->client = static::createClient();
        $urlGenerator = $this->client->getContainer()->get('router.default');
        $this->crawler = $this->client->request(Request::METHOD_GET, $urlGenerator->generate('login'));
    }

    public function testLogin(){

        $form = $this->crawler->selectButton('Se connecter')->form();
        $form['_username']= 'User1';
        $form['_password']= 'test';
        $this->client->submit($form);
        

        $this->assertResponseStatusCodeSame(302);
        $this->client->followRedirect();
        $this->assertResponseStatusCodeSame(200);
    }

    public function testLoginFailWithBadCredentials(){
        $form = $this->crawler->selectButton('Se connecter')->form();
        $form['_username']= 'User1';
        $form['_password']= 'testing';
        $this->client->submit($form);
        
        $this->client->followRedirect();
        $this->assertSelectorTextContains('div.alert.alert-danger','Invalid credentials.');
        
    }
}