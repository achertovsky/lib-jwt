<?php

declare(strict_types=1);

namespace achertovsky\jwt\tests\Service;

use PHPUnit\Framework\TestCase;
use achertovsky\jwt\Service\HS256Signer;

class HS256SignerTest extends TestCase
{
    private const string SIGN_DATA = 'TEST123';
    private const string SIGN_KEY = 'secretKey';
    private const string EXPECTED_RESULT = 'mjT4BgTzV4wQJp3UjRPAjyEkk6bREN1XGRlR7UNKhbg';

    private HS256Signer $signer;

    protected function setUp(): void
    {
        $this->signer = new HS256Signer();
    }

    public function testSuccess(): void
    {
        $this->assertEquals(
            self::EXPECTED_RESULT,
            $this->signer->sign(self::SIGN_DATA, self::SIGN_KEY)
        );
    }

    public function testFailure(): void
    {
        $this->assertNotEquals(
            self::EXPECTED_RESULT,
            $this->signer->sign(self::SIGN_DATA, 'wrongKey')
        );
    }
}
