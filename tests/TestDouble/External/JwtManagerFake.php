<?php

declare(strict_types=1);

namespace achertovsky\jwt\tests\TestDouble\External;

use achertovsky\jwt\Entity\Payload;
use achertovsky\jwt\Exception\MalformedJwtException;
use achertovsky\jwt\Exception\SignatureInvalidException;
use achertovsky\jwt\Exception\TokenExpiredException;
use achertovsky\jwt\Exception\UnexpectedPayloadException;
use achertovsky\jwt\Service\JwtManager;

class JwtManagerFake extends JwtManager
{
    public const string SUCCESS_TOKEN = 'successToken';
    public const string MALFORMED_TOKEN = 'malformedToken';
    public const string INVALID_SIGNATURE_TOKEN = 'invalidSignatureToken';
    public const string EXPIRED_TOKEN = 'expiredToken';
    public const string UNEXPECTED_PAYLOAD_TOKEN = 'unexpectedPayloadToken';
    public const string PAYLOAD_ID = 'id';
    public const int PAYLOAD_EXPIRE_AT = 1720875030;

    public function __construct(
        private string $successToken = self::SUCCESS_TOKEN,
        private string $id = self::PAYLOAD_ID,
        private int $expireAt = self::PAYLOAD_EXPIRE_AT,
        private string $malformedToken = self::MALFORMED_TOKEN,
        private string $invalidSignatureToken = self::INVALID_SIGNATURE_TOKEN,
        private string $expiredToken = self::EXPIRED_TOKEN,
        private string $unexpectedPayloadToken = self::UNEXPECTED_PAYLOAD_TOKEN
    ) {
    }

    public function encode(Payload $payload): string
    {
        return $this->successToken;
    }

    public function decode(string $token): Payload
    {
        switch ($token) {
            case $this->malformedToken:
                throw new MalformedJwtException();
            case $this->invalidSignatureToken:
                throw new SignatureInvalidException();
            case $this->expiredToken:
                throw new TokenExpiredException();
            case $this->unexpectedPayloadToken:
                throw new UnexpectedPayloadException();
        }

        return new Payload(
            $this->id,
            $this->expireAt
        );
    }
}
