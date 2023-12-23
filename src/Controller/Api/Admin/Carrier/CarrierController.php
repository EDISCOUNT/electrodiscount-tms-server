<?php

namespace App\Controller\Api\Admin\Carrier;

use App\Entity\Carrier\Carrier;
use App\Form\Carrier\CarrierType;
use App\Repository\Carrier\CarrierRepository;
use Doctrine\ORM\EntityManagerInterface;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/admin/carrier/carriers')]
class CarrierController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private CarrierRepository $carrierRepository,
    ) {
    }

    #[Route('', name: 'app_api_admin_carrier_carrier_index', methods: ['GET'])]
    public function index(Request $request): Response
    {
        $page = $request->query->get('page', 1);
        $limit = $request->query->get('limit', 10);

        if ($page < 1) {
            $page = 1;
        }
        if ($limit > 100) {
            $limit = 100;
        }

        $qb = $this->carrierRepository->createQueryBuilder('carrier');
        $adapter = new QueryAdapter($qb);
        $pagination = new Pagerfanta($adapter);

        $pagination->setMaxPerPage($limit);
        $pagination->setCurrentPage($page);

        return $this->json($pagination, context: [
            'groups' => [
                'carrier:list'
            ]
        ]);
    }

    #[Route('', name: 'app_api_admin_carrier_carrier_new', methods: ['POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $carrier = new Carrier();
        $form = $this->createForm(CarrierType::class, $carrier, ['csrf_protection' => false]);

        $data = json_decode($request->getContent(), true);
        $form->submit($data, false);

        if ($form->isValid()) {
            $entityManager->persist($carrier);
            $entityManager->flush();

            return $this->json($carrier, Response::HTTP_CREATED);
        }

        return $this->json(['errors' => $this->getFormErrors($form)], Response::HTTP_BAD_REQUEST);
    }

    #[Route('/{id}', name: 'app_api_admin_carrier_carrier_show', methods: ['GET'])]
    public function show(Carrier $carrier): Response
    {
        return $this->json($carrier);
    }

    #[Route('/{id}', name: 'app_api_admin_carrier_carrier_update', methods: ['PATCH'])]
    public function update(Request $request, Carrier $carrier, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CarrierType::class, $carrier, ['csrf_protection' => false]);

        $data = json_decode($request->getContent(), true);
        $form->submit($data, false);

        if ($form->isValid()) {
            $entityManager->flush();

            return $this->json($carrier);
        }

        return $this->json(['errors' => $this->getFormErrors($form)], Response::HTTP_BAD_REQUEST);
    }

    #[Route('/{id}', name: 'app_api_admin_carrier_carrier_delete', methods: ['DELETE'])]
    public function delete(Carrier $carrier, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($carrier);
        $entityManager->flush();

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }

    private function getFormErrors(Form $form): array
    {
        $errors = [];
        foreach ($form->getErrors(true) as $error) {
            $errors[] = sprintf(
                '%s: %s',
                $error->getOrigin()->getName(),
                $error->getMessage()
            );
        }

        return $errors;
    }
}
