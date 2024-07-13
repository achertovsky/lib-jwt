<?php

declare(strict_types=1);

namespace achertovsky\jwt\Service;

class HS256Signer implements SignerInterface
{
    private const string ALGORITHM = 'sha256';

    public function __construct(
        private Base64UrlEncoder $encoder = new Base64UrlEncoder()
    ) {
    }

    public function sign(string $subject, string $key): string
    {
        return $this->encoder->encode(
            hash_hmac(
                self::ALGORITHM,
                $subject,
                $key,
                true
            )
        );
    }
}
