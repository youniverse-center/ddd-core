<?php

namespace Yc\DddCore;

class Projectionist
{
    const MAX_EVENTS = 1000;

    /**
     * @var ProjectorInterface[]
     */
    private array $projectors = [];

    public function __construct(
        private ProjectorRegistryRepositoryInterface $registryRepository,
        private EventStream $eventStream
    ) {}

    public function registerProjector(ProjectorInterface $projector)
    {
        $this->projectors[get_class($projector)] = $projector;
    }

    public function retry()
    {
        foreach ($this->registryRepository->findByStatuses([
            ProjectorRegistryInterface::STATUS_BROKEN,
            ProjectorRegistryInterface::STATUS_STALLED,
            ProjectorRegistryInterface::STATUS_RETIRED
        ]) as $registry) {
            if (isset($this->projectors[$registry->getType()])) {
                $projector = $this->projectors[$registry->getType()];
                if ($projector->getMode() === ProjectorInterface::MODE_FROM_BEGINNING) {
                    $registry->setBooting();
                } else {
                    $registry->setValid();
                }
            } else {
                $registry->setRetired();
            }
            $this->registryRepository->save($registry);
        }
    }

    public function boot()
    {
        $failure = false;
        foreach ($this->projectors as $projector) {
            $registry = $this->loadRegistry($projector);

            if (!$projector->getMode() === ProjectorInterface::MODE_FROM_BEGINNING) {
                continue;
            }

            if (!$registry->hasStatus(ProjectorRegistryInterface::STATUS_BOOTING)) {
                continue;
            }

            if ($failure) {
                $registry->setStalled();
                $this->registryRepository->save($registry);

                continue;
            }

            if (!$this->playEvents($projector, $registry)) {
                $failure = true;
                $this->registryRepository->save($registry);
                continue;
            }

            if (!$this->hasEvents($registry->getLastEventDate())) {
                $registry->setValid();
            }

            $this->registryRepository->save($registry);
        }
    }

    public function play()
    {
        foreach ($this->projectors as $projector) {
            $registry = $this->loadRegistry($projector);
        }
    }

    private function playEvents(ProjectorInterface $projector, ProjectorRegistryInterface $registry)
    {
        $events = $this->eventStream->findEventSince($registry->getLastEventDate(), self::MAX_EVENTS);
        foreach ($events as $event) {
            try {
                $projector->handleEvent($event);
                $registry->setLastEventDate($event->getOccuredAt());
            } catch (\Exception $exception) {
                $registry->setBroken($exception);
                return false;
            }
        }

        return true;
    }

    private function hasEvents(\DateTime $since = null): bool
    {
        return $this->eventStream->countEventsSince($since) > 0;
    }

    private function loadRegistry(ProjectorInterface $projector): ProjectorRegistryInterface
    {
        $registry = $this->registryRepository->findByType(get_class($projector));

        if (!$registry) {
            $registry = $this->createRegistry($projector);
        }

        return $registry;
    }

    private function createRegistry(ProjectorInterface $projector): ProjectorRegistryInterface
    {
        $registry = $this->registryRepository->create(get_class($projector));
        if ($projector->getMode() === ProjectorInterface::MODE_FROM_NOW) {
            $registry->setLastEventDate($this->eventStream->findLastEventDate());
            $registry->setValid();
        } else {
            $registry->setBooting();
        }

        $this->registryRepository->save($registry);

        return $registry;
    }
}
