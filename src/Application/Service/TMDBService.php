<?php

declare(strict_types=1);

namespace App\Application\Service;

use App\Domain\Entity\Genre;
use App\Domain\Entity\Movie;
use App\Infrastructure\TMDB\Client\TMDBClient;
use App\Infrastructure\TMDB\Mapper\TMDBMapper;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

class TMDBService
{
    public function __construct(
        private readonly TMDBClient $client,
        private readonly TMDBMapper $mapper,
        private readonly CacheInterface $cache,
    ) {
    }

    /**
     * Récupère tous les genres de films.
     *
     * @return Genre[]
     */
    public function getAllGenres(): array
    {
        return $this->cache->get('movie_genres', function (ItemInterface $item) {
            $item->expiresAfter(3600); // Cache pour 1 heure
            $response = $this->client->getGenres();
            $data = $response->toArray();

            return $this->mapper->mapGenres($data);
        });
    }

    /**
     * Récupère les films populaires.
     *
     * @return Movie[]
     */
    public function getPopularMovies(int $page = 1): array
    {
        $cacheKey = sprintf('popular_movies_page_%d', $page);

        return $this->cache->get($cacheKey, function (ItemInterface $item) use ($page) {
            $item->expiresAfter(1800); // Cache pour 30 minutes
            $response = $this->client->getPopularMovies($page);
            $data = $response->toArray();

            return $this->mapper->mapMovies($data);
        });
    }

    /**
     * Recherche des films par titre.
     *
     * @return Movie[]
     */
    public function searchMovies(string $query): array
    {
        // Pas de cache pour la recherche car les résultats peuvent changer fréquemment
        $response = $this->client->searchMovies($query);
        $data = $response->toArray();

        return $this->mapper->mapMovies($data);
    }

    /**
     * Récupère les films par genre.
     *
     * @return Movie[]
     */
    public function getMoviesByGenre(int $genreId, int $page = 1): array
    {
        $cacheKey = sprintf('genre_%d_movies_page_%d', $genreId, $page);

        return $this->cache->get($cacheKey, function (ItemInterface $item) use ($genreId, $page) {
            $item->expiresAfter(1800); // Cache pour 30 minutes
            $response = $this->client->getMoviesByGenre($genreId, $page);
            $data = $response->toArray();

            return $this->mapper->mapMovies($data);
        });
    }

    /**
     * Récupère les détails d'un film.
     */
    public function getMovie(int $id): ?Movie
    {
        $cacheKey = sprintf('movie_details_%d', $id);

        return $this->cache->get($cacheKey, function (ItemInterface $item) use ($id) {
            $item->expiresAfter(3600); // Cache pour 1 heure
            try {
                $response = $this->client->getMovie($id);
                $data = $response->toArray();

                return $this->mapper->mapMovie($data);
            } catch (\Exception $e) {
                return null;
            }
        });
    }

    public function getMovieVideo(int $movieId): ?array
    {
        try {
            $response = $this->client->getMovieVideo($movieId);
            $data = $response->toArray();

            // Filtrer pour obtenir la bande-annonce en français ou en anglais
            $videos = array_filter($data['results'] ?? [], function ($video) {
                return 'Trailer' === $video['type']
                    && 'YouTube' === $video['site']
                    && in_array($video['iso_639_1'], ['fr', 'en'], true);
            });

            return !empty($videos) ? reset($videos) : null;
        } catch (\Exception $e) {
            return null;
        }
    }
}
