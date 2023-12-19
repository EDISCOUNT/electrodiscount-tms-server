<?php

namespace App\Sourcing\Factory;

use App\Entity\Channel\Channel;
use App\Sourcing\Repository\RepositoryInterface;


/**
 * @template T
 */
interface ChannelEntityRepositoryFactoryInterface
{
    /**
     * @return RepositoryInterface<T>
     */
    public function create(
        Channel $channel,
        array $metadata = []
    ): RepositoryInterface;
}
