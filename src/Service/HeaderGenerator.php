<?php

declare(strict_types=1);

namespace achertovsky\jwt\Service;

use achertovsky\jwt\Const\JwtClaims;

class HeaderGenerator
{
    private Base64UrlEncoder $encoder;

    public function __construct()
    {
        $this->encoder = new Base64UrlEncoder();
    }

    public function generateHeader(): string
    {
        return $this->encoder->encode(
            json_encode(
                [
                    JwtClaims::TYPE => 'JWT',
                ]
            )
        );
    }
}
