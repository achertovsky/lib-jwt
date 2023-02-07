<?php

declare(strict_types=1);

namespace achertovsky\jwt\tests\Normalizer;

use PHPUnit\Framework\TestCase;
use achertovsky\jwt\Entity\Payload;
use achertovsky\jwt\Const\JwtClaims;
use achertovsky\jwt\Normalizer\PayloadNormalizer;

class PayloadNormalizerTest extends TestCase
{
    private const ID = 'id';
    private const TIME = 1675514814;

    private PayloadNormalizer $normalizer;

    protected function setUp(): void
    {
        $this->normalizer = new PayloadNormalizer();
    }

    /**
     * @param array<string,string|null|int> $normalizedData
     * @param Payload $denormalizedData
     *
     * @dataProvider dataNormalize
     */
    public function testNormalize(
        array $normalizedData,
        Payload $denormalizedData
    ): void {
        $this->assertEquals(
            $normalizedData,
            $this->normalizer->normalize($denormalizedData)
        );
    }

    /**
     * @return array<array<array<string,string|int>|Payload>>
     */
    public function dataNormalize(): array
    {
        return [
            [
                [
                    JwtClaims::SUBJECT => self::ID,
                    JwtClaims::EXPIRATION_TIME => self::TIME
                ],
                new Payload(
                    self::ID,
                    self::TIME
                )
            ],
            [
                [
                    JwtClaims::SUBJECT => self::ID
                ],
                new Payload(
                    self::ID
                )
            ],
        ];
    }

    /**
     * @param Payload $denormalizedData
     * @param array<string,string|null|int> $normalizedData
     *
     * @dataProvider dataDenormalize
     */
    public function testDenormalize(
        Payload $denormalizedData,
        array $normalizedData
    ): void {
        $this->assertEquals(
            $denormalizedData,
            $this->normalizer->denormalize($normalizedData)
        );
    }

    /**
     * @return array<array<array<string,string|int>|Payload>>
     */
    public function dataDenormalize(): array
    {
        return [
            [
                new Payload(
                    self::ID,
                    self::TIME
                ),
                [
                    JwtClaims::SUBJECT => self::ID,
                    JwtClaims::EXPIRATION_TIME => self::TIME
                ],
            ],
            [
                new Payload(
                    self::ID
                ),
                [
                    JwtClaims::SUBJECT => self::ID
                ],
            ],
        ];
    }
}
