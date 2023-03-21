<?php

declare(strict_types=1);

namespace achertovsky\jwt\Service;

class TimeProvider implements TimeProviderInterface
{
    public function getTime(): int
    {
        return time();
    }
}
