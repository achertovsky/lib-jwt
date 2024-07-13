<?php

declare(strict_types=1);

namespace achertovsky\jwt\tests\Service;

use achertovsky\jwt\Entity\Payload;
use achertovsky\jwt\Exception\MalformedJwtException;
use achertovsky\jwt\Exception\SignatureInvalidException;
use achertovsky\jwt\Exception\TokenExpiredException;
use achertovsky\jwt\Exception\UnexpectedPayloadException;
use achertovsky\jwt\Service\JwtManager;
use achertovsky\jwt\Service\SignerInterface;
use achertovsky\jwt\tests\TestDouble\Internal\ClockFake;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Psr\Clock\ClockInterface;

class JwtManagerTest extends TestCase
{
    private const JWT_SIGNATURE_KEY = 'key';
    private const JWT_PAYLOAD_SUB = '1';
    private const JWT_PAYLOAD_EXPIRE_AT = 1678984407;

    private const MOCKED_SIGN = 'sign';

    private const EXPIRED_JWT = 'eyJ0eXAiOiJKV1QifQ'
        . '.eyJzdWIiOiIxIiwiZXhwIjoxNjc4OTg0NDA3fQ'
        . '.' . self::MOCKED_SIGN
    ;

    private SignerInterface $signerMock;

    private JwtManager $manager;

    private ClockInterface $clock;

    protected function setUp(): void
    {
        $this->signerMock = $this->createMock(SignerInterface::class);
        $this->clock = new ClockFake();


        $this->manager = new JwtManager(
            $this->clock,
            self::JWT_SIGNATURE_KEY,
            $this->signerMock
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

        $payload = new Payload(
            self::JWT_PAYLOAD_SUB,
            $this->clock->now()->getTimestamp() + 1000
        );

        $this->assertEquals(
            $payload,
            $this->manager->decode(
                $this->manager->encode($payload)
            )
        );
    }

    public function testDecodeSignatureMismatch(): void
    {
        $this->expectException(SignatureInvalidException::class);

        $this->configureSignerSignReturn('anotherSignature');

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

        $this->configureSignerSignReturn(self::MOCKED_SIGN);

        $this->manager->decode(
            self::EXPIRED_JWT
        );
    }

    public function testIssueDecodeJwtPayloadDontHaveExpectedFields(): void
    {
        $this->expectException(UnexpectedPayloadException::class);

        $this->configureSignerSignReturn(self::MOCKED_SIGN);

        $this->manager->decode(
            sprintf(
                '%s.%s.%s',
                'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9',
                'eyJzdWJqIjoiMTIzNDU2Nzg5MCIsImlhdCI6MTUxNjIzOTAyMn0',
                self::MOCKED_SIGN
            )
        );
    }

    #[DataProvider('dataIssueMalformedJwt')]
    public function testIssueMalformedJwt(
        string $toDecode
    ): void {
        $this->expectException(MalformedJwtException::class);

        $this->configureSignerSignReturn(self::MOCKED_SIGN);

        $this->manager->decode(
            $toDecode
        );
    }

    public static function dataIssueMalformedJwt(): array
    {
        return [
            'just random string' => [
                'definitely not a jwt',
            ],
            '3 parts, but string' => [
                'not.valid.'.self::MOCKED_SIGN,
            ],
        ];
    }
}
