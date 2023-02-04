<?php

declare(strict_types=1);

namespace achertovsky\jwt\tests\Service;

use PHPUnit\Framework\TestCase;
use achertovsky\jwt\Service\HmacSignatureCreator;

class HmacSignatureCreatorTest extends TestCase
{
    private const ALGORITHM = 'sha256';
    private const SECRET_KEY = 'somuchsecretwow';

    private HmacSignatureCreator $creator;

    protected function setUp(): void
    {
        $this->creator = new SignatureCreator(
            self::ALGORITHM,
            self::SECRET_KEY
        );
    }

    public function testSign(): void
    {
        $toBeSigned = 'string';

        $this->assertEquals(
            hash_hmac(
                self::ALGORITHM,
                $toBeSigned,
                self::SECRET_KEY
            ),
            $this->creator->sign($toBeSigned)
        );
    }
}
