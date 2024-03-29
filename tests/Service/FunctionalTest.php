<?php

declare(strict_types=1);

namespace achertovsky\jwt\tests\Service;

use PHPUnit\Framework\TestCase;
use achertovsky\jwt\Entity\Payload;
use achertovsky\jwt\Service\JwtManager;
use achertovsky\jwt\Service\HS256Signer;

class FunctionalTest extends TestCase
{
    private const KEY = 'key';
    private const JWT = 'eyJ0eXAiOiJKV1QifQ.eyJzdWIiOiIxMjMiLCJleHAiOjE1MTYyMzkwMjJ9.nx5NzssasukjE_xSar23Smu7ALHg42YVfDpkWetyGKI';
    private const SUBJECT = '123';
    private const EXPIRE_AT = 1516239022;

    private HS256Signer $signer;
    private JwtManager $manager;

    protected function setUp(): void
    {
        $this->signer = new HS256Signer();
        $this->manager = new JwtManager(
            $this->signer,
            self::KEY
        );
    }

    public function testCreatesRightJwt(): void
    {
        $this->assertEquals(
            self::JWT,
            $this->manager->encode(
                new Payload(
                    self::SUBJECT,
                    self::EXPIRE_AT
                )
            )
        );
    }

    public function testDecodesJwt(): void
    {
        $payload = new Payload(
            self::SUBJECT,
            time()+60,
        );

        $this->assertEquals(
            $payload,
            $this->manager->decode(
                $this->manager->encode($payload)
            )
        );
    }
}
