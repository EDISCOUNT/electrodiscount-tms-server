<?php

namespace App\Controller\Api\Carrier\Shipment;

use App\Entity\Account\User;
use App\Entity\Carrier\Carrier;
use App\Entity\Shipment\Shipment;
use App\Form\Shipment\BulkUpdateShipmentStatusType;
use App\Form\Shipment\ShipmentPacklistRequestType;
use App\Form\Shipment\ShipmentTransitionType;
use App\Repository\Shipment\ShipmentRepository;
use App\Service\File\UploaderInterface;
use App\Service\Shipment\ShipmentEventLogger;
use App\Service\Util\CodeGeneratorInterface;
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
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Workflow\WorkflowInterface;
use App\Util\Doctrine\QueryBuilderHelper;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use App\Entity\Shipment\ShipmentAttachment;
use App\Form\Shipment\ShipmentExportRequestType;

#[Route('/api/carrier/shipment/shipments', name: 'app_api_carrier_shipment_shipment')]
class ShipmentController extends AbstractController
{

    public const SERIALIZER_GROUPS = [
        'shipment:list',
        'shipment:with_items',
        'shipment:with_address',
        'shipment:with_carrier',
        'shipment:with_channel',
        'address:list',
        'carrier:list',
        'shipment_item:read',
        'shipment_item:with_product',
        // 
        'shipment:with_fulfilment',
        'shipment_item:with_fulfilment',
        'shipment_fulfilment:list',
        // 
        'product:list'
    ];

    public function __construct(
        private EntityManagerInterface $entityManager,
        private ShipmentRepository $shipmentRepository,
        private CodeGeneratorInterface $codeGenerator,
        private ShipmentEventLogger $logger,
        #[Target('shipment_operation')]
        private WorkflowInterface $workflow,
        private UrlSignerInterface $urlSigner,
        //

        // #[Target('default_filesystem')]
        // private FilesystemOperator $filesystem,
        private UploaderInterface $uploader,
        private FormFactoryInterface $formFactory,
    ) {
    }


    #[Route('', name: 'app_api_carrier_shipment_shipment_index')]
    public function index(Request  $request): Response
    {
        try {

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



            $carrier = $this->getCarrier();

            $qb = $this->shipmentRepository->createQueryBuilder('shipment');

            $qb
                ->addOrderBy(
                    "
                CASE 
                    WHEN shipment.status = 'new'  THEN 0 
                    WHEN shipment.status = 'assigned'  THEN 1 
                    WHEN shipment.status = 'intransit' THEN 2 
                    WHEN shipment.status = 'delivered' THEN 3 
                ELSE 9999 
                END
                ",
                    'ASC'
                )
                // ->setParameter('priority1', 'assigned')
                // ->setParameter('priority2', 'intransit')
                // ->setParameter('priority3', 'delivered')
                ;

            $qb
                ->innerJoin('shipment.carrier', 'carrier')
                ->andWhere('carrier.id = :carrier')
                ->setParameter('carrier', $carrier);

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
        } catch (AccessDeniedHttpException $e) {
            return $this->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 403);
        } catch (\Throwable $e) {
            return $this->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }


    #[Route('/{shipment}', name: 'app_api_carrier_shipment_shipment_show', methods: ['GET'])]
    public function show(Shipment $shipment): Response
    {
        return $this->json($shipment, context: [
            'groups' => [
                'shipment:read',
                'shipment:with_carrier',
                'carrier:list',
                'shipment:with_fulfilment',
                'shipment_fulfilment:list',
                'additional_service:list',
                ...self::SERIALIZER_GROUPS
            ],
        ]);
    }


