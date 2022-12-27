<?php

declare(strict_types=1);

namespace achertovsky\jwt\Normalizer;

use achertovsky\jwt\Const\JwtClaims;
use achertovsky\jwt\Entity\Payload;

class PayloadNormalizer
{
    /**
     * @param Payload $payload
     * @return array<string,string>
     */
    public function normalize(Payload $payload): array
    {
        $result = [
            JwtClaims::SUBJECT => $payload->getId(),
            JwtClaims::EXPIRATION_TIME => (string) $payload->getExpireAt(),
        ];

        return array_filter(
            $result
        );
    }
}
