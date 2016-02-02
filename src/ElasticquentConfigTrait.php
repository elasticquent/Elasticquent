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
    public function getElasticConfig($key = 'config', $prefix = 'elasticquent')
    {
        $key = $prefix . ($key ? '.' : '') . $key;

        if (function_exists('config')) {
            // Get config helper for Laravel 5.1+
            $config_helper = config();
        } elseif (function_exists('app')) {
            // Get config helper for Laravel 4 & Laravel 5.1
            $config_helper = app('config');
        } else {
            // Create a config helper when using stand-alone Eloquent
            $config_helper = $this->getConfigHelper();
        }

        return $config_helper->get($key);
    }

    /**
     * Inject given config file into an instance of Laravel's config
     *
     * @throws \Exception when the configuration file is not found
     * @return \Illuminate\Config\Repository configuration repository
     */
    protected function getConfigHelper()
    {
        $config_file = $this->getConfigFile();

        if (!file_exists($config_file)) {
            throw new \Exception('Config file not found.');
        }

        return new \Illuminate\Config\Repository(array('elasticquent' => require($config_file)));
    }

    /**
     * Get the config path and file name to use when Laravel framework isn't present
     * e.g. using Eloquent stand-alone or running unit tests
     *
     * @return string config file path 
     */
    protected function getConfigFile()
    {
        return __DIR__ . '/config/elasticquent.php';
    }
}
