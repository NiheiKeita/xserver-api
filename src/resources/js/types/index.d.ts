export interface User {
    id: number;
    name: string;
    email: string;
    email_verified_at: string;
    role: string;
    created_at: string;
    updated_at: string;
}

export type PageProps<T extends Record<string, unknown> = Record<string, unknown>> = T & {
    auth: {
        user: User;
    };
};

export interface Repository {
    id: number;
    name: string;
    owner_id: number;
    is_public: boolean;
    created_at: string;
    updated_at: string;
    owner?: User;
    users?: User[];
    branches_count?: number;
    articles_count?: number;
}

export interface Branch {
    id: number;
    repository_id: number;
    name: string;
    base_branch_id?: number;
    created_at: string;
    updated_at: string;
    base_branch?: Branch;
    child_branches?: Branch[];
    article_versions_count?: number;
}

export interface Commit {
    id: number;
    message: string;
    user_id: number;
    created_at: string;
    updated_at: string;
    user?: User;
    article_versions?: ArticleVersion[];
}

export interface Article {
    id: number;
    repository_id: number;
    current_commit_id: number;
    created_at: string;
    updated_at: string;
    current_commit?: Commit;
    versions?: ArticleVersion[];
    attachments?: Attachment[];
}

export interface ArticleVersion {
    id: number;
    article_id: number;
    branch_id: number;
    commit_id: number;
    title: string;
    body: string;
    created_at: string;
    updated_at: string;
    article?: Article;
    branch?: Branch;
    commit?: Commit;
}

export interface PullRequest {
    id: number;
    repository_id: number;
    from_branch_id: number;
    to_branch_id: number;
    title: string;
    description?: string;
    status: 'open' | 'closed' | 'merged';
    created_at: string;
    updated_at: string;
    from_branch?: Branch;
    to_branch?: Branch;
    comments?: PrComment[];
}

export interface PrComment {
    id: number;
    pull_request_id: number;
    user_id: number;
    body: string;
    created_at: string;
    updated_at: string;
    user?: User;
    pull_request?: PullRequest;
}

export interface Attachment {
    id: number;
    article_id: number;
    path: string;
    filename: string;
    mime_type: string;
    size: number;
    created_at: string;
    updated_at: string;
    article?: Article;
}

export interface ActivityLog {
    id: number;
    user_id: number;
    type: string;
    message: string;
    metadata?: Record<string, any>;
    created_at: string;
    updated_at: string;
    user?: User;
}
