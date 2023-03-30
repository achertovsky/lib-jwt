<?php

declare(strict_types=1);

namespace achertovsky\jwt\Service;

use achertovsky\jwt\Entity\Payload;
use achertovsky\jwt\Exception\SignatureInvalidException;
use achertovsky\jwt\Exception\TokenExpiredException;

class JwtManager
{
    private Base64UrlEncoder $encoder;
    private PayloadTransformer $payloadTransformer;
    private HeaderGenerator $headerGenerator;

    public function __construct(
        private SignerInterface $signer,
        private string $signKey
    ) {
        $this->encoder = new Base64UrlEncoder();
        $this->payloadTransformer = new PayloadTransformer();
        $this->headerGenerator = new HeaderGenerator();
    }

    public function encode(Payload $payload): string
    {
        $headerAndPayload = sprintf(
            '%s.%s',
            $this->headerGenerator->generateHeader(),
            $this->encoder->encode(
                json_encode(
                    $this->payloadTransformer->mapToArray($payload)
                )
            )
        );

        return sprintf(
            '%s.%s',
            $headerAndPayload,
            $this->signer->sign(
                $headerAndPayload,
                $this->signKey,
            )
        );
    }

    /**
     * @todo extract methods of validation
     */
    public function decode(string $token): Payload
    {
        list($header, $jsonPayload, $signature) = explode('.', $token);

        $expectedSignature = $this->signer->sign(
            sprintf(
                '%s.%s',
                $header,
                $jsonPayload
            ),
            $this->signKey
        );

        if ($signature !== $expectedSignature) {
            throw new SignatureInvalidException();
        }

        $payload = $this->payloadTransformer->mapToPayload(
            json_decode(
                $this->encoder->decode($jsonPayload),
                true
            )
        );

        if ($payload->getExpireAt() < time()) {
            throw new TokenExpiredException();
        }

        return $payload;
    }
}
