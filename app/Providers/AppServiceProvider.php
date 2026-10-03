<?php

namespace App\Providers;

use Filament\Schemas\Schema;
use Illuminate\Support\ServiceProvider;

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
        // One field per row, one card per row — no side-by-side cards in the admin.
        Schema::configureUsing(fn (Schema $schema): Schema => $schema->columns(1));
    }
}
