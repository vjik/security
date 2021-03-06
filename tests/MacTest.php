<?php

declare(strict_types=1);

namespace Yiisoft\Security\Tests;

require_once __DIR__ . '/MockHelper.php';

use PHPUnit\Framework\TestCase;
use Yiisoft\Security\DataIsTamperedException;
use Yiisoft\Security\Mac;
use Yiisoft\Security\MockHelper;

final class MacTest extends TestCase
{
    protected function tearDown(): void
    {
        MockHelper::resetMocks();
    }

    public function testOriginalMessageIsExtracted(): void
    {
        $mac = new Mac();
        $data = 'known data';
        $key = 'secret';

        $signedData = $mac->sign($data, $key);
        $this->assertNotSame($data, $signedData);
        $this->assertSame($data, $mac->getMessage($signedData, $key));
    }

    public function testDataTamperingIsDetected(): void
    {
        $mac = new Mac();
        $data = 'known data';
        $key = 'secret';

        $signedData = $mac->sign($data, $key);
        $signedData[strlen($signedData) - 1] = 'A';

        $this->expectException(DataIsTamperedException::class);
        $mac->getMessage($signedData, $key);
    }

    public function testSignException(): void
    {
        $this->expectException(\RuntimeException::class);

        MockHelper::$mock_hash_hmac = false;
        $mac = new Mac();
        $mac->sign('test', 'test');
    }

    public function testGetMessageException(): void
    {
        $this->expectException(\RuntimeException::class);

        MockHelper::$mock_hash_hmac = false;
        $mac = new Mac();
        $mac->getMessage('test', 'test');
    }
}
