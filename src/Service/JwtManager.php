<?php

declare(strict_types=1);

namespace achertovsky\jwt\Service;

use achertovsky\jwt\Entity\Token;
use achertovsky\jwt\Entity\Payload;
use achertovsky\jwt\Const\JwtClaims;
use achertovsky\jwt\Exception\JwtException;
use achertovsky\jwt\Normalizer\PayloadNormalizer;
use achertovsky\jwt\Normalizer\JwtPartsNormalizer;
use achertovsky\jwt\Exception\TokenExpiredException;
use achertovsky\jwt\Normalizer\TokenNormalizerInterface;

class JwtManager
{
    private const HEADER_ALGO = 'HS256';

    public function __construct(
        private PayloadNormalizer $payloadNormalizer,
        private TokenNormalizerInterface $tokenNormalizer,
        private JwtPartsNormalizer $jwtPartsNormalizer
    ) {
    }

    public function create(Payload $payload): string
    {
        $token = new Token(
            $this->jwtPartsNormalizer->normalize(
                [
                    JwtClaims::ALGORITHM => self::HEADER_ALGO,
                    JwtClaims::TYPE => 'JWT',
                ]
            ),
            $this->jwtPartsNormalizer->normalize(
                $this->payloadNormalizer->normalize(
                    $payload
                )
            )
        );

        return $this->tokenNormalizer->normalize($token);
    }

    public function validate(string $token): bool
    {
        try {
            $tokenEntity = $this->tokenNormalizer->denormalize($token);
            $ourSignedToken = $this->tokenNormalizer->normalize(
                $tokenEntity
            );

            $this->assureNotExpired($tokenEntity->getPayload());
        } catch (JwtException $exception) {
            return false;
        }

        return $token === $ourSignedToken;
    }

    public function assureNotExpired(string $payload): void
    {
        $decodedPayload = $this->jwtPartsNormalizer->denormalize(
            $payload
        );

        if (
            isset($decodedPayload[JwtClaims::EXPIRATION_TIME])
            && (int) $decodedPayload[JwtClaims::EXPIRATION_TIME] < time()
        ) {
            throw new TokenExpiredException();
        }
    }

    public function decode(string $token): Payload
    {
        $token = $this->tokenNormalizer->denormalize($token);

        return $this->payloadNormalizer->denormalize(
            $this->jwtPartsNormalizer->denormalize($token->getPayload())
        );
    }
}
