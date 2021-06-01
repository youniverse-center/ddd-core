<?php

namespace Yc\DddCore;

class EventStream
{
    public function __construct(
        private AggregateRootRepositoryInterface $repository
    ) {}

    public function load($aggregateType, $aggregateId): ?AggregateRoot
    {
        $events = $this->repository->findEvents($aggregateType, $aggregateId);

        return AggregateRootDecorator::fromHistory($aggregateType, $events);
    }

    public function save(AggregateRoot $aggregateRoot): void
    {
        $events = AggregateRootDecorator::extractRecordedEvents($aggregateRoot);
        $aggregateType = get_class($aggregateRoot);

        foreach ($events as $event) {
            $this->repository->persistEvent($aggregateType, $event);
        }
    }
}
