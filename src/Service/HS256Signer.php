<?php

declare(strict_types=1);

namespace achertovsky\jwt\Service;

class HS256Signer implements SignerInterface
{
    private const ALGORITHM = 'sha256';

    private Base64UrlEncoder $encoder;

    public function __construct()
    {
        $this->encoder = new Base64UrlEncoder();
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
