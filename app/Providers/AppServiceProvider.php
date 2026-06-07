<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Model;
use App\Models\Category;
use App\Models\Article;
use App\Observers\ArticleObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Ngăn chặn N+1 Query do Lazy Loading ở môi trường Dev/Local
        Model::preventLazyLoading(!app()->isProduction());

        // Tự động nhận diện URL và Force HTTPS trên Hosting
        if (!app()->runningInConsole()) {
            if (str_contains(request()->getHost(), 'tranhoangdao.id.vn')) {
                \Illuminate\Support\Facades\URL::forceRootUrl(request()->getSchemeAndHttpHost());
                \Illuminate\Support\Facades\URL::forceScheme('https');
            }
        }

        View::composer('*', function ($view) {
            $categories = Cache::remember('menu_categories_global', 3600, function () {
                return Category::orderBy('name')->get(['id', 'name']);
            });
            $view->with('menuCategories', $categories);
        });

        // Đăng ký observer để invalidate cache gợi ý khi Article thay đổi
        Article::observe(ArticleObserver::class);
    }
}
