<?php

namespace Elasticquent;

use Illuminate\Support\ServiceProvider;

class ElasticquentServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        if (ElasticquentSupport::isLaravel5()) {
            $this->publishes([
                __DIR__.'/config/elasticquent.php' => config_path('elasticquent.php'),
            ]);
        }
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        // Support class
        $this->app->singleton('elasticquent.support', function () {
            return new ElasticquentSupport;
        });

        // Elasticsearch client instance
        $this->app->singleton('elasticquent.elasticsearch', function ($app) {
            return $app->make('elasticquent.support')->getElasticSearchClient();
        });
    }
}
