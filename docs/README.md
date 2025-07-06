# Wiki版Gitライクバージョン管理システム

## 概要

このシステムは、Laravel + Inertia.js + React + TailwindCSSで構築されたWiki版Gitライクバージョン管理システムです。GitHub風の機能を提供しながら、Wikiとしての使いやすさを重視した設計になっています。

## 主要機能

### 1. リポジトリ管理
- リポジトリの作成・編集・削除
- 公開/非公開設定
- ユーザー権限管理（read/write/admin）

### 2. 記事管理
- Markdownエディタによる記事作成・編集
- リアルタイムプレビュー
- 記事の履歴管理
- 添付ファイル管理

### 3. ブランチ管理
- ブランチの作成・削除
- ブランチ間の関係管理
- ブランチ別の記事管理

### 4. コミット管理
- 変更のコミット
- コミットメッセージ
- コミット履歴の表示

### 5. プルリクエスト
- プルリクエストの作成・編集
- レビュー・コメント機能
- マージ・クローズ機能

### 6. アクティビティログ
- ユーザーアクションの記録
- 変更履歴の追跡

## 技術スタック

### バックエンド
- **Laravel 12**: PHPフレームワーク
- **MySQL**: データベース
- **Eloquent ORM**: データベース操作
- **Laravel Sanctum**: API認証

### フロントエンド
- **React 18**: UIライブラリ
- **TypeScript**: 型安全性
- **Inertia.js**: SPA体験
- **TailwindCSS**: スタイリング
- **Markdown Editor**: 記事編集

### 開発ツール
- **Vite**: ビルドツール
- **Storybook**: コンポーネント開発
- **PHPStan**: 静的解析
- **PHPUnit**: テスト

## システムアーキテクチャ

### レイヤー構成
1. **Presentation Layer**: Reactコンポーネント
2. **Application Layer**: コントローラ、サービス、ミドルウェア
3. **Domain Layer**: Eloquentモデル、ビジネスロジック
4. **Infrastructure Layer**: データベース、ファイルストレージ、キャッシュ

### データベース設計
- **users**: ユーザー情報
- **repositories**: リポジトリ情報
- **repository_user**: リポジトリとユーザーの関連
- **branches**: ブランチ情報
- **articles**: 記事情報
- **article_versions**: 記事のバージョン情報
- **commits**: コミット情報
- **pull_requests**: プルリクエスト情報
- **pr_comments**: プルリクエストコメント
- **attachments**: 添付ファイル情報
- **activity_logs**: アクティビティログ

## セットアップ

### 前提条件
- PHP 8.2以上
- Composer
- Node.js 18以上
- npm
- Docker (推奨)

### インストール手順

1. **リポジトリのクローン**
```bash
git clone <repository-url>
cd laravel-inertia-react-example
```

2. **依存関係のインストール**
```bash
# PHP依存関係
composer install

# Node.js依存関係
npm install
```

3. **環境設定**
```bash
cp .env.example .env
php artisan key:generate
```

4. **データベース設定**
```bash
# .envファイルでデータベース設定を編集
php artisan migrate
php artisan db:seed
```

5. **フロントエンドビルド**
```bash
npm run dev
```

6. **サーバー起動**
```bash
php artisan serve
```

### Docker環境での実行

```bash
# Dockerコンテナの起動
docker-compose up -d

# マイグレーション実行
docker-compose exec app php artisan migrate

# フロントエンドビルド
npm run build
```

## 使用方法

### 1. ユーザー登録・ログイン
- システムに登録してログイン
- プロフィール設定

### 2. リポジトリ作成
- リポジトリ名を入力
- 公開/非公開を選択
- デフォルトでmainブランチが作成される

### 3. 記事作成
- リポジトリ内で記事を作成
- Markdownエディタで編集
- ブランチを選択してコミット

### 4. ブランチ管理
- 新しいブランチを作成
- ブランチ間で記事を編集
- ブランチの削除（mainブランチは削除不可）

### 5. プルリクエスト
- ブランチ間の変更をプルリクエストで提案
- レビュー・コメント機能
- マージ・クローズ機能

## API仕様

### 認証
- Laravel Sanctumを使用
- Bearer Token認証

### 主要エンドポイント

#### リポジトリ
- `GET /api/repositories` - リポジトリ一覧
- `POST /api/repositories` - リポジトリ作成
- `GET /api/repositories/{id}` - リポジトリ詳細
- `PUT /api/repositories/{id}` - リポジトリ更新
- `DELETE /api/repositories/{id}` - リポジトリ削除

#### 記事
- `GET /api/repositories/{id}/articles` - 記事一覧
- `POST /api/repositories/{id}/articles` - 記事作成
- `GET /api/repositories/{id}/articles/{article_id}` - 記事詳細
- `PUT /api/repositories/{id}/articles/{article_id}` - 記事更新
- `DELETE /api/repositories/{id}/articles/{article_id}` - 記事削除

#### ブランチ
- `GET /api/repositories/{id}/branches` - ブランチ一覧
- `POST /api/repositories/{id}/branches` - ブランチ作成
- `DELETE /api/repositories/{id}/branches/{branch_id}` - ブランチ削除

#### プルリクエスト
- `GET /api/repositories/{id}/pull-requests` - プルリクエスト一覧
- `POST /api/repositories/{id}/pull-requests` - プルリクエスト作成
- `GET /api/repositories/{id}/pull-requests/{pr_id}` - プルリクエスト詳細
- `PATCH /api/repositories/{id}/pull-requests/{pr_id}/merge` - マージ
- `PATCH /api/repositories/{id}/pull-requests/{pr_id}/close` - クローズ

## 開発ガイド

### コーディング規約
- PSR-12準拠
- TypeScript strict mode
- ESLint + Prettier
- PHPStan level 8

### テスト
```bash
# PHPテスト
vendor/bin/phpunit

# フロントエンドテスト
npm test
```

### 静的解析
```bash
# PHPStan
vendor/bin/phpstan analyse

# ESLint
npm run lint
```

## デプロイ

### 本番環境
1. 環境変数の設定
2. データベースマイグレーション
3. フロントエンドビルド
4. キャッシュクリア

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
npm run build
```

## ライセンス

MIT License

## 貢献

1. Fork the repository
2. Create a feature branch
3. Commit your changes
4. Push to the branch
5. Create a Pull Request

## サポート

- イシュー: GitHub Issues
- ドキュメント: `/docs` フォルダ
- 設計図: draw.ioファイル 