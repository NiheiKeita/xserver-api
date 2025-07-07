<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Kreait\Firebase\Messaging;
use Kreait\Firebase\Exception\MessagingException;
use Kreait\Firebase\Factory;
use App\Models\DeviceToken;

class WebPushController extends Controller
{
    protected ?Messaging $messaging;

    public function __construct()
    {
        try {
            $credentialsPath = __DIR__ . '/firebase/firebase_env.json';
            // $credentialsPath = base_path(env('FIREBASE_CREDENTIALS'));
            // $credentialsPath = base_path('app/Http/Controllers/Api/firebase/firebase_env.json');

            if ($credentialsPath && file_exists($credentialsPath)) {
                $this->messaging = (new Factory)
                    ->withServiceAccount($credentialsPath)
                    ->createMessaging();
            } else {
                $this->messaging = null;
            }
        } catch (\Exception $e) {
            $this->messaging = null;
        }
    }

    public function index()
    {
        $credentialsPath = config('firebase.projects.app.credentials');
        $envCredentialsPath = env('FIREBASE_CREDENTIALS');

        return response()->json([
            'message' => 'WebPush API is working',
            'firebase_config' => [
                'config_credentials_path' => $credentialsPath,
                'env_credentials_path' => $envCredentialsPath,
                'config_exists' => $credentialsPath ? file_exists($credentialsPath) : false,
                'env_exists' => $envCredentialsPath ? file_exists($envCredentialsPath) : false,
                'messaging_initialized' => $this->messaging !== null,
            ]
        ]);
    }

    /**
     * 全ユーザーにWebプッシュ通知を送信
     */
    public function sendToAll(Request $request): JsonResponse
    {
        $request->validate([
            'message' => 'required|string|max:1000',
            'title' => 'required|string|max:100',
            'url' => 'nullable|url',
        ]);

        if (!$this->messaging) {
            return response()->json([
                'success' => false,
                'message' => 'Firebaseの設定が正しくありません。サービスアカウントキーを確認してください。',
                'error' => 'Firebase messaging not initialized',
            ], 500);
        }

        try {
            // データベースから全デバイストークンを取得
            $deviceTokens = DeviceToken::pluck('token')->toArray();

            if (empty($deviceTokens)) {
                return response()->json([
                    'success' => false,
                    'message' => '登録済みのデバイストークンがありません',
                    'error' => 'No device tokens registered',
                ], 400);
            }

            $successCount = 0;
            $errorCount = 0;
            $errors = [];

            foreach ($deviceTokens as $token) {
                try {
                    $message = [
                        'token' => $token,
                        'notification' => [
                            'title' => $request->title,
                            'body' => $request->message,
                        ],
                        'data' => [
                            'url' => $request->url ?? '',
                            'timestamp' => now()->toISOString(),
                        ],
                        'webpush' => [
                            'headers' => [
                                'Urgency' => 'high',
                            ],
                            'notification' => [
                                'title' => $request->title,
                                'body' => $request->message,
                                'icon' => '/favicon.ico',
                                'badge' => '/favicon.ico',
                                'click_action' => $request->url ?? '/',
                            ],
                        ],
                    ];

                    $response = $this->messaging->send($message);
                    $successCount++;
                } catch (\Exception $e) {
                    $errorCount++;
                    $errors[] = "Token {$token}: " . $e->getMessage();
                }
            }

            return response()->json([
                'success' => true,
                'message' => "Webプッシュ通知を送信しました（成功: {$successCount}, 失敗: {$errorCount}）",
                'success_count' => $successCount,
                'error_count' => $errorCount,
                'errors' => $errors,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Webプッシュ通知の送信に失敗しました',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * 特定のトピックにWebプッシュ通知を送信
     */
    public function sendToTopic(Request $request): JsonResponse
    {
        $request->validate([
            'topic' => 'required|string|max:100',
            'message' => 'required|string|max:1000',
            'title' => 'required|string|max:100',
            'url' => 'nullable|url',
        ]);

        if (!$this->messaging) {
            return response()->json([
                'success' => false,
                'message' => 'Firebaseの設定が正しくありません。サービスアカウントキーを確認してください。',
                'error' => 'Firebase messaging not initialized',
            ], 500);
        }

        try {
            $message = [
                'topic' => $request->topic,
                'notification' => [
                    'title' => $request->title,
                    'body' => $request->message,
                ],
                'data' => [
                    'url' => $request->url ?? '',
                    'timestamp' => now()->toISOString(),
                ],
                'webpush' => [
                    'headers' => [
                        'Urgency' => 'high',
                    ],
                    'notification' => [
                        'title' => $request->title,
                        'body' => $request->message,
                        'icon' => '/favicon.ico',
                        'badge' => '/favicon.ico',
                        'click_action' => $request->url ?? '/',
                    ],
                ],
            ];

            $response = $this->messaging->send($message);

            return response()->json([
                'success' => true,
                'message' => 'Webプッシュ通知が正常に送信されました',
                'message_id' => $response,
            ], 200);
        } catch (MessagingException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Webプッシュ通知の送信に失敗しました',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * 特定のトークンにWebプッシュ通知を送信
     */
    public function sendToToken(Request $request): JsonResponse
    {
        $request->validate([
            'token' => 'required|string',
            'message' => 'required|string|max:1000',
            'title' => 'required|string|max:100',
            'url' => 'nullable|url',
        ]);

        if (!$this->messaging) {
            return response()->json([
                'success' => false,
                'message' => 'Firebaseの設定が正しくありません。サービスアカウントキーを確認してください。',
                'error' => 'Firebase messaging not initialized',
            ], 500);
        }

        try {
            $message = [
                'token' => $request->token,
                'notification' => [
                    'title' => $request->title,
                    'body' => $request->message,
                ],
                'data' => [
                    'url' => $request->url ?? '',
                    'timestamp' => now()->toISOString(),
                ],
                'webpush' => [
                    'headers' => [
                        'Urgency' => 'high',
                    ],
                    'notification' => [
                        'title' => $request->title,
                        'body' => $request->message,
                        'icon' => '/favicon.ico',
                        'badge' => '/favicon.ico',
                        'click_action' => $request->url ?? '/',
                    ],
                ],
            ];

            $response = $this->messaging->send($message);

            return response()->json([
                'success' => true,
                'message' => 'Webプッシュ通知が正常に送信されました',
                'message_id' => $response,
            ], 200);
        } catch (MessagingException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Webプッシュ通知の送信に失敗しました',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * デバイストークンを登録
     */
    public function registerToken(Request $request): JsonResponse
    {
        $request->validate([
            'token' => 'required|string',
        ]);

        // データベースに保存（重複は無視）
        $token = $request->token;
        $deviceToken = DeviceToken::firstOrCreate(['token' => $token]);

        return response()->json([
            'success' => true,
            'message' => 'デバイストークンが登録されました',
            'registered_token' => $deviceToken->token,
        ]);
    }
}
