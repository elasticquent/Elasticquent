<?php

/*
 * result stubs
 */

function successfulResults()
{
    return [
        'took' => 8,
        'timed_out' => false,
        '_shards' => [
            'total' => 5,
            'successful' => 5,
            'unsuccessful' => 0,
        ],
        'hits' => [
            'total' => 2,
            'max_score' => 0.7768564,
            'hits' => [
                [
                    '_index' => 'my_custom_index_name',
                    '_type' => 'test_table',
                    '_score' => 0.7768564,
                    '_source' => [
                        'name' => 'foo',
                    ]
                ],
                [
                    '_index' => 'my_custom_index_name',
                    '_type' => 'test_table',
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
            'total' => 0,
            'max_score' => null,
            'hits' => [],
        ],
        'aggregations' => [],
    ];
}