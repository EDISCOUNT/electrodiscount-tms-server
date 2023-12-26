<?php

namespace App\Controller\Api\Admin\Order;

use App\Entity\Order\AdditionalService;
use App\Form\Order\AdditionalServiceType;
use App\Repository\Order\AdditionalServiceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/admin/order/additional_services')]
class AdditionalServiceController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private AdditionalServiceRepository $shipmentRepository
    ) {
    }

    #[Route('', name: 'app_api_admin_order_additional_service_index', methods: ['GET'])]
    public function index(Request $request): Response
    {


        $page = (int)$request->query->get('page', 1);
        $limit = (int)$request->query->get('limit', 10);

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


        return $this->json($pagination);
    }

    #[Route('', name: 'app_api_admin_order_additional_service_new', methods: ['POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $additionalService = new AdditionalService();
        $form = $this->createForm(AdditionalServiceType::class, $additionalService, [
            'csrf_protection' => false,

        ]);
        $form->submit(json_decode($request->getContent(), true));

        if ($form->isValid()) {
            $entityManager->persist($additionalService);
            $entityManager->flush();

            return $this->json($additionalService, Response::HTTP_CREATED);
        }

        return $this->json(['errors' => $this->getFormErrors($form)], Response::HTTP_BAD_REQUEST);
    }

    #[Route('/{id}', name: 'app_api_admin_order_additional_service_show', methods: ['GET'])]
    public function show(AdditionalService $additionalService): Response
    {
        return $this->json($additionalService);
    }

    #[Route('/{id}', name: 'app_api_admin_order_additional_service_update', methods: ['PATCH'])]
    public function update(Request $request, AdditionalService $additionalService, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(AdditionalServiceType::class, $additionalService, [
            'csrf_protection' => false,
        ]);
        $form->submit(json_decode($request->getContent(), true), false);

        if ($form->isValid()) {
            $entityManager->flush();

            return $this->json($additionalService);
        }

        return $this->json(['errors' => $this->getFormErrors($form)], Response::HTTP_BAD_REQUEST);
    }

    #[Route('/{id}', name: 'app_api_admin_order_additional_service_delete', methods: ['DELETE'])]
    public function delete(AdditionalService $additionalService, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($additionalService);
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
