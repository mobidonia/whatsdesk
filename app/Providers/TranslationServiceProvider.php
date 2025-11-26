<?php

namespace App\Providers;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\ServiceProvider;

class TranslationServiceProvider extends ServiceProvider
{
    /**
     * The path to the current lang files.
     *
     * @var string
     */
    protected $langPath;

    /**
     * Create a new service provider instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->langPath = resource_path('lang/'.App::getLocale());
    }

    /**
     * Bootstrap the application services.
     */
    public function boot(): void
    {
        $availableLanguagesENV = config('settings.front_languages');
        $exploded = explode(',', $availableLanguagesENV);
        $currentLocale = App::getLocale();
        for ($i = 0; $i < count($exploded); $i += 2) {
            $lng = strtolower($exploded[$i]);
            try {
                Cache::rememberForever('translations'.$lng, function () use ($lng) {
                    return collect(File::allFiles(resource_path('lang/'.$lng)))->flatMap(function ($file) use ($lng) {
                        if ($file->getBasename('.php') == 'custom') {
                            App::setLocale(strtolower($lng));

                            return [
                                ($translation = $file->getBasename('.php')) => trans($translation),
                            ];
                        }

                    })->toJson();
                });
            } catch (\Throwable $th) {
            }

        }
        App::setLocale($currentLocale);
    }
}
