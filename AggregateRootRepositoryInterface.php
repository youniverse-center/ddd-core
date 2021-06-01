<?php


namespace App\Yc\DddCore;


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
    public function persistEvent(Event $event): void;
}
