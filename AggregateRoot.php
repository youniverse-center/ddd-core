<?php

namespace Yc\DddCore;

abstract class AggregateRoot
{
    private int $version = 0;
    private array $recordedEvents = [];

    protected function __construct() {}

    protected static function reconstituteFromHistory(array $events): static
    {
        $instance = new static();
        $instance->reply($events);

        return $instance;
    }

    protected function applyEvent(Event $event): void
    {
        $reflectionClass = new \ReflectionClass($event);
        $methodName = 'when' . ucfirst($reflectionClass->getShortName());

        if (!method_exists($this, $methodName)) {
            throw new \RuntimeException(sprintf(
                    'You must implement "%s" method to handle event "%s".',
                    $methodName,
                    $reflectionClass->getName())
            );
        }

        $this->$methodName($event);
    }

    protected function getVersion(): int
    {
        return $this->version;
    }

    protected function popRecordedEvents(): array
    {
        $events = $this->recordedEvents;
        $this->recordedEvents = [];

        return $events;
    }

    protected function recordThat(Event $event)
    {
        $this->version++;
        $newEvent = $event->withVersion($this->version);
        $this->recordedEvents[] = $newEvent;
        $this->applyEvent($event);
    }

    protected function reply(array $events): void
    {
        foreach ($events as $event) {
            if (!$event instanceof Event) {
                throw new \RuntimeException(sprintf(
                    '"%s" must be instance of "%s".',
                    get_class($this),
                    Event::class
                ));
            }

            $this->version = $event->getVersion();
            $this->applyEvent($event);
        }
    }
}
