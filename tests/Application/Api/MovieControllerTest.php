<?php

declare(strict_types=1);

namespace App\Tests\Application\Api\Controller;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class MovieControllerTest extends WebTestCase
{
    private KernelBrowser $client;

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = static::createClient();
    }

    public function testGetMovie(): void
    {
        $this->client->request('GET', '/api/movie/1');

        $this->assertResponseIsSuccessful();
        $response = $this->client->getResponse();

        // Récupérer le contenu de la réponse
        $content = $response->getContent();
        $this->assertNotFalse($content, 'Response content should not be false');

        // Vérifier que le contenu est un JSON valide
        $this->assertJson($content);
        $data = json_decode($content, true);

        // Assurez-vous que $data est bien un tableau avant de faire les assertions
        $this->assertIsArray($data);
        $this->assertArrayHasKey('id', $data);
        $this->assertArrayHasKey('title', $data);
    }

    public function testGetNonExistentMovie(): void
    {
        $this->client->request('GET', '/api/movie/999999');
        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    public function testSearchMovies(): void
    {
        $this->client->request('GET', '/api/movie/search', ['q' => 'test']);

        $this->assertResponseIsSuccessful();
        $response = $this->client->getResponse();

        // Récupérer le contenu de la réponse
        $content = $response->getContent();
        $this->assertNotFalse($content, 'Response content should not be false');

        // Vérifier que le contenu est un JSON valide
        $this->assertJson($content);
        $data = json_decode($content, true);

        $this->assertIsArray($data);
    }

    public function testSearchWithEmptyQuery(): void
    {
        $this->client->request('GET', '/api/movie/search', ['q' => '']);

        $this->assertResponseIsSuccessful();
        $content = $this->client->getResponse()->getContent();
        $this->assertNotFalse($content, 'Response content should not be false');

        $this->assertJson($content);

        $data = json_decode($content, true);
        $this->assertEmpty($data);
    }

    public function testSearchWithShortQuery(): void
    {
        $this->client->request('GET', '/api/movie/search', ['q' => 'a']);

        $this->assertResponseIsSuccessful();
        $content = $this->client->getResponse()->getContent();
        $this->assertNotFalse($content, 'Response content should not be false');

        $this->assertJson($content);

        $data = json_decode($content, true);
        $this->assertEmpty($data);
    }

    public function testMoviesByGenre(): void
    {
        $this->client->request('GET', '/api/movie/genre/28');

        $this->assertResponseIsSuccessful();
        $content = $this->client->getResponse()->getContent();
        $this->assertNotFalse($content, 'Response content should not be false');

        $this->assertJson($content);

        $data = json_decode($content, true);
        $this->assertIsArray($data);
    }
}
