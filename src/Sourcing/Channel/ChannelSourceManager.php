<?php

namespace App\Sourcing\Channel;

use App\Entity\Channel\Channel;
use App\Sourcing\Factory\ChannelEntityRepositoryFactoryInterface;
use App\Sourcing\Repository\RepositoryInterface;

class ChannelSourceManager
{

    public function __construct(
        private ChannelEntityRepositoryFactoryInterface $orderRepositoryFactory,
        private ChannelEntityRepositoryFactoryInterface $productRepositoryFactory,
        private array $config = [],

    ) {
    }


    public function getOrderRepository(Channel $channel): RepositoryInterface
    {
        return $this->orderRepositoryFactory->create($channel);
    }

    public function getProductRepository(Channel $channel): RepositoryInterface
    {
        return $this->productRepositoryFactory->create($channel);
    }


    public function getConfig(): array
    {
        return $this->config;
    }
    
}
