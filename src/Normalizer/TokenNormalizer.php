<?php

declare(strict_types=1);

namespace achertovsky\jwt\Normalizer;

use achertovsky\jwt\Entity\Token;
use achertovsky\jwt\Service\HmacSignatureCreator;

class TokenNormalizer
{
    public function __construct(
        private HmacSignatureCreator $signatureCreator
    ) {
        $this->signatureCreator = $signatureCreator;
    }

    public function normalize(Token $token): string
    {
        return sprintf(
            '%s.%s.%s',
            $token->getHeader(),
            $token->getPayload(),
            $this->signatureCreator->sign(
                sprintf(
                    '%s.%s',
                    $token->getHeader(),
                    $token->getPayload()
                )
            )
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
