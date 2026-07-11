<?php

namespace App\Providers;

use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        Event::listen(Login::class, function (Login $event) {
            activity()->causedBy($event->user)->event('login')->log('Logged in');
        });

        Event::listen(Logout::class, function (Logout $event) {
            activity()->causedBy($event->user)->event('logout')->log('Logged out');
        });
    }
}
