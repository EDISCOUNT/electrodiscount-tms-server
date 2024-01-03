<?php

namespace App\Controller\Api\Admin\Shipment;

use App\Entity\Shipment\Shipment;
use App\Form\Shipment\ShipmentPacklistRequestType;
use App\Form\Shipment\ShipmentTransitionType;
use App\Form\Shipment\ShipmentType;
use App\Repository\Shipment\ShipmentRepository;
use App\Service\Shipment\ShipmentEventLogger;
use App\Service\Shipment\ShipmentNotificationManager;
use App\Service\Util\CodeGeneratorInterface;
use App\Util\Doctrine\QueryBuilderHelper;
use CoopTilleuls\UrlSignerBundle\UrlSigner\UrlSignerInterface;
use DateInterval;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use LogicException;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Target;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Workflow\WorkflowInterface;

#[Route('/api/admin/shipment/shipments')]
class ShipmentController extends AbstractController
{
    public const SERIALIZER_GROUPS = [
        'shipment:list',
        'shipment:with_items',
        'shipment:with_address',
        'shipment:with_carrier',
        'shipment:with_channel',

        'shipment:with_fulfilment',
        'shipment_item:with_fulfilment',
        'shipment_fulfilment:list',

        'address:list',
        'carrier:list',
        'shipment_item:read',
        'shipment_item:with_product',
        'product:list'
    ];



    public function __construct(
        private EntityManagerInterface $entityManager,
        private ShipmentRepository $shipmentRepository,
        private CodeGeneratorInterface $codeGenerator,
        private ShipmentEventLogger $logger,
        private ShipmentNotificationManager $notifier,
        #[Target('shipment_operation')]
        private WorkflowInterface $workflow,
        private UrlSignerInterface $urlSigner,
    ) {
    }

    #[Route('', name: 'app_api_admin_shipment_shipment_index', methods: ['GET'])]
    public function index(Request  $request): Response
    {
        $page = (int)$request->query->get('page', 1);
        $limit = (int)$request->query->get('limit', 10);
        $statuses = $request->query->get('status',);
        $filter = $request->query->get('filter',);
        if (($statuses != null) && !is_array($statuses)) {
            $statuses = [$statuses];
        }

        if ($page < 1) {
            $page = 1;
        }
        if ($limit > 100) {
            $limit = 100;
        }

        $qb = $this->shipmentRepository->createQueryBuilder('shipment');
        if ($statuses) {
            $qb->andWhere($qb->expr()->in('shipment.status', $statuses))
                // ->setParameter('statuses', $statuses)
            ;
        }

        if ($filter) {
            QueryBuilderHelper::applyCriteria($qb, $filter, 'shipment');
        }

        $adapter = new QueryAdapter($qb);
        $pagination = new Pagerfanta($adapter);

        $pagination->setMaxPerPage($limit);
        $pagination->setCurrentPage($page);

        return $this->json($pagination, context: [
            'groups' => [
                'shipment:list',
                ...self::SERIALIZER_GROUPS
            ],
        ]);
    }

    #[Route('', name: 'app_api_admin_shipment_shipment_new', methods: ['POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $shipment = new Shipment();
        $form = $this->createForm(ShipmentType::class, $shipment, ['csrf_protection' => false]);
        $data = json_decode($request->getContent(), true);
        $form->submit($data, false);

        if ($form->isValid()) {

            if (!($code = $shipment->getCode())) {
                $code = $this->codeGenerator->generateCode(6);
                $shipment->setCode($code);
            }

            $this->logger->logCreated($shipment);

            if ($carrier = $shipment->getCarrier()) {
                $this->workflow->apply($shipment, Shipment::STATUS_ASSIGNED);
                $this->logger->logAssigned($shipment, carrier: $carrier);
                $this->notifier->notifyCarrierOfAssignment($shipment);
            }

            $entityManager->persist($shipment);
            $entityManager->flush();

            return $this->json($shipment, Response::HTTP_CREATED, context: [
                'groups' => [
                    'shipment:read',
                    ...self::SERIALIZER_GROUPS
                ],
            ]);
        }

