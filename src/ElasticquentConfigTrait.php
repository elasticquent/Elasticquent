<?php

namespace Elasticquent;

trait ElasticquentConfigTrait
{
    /**
     * Get the Elasticquent config
     *
     * @param string $key the configuration key
     * @param string $prefix filename of configuration file
     * @return array configuration
     */
    protected function getElasticConfig($key = 'config', $prefix = 'elasticquent')
    {
        $config = array();

        $key = $prefix . ($key ? '.' : '') . $key;

        // Laravel 4 support
        if (!function_exists('config')) {
            $config_helper = app('config');
        } else {
            $config_helper = config();
        }

        if ($config_helper->has($key)) {
            $config = $config_helper->get($key);
        }

        return $config;
    }

}
