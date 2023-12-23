<?php
namespace App\Sourcing\WooCommerce\Authentication;

use App\Entity\Channel\Channel;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class AccessTokenProvider implements AccessTokenProviderInterface
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

        $basic = base64_encode($clientId . ':' . $clientSecret);
        return $basic;
    }


    
}
