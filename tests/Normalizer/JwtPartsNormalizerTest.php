<?php

declare(strict_types=1);

namespace achertovsky\jwt\tests\Normalizer;

use PHPUnit\Framework\TestCase;
use achertovsky\jwt\Exception\JwtException;
use achertovsky\jwt\Normalizer\JwtPartsNormalizer;

class JwtPartsNormalizerTest extends TestCase
{
    private JwtPartsNormalizer $normalizer;

    protected function setUp(): void
    {
        $this->normalizer = new JwtPartsNormalizer();
    }

    public function testNormalize(): void
    {
        $this->assertEquals(
            base64_encode(json_encode(['test'])),
            $this->normalizer->normalize(['test'])
        );
    }

    public function testDenormalize(): void
    {
        $this->assertEquals(
            ['test'],
            $this->normalizer->denormalize(base64_encode(json_encode(['test'])))
        );
    }

    public function testIssueDenormalizeNotBase64(): void
    {
        $this->expectException(JwtException::class);
        $this->expectExceptionMessage('Not base64');
        $this->normalizer->denormalize('notbase64');
    }

    public function testIssueDenormalizeNotJson(): void
    {
        $this->expectException(JwtException::class);
        $this->expectExceptionMessage('Not a json in base64');
        $this->normalizer->denormalize(base64_encode('notajson'));
    }
}

