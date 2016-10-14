<?php

namespace Elasticquent;

use GuzzleHttp\Psr7\Request;
use Aws\Signature\SignatureV4;
use Aws\Credentials\Credentials;
use GuzzleHttp\Ring\Future\CompletedFutureArray;
use GuzzleHttp\Psr7\Uri;
use Psr\Http\Message\ResponseInterface;

trait ElasticquentClientTrait
{
    use ElasticquentConfigTrait;

    /**
     * Get ElasticSearch Client.
     *
     * @return \Elasticsearch\Client
     */
    public function getElasticSearchClient()
    {
        $config = $this->getElasticConfig();

        // elasticsearch v2.0 using builder
        if (class_exists('\Elasticsearch\ClientBuilder')) {
            $awsConfig = $this->getElasticConfig('aws');
            if (!empty($awsConfig) && array_get($this->getElasticConfig('aws'), 'iam', false)) {
                if ($handler = $this->getAwsESHandler()) {
                    array_set($config, 'handler', $handler);
                }
            }

            return \Elasticsearch\ClientBuilder::fromConfig($config);
        }

        // elasticsearch v1
        return new \Elasticsearch\Client($config);
    }

    /**
     * @return bool|\Closure
     */
    private function getAwsESHandler()
    {
        $awsConfig = $this->getElasticConfig('aws');
        if (empty($awsConfig)) {
            return false;
        }

        $key = array_get($awsConfig, 'key');
        $secret = array_get($awsConfig, 'secret');
        $region = array_get($awsConfig, 'region', 'us-west-2');

        $psr7Handler = \Aws\default_http_handler();
        $signer = new SignatureV4('es', $region);

        $handler = function (array $request) use (
            $psr7Handler,
            $signer,
            $key,
            $secret
        ) {
            // Amazon ES listens on standard ports (443 for HTTPS, 80 for HTTP).
            $request['headers']['host'][0] = parse_url($request['headers']['host'][0], PHP_URL_HOST);

            $credentials = new Credentials($key, $secret);

            // Create a PSR-7 request from the array passed to the handler
            $psr7Request = new Request($request['http_method'],
                (new Uri($request['uri']))->withScheme($request['scheme'])->withHost($request['headers']['host'][0]),
                $request['headers'], $request['body']);

            // Sign the PSR-7 request with credentials from the environment
            $signedRequest = $signer->signRequest($psr7Request, $credentials);

            // Send the signed request to Amazon ES
            /** @var ResponseInterface $response */
            $response = $psr7Handler($signedRequest)->wait();

            // Convert the PSR-7 response to a RingPHP response
            return new CompletedFutureArray([
                'status'         => $response->getStatusCode(),
                'headers'        => $response->getHeaders(),
                'body'           => $response->getBody()->detach(),
                'transfer_stats' => ['total_time' => 0],
                'effective_url'  => (string) $psr7Request->getUri(),
            ]);
        };

        return $handler;
    }
}
