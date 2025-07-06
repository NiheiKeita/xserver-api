import React, { useState } from 'react'
import { Head, useForm } from '@inertiajs/react'
import { Repository, Branch } from '@/types'
import WebLayout from '@/Layouts/WebLayout'
import InputLabel from '@/Components/InputLabel'
import TextInput from '@/Components/TextInput'
import InputError from '@/Components/InputError'
import { ArrowLeftIcon } from '@heroicons/react/24/outline'

interface Props {
  repository: Repository;
  branches: Branch[];
}

export default function ArticleCreate({ repository, branches }: Props) {
  const [previewMode, setPreviewMode] = useState(false)

  const { data, setData, post, processing, errors } = useForm({
    title: '',
    body: '',
    branch_id: branches.find(b => b.name === 'main')?.id || '',
    commit_message: '',
  })

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault()
    post(route('repositories.articles.store', repository.id))
  }

  const handleBodyChange = (e: React.ChangeEvent<HTMLTextAreaElement>) => {
    setData('body', e.target.value)
  }

  return (
    <WebLayout>
      <Head title="記事作成" />

      <div className="mx-auto max-w-6xl px-4 py-8 sm:px-6 lg:px-8">
        {/* ヘッダー */}
        <div className="mb-8">
          <a
            href={route('repositories.show', repository.id)}
            className="mb-4 inline-flex items-center text-sm text-gray-500 hover:text-gray-700"
          >
            <ArrowLeftIcon className="mr-1 h-4 w-4" />
            {repository.name} に戻る
          </a>
          <h1 className="text-3xl font-bold text-gray-900">新しい記事</h1>
          <p className="mt-2 text-gray-600">
            リポジトリ「{repository.name}」に新しい記事を作成します。
          </p>
        </div>

        {/* フォーム */}
        <div className="rounded-lg border border-gray-200 bg-white shadow-sm">
          <form onSubmit={handleSubmit}>
            {/* ヘッダー */}
            <div className="border-b border-gray-200 px-6 py-4">
              <div className="flex items-center justify-between">
                <h2 className="text-lg font-medium text-gray-900">記事の作成</h2>
                <div className="flex space-x-3">
                  <button
                    type="button"
                    onClick={() => setPreviewMode(!previewMode)}
                    className="rounded-md border border-gray-300 px-3 py-1 text-sm text-gray-600 transition-colors duration-200 hover:bg-gray-50 hover:text-gray-800"
                  >
                    {previewMode ? '編集' : 'プレビュー'}
                  </button>
                  <button
                    type="submit"
                    disabled={processing}
                    className="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white transition-colors duration-200 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-50"
                  >
                    {processing ? '作成中...' : '記事を作成'}
                  </button>
                </div>
              </div>
            </div>

            <div className="p-6">
              <div className="grid grid-cols-1 gap-6 lg:grid-cols-4">
                {/* メインコンテンツ */}
                <div className="lg:col-span-3">
                  {/* タイトル */}
                  <div className="mb-6">
                    <InputLabel htmlFor="title" value="タイトル" />
                    <TextInput
                      id="title"
                      type="text"
                      className="mt-1 block w-full"
                      value={data.title}
                      onChange={(e) => setData('title', e.target.value)}
                      required
                      autoFocus
                      placeholder="記事のタイトルを入力してください"
                    />
                    <InputError message={errors.title} className="mt-2" />
                  </div>

                  {/* 本文 */}
                  <div className="mb-6">
                    <InputLabel htmlFor="body" value="本文" />
                    {previewMode ? (
                      <div className="prose prose-sm mt-1 min-h-[400px] max-w-none rounded-md border border-gray-300 bg-gray-50 p-4">
                        <div dangerouslySetInnerHTML={{ __html: renderMarkdown(data.body) }} />
                      </div>
                    ) : (
                      <textarea
                        id="body"
                        className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                        rows={20}
                        value={data.body}
                        onChange={handleBodyChange}
                        required
                        placeholder="# 見出し1

## 見出し2

### 見出し3

**太字** や *斜体* を使用できます。

- リスト項目1
- リスト項目2
- リスト項目3

1. 番号付きリスト1
2. 番号付きリスト2

```javascript
// コードブロック
console.log('Hello, World!');
```

[リンクテキスト](https://example.com)

![画像の説明](https://example.com/image.jpg)"
                      />
                    )}
                    <InputError message={errors.body} className="mt-2" />
                    <p className="mt-1 text-sm text-gray-500">
                      Markdown形式で記述できます。プレビューボタンで確認できます。
                    </p>
                  </div>
                </div>

                {/* サイドバー */}
                <div className="space-y-6">
                  {/* ブランチ選択 */}
                  <div className="rounded-lg bg-gray-50 p-4">
                    <h3 className="mb-3 text-sm font-medium text-gray-900">ブランチ</h3>
                    <select
                      value={data.branch_id}
                      onChange={(e) => setData('branch_id', e.target.value)}
                      className="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                    >
                      {branches.map((branch) => (
                        <option key={branch.id} value={branch.id}>
                          {branch.name}
                        </option>
                      ))}
                    </select>
                    <InputError message={errors.branch_id} className="mt-2" />
                  </div>

                  {/* コミットメッセージ */}
                  <div className="rounded-lg bg-gray-50 p-4">
                    <h3 className="mb-3 text-sm font-medium text-gray-900">コミットメッセージ</h3>
                    <TextInput
                      type="text"
                      className="block w-full"
                      value={data.commit_message}
                      onChange={(e) => setData('commit_message', e.target.value)}
                      required
                      placeholder="変更内容を簡潔に説明"
                    />
                    <InputError message={errors.commit_message} className="mt-2" />
                    <p className="mt-1 text-xs text-gray-500">
                      例: 新しい記事「{data.title || 'タイトル'}」を追加
                    </p>
                  </div>

                  {/* マークダウンチート */}
                  <div className="rounded-lg bg-gray-50 p-4">
                    <h3 className="mb-3 text-sm font-medium text-gray-900">Markdown チートシート</h3>
                    <div className="space-y-2 text-xs text-gray-600">
                      <div><code># 見出し1</code></div>
                      <div><code>## 見出し2</code></div>
                      <div><code>**太字**</code></div>
                      <div><code>*斜体*</code></div>
                      <div><code>- リスト</code></div>
                      <div><code>[リンク](URL)</code></div>
                      <div><code>![画像](URL)</code></div>
                      <div><code>`コード`</code></div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </form>
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
