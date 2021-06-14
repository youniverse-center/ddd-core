<?php

namespace Yc\DddCore;

use Ramsey\Uuid\Uuid;

class UuidId
{
    private function __construct(
        private string $id
    ) {}

    public static function new(): static
    {
        return new static(Uuid::uuid4()->toString());
    }

    public static function fromString(string $id): static
    {
        return new static($id);
    }

    public function toString(): string
    {
        return $this->id;
    }

    public function equals($other): bool
    {
        if (!$other instanceof static)
        {
            return false;
        }

        return $this->id === $other->id;
    }
}
