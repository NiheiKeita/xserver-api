<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Repository;
use App\Models\Branch;
use App\Models\Commit;
use App\Models\Article;
use App\Models\ArticleVersion;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        // テストユーザーを作成
        $user = User::create([
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);

        // サンプルリポジトリを作成
        $repository = Repository::create([
            'name' => 'サンプルWiki',
            'owner_id' => $user->id,
            'is_public' => true,
        ]);

        // メインブランチを作成
        $mainBranch = Branch::create([
            'repository_id' => $repository->id,
            'name' => 'main',
            'base_branch_id' => null,
            'created_by' => $user->id,
        ]);

        // 開発ブランチを作成
        $devBranch = Branch::create([
            'repository_id' => $repository->id,
            'name' => 'develop',
            'base_branch_id' => $mainBranch->id,
            'created_by' => $user->id,
        ]);

        // 初期コミットを作成
        $initialCommit = Commit::create([
            'message' => '初期コミット: サンプル記事を追加',
            'user_id' => $user->id,
        ]);

        // サンプル記事を作成
        $article = Article::create([
            'repository_id' => $repository->id,
            'current_commit_id' => $initialCommit->id,
        ]);

        // 記事バージョンを作成
        ArticleVersion::create([
            'article_id' => $article->id,
            'branch_id' => $mainBranch->id,
            'commit_id' => $initialCommit->id,
            'title' => 'Wikiシステムへようこそ',
            'body' => "# Wikiシステムへようこそ\n\nこのシステムは、Gitライクなバージョン管理機能を持つWikiシステムです。\n\n## 主な機能\n\n- **記事管理**: Markdown形式で記事を作成・編集\n- **バージョン管理**: 変更履歴をコミットとして記録\n- **ブランチ管理**: 複数のブランチで並行開発\n- **プルリクエスト**: 変更のレビューとマージ\n- **コラボレーション**: 複数ユーザーでの共同編集\n\n## 使い方\n\n1. リポジトリを作成\n2. 記事を作成・編集\n3. ブランチを作成して並行作業\n4. プルリクエストで変更をレビュー\n5. マージして変更を統合\n\n## Markdown記法\n\n- `# 見出し1`\n- `## 見出し2`\n- `**太字**`\n- `*斜体*`\n- `- リスト`\n- `[リンク](URL)`\n- `![画像](URL)`\n\nこのシステムを使って、効率的なドキュメント管理を始めましょう！",
            'created_by' => $user->id,
        ]);

        // 2つ目の記事を作成
        $secondCommit = Commit::create([
            'message' => '開発ガイドを追加',
            'user_id' => $user->id,
        ]);

        $secondArticle = Article::create([
            'repository_id' => $repository->id,
            'current_commit_id' => $secondCommit->id,
        ]);

        ArticleVersion::create([
            'article_id' => $secondArticle->id,
            'branch_id' => $mainBranch->id,
            'commit_id' => $secondCommit->id,
            'title' => '開発ガイド',
            'body' => "# 開発ガイド\n\n## 開発環境のセットアップ\n\n### 必要な環境\n\n- PHP 8.1以上\n- Composer\n- Node.js 18以上\n- Docker\n\n### セットアップ手順\n\n1. リポジトリをクローン\n2. 依存関係をインストール\n3. 環境変数を設定\n4. データベースをマイグレート\n5. 開発サーバーを起動\n\n## 開発の流れ\n\n1. 機能ブランチを作成\n2. 機能を実装\n3. テストを実行\n4. プルリクエストを作成\n5. レビューを受ける\n6. マージする\n\n## コーディング規約\n\n- PSR-12に準拠\n- 型宣言を適切に使用\n- テストを書く\n- ドキュメントを更新\n\n## デプロイ\n\n- 自動テストが通ることを確認\n- 本番環境にデプロイ\n- 動作確認を行う",
            'created_by' => $user->id,
        ]);

        // 非公開リポジトリも作成
        $privateRepository = Repository::create([
            'name' => 'プライベートWiki',
            'owner_id' => $user->id,
            'is_public' => false,
        ]);

        $privateMainBranch = Branch::create([
            'repository_id' => $privateRepository->id,
            'name' => 'main',
            'base_branch_id' => null,
            'created_by' => $user->id,
        ]);

        $privateCommit = Commit::create([
            'message' => 'プライベート記事を追加',
            'user_id' => $user->id,
        ]);

        $privateArticle = Article::create([
            'repository_id' => $privateRepository->id,
            'current_commit_id' => $privateCommit->id,
        ]);

        ArticleVersion::create([
            'article_id' => $privateArticle->id,
            'branch_id' => $privateMainBranch->id,
            'commit_id' => $privateCommit->id,
            'title' => 'プライベート記事',
            'body' => "# プライベート記事\n\nこの記事は非公開リポジトリにあります。\n\n## セキュリティ\n\n- 非公開リポジトリはオーナーとメンバーのみアクセス可能\n- 適切な権限管理が重要\n- 機密情報は非公開リポジトリで管理\n\n## ベストプラクティス\n\n- 公開情報と非公開情報を適切に分離\n- 定期的なセキュリティレビュー\n- アクセス権限の見直し",
            'created_by' => $user->id,
        ]);

        $this->command->info('シーディングが完了しました！');
        $this->command->info('テストユーザー: test@example.com / password');
    }
}
