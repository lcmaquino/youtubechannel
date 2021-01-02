<?php

namespace Lcmaquino\YouTubeChannel;

use Illuminate\Support\ServiceProvider;

class YouTubeChannelProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $source = realpath(__DIR__ . '/config/googleoauth2.php');
        $this->mergeConfigFrom($source, 'googleoauth2');
        
        $this->app->singleton(YouTubeChannelManager::class, function($app){
            return (new YouTubeChannelManager(config('googleoauth2'), $app->request));
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [YouTubeChannelManager::class];
    }
}