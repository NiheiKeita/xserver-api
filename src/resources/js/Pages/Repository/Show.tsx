import React from 'react'
import { Head, Link } from '@inertiajs/react'
import { Repository, Branch, Article } from '@/types'
import WebLayout from '@/Layouts/WebLayout'
import {
  PlusIcon,
  EyeIcon,
  LockClosedIcon,
  CodeBracketIcon,
  DocumentTextIcon,
  GitBranchIcon,
  ArrowTopRightOnSquareIcon
} from '@heroicons/react/24/outline'

interface Props {
  repository: Repository & {
    branches: Branch[];
    articles: Article[];
  };
}

export default function RepositoryShow({ repository }: Props) {
  return (
    <WebLayout>
      <Head title={`${repository.name} - リポジトリ`} />

      <div className="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        {/* ヘッダー */}
        <div className="mb-8">
          <div className="flex items-center justify-between">
            <div>
              <div className="flex items-center space-x-3">
                <h1 className="text-3xl font-bold text-gray-900">{repository.name}</h1>
                <div className="flex items-center">
                  {repository.is_public ? (
                    <EyeIcon className="mr-1 h-5 w-5 text-green-500" />
                  ) : (
                    <LockClosedIcon className="mr-1 h-5 w-5 text-gray-500" />
                  )}
                  <span className="text-sm text-gray-500">
                    {repository.is_public ? '公開' : '非公開'}
                  </span>
                </div>
              </div>
              <div className="mt-2 flex items-center text-sm text-gray-600">
                <img
                  className="mr-2 h-5 w-5 rounded-full"
                  src={`https://ui-avatars.com/api/?name=${repository.owner?.name}&color=7C3AED&background=EBF4FF`}
                  alt={repository.owner?.name}
                />
                <span>{repository.owner?.name}</span>
                <span className="mx-2">•</span>
                <span>{new Date(repository.created_at).toLocaleDateString('ja-JP')} に作成</span>
              </div>
            </div>
            <div className="flex space-x-3">
              <Link
                href={route('repositories.articles.create', repository.id)}
                className="inline-flex items-center rounded-md border border-transparent bg-blue-600 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white transition duration-150 ease-in-out hover:bg-blue-700 focus:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 active:bg-blue-900"
              >
                <PlusIcon className="mr-2 h-4 w-4" />
                記事を作成
              </Link>
              <Link
                href={route('repositories.branches.create', repository.id)}
                className="inline-flex items-center rounded-md border border-transparent bg-gray-600 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white transition duration-150 ease-in-out hover:bg-gray-700 focus:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 active:bg-gray-900"
              >
                <GitBranchIcon className="mr-2 h-4 w-4" />
                ブランチを作成
              </Link>
            </div>
          </div>
        </div>

        {/* 統計情報 */}
        <div className="mb-8 grid grid-cols-1 gap-6 md:grid-cols-4">
          <div className="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
            <div className="flex items-center">
              <div className="flex-shrink-0">
                <GitBranchIcon className="h-8 w-8 text-blue-600" />
              </div>
              <div className="ml-4">
                <p className="text-sm font-medium text-gray-500">ブランチ</p>
                <p className="text-2xl font-semibold text-gray-900">{repository.branches.length}</p>
              </div>
            </div>
          </div>

          <div className="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
            <div className="flex items-center">
              <div className="flex-shrink-0">
                <DocumentTextIcon className="h-8 w-8 text-green-600" />
              </div>
              <div className="ml-4">
                <p className="text-sm font-medium text-gray-500">記事</p>
                <p className="text-2xl font-semibold text-gray-900">{repository.articles.length}</p>
              </div>
            </div>
          </div>

          <div className="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
            <div className="flex items-center">
              <div className="flex-shrink-0">
                <CodeBracketIcon className="h-8 w-8 text-purple-600" />
              </div>
              <div className="ml-4">
                <p className="text-sm font-medium text-gray-500">コミット</p>
                <p className="text-2xl font-semibold text-gray-900">
                  {repository.articles.reduce((total, article) => total + (article.versions?.length || 0), 0)}
                </p>
              </div>
            </div>
          </div>

          <div className="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
            <div className="flex items-center">
              <div className="flex-shrink-0">
                <ArrowTopRightOnSquareIcon className="h-8 w-8 text-orange-600" />
              </div>
              <div className="ml-4">
                <p className="text-sm font-medium text-gray-500">プルリクエスト</p>
                <p className="text-2xl font-semibold text-gray-900">0</p>
              </div>
            </div>
          </div>
        </div>

        {/* メインコンテンツ */}
        <div className="grid grid-cols-1 gap-8 lg:grid-cols-3">
          {/* 記事一覧 */}
          <div className="lg:col-span-2">
            <div className="rounded-lg border border-gray-200 bg-white shadow-sm">
              <div className="border-b border-gray-200 px-6 py-4">
                <div className="flex items-center justify-between">
                  <h2 className="text-lg font-medium text-gray-900">記事</h2>
                  <Link
                    href={route('repositories.articles.create', repository.id)}
                    className="text-sm font-medium text-blue-600 hover:text-blue-800"
                  >
                    新しい記事 →
                  </Link>
                </div>
              </div>
              <div className="p-6">
                {repository.articles.length > 0 ? (
                  <div className="space-y-4">
                    {repository.articles.map((article) => (
                      <div
                        key={article.id}
                        className="flex items-center justify-between rounded-lg border border-gray-200 p-4 transition-colors duration-200 hover:bg-gray-50"
                      >
                        <div className="flex-1">
                          <Link
                            href={route('repositories.articles.show', [repository.id, article.id])}
                            className="text-lg font-medium text-gray-900 transition-colors duration-200 hover:text-blue-600"
                          >
                            {article.versions?.[0]?.title || '無題'}
                          </Link>
                          <div className="mt-1 flex items-center text-sm text-gray-500">
                            <span>最終更新: {new Date(article.updated_at).toLocaleDateString('ja-JP')}</span>
                            {article.current_commit && (
                              <>
                                <span className="mx-2">•</span>
                                <span>{article.current_commit.message}</span>
                              </>
                            )}
                          </div>
                        </div>
                        <Link
                          href={route('repositories.articles.show', [repository.id, article.id])}
                          className="text-sm font-medium text-blue-600 hover:text-blue-800"
                        >
                          表示 →
                        </Link>
                      </div>
                    ))}
                  </div>
                ) : (
                  <div className="py-8 text-center">
                    <DocumentTextIcon className="mx-auto mb-4 h-12 w-12 text-gray-400" />
                    <h3 className="mb-2 text-lg font-medium text-gray-900">記事がありません</h3>
                    <p className="mb-4 text-gray-500">最初の記事を作成して、Wikiを始めましょう。</p>
                    <Link
                      href={route('repositories.articles.create', repository.id)}
                      className="inline-flex items-center rounded-md border border-transparent bg-blue-600 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white transition duration-150 ease-in-out hover:bg-blue-700 focus:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 active:bg-blue-900"
                    >
                      <PlusIcon className="mr-2 h-4 w-4" />
                      記事を作成
                    </Link>
                  </div>
                )}
              </div>
            </div>
          </div>

          {/* サイドバー */}
          <div className="space-y-6">
            {/* ブランチ一覧 */}
            <div className="rounded-lg border border-gray-200 bg-white shadow-sm">
              <div className="border-b border-gray-200 px-6 py-4">
                <div className="flex items-center justify-between">
                  <h3 className="text-lg font-medium text-gray-900">ブランチ</h3>
                  <Link
                    href={route('repositories.branches.create', repository.id)}
                    className="text-sm font-medium text-blue-600 hover:text-blue-800"
                  >
                    新規作成 →
                  </Link>
                </div>
              </div>
              <div className="p-6">
                <div className="space-y-2">
                  {repository.branches.map((branch) => (
                    <div
                      key={branch.id}
                      className="flex items-center justify-between rounded-lg border border-gray-200 p-3"
                    >
                      <div className="flex items-center">
                        <GitBranchIcon className="mr-2 h-4 w-4 text-gray-400" />
                        <span className="text-sm font-medium text-gray-900">{branch.name}</span>
                        {branch.name === 'main' && (
                          <span className="ml-2 rounded-full bg-blue-100 px-2 py-1 text-xs text-blue-800">
                            デフォルト
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
                  href={route('repositories.edit', repository.id)}
                  className="block w-full rounded-md px-4 py-2 text-left text-sm text-gray-700 transition-colors duration-200 hover:bg-gray-100"
                >
                  リポジトリを編集
                </Link>
                <Link
                  href={route('repositories.pull-requests.index', repository.id)}
                  className="block w-full rounded-md px-4 py-2 text-left text-sm text-gray-700 transition-colors duration-200 hover:bg-gray-100"
                >
                  プルリクエスト
                </Link>
                <Link
                  href={route('repositories.branches.index', repository.id)}
                  className="block w-full rounded-md px-4 py-2 text-left text-sm text-gray-700 transition-colors duration-200 hover:bg-gray-100"
                >
                  ブランチ管理
                </Link>
              </div>
            </div>
          </div>
        </div>
      </div>
    </WebLayout>
  )
}
