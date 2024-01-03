<?php

namespace App\Controller\Api\Admin\Channel;

use App\Entity\Channel\Channel;
use App\Form\Order\BulkImportShipmentOrderType;
use App\Sourcing\ShipmentSourceManager;
use Doctrine\ORM\EntityManagerInterface;
use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\Order\ImportShipmentOrderType;
use App\Entity\Carrier\Carrier;
use App\Entity\Shipment\Shipment;
use App\Entity\Shipment\ShipmentFulfilmentType;
use App\Service\Shipment\ShipmentEventLogger;
use App\Service\Shipment\ShipmentNotificationManager;
use Symfony\Component\DependencyInjection\Attribute\Target;
use Symfony\Component\Workflow\WorkflowInterface;

#[Route('/api/admin/channel/channels/{channel}', name: 'app_api_admin_channel_order')]
class OrderController extends AbstractController
{
    public function __construct(
        private ShipmentSourceManager $shipmentSourceManager,
        private EntityManagerInterface $entityManager,
        private ShipmentEventLogger $logger,
        private ShipmentNotificationManager $notifier,
        #[Target('shipment_operation')]
        private WorkflowInterface $workflow,
    ) {
    }

    #[Route('/orders', name: 'index', methods: ['GET'])]
    public function index(Request $request, Channel $channel): Response
    {


        $page = (int)$request->query->get('page', 1);
        $limit = (int) $request->query->get('limit', 10);
        $status = $request->query->get('status', 'open');
        $filfilmentMethod = $request->query->get('fulfilment-method', 'all');

        if ($page < 1) {
            $page = 1;
        }
        if ($limit > 100) {
            $limit = 100;
        }


        $repository = $this->shipmentSourceManager->getSourceManager($channel)->getOrderRepository($channel);
        $orders = $repository->paginate(
            page: $page,
            limit: $limit,
            criteria: [
                'status' => $status,
                // 'fulfilment-method' => $filfilmentMethod
            ]
        );

        $data = [...$orders->getCurrentPageResults()];
        $adapter = new ArrayAdapter($data);
        $pagination = new Pagerfanta($adapter);

        $pagination->setMaxPerPage($limit);
        $pagination->setCurrentPage($page);


        return $this->json($pagination, context: [
            'groups' => [
                'order:list',
                'order:with_items',
                'order:with_channel',
                'order:with_address',
                'order:with_fulfilment',
                'channel:list',
                'address:list',
                'order_item:read',
                'order_item:with_fulfilment',
                'shipment_fulfilment:list',
                'shipment_fulfilment',
                'product:list'
            ],
        ]);
    }


    #[Route('/orders/{id}', name: 'show', methods: ['GET'])]
    public function show(Request $request, Channel $channel, string $id): Response
    {
        $repository = $this->shipmentSourceManager->getSourceManager($channel)->getOrderRepository($channel);
        $order = $repository->getById($id);

        return $this->json($order, context: [
            'groups' => [
                'order:list',
                'order:with_items',
                'order:with_channel',
                'order:with_address',
                'channel:list',
                'address:list',
                'order_item:read',
                'product:list'
            ],
        ]);
    }


