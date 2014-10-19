<?php namespace Elasticquent;

class ElasticquentResultCollection extends \Illuminate\Database\Eloquent\Collection
{
    protected $took;
    protected $timed_out;
    protected $shards;
    protected $hits;
    protected $aggregations = null;

    /**
     * _construct
     *
     * @param   $results elasticsearch results
     * @param $instance
     * @return \Elasticquent\ElasticquentResultCollection
     */
    public function __construct($results, $instance)
    {
        // Take our result data and map it
        // to some class properties.
        $this->took         = $results['took'];
        $this->timed_out    = $results['timed_out'];
        $this->shards       = $results['_shards'];
        $this->hits         = $results['hits'];
        $this->aggregations = $results['aggregations'];

        // Now we need to assign our hits to the
        // items in the collection.
        $this->items = $this->hitsToItems($instance);
    }

    /**
     * Hits To Items
     *
     * @param   Eloquent model instance $instance
     * @return  array
     */
    private function hitsToItems($instance)
    {
        $items = array();

        foreach ($this->hits['hits'] as $hit) {

            $items[] = $instance->newFromHitBuilder($hit);
        }

        return $items;
    }

    /**
     * Total Hits
     *
     * @return int
     */
    public function totalHits()
    {
        return $this->hits['total'];
    }

    /**
     * Max Score
     *
     * @return float
     */
    public function maxScore()
    {
        return $this->hits['max_score'];
    }

    /**
     * Get Shards
     *
     * @return array
     */
    public function getShards()
    {
        return $this->shards;
    }

    /**
     * Took
     *
     * @return string
     */
    public function took()
    {
        return $this->took;
    }

    /**
     * Timed Out
     *
     * @return bool
     */
    public function timedOut()
    {
        return (bool)$this->timed_out;
    }

    /**
     * Get Hits
     *
     * Get the raw hits array from
     * Elasticsearch results.
     *
     * @return array
     */
    public function getHits()
    {
        return $this->hits;
    }

    /**
     * Get aggregations
     *
     * Get the raw hits array from
     * Elasticsearch results.
     *
     * @return array
     */
    public function getAggregations()
    {
        return $this->aggregations;
    }

}