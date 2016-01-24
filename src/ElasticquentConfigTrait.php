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
        $config = array();

        $key = $prefix . ($key ? '.' : '') . $key;

        if (function_exists('config')) { // Laravel 5.1+
            $config_helper = config();
        } elseif (function_exists('app')) { // Laravel 4 and 5.0
            $config_helper = app('config');
        } else { // stand-alone Eloquent
            $config_helper = $this->getConfigHelper();
        }

        return $config_helper->get($key);
    }

    /**
     * Inject given config file into an instance of Laravel's config
     *
     * @return object Illuminate\Config\Repository
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
        return __DIR__.'/config/elasticquent.php';
    }
}
