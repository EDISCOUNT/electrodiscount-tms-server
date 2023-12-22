<?php
namespace App\Sourcing\WooCommerce\Authentication;

use App\Entity\Channel\Channel;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class AccessTokenProvider
{

    public function __construct(
        private HttpClientInterface $httpClient
    ) {
    }


    public function getAccessTokenForChannel(Channel $channel): string
    {
        $metadata =  $channel->getMetadata();
        return $this->getAccessTokenByCredentials(
            $metadata['client_id']?? ''	,
            $metadata['client_secret']?? '',
        );
    }


    public function getAccessTokenByCredentials(
        string $clientId,
        string $clientSecret,
    ): string {

        $result = $this->doGetAccessTokenByCredentials($clientId, $clientSecret);
        $accessToken = $result['access_token'];
        return $accessToken;
    }


    

    public function doGetAccessTokenForChannel(Channel $channel): array
    {
        $metadata = $channel->getMetadata();
        return $this->doGetAccessTokenByCredentials(
            $metadata['client_id']?? '',
            $metadata['client_secret']?? '',
        );
    }


    public function doGetAccessTokenByCredentials(
        string $clientId,
        string $clientSecret,
    ): array {

        $basic = base64_encode($clientId . ':' . $clientSecret);
        $response = $this->httpClient->request('POST', 'https://login.bol.com/token?grant_type=client_credentials', [
            'headers' => [
                'Authorization' => 'Basic ' . $basic,
            ],
        ]);

        // {
        //     "access_token": "<access_token>",
        //     "token_type": "Bearer",
        //     "expires_in": 299,
        //     "scope": "<scopes>"
        // }

        $result = $response->toArray();
        return $result;
    }
}
