<?php namespace Elasticquent;

use Illuminate\Pagination\LengthAwarePaginator as Paginator;
use Illuminate\Support\Collection;

class ElasticquentPaginator extends Paginator
{
    /**
     * Create a new paginator instance.
     *
     * @param  mixed  $items
     * @param  mixed  $hits
     * @param  int  $total
     * @param  int  $perPage
     * @param  int|null  $currentPage
     * @param  array  $options (path, query, fragment, pageName)
     */
    public function __construct($items, $hits, $total, $perPage, $currentPage = null, array $options = [])
    {
        foreach ($options as $key => $value) {
            $this->{$key} = $value;
        }
        $this->total = $total;
        $this->perPage = $perPage;
        $this->lastPage = (int) ceil($total / $perPage);
        $this->currentPage = $this->setCurrentPage($currentPage, $this->lastPage);
        $this->path = $this->path != '/' ? rtrim($this->path, '/') . '/' : $this->path;
        $this->items = $items instanceof Collection ? $items : Collection::make($items);
        $this->hits = $hits;
    }

    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray()
    {
        return [
            'total'         => $this->total(),
            'per_page'      => $this->perPage(),
            'current_page'  => $this->currentPage(),
            'last_page'     => $this->lastPage(),
            'next_page_url' => $this->nextPageUrl(),
            'prev_page_url' => $this->previousPageUrl(),
            'from'          => $this->firstItem(),
            'to'            => $this->lastItem(),
            'hits'          => $this->hits,
            'data'          => $this->items->toArray(),
        ];
    }
}
