<?php namespace Adamfairholm\Elasticquent;

class ElasticquentResultCollection extends \Illuminate\Database\Eloquent\Collection {

    protected $took;
    protected $timed_out;
    protected $shards;
    protected $hits;

    /**
     * _construct 
     *
     * @param   $results elasticsearch results
     * @return  void
     */
    public function __construct($results, $instance)
    {
        // Take our result data and map it
        // to some class properties.
        $this->took = $results['took'];
        $this->timed_out = $results['timed_out'];
        $this->shards = $results['_shards'];
        $this->hits = $results['hits'];

        // Now we need to assign our hits to the
        // items in the collection.
        $this->items = $this->hitsToItems($instance);
    }

    /**
     * Hits To Items
     *
     * @return void
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
     * 
     *
     * @return void
     */
    public function getShards()
    {
        return $this->shards;
    }

    /**
     * 
     *
     * @return void
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

}