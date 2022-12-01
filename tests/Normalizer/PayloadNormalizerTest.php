<?php

declare(strict_types=1);

namespace achertovsky\jwt\tests\Normalizer;

use PHPUnit\Framework\TestCase;
use achertovsky\jwt\Entity\Payload;
use achertovsky\jwt\Normalizer\PayloadNormalizer;

class PayloadNormalizerTest extends TestCase
{
    public function testNormalize(): void
    {
        $normalizer = new PayloadNormalizer();

        $expTime = time();

        $this->assertEquals(
            [
                'sub' => '1',
                'exp' => $expTime
            ],
            $normalizer->normalize(
                new Payload(
                    '1',
                    $expTime
                )
            )
        );
    }

    public function testNormalizeRequiredOnly(): void
    {
        $normalizer = new PayloadNormalizer();

        $this->assertEquals(
            [
                'sub' => '1',
            ],
            $normalizer->normalize(
                new Payload(
                    '1',
                )
            )
        );
    }
}
