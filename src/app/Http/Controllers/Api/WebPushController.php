<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Kreait\Firebase\Exception\MessagingException;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Contract\Messaging as MessagingContract;
use App\Models\DeviceToken;
use Illuminate\Support\Facades\Log;

class WebPushController extends Controller
{
    /** @var MessagingContract|null */
    protected ?MessagingContract $messaging;

    public function __construct()
    {
        try {
            $credentialsPath = config('firebase.credentials_path');

            if ($credentialsPath && is_string($credentialsPath) && file_exists($credentialsPath)) {
                $this->messaging = (new Factory())
                    ->withServiceAccount($credentialsPath)
                    ->createMessaging();
            } else {
                $this->messaging = null;
            }
        } catch (\Exception $e) {
            $this->messaging = null;
        }
    }

    public function index(): JsonResponse
    {
        $envCredentialsPath = config('firebase.credentials_path');

        return response()->json([
            'message' => 'WebPush API is working',
            'firebase_config' => [
                'env_credentials_path' => $envCredentialsPath,
                'env_exists' => $envCredentialsPath && is_string($envCredentialsPath) ? file_exists($envCredentialsPath) : false,
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
            $deviceTokens = DeviceToken::query()->pluck('token')->toArray();

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
            $sendMessage = $request->input('message');

            foreach ($deviceTokens as $token) {
                try {
                    // $message = [
                    //     'token' => $token,
                    //     'notification' => [
                    //         'title' => "~~言葉の泉に言葉が流れています~~",
                    //         'body' => "",
                    //     ],
                    //     'data' => [
                    //         'url' => $request->input('url') ?? '',
                    //         'timestamp' => now()->toISOString(),
                    //     ],
                    //     'webpush' => [
                    //         'headers' => [
                    //             'Urgency' => 'high',
                    //         ],
                    //         'notification' => [
                    //             'title' => $sendMessage,
                    //             'body' => $sendMessage,
                    //             'icon' => '/favicon.ico',
                    //             'badge' => '/favicon.ico',
                    //             'click_action' => $request->input('url') ?? '/',
                    //         ],
                    //     ],
                    // ];
                    $message = [
                        'token' => $token,
                        'data' => [
                            'message' => $sendMessage,
                            'url' => $request->input('url') ?? '',
                            'timestamp' => now()->toISOString(),
                        ],
                    ];

                    $this->messaging->send($message);
                    $successCount++;
                } catch (\Exception $e) {
                    $errorCount++;
                    $errors[] = "Token {$token}: " . $e->getMessage();

                    // ログ出力（エラー）
                    Log::error("WebPush送信失敗: token={$token}, error=" . $e->getMessage());

                    // 無効なトークンの場合はDBから削除
                    DeviceToken::where('token', $token)->first()->delete();
                }
            }

            return response()->json([
                'success' => true,
                'message' => "Webプッシュ通知を送信しました",
                'success_count' => 0,
                'error_count' => 0,
                'errors' => [],
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
                'topic' => $request->input('topic'),
                'notification' => [
                    'title' => $request->input('title'),
                    'body' => $request->input('message'),
                ],
                'data' => [
                    'url' => $request->input('url') ?? '',
                    'timestamp' => now()->toISOString(),
                ],
                'webpush' => [
                    'headers' => [
                        'Urgency' => 'high',
                    ],
                    'notification' => [
                        'title' => $request->input('title'),
                        'body' => $request->input('message'),
                        'icon' => '/favicon.ico',
                        'badge' => '/favicon.ico',
                        'click_action' => $request->input('url') ?? '/',
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
                'token' => $request->input('token'),
                'notification' => [
                    'title' => $request->input('title'),
                    'body' => $request->input('message'),
                ],
                'data' => [
                    'url' => $request->input('url') ?? '',
                    'timestamp' => now()->toISOString(),
                ],
                'webpush' => [
                    'headers' => [
                        'Urgency' => 'high',
                    ],
                    'notification' => [
                        'title' => $request->input('title'),
                        'body' => $request->input('message'),
                        'icon' => '/favicon.ico',
                        'badge' => '/favicon.ico',
                        'click_action' => $request->input('url') ?? '/',
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

        $token = $request->input('token');
        $deviceToken = DeviceToken::query()->firstOrCreate(['token' => $token]);

        return response()->json([
            'success' => true,
            'message' => 'デバイストークンが登録されました',
            'registered_token' => $deviceToken->token,
        ]);
    }
}
