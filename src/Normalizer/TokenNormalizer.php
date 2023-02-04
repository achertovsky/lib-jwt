<?php

declare(strict_types=1);

namespace achertovsky\jwt\Normalizer;

use achertovsky\jwt\Entity\Token;

class TokenNormalizer
{
    public function normalize(Token $token): string
    {
        return sprintf(
            '%s.%s.%s',
            $token->getHeader(),
            $token->getPayload(),
            'something'
        );
    }

    public function denormalize(string $token): Token
    {
        list($header, $payload, $signature) = explode(
            '.',
            $token
        );

        return new Token(
            $header,
            $payload,
            $signature
        );
    }
}
