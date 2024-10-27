<?php

declare(strict_types=1);

namespace App\Tests\Service;

use App\Application\Service\TMDBService;
use App\Domain\Entity\Genre;
use App\Domain\Entity\Movie;

class MockTMDBService extends TMDBService
{
    public function __construct()
    {
    }

    public function getAllGenres(): array
    {
        return [
            new Genre(28, 'Action'),
            new Genre(35, 'Comedy'),
        ];
    }

    public function getPopularMovies(int $page = 1): array
    {
        return [
            $this->createTestMovie(1),
        ];
    }

    public function getMovie(int $id): ?Movie
    {
        if (999999 === $id) {
            return null;
        }

        return $this->createTestMovie($id);
    }

    public function searchMovies(string $query): array
    {
        if (empty(trim($query))) {
            return [];
        }

        return [$this->createTestMovie(1)];
    }

    public function getMoviesByGenre(int $genreId, int $page = 1): array
    {
        return [$this->createTestMovie(1)];
    }

    public function getMovieVideo(int $movieId): ?array
    {
        if (999999 === $movieId) {
            return null;
        }

        return [
            'key' => 'test_video_key',
            'site' => 'YouTube',
            'type' => 'Trailer',
        ];
    }

    private function createTestMovie(int $id): Movie
    {
        return new Movie(
            $id,
            'Test Movie',
            'Test Description',
            '/test-poster.jpg',
            [new Genre(28, 'Action')],
            8.5,
            '2024-01-01'
        );
    }
}
