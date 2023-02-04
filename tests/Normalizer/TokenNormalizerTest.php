<?php

declare(strict_types=1);

namespace achertovsky\jwt\tests\Normalizer;

use PHPUnit\Framework\TestCase;
use achertovsky\jwt\Entity\Token;
use achertovsky\jwt\Normalizer\TokenNormalizer;

class TokenDenormalizerTest extends TestCase
{
    private TokenNormalizer $normalizer;

    protected function setUp(): void
    {
        $this->normalizer = new TokenNormalizer();
    }

    public function testNormalize(): void
    {
        $this->assertEquals(
            $this->normalizer->normalize(
                new Token(
                    'header',
                    'payload',
                    'signature'
                )
            ),
            'header.payload.signature'
        );
    }

    public function testDenormalize(): void
    {
        $this->assertEquals(
            new Token(
                'header',
                'payload',
                'signature'
            ),
            $this->normalizer->denormalize('header.payload.signature')
        );
    }
}
