<?php

namespace Canvas\Helpers;

use Illuminate\Support\Facades\File;
use RuntimeException;

class Asset
{
    /**
     * Return true if the publishable assets are up to date.
     *
     * @return bool
     */
    public static function upToDate(): bool
    {
        $path = public_path('vendor/canvas/mix-manifest.json');

        if (! File::exists($path)) {
            throw new RuntimeException(__('canvas::app.assets_are_not_up_to_date').__('canvas::app.to_update_run').' php artisan canvas:publish');
        }

        return File::get($path) === File::get(__DIR__.'/../public/mix-manifest.json');
    }
}