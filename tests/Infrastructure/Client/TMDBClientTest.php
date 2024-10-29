<?php

declare(strict_types=1);

namespace App\Tests\Infrastructure\TMDB\Client;

use App\Infrastructure\TMDB\Client\TMDBClient;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Component\HttpFoundation\Response;

class TMDBClientTest extends TestCase
{
    private const FAKE_API_KEY = 'test_api_key';
    private const FAKE_BASE_URI = 'https://api.themoviedb.org/3';

    private function createMockClient(array $responses): TMDBClient
    {
        $responses = array_map(
            function ($data) {
                return new MockResponse(
                    json_encode($data, JSON_THROW_ON_ERROR) ?: '',
                    ['http_code' => Response::HTTP_OK]
                );
            },
            $responses
        );

        $httpClient = new MockHttpClient($responses);

        return new TMDBClient(
            httpClient: $httpClient,
            apiKey: self::FAKE_API_KEY,
            baseUri: self::FAKE_BASE_URI
        );
    }

    public function testGetAllGenres(): void
    {
        $expectedGenres = [
            'genres' => [
                ['id' => 28, 'name' => 'Action'],
                ['id' => 35, 'name' => 'Comedy'],
            ],
        ];

        $client = $this->createMockClient([$expectedGenres]);
        $genres = $client->getAllGenres();

        $this->assertCount(2, $genres);
        $this->assertEquals('Action', $genres[0]['name']);
    }

    public function testGetMovie(): void
    {
        $expectedMovie = [
            'id' => 1,
            'title' => 'Test Movie',
            'overview' => 'Test Description',
        ];

        $client = $this->createMockClient([$expectedMovie]);
        $movie = $client->getMovie(1);

        $this->assertNotNull($movie);
        $this->assertEquals('Test Movie', $movie['title']);
    }

    public function testGetNonExistentMovie(): void
    {
        $errorResponse = [
            'success' => false,
            'status_code' => 404,
        ];

        $mockResponse = new MockResponse(
            json_encode($errorResponse, JSON_THROW_ON_ERROR) ?: '',
            ['http_code' => Response::HTTP_NOT_FOUND]
        );

        $httpClient = new MockHttpClient($mockResponse);

        $client = new TMDBClient(
            httpClient: $httpClient,
            apiKey: self::FAKE_API_KEY,
            baseUri: self::FAKE_BASE_URI
        );

        $movie = $client->getMovie(999999);
        $this->assertNull($movie);
    }

    public function testSearchMovies(): void
    {
        $expectedResults = [
            'results' => [
                [
                    'id' => 1,
                    'title' => 'Test Movie',
                ],
            ],
        ];

        $client = $this->createMockClient([$expectedResults]);
        $results = $client->searchMovies('test');

        $this->assertCount(1, $results);
        $this->assertEquals('Test Movie', $results[0]['title']);
    }
}
