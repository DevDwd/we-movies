<?php

declare(strict_types=1);

namespace App\Application\Api\Controller;

use App\Infrastructure\TMDB\Client\TMDBClientInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/movie')]
final class MovieController extends AbstractController
{
    public function __construct(
        private readonly TMDBClientInterface $tmdbClient,
    ) {
    }

    #[Route('/search', name: 'api_movie_search', methods: ['GET'])]
    public function search(Request $request): JsonResponse
    {
        $query = $request->query->get('q');

        if (!is_string($query) || strlen($query) < 2) {
            return $this->json([]);
        }

        return $this->json($this->tmdbClient->searchMovies($query));
    }

    #[Route('/genre/{id}', name: 'api_movies_by_genre', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function getMoviesByGenre(int $id): JsonResponse
    {
        return $this->json($this->tmdbClient->getMoviesByGenre($id));
    }

    #[Route('/{id}/video', name: 'api_movie_video', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function getMovieVideo(int $id): JsonResponse
    {
        $video = $this->tmdbClient->getMovieVideo($id);

        if (!$video) {
            return $this->json(['error' => 'Aucune vidéo disponible'], Response::HTTP_NOT_FOUND);
        }

        return $this->json($video);
    }

    #[Route('/{id}/rate', name: 'app_movie_rate', methods: ['POST'])]
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

            $movie = $this->tmdbClient->getMovie($id);
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

    #[Route('/{id}', name: 'api_movie_details', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function getMovie(int $id): JsonResponse
    {
        $movie = $this->tmdbClient->getMovie($id);

        if (!$movie) {
            throw $this->createNotFoundException('Film non trouvé');
        }

        return $this->json($movie);
    }
}
