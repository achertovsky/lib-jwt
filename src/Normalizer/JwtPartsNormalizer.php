<?php

declare(strict_types=1);

namespace achertovsky\jwt\Normalizer;

use achertovsky\jwt\Exception\JwtException;

class JwtPartsNormalizer
{
    /**
     * @param array<string|int,string|int> $data
     * @return string
     */
    public function normalize(array $data): string
    {
        return base64_encode(json_encode($data));
    }

    /**
     * @param string $data
     * @return array<string|int,string|int>
     */
    public function denormalize(string $data): array
    {
        $json = base64_decode($data, true);
        if ($json === false) {
            throw new JwtException('Not base64');
        }

        $result = json_decode($json);
        if ($result === null) {
            throw new JwtException('Not a json in base64');
        }

        return $result;
    }
}
