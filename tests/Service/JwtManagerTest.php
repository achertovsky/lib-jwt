<?php

declare(strict_types=1);

namespace achertovsky\jwt\tests\Service;

use achertovsky\jwt\Entity\Payload;
use achertovsky\jwt\Exception\SignatureInvalidException;
use achertovsky\jwt\Exception\TokenExpiredException;
use achertovsky\jwt\Service\JwtManager;
use PHPUnit\Framework\TestCase;
use achertovsky\jwt\Service\SignerInterface;
use achertovsky\jwt\Service\TimeProviderInterface;

class JwtManagerTest extends TestCase
{
    private const JWT_SIGNATURE_KEY = 'key';
    private const JWT_PAYLOAD_SUB = '1';
    private const JWT_PAYLOAD_EXPIRE_AT = 1678984407;

    private const EXPIRED_JWT = 'eyJ0eXAiOiJKV1QifQ'
        . '.eyJzdWIiOiIxIiwiZXhwIjoxNjc4OTg0NDA3fQ'
        . '.sign'
    ;

    private TimeProviderInterface $timeProvider;
    private SignerInterface $signerMock;

    private JwtManager $manager;

    protected function setUp(): void
    {
        $this->timeProvider = $this->createMock(TimeProviderInterface::class);
        $this->signerMock = $this->createMock(SignerInterface::class);

        $this->manager = new JwtManager(
            $this->timeProvider,
            $this->signerMock,
            self::JWT_SIGNATURE_KEY
        );
    }

    public function testEncode(): void
    {
        $this->configureSignerSignReturn('sign');

        $this->signerMock
            ->method('sign')
            ->with(
                $this->anything(),
                self::JWT_SIGNATURE_KEY
            )
        ;

        $this->assertEquals(
            self::EXPIRED_JWT,
            $this->manager->encode(
                new Payload(
                    self::JWT_PAYLOAD_SUB,
                    self::JWT_PAYLOAD_EXPIRE_AT
                )
            )
        );
    }

    private function configureSignerSignReturn(string $willReturn): void
    {
        $this->signerMock
            ->method('sign')
            ->willReturn($willReturn)
        ;
    }

    public function testDecode(): void
    {
        $this->configureSignerSignReturn('sign');

        $this->assertEquals(
            new Payload(
                self::JWT_PAYLOAD_SUB,
                self::JWT_PAYLOAD_EXPIRE_AT
            ),
            $this->manager->decode(
                self::EXPIRED_JWT
            )
        );
    }

    public function testDecodeSignatureMismatch(): void
    {
        $this->expectException(SignatureInvalidException::class);

        $this->configureSignerSignReturn('anotherSignature');
        $this->timeProvider
            ->method('getTime')
            ->willReturn(self::JWT_PAYLOAD_EXPIRE_AT - 1)
        ;

        $this->assertEquals(
            new Payload(
                self::JWT_PAYLOAD_SUB,
                self::JWT_PAYLOAD_EXPIRE_AT
            ),
            $this->manager->decode(
                self::EXPIRED_JWT
            )
        );
    }

    public function testDecodeTokenExpired(): void
    {
        $this->expectException(TokenExpiredException::class);

        $this->configureSignerSignReturn('sign');
        $this->timeProvider
            ->method('getTime')
            ->willReturn(time())
        ;

        $this->assertEquals(
            new Payload(
                self::JWT_PAYLOAD_SUB,
                self::JWT_PAYLOAD_EXPIRE_AT
            ),
            $this->manager->decode(
                self::EXPIRED_JWT
            )
        );
    }
}
