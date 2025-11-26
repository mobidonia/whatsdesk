<?php

namespace App\NotificationChannels\Expo;

use GuzzleHttp\Client as GuzzleClient;
use Illuminate\Support\ServiceProvider;

class ExpoServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot(): void
    {
        $this->app->when(ExpoChannel::class)
            ->needs(GuzzleClient::class)
            ->give(function () {
                $accessToken = config('expo.access_token');
                if ($accessToken) {
                    return new GuzzleClient(['headers' => ['Authorization' => "Bearer $accessToken"]]);
                } else {
                    return new GuzzleClient();
                }
            });

        $this->publishes([
            realpath(__DIR__.'/../config/expo.php') => config_path('expo.php'),
        ], 'config');
    }

    /**
     * Register the application services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(realpath(__DIR__.'/../config/expo.php'), 'expo');
    }
}
