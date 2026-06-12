<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;

class ComponentServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        Blade::component('quickview', \App\Views\QuickView::class);
        Blade::component('header', \App\Views\Header::class);
        Blade::component('modal-category', \App\Views\ModalCategory::class);
        Blade::component('footer', \App\Views\Footer::class);
        Blade::component('floating-contact', \App\Views\FloatingContact::class);
        Blade::component('slider', \App\Views\Slider::class);
        Blade::component('section', \App\Views\Section::class);
    }
}
