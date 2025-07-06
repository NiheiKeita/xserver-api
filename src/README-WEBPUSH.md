# Webプッシュ通知機能の実装

## 概要
Next.jsから送られてくるメッセージをFirebase Cloud Messagingを使用してWebプッシュ通知として送信するAPIを実装しました。

## 実装内容

### 1. インストールしたパッケージ
- `kreait/laravel-firebase`: Firebase Admin SDK for PHP

### 2. 作成したファイル

#### コントローラー
- `app/Http/Controllers/Api/WebPushController.php`
  - 全ユーザーへの通知送信 (`sendToAll`)
  - 特定トピックへの通知送信 (`sendToTopic`)
  - 特定トークンへの通知送信 (`sendToToken`)

#### ルート設定
- `routes/api.php` に以下を追加:
  - `POST /api/webpush/send-to-all`
  - `POST /api/webpush/send-to-topic`
  - `POST /api/webpush/send-to-token`

#### 設定ファイル
- `config/firebase.php` (自動生成)
- `storage/app/firebase/` ディレクトリ (サービスアカウントキー配置用)

#### ドキュメント
- `docs/firebase-setup.md`: 詳細な設定ガイド
- `tests/Feature/WebPushTest.php`: テストファイル

## API仕様

### 全ユーザーへの通知送信
```
POST /api/webpush/send-to-all
```

**リクエストボディ:**
```json
{
    "title": "通知タイトル",
    "message": "通知メッセージ",
    "url": "https://example.com/optional-url"
}
```

### 特定トピックへの通知送信
```
POST /api/webpush/send-to-topic
```

**リクエストボディ:**
```json
{
    "topic": "topic-name",
    "title": "通知タイトル",
    "message": "通知メッセージ",
    "url": "https://example.com/optional-url"
}
```

### 特定トークンへの通知送信
```
POST /api/webpush/send-to-token
```

**リクエストボディ:**
```json
{
    "token": "device-token",
    "title": "通知タイトル",
    "message": "通知メッセージ",
    "url": "https://example.com/optional-url"
}
```

## レスポンス形式

### 成功時
```json
{
    "success": true,
    "message": "Webプッシュ通知が正常に送信されました",
    "message_id": "message-id-from-firebase"
}
```

### エラー時
```json
{
    "success": false,
    "message": "Webプッシュ通知の送信に失敗しました",
    "error": "エラーの詳細"
}
```

## 必要な設定

### 1. Firebaseプロジェクトの設定
1. [Firebase Console](https://console.firebase.google.com/)でプロジェクトを作成
2. サービスアカウントキーをダウンロード
3. `storage/app/firebase/service-account-key.json` に配置

### 2. 環境変数の設定
`.env`ファイルに以下を追加:
```env
FIREBASE_PROJECT=your-project-id
FIREBASE_CREDENTIALS=storage/app/firebase/service-account-key.json
FIREBASE_DATABASE_URL=https://your-project-id.firebaseio.com
```

## Next.jsからの使用例

```javascript
const sendWebPushNotification = async (title, message, url = null) => {
    try {
        const response = await fetch('/api/webpush/send-to-all', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                title,
                message,
                url
            })
        });
        
        const result = await response.json();
        
        if (result.success) {
            console.log('通知が送信されました:', result.message_id);
        } else {
            console.error('通知の送信に失敗:', result.error);
        }
    } catch (error) {
        console.error('API呼び出しエラー:', error);
    }
};

// 使用例
sendWebPushNotification(
    '新しい記事が投稿されました',
    '新しい記事「Laravel入門」が投稿されました',
    'https://example.com/articles/123'
);
```

## バリデーション

- `title`: 必須、最大100文字
- `message`: 必須、最大1000文字
- `url`: オプション、有効なURL形式
- `topic`: トピック送信時必須、最大100文字
- `token`: トークン送信時必須

## セキュリティ考慮事項

1. **サービスアカウントキー**: 機密情報のため、Gitにコミットしない
2. **レート制限**: Firebaseの送信制限に注意
3. **エラーハンドリング**: 適切なエラーハンドリングを実装
4. **認証**: 必要に応じてAPI認証を追加

## トラブルシューティング

### よくある問題
1. **認証エラー**: サービスアカウントキーのパスを確認
2. **プロジェクトIDエラー**: `FIREBASE_PROJECT`の値を確認
3. **権限エラー**: サービスアカウントの権限を確認

### ログの確認
```bash
docker-compose exec app tail -f storage/logs/laravel.log
```

## 今後の拡張案

1. **認証機能の追加**: APIキー認証やJWT認証
2. **通知履歴の保存**: データベースに通知履歴を保存
3. **バッチ処理**: 大量通知のためのキュー処理
4. **通知テンプレート**: 事前定義された通知テンプレート
5. **統計機能**: 通知の送信統計や開封率の追跡 
