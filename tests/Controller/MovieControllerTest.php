<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use App\Tests\Service\MockTMDBService;
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
        self::getContainer()->set('App\Application\Service\TMDBService', new MockTMDBService());
    }

    public function testHomepageLoadsSuccessfully(): void
    {
        $this->client->request('GET', '/');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('.navbar');
        $this->assertSelectorExists('.movies-grid');
        $this->assertSelectorExists('.genres-list');
    }

    public function testSearchWithValidQuery(): void
    {
        $this->client->request('GET', '/search', ['q' => 'test']);

        $this->assertResponseIsSuccessful();
        $response = $this->client->getResponse();
        $content = $response->getContent();

        $this->assertNotFalse($content);
        $this->assertJson($content);

        /** @var array<int, array<string, mixed>> $data */
        $data = json_decode($content, true, 512, JSON_THROW_ON_ERROR);
        $this->assertIsArray($data);
        $this->assertNotEmpty($data);
        $this->assertArrayHasKey('title', $data[0]);
    }

    public function testSearchWithEmptyQuery(): void
    {
        $this->client->request('GET', '/search', ['q' => '']);

        $this->assertResponseIsSuccessful();
        $content = $this->client->getResponse()->getContent();
        $this->assertNotFalse($content);

        /** @var array $data */
        $data = json_decode($content, true, 512, JSON_THROW_ON_ERROR);
        $this->assertEmpty($data);
    }

    public function testMovieDetails(): void
    {
        $this->client->request('GET', '/movie/1');

        $this->assertResponseIsSuccessful();
        $content = $this->client->getResponse()->getContent();
        $this->assertNotFalse($content);

        /** @var array<string, mixed> $data */
        $data = json_decode($content, true, 512, JSON_THROW_ON_ERROR);

        $this->assertIsArray($data);
        $this->assertArrayHasKey('id', $data);
        $this->assertArrayHasKey('title', $data);
        $this->assertArrayHasKey('overview', $data);
    }

    public function testNonExistentMovie(): void
    {
        $this->client->request('GET', '/movie/999999');

        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
        $content = $this->client->getResponse()->getContent();
        $this->assertNotFalse($content);

        /** @var array<string, string> $data */
        $data = json_decode($content, true, 512, JSON_THROW_ON_ERROR);
        $this->assertArrayHasKey('error', $data);
    }

    public function testMoviesByGenre(): void
    {
        $this->client->request('GET', '/genre/28');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('.movies-grid');
    }

    public function testRateMovie(): void
    {
        $this->client->request(
            'POST',
            '/movie/1/rate',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode(['rating' => 4], JSON_THROW_ON_ERROR)
        );

        $this->assertResponseIsSuccessful();
        $content = $this->client->getResponse()->getContent();
        $this->assertNotFalse($content);

        /** @var array{success: bool} $data */
        $data = json_decode($content, true, 512, JSON_THROW_ON_ERROR);
        $this->assertTrue($data['success']);
    }

    public function testInvalidRating(): void
    {
        $this->client->request(
            'POST',
            '/movie/1/rate',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode(['rating' => 6], JSON_THROW_ON_ERROR)
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        unset($this->client);
    }
}
