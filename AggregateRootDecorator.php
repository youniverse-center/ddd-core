<?php

namespace App\Yc\DddCore;

class AggregateRootDecorator extends AggregateRoot
{
    public static function fromHistory(string $class, array $events): AggregateRoot
    {
        return $class::reconstituteFromHistory($events);
    }

    public static function extractRecordedEvents(AggregateRoot $aggregateRoot): array
    {
        return $aggregateRoot->popRecordedEvents();
    }
}
