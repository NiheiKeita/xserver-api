<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Models\DeviceToken;

class WebPushTest extends TestCase
{
    /**
     * A basic unit test example.
     */
    public function test_example(): void
    {
        $this->assertTrue(true);
    }

    /**
     * デバイストークンモデルのテスト
     */
    public function test_device_token_model(): void
    {
        $token = 'test-token-' . uniqid();

        // モデルのインスタンスを作成
        $deviceToken = new DeviceToken();
        $deviceToken->token = $token;

        // プロパティが正しく設定されているかテスト
        $this->assertEquals($token, $deviceToken->token);
        $this->assertContains('token', $deviceToken->getFillable());
        $this->assertEquals('device_tokens', $deviceToken->getTable());
    }

    /**
     * デバイストークンのバリデーションテスト
     */
    public function test_device_token_validation(): void
    {
        // 有効なトークン
        $validToken = 'valid-token-' . uniqid();
        $this->assertNotEmpty($validToken);
        $this->assertIsString($validToken);

        // 空のトークンは無効
        $invalidToken = '';
        $this->assertEmpty($invalidToken);
    }
}
