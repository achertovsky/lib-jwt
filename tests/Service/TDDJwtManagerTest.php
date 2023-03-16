<?php

declare(strict_types=1);

namespace achertovsky\jwt\tests\Service;

use achertovsky\jwt\Service\TDDJwtManager;
use PHPUnit\Framework\TestCase;

class TDDJwtManagerTest extends TestCase
{
    private const JWT_HEADER_ALGO = 'HS256';
    private const JWT_HEADER_TYPE = 'JWT';
    private const JWT_SIGNATURE_KEY = 'key';
    private const JWT_PAYLOAD_SUB = '1';
    private const JWT_PAYLOAD_EXPIRE_AT = 1678984407;

    private const JWT_HEADER = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9';

    private const JWT = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9'
        .'.eyJzdWIiOiIxIn0'
        .'.pHaus62nD9DrNbTCRTVOOhnRbbXZnL031tfLEk_qP5s'
    ;
    private const EXPIRED_JWT = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9'
        . '.eyJzdWIiOiIxIiwiZXhwIjoxNjc4OTg0NDA3fQ'
        . '.EtinKhAWI4_UjvQPylVh9Zc8iiKzOQSRPOxjbtbhjA8'
    ;

    private TDDJwtManager $manager;
}
