<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View; // [1] Thêm dòng này
use App\Models\Category; // [2] Thêm dòng này
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
        // Tự động nhận diện URL và Force HTTPS trên Hosting
        if (!app()->runningInConsole()) {
            if (str_contains(request()->getHost(), 'tranhoangdao.id.vn')) {
                \Illuminate\Support\Facades\URL::forceRootUrl(request()->getSchemeAndHttpHost());
                \Illuminate\Support\Facades\URL::forceScheme('https');
            }
        }

        View::composer('*', function ($view) {
            $view->with('menuCategories', Category::orderBy('name')->get(['id', 'name']));
        });

        // Đăng ký observer để invalidate cache gợi ý khi Article thay đổi
        Article::observe(ArticleObserver::class);
    }
}
