<?php

declare(strict_types=1);

namespace achertovsky\jwt\tests\TestDouble\External;

use PHPUnit\Framework\TestCase;
use achertovsky\jwt\Entity\Payload;
use achertovsky\jwt\Exception\MalformedJwtException;
use achertovsky\jwt\Exception\SignatureInvalidException;
use achertovsky\jwt\Exception\TokenExpiredException;
use achertovsky\jwt\Exception\UnexpectedPayloadException;

class JwtManagerFakeTest extends TestCase
{
    private JwtManagerFake $jwtManagerFake;

    protected function setUp(): void
    {
        $this->jwtManagerFake = new JwtManagerFake();
    }

    public function testEncodeReturnsSuccessToken(): void
    {
        $payload = new Payload(
            'id',
            1720875030
        );
        $token = $this->jwtManagerFake->encode($payload);
        $this->assertEquals(
            JwtManagerFake::SUCCESS_TOKEN,
            $token
        );
    }

    public function testDecodeSuccessTokenReturnsExpectedPayload(): void
    {
        $payload = $this->jwtManagerFake->decode(
            JwtManagerFake::SUCCESS_TOKEN
        );
        $this->assertEquals(
            'id',
            $payload->getId()
        );
        $this->assertEquals(
            1720875030,
            $payload->getExpireAt()
        );
    }

    public function testDecodeMalformedTokenThrowsMalformedJwtException(): void
    {
        $this->expectException(MalformedJwtException::class);
        $this->expectExceptionMessage('JWT is missing or malformed');

        $this->jwtManagerFake->decode(
            JwtManagerFake::MALFORMED_TOKEN
        );
    }

    public function testDecodeInvalidSignatureTokenThrowsSignatureInvalidException(): void
    {
        $this->expectException(SignatureInvalidException::class);
        $this->expectExceptionMessage('JWT signature is invalid');

        $this->jwtManagerFake->decode(
            JwtManagerFake::INVALID_SIGNATURE_TOKEN
        );
    }

    public function testDecodeExpiredTokenThrowsTokenExpiredException(): void
    {
        $this->expectException(TokenExpiredException::class);
        $this->expectExceptionMessage('JWT has expired');

        $this->jwtManagerFake->decode(
            JwtManagerFake::EXPIRED_TOKEN
        );
    }

    public function testDecodeUnexpectedPayloadTokenThrowsUnexpectedPayloadException(): void
    {
        $this->expectException(UnexpectedPayloadException::class);
        $this->expectExceptionMessage('Unexpected payload');

        $this->jwtManagerFake->decode(
            JwtManagerFake::UNEXPECTED_PAYLOAD_TOKEN
        );
    }

    public function testAbleToReplacePayloadFields(): void
    {
        $newId = 'new_id';
        $expiredAt = 11111111111;

        $this->jwtManagerFake->id = $newId;
        $this->jwtManagerFake->expireAt = $expiredAt;

        $this->assertEquals(
            new Payload(
                $newId,
                $expiredAt
            ),
            $this->jwtManagerFake->decode('whatever token')
        );
    }
}
