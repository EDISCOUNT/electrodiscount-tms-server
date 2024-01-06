<?php

namespace App\Sourcing;

use App\Entity\Channel\Channel;
use App\Entity\Order\Order;
use App\Entity\Shipment\Shipment;
use App\Repository\Shipment\ShipmentRepository;
use App\Sourcing\Channel\ChannelSourceManager;
use App\Sourcing\Exception\DouplicateOrderShipmentImportation;
use Doctrine\ORM\EntityManagerInterface;
use Pagerfanta\Pagerfanta;

class ShipmentSourceManager
{

    /**
     * @var array<string,array>
     */
    private array $sources = [];

    /**
     * @var array<string,ChannelSourceManager>
     */
    private array $shipmentSourceManagers = [];

    public function __construct(
        private ShipmentRepository $shipmentRepository,
        private EntityManagerInterface $entityManager,
        iterable $sources = [],
    ) {
        $this->sources = [];
        foreach ($sources as $code => $config) {
            $this->addShipmentSource($code, $config);
        }
    }


    public function paginateBySource(
        string $sourceName,
        Channel $channel,
        int $page = 1,
        int $limit = 10,
        array $criteria = [],
        array $orderBy = []
    ): Pagerfanta {
        $source = $this->getSourceManager($sourceName);
        $repository = $source->getOrderRepository($channel);
        return $repository->paginate($page, $limit, $criteria, $orderBy);
    }


    public function importShipmentForOrder(Order $order, bool $commit = false, bool $save = false): Shipment
    {
        $channel = $order->getChannel();

        $existing = $this->shipmentRepository->findOneBy([
            'channel' => $channel,
            'channelOrderId' => $order->getChannelOrderId(),
        ]);

        if($existing){
            // $message = sprintf("Order [%s] from channel \"%s\" is already imported as shipment.", $order->getChannelOrderId(), $channel->getName());
            throw new DouplicateOrderShipmentImportation(
                channel: $channel,
                shipment: $existing,
                channelOrderId: $order->getChannelOrderId(),
            );
        }

        $source = $this->getSourceManager($channel);
        $shipment = $source->mapOrderToShipment($order);

        if ($commit) {
            try {
                $source->commitShipment($shipment, $order);
                if ($save) {
                    $this->entityManager->persist($shipment);
                    $this->entityManager->flush();
                }
            } catch (\Throwable $e) {
                throw $e;
            }
        }
        return $shipment;
    }


    public function commitShipment(Shipment $shipment, Order $order): mixed
    {
        $source = $this->getSourceManager($order->getChannel());
        $source->commitShipment($shipment, $order);
        return null;
    }

    public function getSources(): array
    {
        return $this->sources;
    }

    public function getSourcesArray(): array
    {
        $sources = [];
        foreach ($this->sources as $code => $config) {
            $sources[] = $config;
        }
        return $sources;
    }


    public function getSourceManager(string| Channel $channel): ChannelSourceManager
    {
        if ($channel instanceof Channel) {
            $channel = $channel->getType();
        }
        $shipmentSource = $channel;
        if (!isset($this->shipmentSourceManagers[$shipmentSource])) {
            throw new \InvalidArgumentException(sprintf('Shipment source "%s" is not supported.', $shipmentSource));
        }

        return $this->shipmentSourceManagers[$shipmentSource];
    }


    /**
     * @param string|int $code
     * @param ChannelSourceManager $manager
     */
    public function addShipmentSource(string|int|null $code, ChannelSourceManager $manager)
    {
        if (isset($this->sources[$code])) {
            throw new \InvalidArgumentException(sprintf('Shipment source "%s" already defined.', $code));
        }
        $config = $manager->getConfig();
        $code = $config['code'] ?? $config['id'] ?? $code; ///$config['code'
        $this->sources[$code] = $config;
        $this->shipmentSourceManagers[$code] = $manager; ///$config['manager'];
    }
}
