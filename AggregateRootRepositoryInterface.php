<?php


namespace Yc\DddCore;


interface AggregateRootRepositoryInterface
{
    /**
     * @param string $aggregateType
     * @param $aggregateId
     * @return Event[]
     */
    public function findEvents(string $aggregateType, $aggregateId): array;

    /**
     * @param Event $event
     */
    public function persistEvent(string $aggregateType, Event $event): void;
}
