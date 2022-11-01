<?php

/*
 * result stubs
 */

class results
{

    public $data;

    function __construct($type) {
        if($type == 'successful')
            $this->data = $this->successfulResults();
        else
            $this->data = $this->unsuccessfulResults();
    }

    function successfulResults()
    {
        return (object) [
            'took' => 8,
            'timed_out' => false,
            '_shards' => [
                'total' => 5,
                'successful' => 5,
                'unsuccessful' => 0,
            ],
            'hits' => [
                'total' => ['value' => 2],
                'max_score' => 0.7768564,
                'hits' => [
                    [
                        '_index' => 'my_custom_index_name_test_table_index',
                        '_score' => 0.7768564,
                        '_source' => [
                            'name' => 'foo',
                        ]
                    ],
                    [
                        '_index' => 'my_custom_index_name_test_table_index',
                        '_score' => 0.5634561,
                        '_source' => [
                            'name' => 'bar',
                        ]
                    ],
                ],
            ],
            'aggregations' => [],
        ];
    }

    function unsuccessfulResults()
    {
        return [
            'took' => 4,
            'timed_out' => false,
            '_shards' => [
                'total' => 5,
                'successful' => 5,
                'unsuccessful' => 0,
            ],
            'hits' => [
                'total' => ['value' => 0],
                'max_score' => null,
                'hits' => [],
            ],
            'aggregations' => [],
        ];
    }

    function asArray()
    {
        return (array) $this->data;
    }
}