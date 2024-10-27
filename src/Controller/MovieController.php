<?php

declare(strict_types=1);

namespace App\Controller;

use App\Application\Service\TMDBService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MovieController extends AbstractController
{
    public function __construct(
        private readonly TMDBService $tmdbService,
    ) {
    }

    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        $genres = $this->tmdbService->getAllGenres();
        $movies = $this->tmdbService->getPopularMovies();
        $popularMovie = $this->tmdbService->getPopularMovies()[0] ?? null;

        return $this->render('movie/index.html.twig', [
            'genres' => $genres,
            'movies' => $movies,
            'featuredMovie' => $popularMovie,
            'selectedGenreIds' => [],
        ]);
    }

    #[Route('/search', name: 'app_movie_search', methods: ['GET'])]
    public function search(Request $request): JsonResponse
    {
        $query = $request->query->get('q');

        if (!is_string($query) || strlen(trim($query)) < 2) {
            return new JsonResponse([]);
        }

        try {
            $movies = $this->tmdbService->searchMovies(trim($query));

            $moviesData = array_map(function ($movie) {
                return [
                    'id' => $movie->getId(),
                    'title' => $movie->getTitle(),
                    'overview' => $movie->getOverview(),
                    'posterPath' => $movie->getPosterPath(),
                    'releaseDate' => $movie->getReleaseDate(),
                    'voteAverage' => $movie->getVoteAverage(),
                    'genres' => array_map(fn ($genre) => [
                        'id' => $genre->getId(),
                        'name' => $genre->getName(),
                    ], $movie->getGenres()),
                ];
            }, $movies);

            return new JsonResponse($moviesData);
        } catch (\Exception $e) {
            return new JsonResponse(
                ['error' => 'Erreur lors de la recherche'],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    #[Route('/genre/{ids}', name: 'app_genre_movies')]
    public function moviesByGenre(string $ids): Response
    {
        // Convertir la chaîne d'IDs en tableau
        $genreIds = array_map('intval', explode(',', $ids));

        try {
            $genres = $this->tmdbService->getAllGenres();
            $movies = [];

            // Récupérer les films pour chaque genre
            foreach ($genreIds as $genreId) {
                $genreMovies = $this->tmdbService->getMoviesByGenre($genreId);
                $movies = array_merge($movies, $genreMovies);
            }

            // Supprimer les doublons potentiels
            $uniqueMovies = [];
            foreach ($movies as $movie) {
                $uniqueMovies[$movie->getId()] = $movie;
            }
            $movies = array_values($uniqueMovies);

            // Trier par popularité
            usort($movies, function ($a, $b) {
                return $b->getVoteAverage() <=> $a->getVoteAverage();
            });

            return $this->render('movie/index.html.twig', [
                'genres' => $genres,
                'selectedGenreIds' => $genreIds,  // Pour maintenir la sélection
                'featuredMovie' => reset($movies), // Premier film comme film à la une
                'movies' => $movies,
            ]);

        } catch (\Exception $e) {
            // En cas d'erreur, rediriger vers la page d'accueil
            $this->addFlash('error', 'Une erreur est survenue lors du chargement des films.');

            return $this->redirectToRoute('app_home');
        }
    }

    #[Route('/movie/{id}', name: 'app_movie_details')]
    public function movieDetails(int $id): JsonResponse
    {
        try {
            $movie = $this->tmdbService->getMovie($id);

            if (!$movie) {
                return new JsonResponse(['error' => 'Film non trouvé'], Response::HTTP_NOT_FOUND);
            }

            // Transformer le film pour la réponse JSON
            $movieData = [
                'id' => $movie->getId(),
                'title' => $movie->getTitle(),
                'overview' => $movie->getOverview(),
                'posterPath' => $movie->getPosterPath(),
                'releaseDate' => $movie->getReleaseDate(),
                'genres' => array_map(fn ($genre) => [
                    'id' => $genre->getId(),
                    'name' => $genre->getName(),
                ], $movie->getGenres()),
                'voteAverage' => $movie->getVoteAverage(),
            ];

            return new JsonResponse($movieData);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Erreur lors du chargement du film'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/movie/{id}/rate', name: 'app_movie_rate', methods: ['POST'])]
    public function rateMovie(int $id, Request $request): JsonResponse
    {
        try {
            $content = $request->getContent();

            if (!is_string($content)) {
                throw new \JsonException('Invalid request content');
            }

            /** @var array<string, mixed> $data */
            $data = json_decode($content, true, 512, JSON_THROW_ON_ERROR);

            if (!isset($data['rating'])) {
                return new JsonResponse(
                    ['error' => 'La note est requise'],
                    Response::HTTP_BAD_REQUEST
                );
            }

            $rating = $data['rating'];

            if (!is_numeric($rating) || $rating < 1 || $rating > 5) {
                return new JsonResponse(
                    ['error' => 'La note doit être comprise entre 1 et 5'],
                    Response::HTTP_BAD_REQUEST
                );
            }

            $movie = $this->tmdbService->getMovie($id);
            if (!$movie) {
                return new JsonResponse(
                    ['error' => 'Film non trouvé'],
                    Response::HTTP_NOT_FOUND
                );
            }

            // Convertir en float pour la consistance
            $floatRating = (float) $rating;

            return new JsonResponse([
                'success' => true,
                'message' => 'Note enregistrée',
                'movieId' => $id,
                'rating' => $floatRating,
            ]);

        } catch (\JsonException $e) {
            return new JsonResponse(
                ['error' => 'Données JSON invalides'],
                Response::HTTP_BAD_REQUEST
            );
        } catch (\Exception $e) {
            return new JsonResponse(
                ['error' => 'Erreur lors de l\'enregistrement de la note'],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    // Récupérer la vidéo du film
    #[Route('/movie/{id}/video', name: 'app_movie_video')]
    public function getMovieVideo(int $id): JsonResponse
    {
        $video = $this->tmdbService->getMovieVideo($id);

        return new JsonResponse($video);
    }
}
