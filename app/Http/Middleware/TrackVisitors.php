<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\SiteVisit;
use App\Models\SiteStatistic;
use Symfony\Component\HttpFoundation\Response;

class TrackVisitors
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Chỉ track các request GET (không phải API, assets, etc.)
        if ($request->isMethod('GET') && !$request->ajax() && !$request->is('api/*')) {
            $ip = $request->ip();

            // Cập nhật visit theo IP
            SiteVisit::trackVisit($ip);

            // Tăng counter tổng page views
            SiteStatistic::incrementValue('total_page_views');

            // Random dọn dẹp (1% chance để không ảnh hưởng performance)
            if (rand(1, 100) === 1) {
                SiteVisit::cleanOldVisits();
            }
        }

        return $next($request);
    }
}
