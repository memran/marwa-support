<?php

declare(strict_types=1);

namespace Marwa\Support\Tests;

use Marwa\Support\Crypt;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class CryptTest extends TestCase
{
    public function testEncryptAndDecryptRoundTrip(): void
    {
        $encrypted = Crypt::encrypt('secret-value', 'app-key');

        $this->assertNotSame('secret-value', $encrypted);
        $this->assertSame('secret-value', Crypt::decrypt($encrypted, 'app-key'));
    }

    public function testDecryptRejectsInvalidPayload(): void
    {
        $this->expectException(RuntimeException::class);
        Crypt::decrypt('not-base64', 'app-key');
    }

    public function testEncryptRejectsEmptyKey(): void
    {
        $this->expectException(RuntimeException::class);
        Crypt::encrypt('secret-value', '');
    }
}
