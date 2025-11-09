<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Blade;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // …
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Fix legacy MySQL key‑length issue
        Schema::defaultStringLength(191);

        // ─── 12‑hour time helper  @time12($date) ────────────
        Blade::directive('time12', function ($exp) {
            return "<?php echo empty($exp)
                ? ''
                : \\Illuminate\\Support\\Carbon::parse($exp)
                    ->format('g:i A'); ?>";
        });
        // ────────────────────────────────────────────────────
    }
}
