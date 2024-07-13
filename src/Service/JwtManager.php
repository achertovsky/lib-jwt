<?php

declare(strict_types=1);

namespace achertovsky\jwt\Service;

use achertovsky\jwt\Entity\Payload;
use achertovsky\jwt\Exception\MalformedJwtException;
use achertovsky\jwt\Exception\SignatureInvalidException;
use achertovsky\jwt\Exception\TokenExpiredException;
use Psr\Clock\ClockInterface;

class JwtManager
{
    private const int JWT_CHUNKS_AMOUNT = 3;

    public function __construct(
        private ClockInterface $clock,
        private string $signKey,
        private SignerInterface $signer = new HS256Signer(),
        private Base64UrlEncoder $encoder = new Base64UrlEncoder(),
        private PayloadTransformer $payloadTransformer = new PayloadTransformer(),
        private HeaderGenerator $headerGenerator = new HeaderGenerator()
    ) {
    }

    public function encode(Payload $payload): string
    {
        $headerAndPayload = sprintf(
            '%s.%s',
            $this->headerGenerator->generateHeader(),
            $this->encoder->encode(
                json_encode(
                    $this->payloadTransformer->mapToArray($payload)
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

    public function decode(string $token): Payload
    {
        list($header, $jsonPayload, $signature) = $this->getTokenParts($token);

        $this->validateSignature(
            $header,
            $jsonPayload,
            $signature
        );

        $payload = $this->payloadTransformer->mapToPayload(
            $this->getDecodedJson($jsonPayload)
        );

        $this->validateTokenNotExpired($payload);

        return $payload;
    }

    private function getTokenParts(
        string $token
    ): array {
        $tokenParts = explode('.', $token, self::JWT_CHUNKS_AMOUNT);
        if (count($tokenParts) !== self::JWT_CHUNKS_AMOUNT) {
            throw new MalformedJwtException();
        }

        return $tokenParts;
    }

    private function validateSignature(
        string $header,
        string $payload,
        string $signature
    ): void {
        $expectedSignature = $this->signer->sign(
            sprintf(
                '%s.%s',
                $header,
                $payload
            ),
            $this->signKey
        );

        if ($signature !== $expectedSignature) {
            throw new SignatureInvalidException();
        }
    }

    private function getDecodedJson(string $payload): array
    {
        $decodedPayload = json_decode(
            $this->encoder->decode($payload),
            true
        );

        if ($decodedPayload === null) {
            throw new MalformedJwtException();
        }

        return $decodedPayload;
    }

    private function validateTokenNotExpired(Payload $payload): void
    {
        if ($payload->getExpireAt() < $this->clock->now()->getTimestamp()) {
            throw new TokenExpiredException();
        }
    }
}
