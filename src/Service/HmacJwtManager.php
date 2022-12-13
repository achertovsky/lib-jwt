<?php

declare(strict_types=1);

namespace achertovsky\jwt\Service;

use achertovsky\jwt\Entity\Payload;
use achertovsky\jwt\Exception\TokenExpiredException;
use achertovsky\jwt\Normalizer\PayloadNormalizer;

class HmacJwtManager implements JwtManagerInterface
{
    private const ALGO = 'sha256';
    private const HEADER_ALGO = 'HS256';

    public function __construct(
        private PayloadNormalizer $payloadNormalizer,
        private string $secret
    ) {
    }


    public function create(Payload $payload): string
    {
        $header = $this->encode(
            [
                'alg' => self::HEADER_ALGO,
                'typ' => 'JWT',
            ]
        );

        $payload = $this->encode(
            $this->payloadNormalizer->normalize(
                $payload
            )
        );

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

    private function encode(array $array): string
    {
        return base64_encode(
            json_encode(
                $array
            )
        );
    }

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
        list($header, $payload, $signature) = explode(
            '.',
            $token
        );

        $this->assureNotExpired($payload);

        return $this->sign(
            $header,
            $payload
        )
            === $signature
        ;
    }

    public function assureNotExpired(string $payload): void
    {
        $decodedPayload = $this->decode(
            $payload
        );

        if (isset($decodedPayload['exp']) && $decodedPayload['exp'] < time()) {
            throw new TokenExpiredException();
        }
    }

    private function decode(string $part): array
    {
        return json_decode(
            base64_decode(
                $part,
                true
            ),
            true
        );
    }
}
