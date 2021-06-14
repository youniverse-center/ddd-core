<?php

namespace Yc\DddCore;

interface ProjectorInterface
{
    const MODE_FROM_NOW = 'from_now';
    const MODE_FROM_BEGINNING = 'from_beginning';

    public function getMode(): string;
    public function handleEvent(Event $event): void;
}
