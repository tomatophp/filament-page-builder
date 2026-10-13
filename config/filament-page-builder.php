<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Builder Routes
    |--------------------------------------------------------------------------
    |
    | The page builder registers its routes under `admin/pages/{model}/*`.
    | Every request is authorized with the host application's policy for the
    | model below (`update` ability). Without a registered policy all builder
    | requests are denied.
    |
    */
    'routes' => [
        'enabled' => env('FILAMENT_PAGE_BUILDER_ROUTES', true),
        'middleware' => ['web', 'auth'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Page Model
    |--------------------------------------------------------------------------
    |
    | The Eloquent model edited by the builder. It must expose a `meta()`
    | method (like the Filament CMS `Post` model).
    |
    */
    'model' => 'TomatoPHP\FilamentCms\Models\Post',
];
