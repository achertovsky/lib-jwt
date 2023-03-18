<?php

declare(strict_types=1);

namespace achertovsky\jwt\tests\Service;

use achertovsky\jwt\Entity\Payload;
use achertovsky\jwt\Service\TDDJwtManager;
use PHPUnit\Framework\TestCase;
use achertovsky\jwt\Service\SignerInterface;

class TDDJwtManagerTest extends TestCase
{
    private const JWT_SIGNATURE_KEY = 'key';
    private const JWT_PAYLOAD_SUB = '1';
    private const JWT_PAYLOAD_EXPIRE_AT = 1678984407;

    private const JWT = 'eyJ0eXAiOiJKV1QifQ'
        . '.eyJzdWIiOiIxIn0'
        . '.sign'
    ;
    private const EXPIRED_JWT = 'eyJ0eXAiOiJKV1QifQ'
        . '.eyJzdWIiOiIxIiwiZXhwIjoxNjc4OTg0NDA3fQ'
        . '.sign'
    ;

    private SignerInterface $signerMock;

    private TDDJwtManager $manager;

    protected function setUp(): void
    {
        $this->signerMock = $this->createMock(SignerInterface::class);
        $this->signerMock
            ->method('sign')
            ->willReturn('sign')
        ;
        $this->manager = new TDDJwtManager(
            $this->signerMock,
            self::JWT_SIGNATURE_KEY
        );
    }

    public function testGeneratesPayload(): void
    {
        $this->signerMock
            ->method('sign')
            ->with(
                $this->anything(),
                self::JWT_SIGNATURE_KEY
            )
        ;

        $this->assertEquals(
            self::JWT,
            $this->manager->create(
                new Payload(
                    self::JWT_PAYLOAD_SUB
                )
            )
        );
    }

    public function testGeneratesPayloadWithExpirationTime(): void
    {
        $this->assertEquals(
            self::EXPIRED_JWT,
            $this->manager->create(
                new Payload(
                    self::JWT_PAYLOAD_SUB,
                    self::JWT_PAYLOAD_EXPIRE_AT
                )
            )
        );
    }
}
