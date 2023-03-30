<?php

declare(strict_types=1);

namespace achertovsky\jwt\Service;

use achertovsky\jwt\Const\JwtClaims;
use achertovsky\jwt\Entity\Payload;

class PayloadTransformer
{
    public function mapToArray(Payload $payload): array
    {
        return [
            JwtClaims::SUBJECT => $payload->getId(),
            JwtClaims::EXPIRATION_TIME => $payload->getExpireAt(),
        ];
    }

    public function mapToPayload(array $arrayPayload): Payload
    {
        return new Payload(
            $arrayPayload[JwtClaims::SUBJECT],
            $arrayPayload[JwtClaims::EXPIRATION_TIME]
        );
    }
}
