import React from 'react'
import { Head, Link } from '@inertiajs/react'
import { Repository, Article, ArticleVersion, Branch } from '@/types'
import WebLayout from '@/Layouts/WebLayout'
import {
  PencilIcon,
  ClockIcon,
  UserIcon,
  GitBranchIcon,
  DocumentTextIcon
} from '@heroicons/react/24/outline'

interface Props {
  repository: Repository;
  article: Article & {
    versions: ArticleVersion[];
    current_commit: {
      id: number;
      message: string;
      user: {
        id: number;
        name: string;
      };
      created_at: string;
    };
  };
  branches: Branch[];
}

export default function ArticleShow({ repository, article, branches }: Props) {
  const currentVersion = article.versions.find(v => v.commit_id === article.current_commit_id)
  const versionHistory = article.versions.sort((a, b) =>
    new Date(b.created_at).getTime() - new Date(a.created_at).getTime()
  )

  return (
    <WebLayout>
      <Head title={`${currentVersion?.title || '無題'} - ${repository.name}`} />

      <div className="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        {/* ヘッダー */}
        <div className="mb-8">
          <div className="mb-4 flex items-center space-x-2 text-sm text-gray-500">
            <Link href={route('repositories.index')} className="hover:text-gray-700">
              リポジトリ
            </Link>
            <span>/</span>
            <Link href={route('repositories.show', repository.id)} className="hover:text-gray-700">
              {repository.name}
            </Link>
            <span>/</span>
            <span className="text-gray-900">{currentVersion?.title || '無題'}</span>
          </div>

          <div className="flex items-start justify-between">
            <div className="flex-1">
              <h1 className="mb-2 text-3xl font-bold text-gray-900">
                {currentVersion?.title || '無題'}
              </h1>
              <div className="flex items-center space-x-4 text-sm text-gray-500">
                <div className="flex items-center">
                  <UserIcon className="mr-1 h-4 w-4" />
                  <span>{article.current_commit.user.name}</span>
                </div>
                <div className="flex items-center">
                  <ClockIcon className="mr-1 h-4 w-4" />
                  <span>{new Date(article.updated_at).toLocaleDateString('ja-JP')}</span>
                </div>
                <div className="flex items-center">
                  <GitBranchIcon className="mr-1 h-4 w-4" />
                  <span>{branches.find(b => b.id === currentVersion?.branch_id)?.name}</span>
                </div>
              </div>
            </div>
            <div className="flex space-x-3">
              <Link
                href={route('repositories.articles.edit', [repository.id, article.id])}
                className="inline-flex items-center rounded-md border border-transparent bg-blue-600 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white transition duration-150 ease-in-out hover:bg-blue-700 focus:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 active:bg-blue-900"
              >
                <PencilIcon className="mr-2 h-4 w-4" />
                編集
              </Link>
            </div>
          </div>
        </div>

        {/* メインコンテンツ */}
        <div className="grid grid-cols-1 gap-8 lg:grid-cols-4">
          {/* 記事本文 */}
          <div className="lg:col-span-3">
            <div className="rounded-lg border border-gray-200 bg-white shadow-sm">
              <div className="border-b border-gray-200 px-6 py-4">
                <div className="flex items-center justify-between">
                  <h2 className="text-lg font-medium text-gray-900">記事</h2>
                  <div className="text-sm text-gray-500">
                    最終更新: {new Date(article.updated_at).toLocaleString('ja-JP')}
                  </div>
                </div>
              </div>
              <div className="p-6">
                <div className="prose prose-lg max-w-none">
                  <div dangerouslySetInnerHTML={{ __html: renderMarkdown(currentVersion?.body || '') }} />
                </div>
              </div>
            </div>

            {/* コミット情報 */}
            <div className="mt-6 rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
              <h3 className="mb-4 text-lg font-medium text-gray-900">コミット情報</h3>
              <div className="rounded-lg bg-gray-50 p-4">
                <div className="flex items-start space-x-3">
                  <img
                    className="h-8 w-8 rounded-full"
                    src={`https://ui-avatars.com/api/?name=${article.current_commit.user.name}&color=7C3AED&background=EBF4FF`}
                    alt={article.current_commit.user.name}
                  />
                  <div className="flex-1">
                    <div className="flex items-center space-x-2">
                      <span className="font-medium text-gray-900">
                        {article.current_commit.user.name}
                      </span>
                      <span className="text-sm text-gray-500">
                        {new Date(article.current_commit.created_at).toLocaleString('ja-JP')}
                      </span>
                    </div>
                    <p className="mt-1 text-gray-700">{article.current_commit.message}</p>
                  </div>
                </div>
              </div>
            </div>
          </div>

          {/* サイドバー */}
          <div className="space-y-6">
            {/* バージョン履歴 */}
            <div className="rounded-lg border border-gray-200 bg-white shadow-sm">
              <div className="border-b border-gray-200 px-6 py-4">
                <h3 className="text-lg font-medium text-gray-900">バージョン履歴</h3>
              </div>
              <div className="p-6">
                <div className="space-y-3">
                  {versionHistory.map((version, index) => (
                    <div
                      key={version.id}
                      className={`rounded-lg border p-3 ${version.id === currentVersion?.id
                          ? 'border-blue-200 bg-blue-50'
                          : 'border-gray-200 hover:bg-gray-50'
                        }`}
                    >
                      <div className="flex items-center justify-between">
                        <div className="flex-1">
                          <div className="text-sm font-medium text-gray-900">
                            {version.title || '無題'}
                          </div>
                          <div className="mt-1 text-xs text-gray-500">
                            {new Date(version.created_at).toLocaleDateString('ja-JP')}
                          </div>
                        </div>
                        {version.id === currentVersion?.id && (
                          <span className="rounded-full bg-blue-100 px-2 py-1 text-xs text-blue-800">
                            現在
                          </span>
                        )}
                      </div>
                    </div>
                  ))}
                </div>
              </div>
            </div>

            {/* ブランチ情報 */}
            <div className="rounded-lg border border-gray-200 bg-white shadow-sm">
              <div className="border-b border-gray-200 px-6 py-4">
                <h3 className="text-lg font-medium text-gray-900">ブランチ</h3>
              </div>
              <div className="p-6">
                <div className="space-y-2">
                  {branches.map((branch) => (
                    <div
                      key={branch.id}
                      className={`flex items-center justify-between rounded-lg border p-3 ${branch.id === currentVersion?.branch_id
                          ? 'border-green-200 bg-green-50'
                          : 'border-gray-200'
                        }`}
                    >
                      <div className="flex items-center">
                        <GitBranchIcon className="mr-2 h-4 w-4 text-gray-400" />
                        <span className="text-sm font-medium text-gray-900">{branch.name}</span>
                        {branch.id === currentVersion?.branch_id && (
                          <span className="ml-2 rounded-full bg-green-100 px-2 py-1 text-xs text-green-800">
                            現在
                          </span>
                        )}
                      </div>
                      <Link
                        href={route('repositories.branches.show', [repository.id, branch.id])}
                        className="text-sm text-blue-600 hover:text-blue-800"
                      >
                        表示
                      </Link>
                    </div>
                  ))}
                </div>
              </div>
            </div>

            {/* アクション */}
            <div className="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
              <h3 className="mb-4 text-lg font-medium text-gray-900">アクション</h3>
              <div className="space-y-3">
                <Link
                  href={route('repositories.articles.edit', [repository.id, article.id])}
                  className="block w-full rounded-md px-4 py-2 text-left text-sm text-gray-700 transition-colors duration-200 hover:bg-gray-100"
                >
                  記事を編集
                </Link>
                <Link
                  href={route('repositories.articles.history', [repository.id, article.id])}
                  className="block w-full rounded-md px-4 py-2 text-left text-sm text-gray-700 transition-colors duration-200 hover:bg-gray-100"
                >
                  履歴を表示
                </Link>
                <Link
                  href={route('repositories.pull-requests.create', [repository.id, { article_id: article.id }])}
                  className="block w-full rounded-md px-4 py-2 text-left text-sm text-gray-700 transition-colors duration-200 hover:bg-gray-100"
                >
                  プルリクエスト作成
                </Link>
              </div>
            </div>
          </div>
        </div>
      </div>
    </WebLayout>
  )
}

// 簡易的なMarkdownレンダリング関数
function renderMarkdown(text: string): string {
  return text
    .replace(/^### (.*$)/gim, '<h3>$1</h3>')
    .replace(/^## (.*$)/gim, '<h2>$1</h2>')
    .replace(/^# (.*$)/gim, '<h1>$1</h1>')
    .replace(/\*\*(.*)\*\*/gim, '<strong>$1</strong>')
    .replace(/\*(.*)\*/gim, '<em>$1</em>')
    .replace(/`(.*)`/gim, '<code>$1</code>')
    .replace(/^- (.*$)/gim, '<li>$1</li>')
    .replace(/\n\n/gim, '<br><br>')
}
