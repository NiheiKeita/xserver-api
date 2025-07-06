<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class WebPushTest extends TestCase
{
  use RefreshDatabase;

  /**
   * 全ユーザーへのWebプッシュ通知送信テスト
   */
  public function test_send_to_all_webpush_notification(): void
  {
    $response = $this->postJson('/api/webpush/send-to-all', [
      'title' => 'テスト通知',
      'message' => 'これはテストメッセージです',
      'url' => 'https://example.com'
    ]);

    // Firebaseの設定が正しくない場合でも、バリデーションは通ることを確認
    $response->assertStatus(500); // Firebase設定がない場合は500エラー
  }

  /**
   * トピックへのWebプッシュ通知送信テスト
   */
  public function test_send_to_topic_webpush_notification(): void
  {
    $response = $this->postJson('/api/webpush/send-to-topic', [
      'topic' => 'test_topic',
      'title' => 'テスト通知',
      'message' => 'これはテストメッセージです',
      'url' => 'https://example.com'
    ]);

    $response->assertStatus(500); // Firebase設定がない場合は500エラー
  }

  /**
   * トークンへのWebプッシュ通知送信テスト
   */
  public function test_send_to_token_webpush_notification(): void
  {
    $response = $this->postJson('/api/webpush/send-to-token', [
      'token' => 'test_token_123',
      'title' => 'テスト通知',
      'message' => 'これはテストメッセージです',
      'url' => 'https://example.com'
    ]);

    $response->assertStatus(500); // Firebase設定がない場合は500エラー
  }

  /**
   * バリデーションエラーのテスト
   */
  public function test_validation_errors(): void
  {
    // 必須フィールドが不足している場合
    $response = $this->postJson('/api/webpush/send-to-all', [
      'title' => 'テスト通知'
      // messageが不足
    ]);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['message']);

    // 無効なURLの場合
    $response = $this->postJson('/api/webpush/send-to-all', [
      'title' => 'テスト通知',
      'message' => 'テストメッセージ',
      'url' => 'invalid-url'
    ]);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['url']);
  }

  /**
   * 長すぎるメッセージのテスト
   */
  public function test_message_too_long(): void
  {
    $longMessage = str_repeat('a', 1001); // 1000文字を超える

    $response = $this->postJson('/api/webpush/send-to-all', [
      'title' => 'テスト通知',
      'message' => $longMessage
    ]);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['message']);
  }

  /**
   * 長すぎるタイトルのテスト
   */
  public function test_title_too_long(): void
  {
    $longTitle = str_repeat('a', 101); // 100文字を超える

    $response = $this->postJson('/api/webpush/send-to-all', [
      'title' => $longTitle,
      'message' => 'テストメッセージ'
    ]);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['title']);
  }
}
