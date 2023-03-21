<?php

declare(strict_types=1);

namespace achertovsky\jwt\Service;

use achertovsky\jwt\Const\JwtClaims;
use achertovsky\jwt\Entity\Payload;

class PayloadTransformer
{
    public function mapToArray(Payload $payload): array
    {
        $arrayPayload = [
            JwtClaims::SUBJECT => $payload->getId(),
        ];

        if ($payload->getExpireAt() !== null) {
            $arrayPayload[JwtClaims::EXPIRATION_TIME] = $payload->getExpireAt();
        }

        return $arrayPayload;
    }

    public function mapToPayload(array $arrayPayload): Payload
    {
        return new Payload(
            $arrayPayload[JwtClaims::SUBJECT],
            $arrayPayload[JwtClaims::EXPIRATION_TIME]
        );
    }
}
