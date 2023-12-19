<?php

namespace App\Controller\Api\Admin\Shipment;

use App\Entity\Shipment\Shipment;
use App\Form\Shipment\ShipmentType;
use App\Repository\Shipment\ShipmentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/admin/shipment/shipments')]
class ShipmentController extends AbstractController
{
    #[Route('', name: 'app_api_admin_shipment_shipment_index', methods: ['GET'])]
    public function index(ShipmentRepository $shipmentRepository): Response
    {
        $shipments = $shipmentRepository->findAll();

        return $this->json($shipments);
    }

    #[Route('', name: 'app_api_admin_shipment_shipment_new', methods: ['POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $shipment = new Shipment();
        $form = $this->createForm(ShipmentType::class, $shipment, ['csrf_protection' => false]);
        $data = json_decode($request->getContent(), true);
        $form->submit($data, false);

        if ($form->isValid()) {
            $entityManager->persist($shipment);
            $entityManager->flush();

            return $this->json($shipment, Response::HTTP_CREATED);
        }

        return $this->json(['errors' => $this->getFormErrors($form)], Response::HTTP_BAD_REQUEST);
    }

    #[Route('/{id}', name: 'app_api_admin_shipment_shipment_show', methods: ['GET'])]
    public function show(Shipment $shipment): Response
    {
        return $this->json($shipment);
    }

    #[Route('/{id}', name: 'app_api_admin_shipment_shipment_update', methods: ['PATCH'])]
    public function update(Request $request, Shipment $shipment, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ShipmentType::class, $shipment, ['csrf_protection' => false]);
       
        $data = json_decode($request->getContent(), true);
        $form->submit($data, false);
        
        if ($form->isValid()) {
            $entityManager->flush();

            return $this->json($shipment);
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