    #[Route('/{shipment}/apply-transition', name: 'app_api_carrier_shipment_shipment_apply_transition', methods: ['POST'])]
    public function updateStatus(Shipment $shipment, Request $request): Response
    {
        try {
            // $form = $this->createForm(ShipmentTransitionType::class, $shipment, ['csrf_protection' => false]);
            $form = $this->formFactory->createNamed('', ShipmentTransitionType::class, $shipment, ['csrf_protection' => false]);

            // // $data = json_decode($request->getContent(), true);
            // $files =  $request->files->all();
            // $postfields = $request->request->all();
            // $data = [
            //     ...$postfields,
            //     ...$files,
            // ];

            // $form->submit($data, false);

            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {

                $transition = $form->get('transition')->getData();
                $description = $form->get('description')->getData();
                $attachmentsInput = $form->get('attachments');


                /**
                 * @var ShipmentAttachment[]
                 * 
                 * */
                $attachments = [];
                // pfoe
                // return new Response();
                $path = sprintf('shipment/%s/documents/attachments', $shipment->getId());

                foreach ($attachmentsInput as $aInput) {
                    /** @var UploadedFile */
                    $file = $aInput->get('file')->getData();
                    $reference = $this->uploader->upload(
                        $file,
                        path: $path,
                    );

                    /** @var ShipmentAttachment */
                    $attachment = $aInput->getData();
                    $attachment->setReference($reference);
                    $attachments[] = $attachment;
                    $shipment->addAttachment($attachment);
                }


                $this->workflow->apply($shipment, $transition);

                if (true) {
                    $this->logger->logTransition(
                        $shipment,
                        $transition,
                        attachments: $attachments,
                        subtitle: $description,
                    );
                }

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


    #[Route('/apply-transition', name: 'app_api_carrier_shipment_shipment_bulk_apply_transition', methods: ['POST'])]
    public function bulkUpdateStatus(Request $request): Response
    {
        try {
            $form = $this->createForm(BulkUpdateShipmentStatusType::class, options: ['csrf_protection' => false]);

            // $data = json_decode($request->getContent(), true);
            $data = $request->request->all();
            $form->submit($data, false);

            if ($form->isValid()) {

                /** @var Shipment[] */
                $updated_shipments = [];

                $transition = $form->get('transition')->getData();
                /** @var Shipment[] */
                $shipments = $form->get('shipments')->getData();
                foreach ($shipments as $shipment) {

                    if ($this->workflow->can($shipment, $transition)) {
                        $this->workflow->apply($shipment, $transition);
                        $this->logger->logTransition(
                            $shipment,
                            $transition,
                            // attachments: $data['attachments'] ?? []
                        );
                        $this->entityManager->persist($shipment);
                        $updated_shipments[] = $shipment;
                    }
                }
                $this->entityManager->flush();
                $updated_shipment_ids = array_map(fn (Shipment $shipment) => $shipment->getId(), $updated_shipments);
                return $this->json(
                    $updated_shipment_ids,
                    context: [
                        'groups' => [
                            'shipment:read',
                            ...self::SERIALIZER_GROUPS
                        ],
                    ]
                );
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




    #[Route('/operation/generate-packlist', name: 'app_api_carrier_shipment_shipment_generate_packlist', methods: ['POST'])]
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

            $url = $this->generateSignedUrl(route: 'app_shipment_shipment_packlist_request', params: ['code' => $idEncoded]);

            return $this->json([
                'url' => $url,
            ]);
        }
        return $this->json(['errors' => $this->getFormErrors($form)], Response::HTTP_BAD_REQUEST);
    }


    
    #[Route('/operation/export', name: 'app_api_carrier_shipment_shipment_export', methods: ['POST'])]
    public function exportExcel(Request $request): Response
    {
        $form = $this->createForm(ShipmentExportRequestType::class, null, ['csrf_protection' => false]);
        $data = json_decode($request->getContent(), true);
        $form->submit($data, false);

        if ($form->isValid()) {
            $shipments = $form->get('shipments')->getData();

            $shipmentIds = array_map(fn (Shipment $shipment) => $shipment->getId(), $shipments->toArray());
            $idJson = json_encode($shipmentIds);
            $idEncoded = base64_encode($idJson);

            $url = $this->generateSignedUrl(route: 'app_shipment_shipment_export_request', params: ['code' => $idEncoded]);

            return $this->json([
                'url' => $url,
            ]);
        }
        return $this->json(['errors' => $this->getFormErrors($form)], Response::HTTP_BAD_REQUEST);
    }

    
    private function generateSignedUrl(string $route, array $params = [], string $duration = 'PT60S'): string
    {
        $url = $this->generateUrl(
            $route,
            $params,
            referenceType: UrlGeneratorInterface::ABSOLUTE_URL,
        );
        // Will expire after 10 seconds.
        $expiration = (new DateTime('now'))->add(new DateInterval($duration));
        return $this->urlSigner->sign($url, $expiration);
    }



    private function getCarrier(): Carrier
    {
        /**
         * @var User| null
         */
        $user = $this->getUser();
        if (null == $user) {
            throw $this->createAccessDeniedException();
        }
        $carrier = $user->getCarrier();
        if (null == $carrier) {
            throw new   AccessDeniedHttpException("You are not a carrier!");
        }
        return $carrier;
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
