<?php

declare(strict_types=1);

namespace achertovsky\jwt\Service;

use achertovsky\jwt\Entity\Payload;
use achertovsky\jwt\Const\JwtClaims;
use achertovsky\jwt\Normalizer\PayloadNormalizer;
use achertovsky\jwt\Normalizer\TokenNormalizer;
use achertovsky\jwt\Exception\TokenExpiredException;

class HmacJwtManager implements JwtManagerInterface
{
    private const ALGO = 'sha256';
    private const HEADER_ALGO = 'HS256';

    public function __construct(
        private PayloadNormalizer $payloadNormalizer,
        private TokenNormalizer $tokenDenormalizer,
        private string $secret
    ) {
    }

    public function create(Payload $payload): string
    {
        $header = $this->encode(
            [
                JwtClaims::ALGORITHM => self::HEADER_ALGO,
                JwtClaims::TYPE => 'JWT',
            ]
        );

        $payload = $this->encode(
            $this->payloadNormalizer->normalize(
                $payload
            )
        );

        /**
         * @todo use token denormalizer
         */
        return sprintf(
            '%s.%s.%s',
            $header,
            $payload,
            $this->sign(
                $header,
                $payload
            )
        );
    }

    /**
     * @param array<string,string> $array
     * @return string
     */
    private function encode(array $array): string
    {
        $encodedJson = json_encode(
            $array
        );

        return base64_encode(
            (string) $encodedJson
        );
    }

    /**
     * @todo move method to separated service
     */
    private function sign(string $header, string $payload): string
    {
        return hash_hmac(
            self::ALGO,
            sprintf(
                '%s.%s',
                $header,
                $payload
            ),
            $this->secret
        );
    }

    public function validate(string $token): bool
    {
        $token = $this->tokenDenormalizer->denormalize($token);

        $this->assureNotExpired($token->getPayload());

        return $this->sign(
            $token->getHeader(),
            $token->getPayload()
        )
            === $token->getSignature()
        ;
    }

    public function assureNotExpired(string $payload): void
    {
        $decodedPayload = $this->payloadToArray(
            $payload
        );

        if (
            isset($decodedPayload[JwtClaims::EXPIRATION_TIME])
            && (int) $decodedPayload[JwtClaims::EXPIRATION_TIME] < time()
        ) {
            throw new TokenExpiredException();
        }
    }

    /**
     * @param string $encodedData
     * @return array<string,array<string,string>>
     */
    private function payloadToArray(string $encodedData): array
    {
        $base64Decoded = base64_decode(
            $encodedData,
            true
        );

        if ($base64Decoded === false) {
            return [];
        }

        $jsonDecoded = json_decode(
            $base64Decoded,
            true
        );

        if ($jsonDecoded === null) {
            return [];
        }

        return $jsonDecoded;
    }

    public function decode(string $token): Payload
    {
        $token = $this->tokenDenormalizer->denormalize($token);

        $payload = $this->payloadToArray($token->getPayload());

        return $this->payloadNormalizer->denormalize($payload);
    }
}
