<?php

declare(strict_types=1);

namespace achertovsky\jwt\tests\Service;

use PHPUnit\Framework\TestCase;
use achertovsky\jwt\Entity\Payload;
use achertovsky\jwt\Const\JwtClaims;
use achertovsky\jwt\Normalizer\HmacTokenNormalizer;
use achertovsky\jwt\Exception\JwtException;
use achertovsky\jwt\Normalizer\JwtPartsNormalizer;
use achertovsky\jwt\Service\JwtManager;
use achertovsky\jwt\Normalizer\PayloadNormalizer;
use achertovsky\jwt\Service\HmacSignatureCreator;

class JwtManagerTest extends TestCase
{
    private const KEY = 'soprivatekeywow';
    private const ALGORITHM = 'sha256';
    private const PAYLOAD_SUB = '123';
    private JwtManager $manager;

    protected function setUp(): void
    {
        $this->manager = new JwtManager(
            new PayloadNormalizer(),
            new HmacTokenNormalizer(
                new HmacSignatureCreator(
                    self::ALGORITHM,
                    self::KEY
                )
            ),
            new JwtPartsNormalizer()
        );
    }

    public function testCreate(): void
    {
        $this->assertEquals(
            $this->generateJwt(),
            $this->manager
                ->create(
                    new Payload(
                        self::PAYLOAD_SUB
                    )
                )
        );
    }

    private function generateJwt(?int $exp = null): string
    {
        $encodedJson = json_encode(
            [
                JwtClaims::ALGORITHM => 'HS256',
                JwtClaims::TYPE => 'JWT'
            ]
        );

        $header = base64_encode(
            (string) $encodedJson
        );
        $payloadData = [
            JwtClaims::SUBJECT => self::PAYLOAD_SUB,
        ];
        if ($exp !== null) {
            $payloadData[JwtClaims::EXPIRATION_TIME] = $exp;
        }
        $encodedJson = json_encode(
            $payloadData
        );
        $payload = base64_encode(
            (string) $encodedJson
        );
        $signature = hash_hmac(
            'sha256',
            sprintf(
                '%s.%s',
                $header,
                $payload
            ),
            self::KEY
        );

        return sprintf(
            '%s.%s.%s',
            $header,
            $payload,
            $signature
        );
    }

    public function testValidate(): void
    {
        $this->assertTrue(
            $this->manager
                ->validate(
                    $this->generateJwt()
                )
        );
    }

    public function testValidateInvalidSignatureKey(): void
    {
        $manager = new JwtManager(
            new PayloadNormalizer(),
            new HmacTokenNormalizer(
                new HmacSignatureCreator(
                    self::ALGORITHM,
                    'wrongkey'
                )
            ),
            new JwtPartsNormalizer()
        );
        $this->assertFalse(
            $manager
                ->validate(
                    $this->generateJwt()
                )
        );
    }

    public function testValidateModifiedPayload(): void
    {
        $jwt = $this->generateJwt();
        list($header, $payload, $signature) = explode(
            '.',
            $jwt
        );

        $payload = json_decode(
            base64_decode(
                $payload
            ),
            true
        );

        $payload['newkey'] = 'whynot';

        $encodedJson = json_encode(
            $payload
        );
        $payload = base64_encode(
            (string) $encodedJson
        );

        $this->assertFalse(
            $this->manager
                ->validate(
                    sprintf(
                        '%s.%s.%s',
                        $header,
                        $payload,
                        $signature
                    )
                )
        );
    }

    public function testValidateExpired(): void
    {
        $this->assertFalse(
            $this->manager->validate($this->generateJwt(strtotime('-1 day')))
        );
    }

    public function testCreateAndValidate(): void
    {
        $jwt = $this->manager->create(
            new Payload(
                'subject'
            )
        );

        $this->assertTrue(
            $this->manager->validate($jwt)
        );
    }

    public function testDecode(): void
    {
        $payload = new Payload(
            'id',
            strtotime('+30 min')
        );
        $jwt = $this->manager->create($payload);

        $this->assertEquals(
            $payload,
            $this->manager->decode($jwt)
        );
    }

    public function testIssueValidateExceptionCatch(): void
    {
        $jwtPartsNormalizerMock = $this->createMock(JwtPartsNormalizer::class);
        $jwtPartsNormalizerMock
            ->method('denormalize')
            ->will($this->throwException(new JwtException()))
        ;

        /** @var JwtPartsNormalizer $jwtPartsNormalizerMock */
        $manager = new JwtManager(
            $this->createMock(PayloadNormalizer::class),
            $this->createMock(HmacTokenNormalizer::class),
            $jwtPartsNormalizerMock
        );

        $this->assertFalse(
            $manager->validate('token')
        );
    }
}
