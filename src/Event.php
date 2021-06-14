<?php

namespace Yc\DddCore;

abstract class Event
{
    private ?\DateTime $occurredAt = null;

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

    public function withOccurredAt(\DateTime $occurredAt): static
    {
        $clone = clone $this;
        $clone->occurredAt = $occurredAt;

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

    public function getOccurredAt(): ?\DateTime
    {
        return $this->occurredAt;
    }

    protected function get(string $name, $default = null)
    {
        return $this->payload[$name] ?? $default;
    }
}
