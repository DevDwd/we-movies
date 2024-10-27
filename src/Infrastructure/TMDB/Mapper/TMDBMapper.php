<?php

declare(strict_types=1);

namespace App\Infrastructure\TMDB\Mapper;

use App\Domain\Entity\Genre;
use App\Domain\Entity\Movie;

class TMDBMapper
{
    /**
     * @return Movie[]
     */
    public function mapMovies(array $data): array
    {
        return array_map(
            fn (array $movieData) => $this->mapMovie($movieData),
            $data['results'] ?? []
        );
    }

    public function mapMovie(array $data): Movie
    {
        // Pour les résultats de recherche, les genres sont des IDs
        $genres = [];
        if (isset($data['genre_ids'])) {
            $genres = array_map(
                fn (int $id) => new Genre($id, ''), // Nom temporaire
                $data['genre_ids']
            );
        } elseif (isset($data['genres'])) {
            // Pour les détails d'un film, nous avons les objets genres complets
            $genres = array_map(
                fn (array $genre) => new Genre($genre['id'], $genre['name']),
                $data['genres']
            );
        }

        return new Movie(
            id: $data['id'],
            title: $data['title'],
            overview: $data['overview'] ?? '',
            posterPath: $data['poster_path'] ?? null,
            genres: $genres,
            voteAverage: $data['vote_average'] ?? 0.0,
            releaseDate: $data['release_date'] ?? '',
            userRatings: []
        );
    }

    /**
     * @return Genre[]
     */
    public function mapGenres(array $data): array
    {
        return array_map(
            fn (array $genreData) => new Genre(
                id: $genreData['id'],
                name: $genreData['name']
            ),
            $data['genres'] ?? []
        );
    }
}
