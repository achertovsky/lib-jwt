<?php

declare(strict_types=1);

namespace achertovsky\jwt\Service;

use achertovsky\jwt\Const\JwtClaims;
use achertovsky\jwt\Entity\Payload;
use achertovsky\jwt\Exception\UnexpectedPayloadException;

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
        $this->assureRequiredPayloadFieldsPresent($arrayPayload);

        return new Payload(
            $arrayPayload[JwtClaims::SUBJECT],
            $arrayPayload[JwtClaims::EXPIRATION_TIME]
        );
    }

    private function assureRequiredPayloadFieldsPresent(array $arrayPayload): void
    {
        $requiredFields = [
            JwtClaims::SUBJECT,
            JwtClaims::EXPIRATION_TIME,
        ];

        $values = array_intersect(
            $requiredFields,
            array_keys($arrayPayload)
        );

        if (count($values) !== count($requiredFields)) {
            throw new UnexpectedPayloadException();
        }
    }
}
