<?php

namespace Elasticquent;

trait EloquentMethods
{
    /**
     * Convert the model instance to an array.
     */
    abstract public function toArray();

    /**
     * Get the value of the model's primary key.
     */
    abstract public function getKey();

    /**
     * Get the table associated with the model.
     */
    abstract public function getTable();

    /**
     * Create a new instance of the given model.
     *
     * @param  array  $attributes
     * @param  bool  $exists
     */
    abstract public function newInstance($attributes = [], $exists = false);

    /**
     * Get a new query builder for the model's table.
     */
    abstract public function newQuery();
}