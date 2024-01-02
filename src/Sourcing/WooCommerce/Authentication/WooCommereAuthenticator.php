<?php

namespace App\Sourcing\WooCommerce\Authentication;

use App\Entity\Channel\Channel;
use Automattic\WooCommerce\HttpClient\Options;
use Automattic\WooCommerce\Client;
use Automattic\WooCommerce\HttpClient\OAuth;

class WooCommereAuthenticator
{



    private string $url;
    private ?string $consumerSecret;
    private ?string $consumerKey;

    private Options $options;

    // private ?string $url;

    public function __construct(
        private Channel $channel,
        array $options = []
    ) {
        $options = [
            ...[
                'wp_api' => true,
                'version' => 'wc/v3',
                'query_string_auth' => true,
                'verify_ssl' => false,
            ],
            ...$options,
        ];


        $metadata = $channel->getMetadata();

        $consumerKey = $metadata['client_id'];
        $consumerSecret = $metadata['client_secret'];
        $url = $metadata['base_url'] ?? '';

        $this->options        = new Options($options);
        $this->url            = $this->buildApiUrl($url);
        $this->consumerKey    = $consumerKey;
        $this->consumerSecret = $consumerSecret;
    }



    protected function isSsl()
    {
        return 'https://' === \substr($this->url, 0, 8);
    }


    /**
     * Authenticate.
     *
     * @param string $url        Request URL.
     * @param string $method     Request method.
     * @param array  $parameters Request parameters.
     *
     * @return array
     */
    public function authenticate($url, $method, $parameters = [])
    {

        $headers = $this->getRequestHeaders(false);
        $basic = 'Basic ' . \base64_encode($this->consumerKey . ':' . $this->consumerSecret);

        // Setup authentication.
        if (!$this->options->isOAuthOnly() && $this->isSsl()) {
            return [
                'headers' => [
                    'Authorization' => $basic,
                    // 'Accepts' => 'application/json',
                    ...$headers,
                ],
                'query' => $parameters,
            ];
        } else {
            $oAuth = new OAuth(
                $url,
                $this->consumerKey,
                $this->consumerSecret,
                $this->options->getVersion(),
                $method,
                $parameters,
                $this->options->oauthTimestamp()
            );
            $parameters = $oAuth->getParameters();
        }

        return [
            'query' => $parameters,
            'headers' => [
                // 'Accepts' => 'application/json',
                ...$headers,
            ]
        ];
    }


    /**
     * Get request headers.
     *
     * @param  bool $sendData If request send data or not.
     *
     * @return array
     */
    protected function getRequestHeaders($sendData = false)
    {
        $headers = [
            'Accept'     => 'application/json',
            'User-Agent' => $this->options->userAgent() . '/' . Client::VERSION,
        ];

        if ($sendData) {
            $headers['Content-Type'] = 'application/json;charset=utf-8';
        }

        return $headers;
    }


    /**
     * Build API URL.
     *
     * @param string $url Store URL.
     *
     * @return string
     */
    public function buildApiUrl($url)
    {
        $api = $this->options->isWPAPI() ? $this->options->apiPrefix() : '/wc-api/';

        return \rtrim($url, '/') . $api . $this->options->getVersion() . '/';
    }


    public function getURL(): string
    {
        return $this->url;
    }
}
