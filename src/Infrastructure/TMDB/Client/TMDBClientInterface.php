<?php

declare(strict_types=1);

namespace App\Infrastructure\TMDB\Client;

interface TMDBClientInterface
{
    /**
     * Récupère tous les genres de films.
     *
     * @return array<array{id: int, name: string}>
     */
    public function getAllGenres(): array;

    /**
     * Récupère les détails d'un film par son ID.
     */
    public function getMovie(int $id): ?array;

    /**
     * Recherche des films par titre.
     *
     * @return array<array-key, array>
     */
    public function searchMovies(string $query): array;

    /**
     * Récupère les films d'un genre spécifique.
     *
     * @return array<array-key, array>
     */
    public function getMoviesByGenre(int $genreId): array;

    /**
     * Récupère les films populaires.
     *
     * @return array<array-key, array>
     */
    public function getPopularMovies(): array;

    /**
     * Récupère la vidéo/bande-annonce d'un film.
     *
     * @return array{key: string, site: string, type: string}|null
     */
    public function getMovieVideo(int $id): ?array;
}
