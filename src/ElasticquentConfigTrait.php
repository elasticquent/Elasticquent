<?php

namespace Elasticquent;


trait ElasticquentConfigTrait
{
    /**
     * Get Routing Name
     *
     * @return string|null
     */
    public function getRoutingName()
    {
        return null;
    }

    /**
     * Get Index Name
     *
     * @return string
     */
    public function getIndexName()
    {
        // The first thing we check is if there is an elasticquent
        // config file and if there is a default index.
        $index_name = $this->getElasticConfig('default_index');

        if (!empty($index_name)) {
            //ES 7 -> we need to name index
            return $index_name.'_'.$this->getTypeName().'_index';
        }

        // Otherwise we will just go with 'default'
        return 'default_'.$this->getTypeName().'_index';
    }

    /**
     * Get the Elasticquent config
     *
     * @param string $key the configuration key
     * @param string $prefix filename of configuration file
     * @return array|string configuration
     */
    public function getElasticConfig(string $key = 'config', string $prefix = 'elasticquent')
    {
        $key = $prefix . ($key ? '.' : '') . $key;

        //If there is no config, then theres no Laravel
        if(!class_exists('Config')) {
            //Create a config helper when using stand-alone Eloquent
            $config_helper = $this->getConfigHelper();
        }
        else {
            $config_helper = config();
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
