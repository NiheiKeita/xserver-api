import React from 'react'
import { Head, Link } from '@inertiajs/react'
import { Repository } from '@/types'
import WebLayout from '@/Layouts/WebLayout'
import { PlusIcon, EyeIcon, LockClosedIcon } from '@heroicons/react/24/outline'

interface Props {
  repositories: {
    data: Repository[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
  };
}

export default function RepositoryIndex({ repositories }: Props) {
  return (
    <WebLayout>
      <Head title="リポジトリ一覧" />

      <div className="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        {/* ヘッダー */}
        <div className="mb-8 flex items-center justify-between">
          <div>
            <h1 className="text-3xl font-bold text-gray-900">リポジトリ</h1>
            <p className="mt-2 text-gray-600">
              あなたのリポジトリと公開リポジトリを管理します
            </p>
          </div>
          <Link
            href={route('repositories.create')}
            className="inline-flex items-center rounded-md border border-transparent bg-blue-600 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white transition duration-150 ease-in-out hover:bg-blue-700 focus:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 active:bg-blue-900"
          >
            <PlusIcon className="mr-2 h-4 w-4" />
            新しいリポジトリ
          </Link>
        </div>

        {/* リポジトリ一覧 */}
        <div className="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
          {repositories.data.map((repository) => (
            <div
              key={repository.id}
              className="rounded-lg border border-gray-200 bg-white shadow-sm transition-shadow duration-200 hover:shadow-md"
            >
              <div className="p-6">
                <div className="flex items-start justify-between">
                  <div className="flex-1">
                    <Link
                      href={route('repositories.show', repository.id)}
                      className="group block"
                    >
                      <h3 className="text-lg font-semibold text-gray-900 transition-colors duration-200 group-hover:text-blue-600">
                        {repository.name}
                      </h3>
                    </Link>
                    <div className="mt-2 flex items-center">
                      {repository.is_public ? (
                        <EyeIcon className="mr-1 h-4 w-4 text-green-500" />
                      ) : (
                        <LockClosedIcon className="mr-1 h-4 w-4 text-gray-500" />
                      )}
                      <span className="text-sm text-gray-500">
                        {repository.is_public ? '公開' : '非公開'}
                      </span>
                    </div>
                  </div>
                </div>

                <div className="mt-4 flex items-center justify-between">
                  <div className="flex items-center">
                    <img
                      className="h-6 w-6 rounded-full"
                      src={`https://ui-avatars.com/api/?name=${repository.owner?.name}&color=7C3AED&background=EBF4FF`}
                      alt={repository.owner?.name}
                    />
                    <span className="ml-2 text-sm text-gray-600">
                      {repository.owner?.name}
                    </span>
                  </div>
                  <div className="text-sm text-gray-500">
                    {new Date(repository.created_at).toLocaleDateString('ja-JP')}
                  </div>
                </div>

                <div className="mt-4 flex items-center justify-between">
                  <div className="flex space-x-4 text-sm text-gray-500">
                    <span>{repository.branches_count || 0} ブランチ</span>
                    <span>{repository.articles_count || 0} 記事</span>
                  </div>
                  <Link
                    href={route('repositories.show', repository.id)}
                    className="text-sm font-medium text-blue-600 hover:text-blue-800"
                  >
                    詳細を見る →
                  </Link>
                </div>
              </div>
            </div>
          ))}
        </div>

        {/* 空の状態 */}
        {repositories.data.length === 0 && (
          <div className="py-12 text-center">
            <div className="mx-auto mb-4 flex h-24 w-24 items-center justify-center rounded-full bg-gray-100">
              <PlusIcon className="h-12 w-12 text-gray-400" />
            </div>
            <h3 className="mb-2 text-lg font-medium text-gray-900">
              リポジトリがありません
            </h3>
            <p className="mb-6 text-gray-500">
              最初のリポジトリを作成して、記事の管理を始めましょう。
            </p>
            <Link
              href={route('repositories.create')}
              className="inline-flex items-center rounded-md border border-transparent bg-blue-600 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white transition duration-150 ease-in-out hover:bg-blue-700 focus:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 active:bg-blue-900"
            >
              <PlusIcon className="mr-2 h-4 w-4" />
              リポジトリを作成
            </Link>
          </div>
        )}

        {/* ページネーション */}
        {repositories.last_page > 1 && (
          <div className="mt-8 flex justify-center">
            <nav className="flex items-center space-x-2">
              {Array.from({ length: repositories.last_page }, (_, i) => i + 1).map((page) => (
                <Link
                  key={page}
                  href={route('repositories.index', { page })}
                  className={`rounded-md px-3 py-2 text-sm font-medium ${page === repositories.current_page
                      ? 'bg-blue-600 text-white'
                      : 'text-gray-500 hover:bg-gray-100 hover:text-gray-700'
                    }`}
                >
                  {page}
                </Link>
              ))}
            </nav>
          </div>
        )}
      </div>
    </WebLayout>
  )
}
