<?php

declare(strict_types=1);

namespace achertovsky\jwt\Normalizer;

use achertovsky\jwt\Entity\Token;

interface TokenNormalizerInterface
{
    /**
     * Expected to contain signing part inside
     * @param Token $token
     * @return string
     */
    public function normalize(Token $token): string;

    public function denormalize(string $token): Token;
}
