<?php

declare(strict_types=1);

namespace App\Infrastructure\TMDB\Client;

final class StubTMDBClient implements TMDBClientInterface
{
    private const FAKE_MOVIES = [
        1 => [
            'id' => 1,
            'title' => 'Test Movie',
            'overview' => 'Test Description',
            'poster_path' => '/test.jpg',
            'genre_ids' => [28],
            'vote_average' => 8.5,
            'release_date' => '2024-01-01',
        ],
    ];

    private const FAKE_VIDEOS = [
        [
            'id' => '12345',
            'key' => 'fake_video_key_1',
            'name' => 'Fake Video 1',
            'site' => 'YouTube',
            'type' => 'Trailer',
        ],
    ];

    public function getAllGenres(): array
    {
        return [
            ['id' => 28, 'name' => 'Action'],
            ['id' => 35, 'name' => 'Comédie'],
        ];
    }

    public function getMovie(int $id): ?array
    {
        return self::FAKE_MOVIES[$id] ?? null;
    }

    public function searchMovies(string $query): array
    {
        return array_values(self::FAKE_MOVIES);
    }

    public function getMoviesByGenre(int $genreId): array
    {
        return array_values(self::FAKE_MOVIES);
    }

    public function getPopularMovies(): array
    {
        return array_values(self::FAKE_MOVIES);
    }

    public function getMovieVideo(int $id): ?array
    {
        return self::FAKE_VIDEOS[$id] ?? null;
    }
}
