<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;

use App\Observers\UserObserver;
use App\Observers\SchoolObserver;
use App\Observers\PoliticalPartyObserver;
use App\Observers\MesaObserver;

use App\Models\User;
use App\Models\School;
use App\Models\PoliticalParty;
use App\Models\Mesa;

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
     // 1. Mesa (Lógica compleja de fiscales)
    Mesa::observe(MesaObserver::class);

    // 2. Modelos con Observers "Inteligentes" (Array de campos)
    School::observe(SchoolObserver::class);
    User::observe(UserObserver::class);
    PoliticalParty::observe(PoliticalPartyObserver::class);


    // Configurar paginación para Bootstrap 5
    Paginator::useBootstrapFive();
    }
}
