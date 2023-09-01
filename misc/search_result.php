<?php

use Freento\FastSearchAutocomplete\Api\Data\ElasticProductInterface;
use Freento\FastSearchAutocomplete\Model\ElasticSearch;

const INDEXER_ID = 'product';
const ELASTIC_CONFIG_FILE = 'freento-elastic-config.json';
const ELASTIC_CONFIG_DIRECTORY = '/var/';

try {
    require_once __DIR__ .  '/../app/bootstrap.php';
} catch (\Exception $e) {
    echo 'Autoload error: ' . $e->getMessage();
    exit(1);
}

header('Content-Type: application/json; charset=utf-8');

function getQuery(string $value, string $queryIndex, int $maxResultsCount)
{
    return json_decode(sprintf(
        '{
    "index": "%1$s",
    "body": {
        "from": 0,
        "size": %3$s,
        "docvalue_fields": [
            "_id",
            "_score"
        ],
        "sort": [],
        "query": {
            "bool": {
                "should": [
                    {
                        "match": {
                            "sku": {
                                "query": "%2$s",
                                "boost": 2
                            }
                        }
                    },
                    {
                        "match": {
                            "%4$s": {
                                "query": "%2$s",
                                "boost": 2
                            }
                        }
                    },
                    {
                        "match_phrase_prefix": {
                            "%4$s": {
                                "query": "%2$s",
                                "boost": 2,
                                "analyzer": "prefix_search"
                            }
                        }
                    },
                    {
                        "match_phrase_prefix": {
                            "sku": {
                                "query": "%2$s",
                                "boost": 2,
                                "analyzer": "sku_prefix_search"
                            }
                        }
                    }
                ],
                "minimum_should_match": 1
            }
        },
        "_source": [
            "%4$s"
        ]
    },
    "track_total_hits": true
}',
        $queryIndex,
        $value,
        $maxResultsCount,
        ElasticProductInterface::ORIGINAL_NAME
    ), true);
}

function getConnection(array $options = []) {
    $engine = $options['engine'] ?? 'elasticsearch7';
    $connectionConfig = buildESConfig($options);
    $connection = null;

    if (str_contains(strtolower($engine), 'elasticsearch')) {
        $connection = \Elasticsearch\ClientBuilder::fromConfig($connectionConfig, true);
    } elseif (str_contains(strtolower($engine), 'opensearch') && class_exists('\OpenSearch\ClientBuilder')) {
        $connection = \OpenSearch\ClientBuilder::fromConfig($connectionConfig, true);
    }

    return $connection;
}

function buildESConfig(array $options = []): array
{
    $hostname = parse_url($options['hostname'], PHP_URL_HOST);
    $protocol = parse_url($options['hostname'], PHP_URL_SCHEME);

    if (!$hostname) {
        $hostname = $options['hostname'];
    }

    if (!$protocol) {
        $protocol = 'http';
    }

    $authString = '';
    if (!empty($options['enableAuth']) && (int)$options['enableAuth'] === 1) {
        $authString = '{' . $options['username'] . '}:{' . $options['password'] . '}@';
    }

    $portString = '';
    if (!empty($options['port'])) {
        $portString = ':' . $options['port'];
    }

    $host = $protocol . '://' . $authString . $hostname . $portString;

    $options['hosts'] = [$host];

    return $options;
}

if (!file_exists(BP . ELASTIC_CONFIG_DIRECTORY . ELASTIC_CONFIG_FILE)) {
    echo json_encode([['title' => 'Please, configure Fast autocomplete extension, press
     "Generate Search Configuration File." button.']], true);
    exit(1);
}

$storeId = $_GET['storeId'] ?? '';

$configJson = file_get_contents(BP . ELASTIC_CONFIG_DIRECTORY . ELASTIC_CONFIG_FILE);
$elasticConfig = json_decode($configJson, true);

$queryIndex = $elasticConfig['index'] . '_' . INDEXER_ID . '_' . $storeId;

$searchTerm = $_GET['q'] ?? '';

$maxResultsCount = $elasticConfig['max_results_count'] ?? ElasticSearch::SEARCH_QUERY_SIZE;

$query = getQuery($searchTerm, $queryIndex, $maxResultsCount);
if (str_contains(strtolower($elasticConfig['engine']), 'opensearch')) {
    unset($query['track_total_hits']);
}

$connection = getConnection($elasticConfig);
$response = [];

if ($connection) {
    $results = $connection->search($query);
    foreach ($results['hits']['hits'] as $result) {
        if (!isset($result['_source'][ElasticProductInterface::ORIGINAL_NAME])) {
            continue;
        }

        $productName = $result['_source'][ElasticProductInterface::ORIGINAL_NAME] ?? '';

        if (is_array($productName)) {
            $productName = current($productName);
        }

        $maxSearchResultSymbols = $elasticConfig['max_search_result_symbols'] ?? 0;

        if ($maxSearchResultSymbols !== 0 && strlen($productName) > $maxSearchResultSymbols) {
            $productName = mb_substr($productName, 0, $maxSearchResultSymbols);
            $productName .= '...';
        }

        $response[] = [
            'title' => $productName,
            'id' => $result['_id']
        ];
    }
} else {
    $response = [['title' => 'Something wrong with connection client']];
}

echo json_encode($response, true);
