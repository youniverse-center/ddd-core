<?php


namespace Yc\DddCore;


interface AggregateRootRepositoryInterface
{
    /**
     * @param string $aggregateType
     * @param string $aggregateId
     * @return Event[]
     */
    public function findEvents(string $aggregateType, string $aggregateId): array;

    public function persistEvent(string $aggregateType, Event $event): void;

    /**
     * @param \DateTime $eventsSince
     * @param int $maxEvents
     * @return Event[]
     */
    public function findEventsSince(\DateTime $eventsSince, int $maxEvents): array;

    public function findLastEventDate(): \DateTime;

    public function countEventsSince(\DateTime $since = null): int;
}
