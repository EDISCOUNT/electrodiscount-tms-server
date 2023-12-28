<?php

namespace App\Controller\Api\Carrier\Shipment;

use App\Entity\Account\User;
use App\Entity\Carrier\Carrier;
use App\Entity\Shipment\Shipment;
use App\Form\Shipment\ShipmentTransitionType;
use App\Repository\Shipment\ShipmentRepository;
use App\Service\Shipment\ShipmentEventLogger;
use App\Service\Util\CodeGeneratorInterface;
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
use Symfony\Component\Workflow\WorkflowInterface;

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
        'product:list'
    ];

    public function __construct(
        private EntityManagerInterface $entityManager,
        private ShipmentRepository $shipmentRepository,
        private CodeGeneratorInterface $codeGenerator,
        private ShipmentEventLogger $logger,
        #[Target('shipment_operation')]
        private WorkflowInterface $workflow,
    ) {
    }


    #[Route('', name: 'app_api_carrier_shipment_shipment_index')]
    public function index(Request  $request): Response
    {
        try {

            $page = $request->query->get('page', 1);
            $limit = $request->query->get('limit', 10);

            if ($page < 1) {
                $page = 1;
            }
            if ($limit > 100) {
                $limit = 100;
            }

            $carrier = $this->getCarrier();
            $qb = $this->shipmentRepository->createQueryBuilder('shipment');
            $qb
                ->innerJoin('shipment.carrier', 'carrier')
                ->andWhere('carrier.id = :carrier')
                ->setParameter('carrier', $carrier);

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