    #[Route('/import-order/{id}', name: 'import_order', methods: ['POST'])]
    public function importShipment(Request $request, Channel $channel, string $id): Response
    {

        $repository = $this->shipmentSourceManager->getSourceManager($channel)->getOrderRepository($channel);


        $order = $repository->getById($id);
        $shipment = $this->shipmentSourceManager->importShipmentForOrder($order, commit: false);

        $form = $this->createForm(ImportShipmentOrderType::class, $shipment, ['csrf_protection' => false]);

        $data = json_decode($request->getContent(), true);
        // if(isset($data['fulfilmentType'])){
        //     $data['fulfilmentType'] = ShipmentFulfilmentType::from($data['fulfilmentType']);
        // }
        $form->submit($data, false);


        if ($form->isValid()) {
            $this->shipmentSourceManager->commitShipment($shipment, $order);

            $this->logger->logImported($shipment, channel: $channel, order: $order);


            // $shipment->setFulfilmentType($fulfilmentType);

            /** @var bool */
            $notify = $form->get('notify')->getData() ?? false;

            if ($carrier = $shipment->getCarrier()) {
                $this->workflow->apply($shipment, Shipment::STATUS_ASSIGNED);
                $this->logger->logAssigned($shipment, carrier: $carrier);
                if ($notify) {
                    $this->notifier->notifyCarrierOfAssignment($shipment);
                }
            }

            $this->entityManager->persist($shipment);
            $this->entityManager->flush();

            return $this->json($shipment, Response::HTTP_CREATED, context: [
                'groups' => [
                    'shipment:list',
                    'shipment:with_items',
                    'shipment:with_address',
                    'shipment:with_carrier',
                    'carrier:list',
                    'address:list',
                    'shipment_item:read',
                    'shipment_item:with_product',
                    'product:list'
                ],
            ]);
        }

        return $this->json(['errors' => $this->getFormErrors($form)], Response::HTTP_BAD_REQUEST);
    }




    #[Route('/bulk-import-order', name: 'bulk_import_order', methods: ['POST'])]
    public function bulkImportShipment(Request $request, Channel $channel,): Response
    {
        $form = $this->createForm(BulkImportShipmentOrderType::class, options: ['csrf_protection' => false]);

        $data = json_decode($request->getContent(), true);
        // if(isset($data['fulfilmentType'])){
        //     $data['fulfilmentType'] = ShipmentFulfilmentType::from($data['fulfilmentType']);
        // }
        $form->submit($data, false);


        if ($form->isValid()) {

            $repository = $this->shipmentSourceManager->getSourceManager($channel)->getOrderRepository($channel);

            /**
             * @var Shipment[]
             */
            $shipments = [];

            $data = $form->getData();
            /**
             * @var Carrier | null
             */
            $carrier = $data['carrier'] ?? null;

            /**
             * @var ShipmentFulfilmentType
             */
            $fulfilmentType = $data['fulfilmentType'] ?? ShipmentFulfilmentType::PICKUP_AND_DELIVER;

            /**
             * @var string[]
             */
            $orderIds = $data['orders'];

            /** @var bool */
            $notify = $data['notify'] ?? false;


            foreach ($orderIds as $orderId) {
                $order = $repository->getById($orderId);
                $shipment = $this->shipmentSourceManager->importShipmentForOrder($order, commit: false);
                $shipment->setCarrier($carrier);
                $shipment->setFulfilmentType($fulfilmentType);
                $this->shipmentSourceManager->commitShipment($shipment, $order);
                $this->logger->logImported($shipment, channel: $channel, order: $order);

                if ($carrier = $shipment->getCarrier()) {
                    $this->workflow->apply($shipment, Shipment::STATUS_ASSIGNED);
                    $this->logger->logAssigned($shipment, carrier: $carrier);
                    if ($notify) {
                        $this->notifier->notifyCarrierOfAssignment($shipment);
                    }
                }

                $this->entityManager->persist($shipment);
            }

            $this->entityManager->flush();
            $shipmentIds = array_map(fn (Shipment $shipment) => $shipment->getId(), $shipments);

            return $this->json([
                'shipments' => $shipmentIds,
            ], Response::HTTP_CREATED, context: [
                'groups' => [
                    'shipment:list',
                    'shipment:with_items',
                    'shipment:with_address',
                    'shipment:with_carrier',
                    'carrier:list',
                    'address:list',
                    'shipment_item:read',
                    'shipment_item:with_product',
                    'product:list'
                ],
            ]);
        }

        return $this->json(['errors' => $this->getFormErrors($form)], Response::HTTP_BAD_REQUEST);
    }


    private function getFormErrors(Form $form): array
    {
        $errors = [];
        foreach ($form->getErrors(true) as $error) {
            $errors[$error->getOrigin()->getName()] = $error->getMessage();
        }
        return $errors;
    }
}
