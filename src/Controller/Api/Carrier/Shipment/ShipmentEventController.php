<?php

namespace App\Controller\Api\Carrier\Shipment;

use App\Entity\Shipment\Shipment;
use App\Entity\Shipment\ShipmentEvent;
use App\Repository\Shipment\ShipmentEventRepository;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ShipmentEventController extends AbstractController
{

    public function __construct(
        private ShipmentEventRepository $shipmentEventRepository,
    ) {
    }

    #[Route('/api/carrier/shipment/shipments/{shipment}/events', name: 'app_api_carrier_shipment_shipment_event_index')]
    public function index(Request  $request, Shipment $shipment): Response
    {
        $page = (int)$request->query->get('page', 1);
        $limit = (int)$request->query->get('limit', 10);

        if ($page < 1) {
            $page = 1;
        }
        if ($limit > 100) {
            $limit = 100;
        }

        $qb = $this->shipmentEventRepository->createQueryBuilder('event');

        $qb
            // ->innerJoin('event.shipment','shipment')
            ->innerJoin(Shipment::class, 'shipment', 'WITH', 'event MEMBER OF shipment.events')
            ->andWhere('shipment.id = :shipment')
            ->setParameter('shipment', $shipment->getId());

        $adapter = new QueryAdapter($qb);
        $pagination = new Pagerfanta($adapter);

        $pagination->setMaxPerPage($limit);
        $pagination->setCurrentPage($page);

        return $this->json($pagination, context: [
            'groups' => [
                'shipment_event:list',
                'shipment_event:with_attachments',
                'shipment_attachment:list',
            ],
        ]);
    }

    #[Route('/api/carrier/shipment/shipments/{shipment}/events/{event}', name: 'app_api_carrier_shipment_shipment_event_show')]
    public function show(Shipment $shipment, ShipmentEvent $event): Response
    {
        // if($event->getShipment() != $shipment){

        // }
        return $this->json($shipment, context: [
            'groups' => [
                'shipment_event:read',
                'shipment_event:with_attachments',
                'shipment_attachment:list',
            ],
        ]);
    }
}
