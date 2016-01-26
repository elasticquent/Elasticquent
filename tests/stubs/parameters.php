<?php

/*
 * parameter stubs
 */

function basicParameters()
{
    return [
        'index' => 'my_custom_index_name',
        'type' => 'test_table',
    ];
}

function searchParams($searchTerm)
{
    $params = basicParameters();
    $params['body'] = ['query' => ['match' => ['_all' => $searchTerm]]];
    return $params;
}
