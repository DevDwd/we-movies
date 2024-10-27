<?php

// src/Infrastructure/TMDB/DTO/MovieDTO.php

declare(strict_types=1);

namespace App\Infrastructure\TMDB\DTO;

class GenreDTO
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'],
            name: $data['name']
        );
    }
}
