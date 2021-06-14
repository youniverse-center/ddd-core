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
        if (empty($events)) {
            return null;
        }

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

    /**
     * @param \DateTime $dateTime
     * @param int $maxEvents
     * @return Event[]
     */
    public function findEventSince(\DateTime $dateTime, int $maxEvents): array
    {
        $this->repository->findEventsSince($dateTime, $maxEvents);
    }

    public function findLastEventDate(): \DateTime
    {
        return $this->repository->findLastEventDate();
    }

    public function countEventsSince(\DateTime $since = null): int
    {
        return $this->repository->countEventsSince($since);
    }
}
