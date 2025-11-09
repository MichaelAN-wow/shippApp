<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\Trace;

class TraceServiceProvider extends ServiceProvider
{
    public function boot()
    {
        (new Trace())->boot();
    }

    public function register()
    {
        //
    }
}