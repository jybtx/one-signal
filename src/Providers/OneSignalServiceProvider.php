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
        ],'one-signal');
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
        $this->app->singleton('OneSignal', function ($app) {
            $config = isset( $app['config']['services']['one-signal'] ) ? $app['config']['services']['one-signal'] : null;
            if ( is_null( $config ) ) {
                $config = $app['config']['one-signal'] ?: $app['config']['one-signal::config'];
            }
            return new OneSignalClient($config['app_id'], $config['api_key']);
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