<?php

namespace App\Yc\DddCore;

abstract class Event
{
    private function __construct(
        private string $aggregateId,
        private array $payload = [],
        private int $version = 0
    ) {}

    public static function occur(string $aggregateId, array $payload = []): static
    {
        return new static($aggregateId, $payload);
    }

    public function getAggregateId(): string
    {
        return $this->aggregateId;
    }

    public function withVersion(int $version): static
    {
        $clone = clone $this;
        $clone->version = $version;

        return $clone;
    }

    public function getPayload(): array
    {
        return $this->payload;
    }

    public function getVersion(): int
    {
        return $this->version;
    }

    protected function get(string $name, $default = null)
    {
        return $this->payload[$name] ?? $default;
    }
}
