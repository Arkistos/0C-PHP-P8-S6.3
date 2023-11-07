<?php
namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DefaultControllerTest extends WebTestCase
{
    private KernelBrowser|null $client = null;

    public function setUp():void
    {
        $this->client = static::createClient();
        $urlGenerator = $this->client->getContainer()->get('router.default');
    }

    public function testpage(){
        $urlGenerator = $this->client->getContainer()->get('router.default');
        $this->client->request(Request::METHOD_GET, $urlGenerator->generate('homepage'));

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }
}