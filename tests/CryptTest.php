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
        $encrypted = Crypt::encrypt('secret-value', 'my-secret-passphrase');

        $this->assertNotSame('secret-value', $encrypted);
        $this->assertSame('secret-value', Crypt::decrypt($encrypted, 'my-secret-passphrase'));
    }

    public function testDecryptRejectsInvalidPayload(): void
    {
        $this->expectException(RuntimeException::class);
        Crypt::decrypt('not-base64', 'my-secret-passphrase');
    }

    public function testEncryptRejectsEmptyKey(): void
    {
        $this->expectException(RuntimeException::class);
        Crypt::encrypt('secret-value', '');
    }
}
