<?php

declare(strict_types=1);

namespace achertovsky\jwt\Service;

class Base64UrlEncoder
{
    public function encode(string $payload): string
    {
        return rtrim(
            strtr(
                base64_encode(
                    $payload
                ),
                '+/',
                '-_'
            ),
            '='
        );
    }

    public function decode(string $payload): string
    {
        return base64_decode(
            strtr(
                $payload,
                '+/',
                '-_'
            )
        );
    }
}
