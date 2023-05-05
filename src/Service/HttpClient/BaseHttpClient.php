<?php

namespace App\Service\HttpClient;

use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;

abstract class BaseHttpClient
{
    protected HttpClientInterface $httpClient;
    protected const PREFIX = '/api';
    protected array $defaultOptions;

    public function __construct(string $host, array $defaultOptions = [])
    {
        $this->httpClient = HttpClient::createForBaseUri($host);
        $this->defaultOptions = array_merge_recursive($defaultOptions,
            $this->defaultOptions = [
                'headers' => [
                    'Content-Type' => 'application/json'
                ]
            ]);
    }

    /**
     * @param array $options
     */
    public function addDefaultOption(array $options): void
    {
        $this->defaultOptions = array_merge_recursive($this->defaultOptions, $options);
    }
}
