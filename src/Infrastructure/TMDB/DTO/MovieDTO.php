<?php

// src/Infrastructure/TMDB/DTO/MovieDTO.php

declare(strict_types=1);

namespace App\Infrastructure\TMDB\DTO;

class MovieDTO
{
    public function __construct(
        public readonly int $id,
        public readonly string $title,
        public readonly string $overview,
        public readonly ?string $posterPath,
        public readonly array $genreIds,
        public readonly float $voteAverage,
        public readonly string $releaseDate,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'],
            title: $data['title'],
            overview: $data['overview'],
            posterPath: $data['poster_path'] ?? null,
            genreIds: $data['genre_ids'] ?? [],
            voteAverage: $data['vote_average'],
            releaseDate: $data['release_date']
        );
    }
}
