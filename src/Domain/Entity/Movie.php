<?php

declare(strict_types=1);

namespace App\Domain\Entity;

class Movie
{
    /**
     * @param Genre[] $genres
     * @param float[] $userRatings
     */
    public function __construct(
        private readonly int $id,
        private readonly string $title,
        private readonly string $overview,
        private readonly ?string $posterPath,
        private readonly array $genres,
        private readonly float $voteAverage,
        private readonly string $releaseDate,
        private array $userRatings = [],
    ) {
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getOverview(): string
    {
        return $this->overview;
    }

    public function getPosterPath(): ?string
    {
        return $this->posterPath;
    }

    /**
     * @return Genre[]
     */
    public function getGenres(): array
    {
        return $this->genres;
    }

    public function getVoteAverage(): float
    {
        return $this->voteAverage;
    }

    public function getReleaseDate(): string
    {
        return $this->releaseDate;
    }

    public function getFullPosterPath(): string
    {
        return $this->posterPath
            ? 'https://image.tmdb.org/t/p/w500'.$this->posterPath
            : '/images/no-poster.jpg';
    }

    /**
     * Ajoute une note utilisateur (1-5 étoiles).
     */
    public function addRating(float $rating): void
    {
        if ($rating < 1 || $rating > 5) {
            throw new \InvalidArgumentException('La note doit être comprise entre 1 et 5');
        }
        $this->userRatings[] = $rating;
    }

    /**
     * Calcule la moyenne des notes utilisateurs.
     */
    public function getUserRatingAverage(): float
    {
        if (empty($this->userRatings)) {
            return 0;
        }

        return array_sum($this->userRatings) / count($this->userRatings);
    }

    /**
     * Retourne le nombre de notes utilisateurs.
     */
    public function getUserRatingsCount(): int
    {
        return count($this->userRatings);
    }

    /**
     * Convertit l'objet en tableau pour l'API.
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'overview' => $this->overview,
            'posterPath' => $this->getFullPosterPath(),
            'genres' => array_map(fn (Genre $genre) => $genre->toArray(), $this->genres),
            'voteAverage' => $this->voteAverage,
            'releaseDate' => $this->releaseDate,
            'userRating' => $this->getUserRatingAverage(),
            'userRatingsCount' => $this->getUserRatingsCount(),
        ];
    }
}
