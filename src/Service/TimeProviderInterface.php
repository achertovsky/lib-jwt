<?php

declare(strict_types=1);

namespace achertovsky\jwt\Service;

interface TimeProviderInterface
{
    public function getTime(): int;
}
