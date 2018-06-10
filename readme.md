# Elasticquent   
This fork fixed two issue of main [repository](https://github.com/elasticquent/Elasticquent) . check main repo for more details. 

1. issue with elasticSearch 6+ version. Content-Type : [] 
2. Allow to pass raw json query. example: 

```php
$jsonQuery = '{
  "query": {
    "bool": {
      "must": [
        { "match": { "transcription":"'.$keyword.'" }},
        { "match": { "userID": '.$userid.' }}
      ]
    }
  }
}';
$audios = Audio::searchByQuery($jsonQuery);
```

