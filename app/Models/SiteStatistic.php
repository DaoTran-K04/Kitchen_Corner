<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiteStatistic extends Model
{
    protected $fillable = ['key', 'value'];

    /**
     * Lấy giá trị thống kê theo key
     */
    public static function getValue(string $key, int $default = 0): int
    {
        $stat = self::where('key', $key)->first();
        return $stat ? (int)$stat->value : $default;
    }

    /**
     * Tăng giá trị thống kê
     */
    public static function incrementValue(string $key, int $amount = 1): void
    {
        $stat = self::firstOrCreate(
            ['key' => $key],
            ['value' => 0]
        );
        $stat->increment('value', $amount);
    }

    /**
     * Lấy tổng lượt xem trang
     */
    public static function getTotalPageViews(): int
    {
        return self::getValue('total_page_views', 0);
    }
}
