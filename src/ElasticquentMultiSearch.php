<?php

namespace Elasticquent;

use Illuminate\Database\Eloquent\Model;

class ElasticquentMultiSearch extends Model implements ElasticquentInterface
{
    use ElasticquentTrait {
        getBasicEsParams as TraitGetBasicEsParams;
        getIndexName as TraitGetIndexName;
    }

    protected $types = [];

    protected $indices = [];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        // initialize indices
        $this->indices = [$this->TraitGetIndexName()];
    }

    /**
     * Set indcies to search from
     * @param mixed $indices
     */
    public function setIndexName($indices)
    {
        $this->indices = $indices;
    }

    public function getIndexName()
    {
        return $this->indices;
    }

    /**
     * Set types to search from
     * @param mixed $types
     */
    public function setTypeName($types)
    {
        $this->types = $types;

    }

    public function getTypeName()
    {
        return $this->types;
    }

    /**
     * Create a elacticquent result collection of models from plain arrays.
     *
     * Use _type to instantiate models
     *
     * @param  array  $items
     * @param  array  $meta
     * @return \Elasticquent\ElasticquentResultCollection
     */
    public static function hydrateElasticquentResult(array $items, $meta = null)
    {
        // Cache instances
        $instances = [];

        $results = [];

        foreach ($items as $item) {
            $className = $item['_type'];
            if (!class_exists($className)) {
                continue;
            }
            if (!isset($instances[$className])) {
                $instances[$className] = new $className;
            }
            $results[] = $instances[$className]->newFromHitBuilder($item);
        }

        return (new static)->newElasticquentResultCollection($results, $meta);
    }
}
