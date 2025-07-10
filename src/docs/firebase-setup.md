# Firebase Webプッシュ通知設定ガイド

## 概要
このドキュメントでは、LaravelアプリケーションでFirebase Cloud Messagingを使用してWebプッシュ通知を送信する方法を説明します。

## 必要な設定

### 1. Firebaseプロジェクトの設定

1. [Firebase Console](https://console.firebase.google.com/)にアクセス
2. 新しいプロジェクトを作成または既存のプロジェクトを選択
3. プロジェクト設定で「サービスアカウント」タブを開く
4. 「新しい秘密鍵の生成」をクリックしてJSONファイルをダウンロード

### 2. 環境変数の設定

`.env`ファイルに以下の環境変数を追加してください：

```env
# Firebase設定
FIREBASE_PROJECT=your-project-id
FIREBASE_CREDENTIALS=/path/to/your/service-account-key.json
FIREBASE_DATABASE_URL=https://your-project-id.firebaseio.com
```

### 3. サービスアカウントキーファイルの配置

ダウンロードしたJSONファイルを`storage/app/firebase/`ディレクトリに配置し、`.env`ファイルでパスを指定：

```env
FIREBASE_CREDENTIALS=storage/app/firebase/service-account-key.json
```

## API エンドポイント

### 全ユーザーに通知を送信
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

### 特定のトピックに通知を送信
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

### 特定のトークンに通知を送信
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

## レスポンス例

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

## Next.jsからの使用例

```javascript
// Next.jsからAPIを呼び出す例
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

## 注意事項

1. **セキュリティ**: サービスアカウントキーは機密情報です。Gitにコミットしないでください。
2. **トピック管理**: トピックベースの通知を使用する場合は、クライアント側でトピックへの登録が必要です。
3. **レート制限**: Firebaseには送信レート制限があります。大量の通知を送信する場合は注意してください。
4. **エラーハンドリング**: 無効なトークンやネットワークエラーに対して適切なエラーハンドリングを実装してください。

## トラブルシューティング

### よくある問題

1. **認証エラー**: サービスアカウントキーのパスが正しく設定されているか確認
2. **プロジェクトIDエラー**: `FIREBASE_PROJECT`が正しいプロジェクトIDに設定されているか確認
3. **権限エラー**: サービスアカウントに適切な権限が付与されているか確認

### ログの確認

```bash
# Laravelのログを確認
docker-compose exec app tail -f storage/logs/laravel.log
``` 
