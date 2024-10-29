<?php

declare(strict_types=1);

namespace App\Infrastructure\TMDB\Client;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class TMDBClient implements TMDBClientInterface
{
    public function __construct(
        private readonly HttpClientInterface $httpClient,
        private readonly string $apiKey,
        private readonly string $baseUri = 'https://api.themoviedb.org/3',
    ) {
    }

    public function getPopularMovies(): array
    {
        $response = $this->httpClient->request(
            'GET',
            sprintf('%s/movie/popular', $this->baseUri),
            [
                'query' => [
                    'api_key' => $this->apiKey,
                    'language' => 'fr-FR',
                ],
            ]
        );

        $data = $response->toArray();

        return $data['results'] ?? [];
    }

    public function getMoviesByGenre(int $id): array
    {
        $response = $this->httpClient->request(
            'GET',
            sprintf('%s/discover/movie', $this->baseUri),
            [
                'query' => [
                    'api_key' => $this->apiKey,
                    'language' => 'fr-FR',
                    'with_genres' => $id,
                ],
            ]
        );

        $data = $response->toArray();

        return $data['results'] ?? [];
    }

    public function getAllGenres(): array
    {
        $response = $this->httpClient->request(
            'GET',
            sprintf('%s/genre/movie/list', $this->baseUri),
            [
                'query' => [
                    'api_key' => $this->apiKey,
                    'language' => 'fr-FR',
                ],
            ]
        );

        $data = $response->toArray();

        return $data['genres'] ?? [];
    }

    public function getMovie(int $id): ?array
    {
        try {
            $response = $this->httpClient->request(
                'GET',
                sprintf('%s/movie/%d', $this->baseUri, $id),
                [
                    'query' => [
                        'api_key' => $this->apiKey,
                        'language' => 'fr-FR',
                    ],
                ]
            );

            if (404 === $response->getStatusCode()) {
                return null;
            }

            return $response->toArray();
        } catch (\Exception $e) {
            return null;
        }
    }

    public function searchMovies(string $query): array
    {
        $response = $this->httpClient->request(
            'GET',
            sprintf('%s/search/movie', $this->baseUri),
            [
                'query' => [
                    'api_key' => $this->apiKey,
                    'language' => 'fr-FR',
                    'query' => $query,
                ],
            ]
        );

        $data = $response->toArray();

        return $data['results'] ?? [];
    }

    public function getMovieVideo(int $id): ?array
    {
        try {
            $response = $this->httpClient->request(
                'GET',
                sprintf('%s/movie/%d/videos', $this->baseUri, $id),
                [
                    'query' => [
                        'api_key' => $this->apiKey,
                        'language' => 'fr-FR',
                    ],
                ]
            );

            $data = $response->toArray();
            $videos = $data['results'] ?? [];

            // On cherche la première bande-annonce
            $trailer = array_filter(
                $videos,
                fn ($video) => 'Trailer' === $video['type'] && 'YouTube' === $video['site']
            );

            if (empty($trailer)) {
                return null;
            }

            $video = reset($trailer); // Premier résultat

            return [
                'key' => $video['key'],
                'site' => $video['site'],
                'type' => $video['type'],
            ];

        } catch (\Exception $e) {
            return null;
        }
    }
}
