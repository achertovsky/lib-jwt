<?php

declare(strict_types=1);

namespace achertovsky\jwt\tests\Service;

use achertovsky\jwt\Entity\Payload;
use achertovsky\jwt\Service\HS256Signer;
use achertovsky\jwt\Service\JwtManager;
use achertovsky\jwt\tests\TestDouble\Internal\ClockFake;
use PHPUnit\Framework\TestCase;
use Psr\Clock\ClockInterface;

class FunctionalTest extends TestCase
{
    private const string KEY = 'key';
    private const string JWT = 'eyJ0eXAiOiJKV1QifQ.eyJzdWIiOiIxMjMiLCJleHAiOjE1MTYyMzkwMjJ9.nx5NzssasukjE_xSar23Smu7ALHg42YVfDpkWetyGKI';
    private const string SUBJECT = '123';
    private const int EXPIRE_AT = 1516239022;

    private HS256Signer $signer;
    private JwtManager $manager;
    private ClockInterface $clock;

    protected function setUp(): void
    {
        $this->signer = new HS256Signer();
        $this->clock = new ClockFake();
        $this->manager = new JwtManager(
            $this->clock,
            self::KEY,
            $this->signer
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
            $this->clock->now()->getTimestamp() + 60,
        );

        $this->assertEquals(
            $payload,
            $this->manager->decode(
                $this->manager->encode($payload)
            )
        );
    }
}
