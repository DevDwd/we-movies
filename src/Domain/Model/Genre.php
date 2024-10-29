<?php

declare(strict_types=1);

namespace App\Domain\Model;

class Genre
{
    public function __construct(
        private readonly int $id,
        private readonly string $name,
    ) {
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Convertit l'objet en tableau pour l'API.
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
        ];
    }

    /**
     * Crée une instance à partir d'un tableau.
     */
    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'],
            name: $data['name']
        );
    }
}
