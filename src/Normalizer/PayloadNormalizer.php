<?php

declare(strict_types=1);

namespace achertovsky\jwt\Normalizer;

use achertovsky\jwt\Entity\Payload;

class PayloadNormalizer
{
    public function normalize(Payload $payload): array
    {
        $result = [
            'sub' => $payload->getId(),
            'exp' => $payload->getExpireAt(),
        ];

        return array_filter(
            $result
        );
    }
}
