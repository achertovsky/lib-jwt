<?php

declare(strict_types=1);

namespace achertovsky\jwt\Service;

interface SignerInterface
{
    public function sign(string $subject, string $key): string;
}
