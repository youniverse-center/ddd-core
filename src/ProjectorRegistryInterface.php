<?php

namespace Yc\DddCore;

use phpDocumentor\Reflection\Types\Boolean;

interface ProjectorRegistryInterface
{
    const STATUS_BOOTING = 0;
    const STATUS_VALID = 1;
    const STATUS_BROKEN = 2;
    const STATUS_STALLED = 3;
    const STATUS_RETIRED = 4;

    /**
     * Set status as booting for Projector is with MODE_FROM_BEGINNING mode.
     */
    public function setBooting(): void;

    /**
     * Set status as valid for Projector is with MODE_FROM_NOW mode.
     */
    public function setValid(): void;

    /**
     * Set status as retired when Projector is no longer registered.
     */
    public function setRetired(): void;

    /**
     * Set status as stalled when previous Projector failed playing events while booting.
     */
    public function setStalled(): void;

    /**
     * Set status as broken when Projector failed playing events.
     */
    public function setBroken(\Exception $exception): void;

    public function hasStatus(int $status): bool;

    /**
     * @return string Projector class name
     */
    public function getType(): string;


    public function getLastEventDate(): \DateTime;

    public function setLastEventDate(\DateTime $dateTime);
}
