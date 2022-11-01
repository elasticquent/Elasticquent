<?php

/*
 * parameter stubs
 */

function basicParameters()
{
    return [
        'index' => 'my_custom_index_name_test_table_index',
    ];
}

function searchParams($searchTerm)
{
    $params = basicParameters();
    $params['body'] = ['query' => ['simple_query_string' => ['query' => $searchTerm]]];
    return $params;
}

function complexParameters()
{
    $params = basicParameters();
    $params['body'] = [
        'query' => [
            'filtered' => [
                'filter' => [
                    'term' => [ 'my_field' => 'abc' ]
                ],
                'query' => [
                    'match' => [ 'my_other_field' => 'xyz' ]
                ]
            ]
        ]
    ];
    return $params;   
}
