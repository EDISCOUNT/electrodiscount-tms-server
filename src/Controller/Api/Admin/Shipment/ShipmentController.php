<?php

namespace App\Controller\Api\Admin\Shipment;

use App\Entity\Shipment\Shipment;
use App\Form\Shipment\ShipmentType;
use App\Repository\Shipment\ShipmentRepository;
use App\Service\Shipment\ShipmentEventLogger;
use App\Service\Util\CodeGeneratorInterface;
use Doctrine\ORM\EntityManagerInterface;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/admin/shipment/shipments')]
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
    ) {
    }

    #[Route('', name: 'app_api_admin_shipment_shipment_index', methods: ['GET'])]
    public function index(Request  $request): Response
    {

        $page = $request->query->get('page', 1);
        $limit = $request->query->get('limit', 10);

        if ($page < 1) {
            $page = 1;
        }
        if ($limit > 100) {
            $limit = 100;
        }

        $qb = $this->shipmentRepository->createQueryBuilder('shipment');
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

            if(!($code = $shipment->getCode())){
                $code = $this->codeGenerator->generateCode(6);
                $shipment->setCode($code);
            }

            $this->logger->logCreated($shipment);

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

    #[Route('/{id}', name: 'app_api_admin_shipment_shipment_show', methods: ['GET'])]
    public function show(Shipment $shipment): Response
    {
        return $this->json($shipment, context: [
            'groups' => [
                'shipment:read',
                ...self::SERIALIZER_GROUPS
            ],
        ]);
    }

    #[Route('/{id}', name: 'app_api_admin_shipment_shipment_update', methods: ['PATCH'])]
    public function update(Request $request, Shipment $shipment, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ShipmentType::class, $shipment, ['csrf_protection' => false]);

        $data = json_decode($request->getContent(), true);
        $form->submit($data, false);

        if ($form->isValid()) {
            
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

    #[Route('/{id}', name: 'app_api_admin_shipment_shipment_delete', methods: ['DELETE'])]
    public function delete(Shipment $shipment, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($shipment);
        $entityManager->flush();

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }

    private function getFormErrors($form): array
    {
        $errors = [];
        foreach ($form->getErrors(true) as $error) {
            $errors[] = $error->getMessage();
        }
        return $errors;
    }
}
