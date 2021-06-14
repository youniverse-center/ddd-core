<?php

namespace Yc\DddCore;

interface ProjectorRegistryRepositoryInterface
{
    /**
     * @param array $statuses
     * @return ProjectorRegistryInterface[]
     */
    public function findByStatuses(array $statuses): array;
    public function findByType(string $type): ?ProjectorRegistryInterface;
    public function save(ProjectorRegistryInterface $registry): void;
    public function create(ProjectorInterface $projector): ProjectorRegistryInterface;
}
