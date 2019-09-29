<?php

namespace Jybtx\OneSignal\Providers;

use Illuminate\Support\ServiceProvider;
use Jybtx\OneSignal\OneSignalClient;

class OneSignalServiceProvider extends ServiceProvider
{
	
	/**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->mergeConfig();
    }
    /**
     * Configure package paths.
     */
    private function configurePaths()
    {
        $this->publishes([
            __DIR__."/../config/one-signal.php" => config_path('one-signal.php'),
        ]);
    }
    /**
     * Merge configuration.
     */
    private function mergeConfig()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/one-signal.php', 'one-signal'
        );
    }
    /**
     * [singleton description]
     * @author 蒋岳
     * @date   2019-09-21
     * @param  string     $value [description]
     * @return [type]            [description]
     */
    private function getRegisterSingleton()
    {
        $this->app->singleton('OneSignal', function () {
            return new OneSignalClient();
        });
    }
    /**
     * Register any application services.
     *  
     * @return void
     */
    public function register()
    {
        $this->configurePaths();        
        $this->getRegisterSingleton();
    }
}