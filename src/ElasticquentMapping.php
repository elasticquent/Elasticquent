<?php
namespace Elasticquent;

trait ElasticquentMapping
{
    /**
     * Mapping Exists
     *
     * @return bool
     */
    public static function mappingExists()
    {
        $instance = new static;

        $mapping = $instance->getMapping();

        return (empty($mapping)) ? false : true;
    }

    /**
     * Get Mapping
     *
     * @return void
     */
    public static function getMapping()
    {
        $instance = new static;

        $params = $instance->getBasicEsParams();

        return $instance->getElasticSearchClient()->indices()->getMapping($params);
    }

    /**
     * Put Mapping.
     *
     * @param bool $ignoreConflicts
     *
     * @return array
     */
    public static function putMapping($ignoreConflicts = false)
    {
        $instance = new static;

        $mapping = $instance->getBasicEsParams();

        $params = array(
            '_source' => array('enabled' => true),
            'properties' => $instance->getMappingProperties(),
        );

        $mapping['body'][$instance->getTypeName()] = $params;

        return $instance->getElasticSearchClient()->indices()->putMapping($mapping);
    }

    /**
     * Delete Mapping
     *
     * @return array
     */
    public static function deleteMapping()
    {
        $instance = new static;

        $params = $instance->getBasicEsParams();

        return $instance->getElasticSearchClient()->indices()->deleteMapping($params);
    }

    /**
     * Rebuild Mapping
     *
     * This will delete and then re-add
     * the mapping for this model.
     *
     * @return array
     */
    public static function rebuildMapping()
    {
        $instance = new static;

        // If the mapping exists, let's delete it.
        if ($instance->mappingExists()) {
            $instance->deleteMapping();
        }

        // Don't need ignore conflicts because if we
        // just removed the mapping there shouldn't
        // be any conflicts.
        return $instance->putMapping();
    }
}