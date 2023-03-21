<?php

declare(strict_types=1);

namespace achertovsky\jwt\Service;

use achertovsky\jwt\Const\JwtClaims;
use achertovsky\jwt\Entity\Payload;

class JwtManager
{
    private Base64UrlEncoder $encoder;

    public function __construct(
        private SignerInterface $signer,
        private string $signKey
    ) {
        $this->encoder = new Base64UrlEncoder();
    }

    public function encode(Payload $payload): string
    {
        $headerAndPayload = sprintf(
            '%s.%s',
            $this->generateHeader(),
            $this->encoder->encode(
                json_encode(
                    $this->mapToArray($payload)
                )
            )
        );

        return sprintf(
            '%s.%s',
            $headerAndPayload,
            $this->signer->sign(
                $headerAndPayload,
                $this->signKey,
            )
        );
    }

    private function generateHeader(): string
    {
        return $this->encoder->encode(
            json_encode(
                [
                    JwtClaims::TYPE => 'JWT',
                ]
            )
        );
    }

    private function mapToArray(Payload $payload): array
    {
        $arrayPayload = [
            JwtClaims::SUBJECT => $payload->getId(),
        ];

        if ($payload->getExpireAt() !== null) {
            $arrayPayload[JwtClaims::EXPIRATION_TIME] = $payload->getExpireAt();
        }

        return $arrayPayload;
    }
}
