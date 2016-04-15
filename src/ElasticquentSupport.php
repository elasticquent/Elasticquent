<?php

namespace Elasticquent;

use Illuminate\Foundation\Application;

class ElasticquentSupport
{
    use ElasticquentClientTrait;

    public static function isLaravel5()
    {
        return version_compare(Application::VERSION, '5', '>');
    }
}