        return $this->json(['errors' => $this->getFormErrors($form)], Response::HTTP_BAD_REQUEST);
    }

    #[Route('/{shipment}', name: 'app_api_admin_shipment_shipment_show', methods: ['GET'])]
    public function show(Shipment $shipment): Response
    {
        return $this->json($shipment, context: [
            'groups' => [
                'shipment:read',
                'shipment:with_carrier',
                'carrier:list',
                'shipment:with_additional_services',
                'additional_service:list',
                ...self::SERIALIZER_GROUPS
            ],
        ]);
    }

    #[Route('/{shipment}', name: 'app_api_admin_shipment_shipment_update', methods: ['PATCH'])]
    public function update(Request $request, Shipment $shipment, EntityManagerInterface $entityManager): Response
    {

        $carrier = $shipment->getCarrier();
        $form = $this->createForm(ShipmentType::class, $shipment, ['csrf_protection' => false]);

        $data = json_decode($request->getContent(), true);
        $form->submit($data, false);

        if ($form->isValid()) {

            if (($carrier == null)  && ($carrier = $shipment->getCarrier())) {
                $this->workflow->apply($shipment, Shipment::STATUS_ASSIGNED);
                $this->logger->logAssigned($shipment, carrier: $carrier);
                $this->notifier->notifyCarrierOfAssignment($shipment);
            }


            $this->logger->logUpdated($shipment, $data);
            $entityManager->flush();

            return $this->json($shipment, context: [
                'groups' => [
                    'shipment:read',
                    ...self::SERIALIZER_GROUPS
                ],
            ]);
        }

        return $this->json(['errors' => $this->getFormErrors($form)], Response::HTTP_BAD_REQUEST);
    }

    #[Route('/{shipment}', name: 'app_api_admin_shipment_shipment_delete', methods: ['DELETE'])]
    public function delete(Shipment $shipment, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($shipment);
        $entityManager->flush();

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }


    #[Route('/{shipment}/apply-transition', name: 'app_api_admin_shipment_shipment_apply_transition', methods: ['POST'])]
    public function updateStatus(Shipment $shipment, Request $request): Response
    {
        try {
            $form = $this->createForm(ShipmentTransitionType::class, $shipment, ['csrf_protection' => false]);

            $data = json_decode($request->getContent(), true);
            $form->submit($data, false);

            if ($form->isValid()) {

                $transition = $form->get('transition')->getData();

                $this->workflow->apply($shipment, $transition);

                $this->entityManager->persist($shipment);
                $this->entityManager->flush();

                return $this->json($shipment, context: [
                    'groups' => [
                        'shipment:read',
                        ...self::SERIALIZER_GROUPS
                    ],
                ]);
            }
            return $this->json(['errors' => $this->getFormErrors($form)], Response::HTTP_BAD_REQUEST);
        } catch (LogicException $e) {
            return $this->json([
                'message' => $e->getMessage(),
                'status' => 'error',
            ], Response::HTTP_BAD_REQUEST);
        } catch (\Throwable $e) {
            return $this->json([
                'message' => $e->getMessage(),
                'status' => 'error',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    #[Route('/operation/generate-packlist', name: 'app_api_admin_shipment_shipment_generate_packlist', methods: ['POST'])]
    public function generatePacklist(Request $request): Response
    {
        $form = $this->createForm(ShipmentPacklistRequestType::class, null, ['csrf_protection' => false]);
        $data = json_decode($request->getContent(), true);
        $form->submit($data, false);

        if ($form->isValid()) {
            $shipments = $form->get('shipments')->getData();

            $shipmentIds = array_map(fn (Shipment $shipment) => $shipment->getId(), $shipments->toArray());
            $idJson = json_encode($shipmentIds);
            $idEncoded = base64_encode($idJson);

            $url = $this->generateSignedUrl(code: $idEncoded);

            return $this->json([
                'url' => $url,
            ]);
        }
        return $this->json(['errors' => $this->getFormErrors($form)], Response::HTTP_BAD_REQUEST);
    }



    private function generateSignedUrl(string $code): string
    {
        $url = $this->generateUrl(
            'app_shipment_shipment_packlist_request',
            ['code' => $code],
            referenceType: UrlGeneratorInterface::ABSOLUTE_URL,
        );
        // Will expire after 10 seconds.
        $expiration = (new DateTime('now'))->add(new DateInterval('PT60S'));
        return $this->urlSigner->sign($url, $expiration);
    }


    private function getFormErrors($form): array
    {
        $errors = [];
        foreach ($form->getErrors(true) as $error) {
            $path = $error->getOrigin()?->getName() ?? 'form';
            // $errors[$path] = $error->getMessage();
            $errors[] = $error->getMessage();
            $message = sprintf(
                '%s: %s',
                $path,
                $error->getMessage()
            );
            $errors[] = $message;
        }
        return $errors;
    }
}
