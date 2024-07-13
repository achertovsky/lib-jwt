<?php

declare(strict_types=1);

namespace achertovsky\jwt\tests\TestDouble\Internal;

use Psr\Clock\ClockInterface;

class ClockFake implements ClockInterface
{
    public const DATE_TIME = '2024-07-13 15:32';

    public function now(): \DateTimeImmutable
    {
        return new \DateTimeImmutable(self::DATE_TIME);
    }
}
