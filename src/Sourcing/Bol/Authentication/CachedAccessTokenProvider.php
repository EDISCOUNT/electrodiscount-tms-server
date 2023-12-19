<?php

namespace App\Sourcing\Bol\Authentication;

use App\Entity\Channel\Channel;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class CachedAccessTokenProvider implements AccessTokenProviderInterface
{

    public function __construct(
        private AccessTokenProvider $provider,
        private CacheInterface $cache,
    ) {
    }


    public function getAccessTokenForChannel(Channel $channel): string
    {
        $key = 'bol.channel[' . $channel->getId() . '].auth.access_token';
        return $this->cache->get($key, function (ItemInterface $item) use ($channel) {
            $result = $this->provider->doGetAccessTokenForChannel($channel);
            $token = $result['access_token'];
            $expiresIn = $result['expires_in'];
            $item->expiresAfter($expiresIn);
            return $token;
        });
    }


    public function getAccessTokenByCredentials(
        string $clientId,
        string $clientSecret,
    ): string {

        throw new \LogicException('Getting channel access token with credentials is not supported yet; Try CachedAccessTokenProvider::getAccessTokenForChannel');
    }
}
