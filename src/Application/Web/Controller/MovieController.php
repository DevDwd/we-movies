<?php

declare(strict_types=1);

namespace App\Application\Web\Controller;

use App\Infrastructure\TMDB\Client\TMDBClientInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class MovieController extends AbstractController
{
    public function __construct(
        private readonly TMDBClientInterface $tmdbClient,
    ) {
    }

    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        return $this->render('movie/index.html.twig', [
            'genres' => $this->tmdbClient->getAllGenres(),
            'movies' => $this->tmdbClient->getPopularMovies(),
        ]);
    }

    #[Route('/genre/{id}', name: 'app_genre_movies', requirements: ['id' => '\d+'])]
    public function moviesByGenre(int $id): Response
    {
        return $this->render('movie/index.html.twig', [
            'genres' => $this->tmdbClient->getAllGenres(),
            'movies' => $this->tmdbClient->getMoviesByGenre($id),
            'selectedGenreId' => $id,
        ]);
    }
}
