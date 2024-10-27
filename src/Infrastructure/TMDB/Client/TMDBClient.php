<?php

declare(strict_types=1);

namespace App\Infrastructure\TMDB\Client;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class TMDBClient
{
    private const BASE_URL = 'https://api.themoviedb.org/3';

    public function __construct(
        private readonly HttpClientInterface $httpClient,
        private readonly string $apiKey,
    ) {
    }

    public function getGenres(): ResponseInterface
    {
        return $this->get('/genre/movie/list');
    }

    public function getMoviesByGenre(int $genreId, int $page = 1): ResponseInterface
    {
        return $this->get('/discover/movie', [
            'with_genres' => $genreId,
            'page' => $page,
            'sort_by' => 'popularity.desc',
        ]);
    }

    public function searchMovies(string $query): ResponseInterface
    {
        return $this->get('/search/movie', [
            'query' => $query,
        ]);
    }

    public function getMovie(int $id): ResponseInterface
    {
        return $this->get("/movie/{$id}");
    }

    public function getPopularMovies(int $page = 1): ResponseInterface
    {
        return $this->get('/movie/popular', [
            'page' => $page,
        ]);
    }

    // Ajout de la méthode manquante
    public function getMovieVideo(int $movieId): ResponseInterface
    {
        return $this->get("/movie/{$movieId}/videos");
    }

    protected function get(string $endpoint, array $params = []): ResponseInterface
    {
        return $this->httpClient->request('GET', self::BASE_URL.$endpoint, [
            'query' => array_merge([
                'api_key' => $this->apiKey,
                'language' => 'fr-FR',
                'include_adult' => false,
            ], $params),
        ]);
    }
}
