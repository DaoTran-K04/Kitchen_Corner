<?php

namespace App\Observers;

use App\Models\Article;
use Illuminate\Support\Facades\Cache;

class ArticleObserver
{
    /**
     * Handle events after a record has been created or updated.
     */
    public function saved(Article $article)
    {
        // Ensure version key exists then increment to invalidate suggestion caches
        if (!Cache::has('article_suggestion_version')) {
            Cache::put('article_suggestion_version', 1);
            return;
        }

        Cache::increment('article_suggestion_version');
    }

    /**
     * Handle the Article "deleted" event.
     */
    public function deleted(Article $article)
    {
        if (!Cache::has('article_suggestion_version')) {
            Cache::put('article_suggestion_version', 1);
            return;
        }

        Cache::increment('article_suggestion_version');
    }
}
