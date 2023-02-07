<?php

declare(strict_types=1);

namespace achertovsky\jwt\tests\Normalizer;

use PHPUnit\Framework\TestCase;
use achertovsky\jwt\Entity\Token;
use achertovsky\jwt\Normalizer\HmacTokenNormalizer;
use achertovsky\jwt\Service\HmacSignatureCreator;

class HmacTokenNormalizerTest extends TestCase
{
    private HmacTokenNormalizer $normalizer;

    protected function setUp(): void
    {
        $hmacSignatureCreatorMock = $this->createMock(HmacSignatureCreator::class);
        $hmacSignatureCreatorMock
            ->method('sign')
            ->willReturn('signature')
        ;

        /** @var HmacSignatureCreator $hmacSignatureCreatorMock */
        $this->normalizer = new HmacTokenNormalizer(
            $hmacSignatureCreatorMock
        );
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