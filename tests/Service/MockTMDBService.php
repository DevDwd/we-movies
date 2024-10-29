<?php

declare(strict_types=1);

namespace App\Tests\Service;

use App\Domain\Model\Genre;
use App\Domain\Model\Movie;
use App\Infrastructure\TMDB\Client\TMDBClientInterface;

class MockTMDBService implements TMDBClientInterface
{
    /**
     * @return array<array{id: int, name: string}>
     */
    public function getAllGenres(): array
    {
        return [
            ['id' => 28, 'name' => 'Action'],
            ['id' => 35, 'name' => 'Comédie'],
        ];
    }

    /**
     * @return array<array-key, array>
     */
    public function getPopularMovies(): array
    {
        return [$this->createTestMovie()->toArray()];
    }

    public function getMovie(int $id): ?array
    {
        if (999999 === $id) {
            return null;
        }

        return $this->createTestMovie()->toArray();
    }

    /**
     * @return array<array-key, array>
     */
    public function searchMovies(string $query): array
    {
        if (empty($query)) {
            return [];
        }

        return [$this->createTestMovie()->toArray()];
    }

    /**
     * @return array<array-key, array>
     */
    public function getMoviesByGenre(int $genreId): array
    {
        if (999999 === $genreId) {
            return [];
        }

        return [$this->createTestMovie()->toArray()];
    }

    /**
     * @return array{key: string, site: string, type: string}|null
     */
    public function getMovieVideo(int $id): ?array
    {
        if (999999 === $id) {
            return null;
        }

        return [
            'key' => 'test_video_key',
            'site' => 'YouTube',
            'type' => 'Trailer',
        ];
    }

    private function createTestMovie(): Movie
    {
        return new Movie(
            id: 1,
            title: 'Test Movie',
            overview: 'Test Description',
            posterPath: '/test/poster.jpg',
            genres: [new Genre(28, 'Action')],
            voteAverage: 8.5,
            releaseDate: '2023-01-01',
            userRatings: []
        );
    }
}
