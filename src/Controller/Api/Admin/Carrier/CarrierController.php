<?php

namespace App\Controller\Api\Admin\Carrier;

use App\Entity\Carrier\Carrier;
use App\Form\Carrier\CarrierLogoImageType;
use App\Form\Carrier\CarrierType;
use App\Repository\Carrier\CarrierRepository;
use App\Service\File\UploaderInterface;
use Doctrine\ORM\EntityManagerInterface;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\UploadedFile;

#[Route('/api/admin/carrier/carriers')]
class CarrierController extends AbstractController
{
    const CARRIER_LOGO_PATH_TEMPLATE = '/uploads/carrier/%s';
    const CARRIER_LOGO_NAME_TEMPLATE = 'logo.%s';

    public function __construct(
        private EntityManagerInterface $entityManager,
        private CarrierRepository $carrierRepository,
        private UploaderInterface $uploader,
        private FormFactoryInterface $formFactory,
    ) {
    }

    #[Route('', name: 'app_api_admin_carrier_carrier_index', methods: ['GET'])]
    public function index(Request $request): Response
    {
        $page = (int) $request->query->get('page', 1);
        $limit = (int) $request->query->get('limit', 10);

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
                'carrier:list',
                'carrier:with_operator',
                'user:list',
            ]
        ]);
    }

    #[Route('', name: 'app_api_admin_carrier_carrier_new', methods: ['POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $carrier = new Carrier();
        // $form = $this->createForm(CarrierType::class, $carrier, ['csrf_protection' => false]);
        $form = $this->formFactory->createNamed('', CarrierType::class, $carrier, ['csrf_protection' => false]);


        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {


            /** @var UploadedFile */
            $logoFile = $form->get('logoImage')->getData();

            if ($logoFile) {
                $logoPath = sprintf(self::CARRIER_LOGO_PATH_TEMPLATE, $carrier->getCode());
                $logoName = sprintf(self::CARRIER_LOGO_NAME_TEMPLATE, $logoFile->guessExtension());
                $path = $this->uploader->upload($logoFile, $logoPath, $logoName);
                $carrier->setLogoImage($path);
            }

            $entityManager->persist($carrier);
            $entityManager->flush();

            return $this->json($carrier, Response::HTTP_CREATED, context: [
                'groups' => [
                    'carrier:list',
                    'carrier:with_operator',
                    'user:list',
                ]
            ]);
        }

        return $this->json(['errors' => $this->getFormErrors($form)], Response::HTTP_BAD_REQUEST);
    }

    #[Route('/{id}', name: 'app_api_admin_carrier_carrier_show', methods: ['GET'])]
    public function show(Carrier $carrier): Response
    {
        return $this->json($carrier, context: [
            'groups' => [
                'carrier:list',
                'carrier:with_operator',
                'user:list',
            ]
        ]);
    }

    #[Route('/{id}', name: 'app_api_admin_carrier_carrier_update', methods: ['PATCH'])]
    public function update(Request $request, Carrier $carrier, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CarrierType::class, $carrier, ['csrf_protection' => false]);
        // $form = $this->formFactory->createNamed('', CarrierType::class, $carrier, ['csrf_protection' => false]);

        $data = json_decode($request->getContent(), true);
        $form->submit($data, false);

        // $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile */
            $logoFile = $form->get('logoImage')->getData();

            if ($logoFile) {
                $logoPath = sprintf(self::CARRIER_LOGO_PATH_TEMPLATE, $carrier->getCode());
                $logoName = sprintf(self::CARRIER_LOGO_NAME_TEMPLATE, $logoFile->guessExtension());
                $path = $this->uploader->upload($logoFile, $logoPath, $logoName);
                $carrier->setLogoImage($path);
            }

            $entityManager->flush();

            return $this->json($carrier, context: [
                'groups' => [
                    'carrier:list',
                    'carrier:with_operator',
                    'user:list',
                ]
            ]);
        }

        return $this->json(['errors' => $this->getFormErrors($form)], Response::HTTP_BAD_REQUEST);
    }


    #[Route('/{id}/logo', name: 'app_api_admin_carrier_carrier_update_logo', methods: ['POST'])]
    public function updateLogo(Request $request, Carrier $carrier, EntityManagerInterface $entityManager): Response
    {
        // $form = $this->createForm(CarrierType::class, $carrier, ['csrf_protection' => false]);
        $form = $this->formFactory->createNamed('', CarrierLogoImageType::class, $carrier, ['csrf_protection' => false]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile */
            $logoFile = $form->get('image')->getData();

            if ($logoFile) {
                $logoPath = sprintf(self::CARRIER_LOGO_PATH_TEMPLATE, $carrier->getCode());
                $logoName = sprintf(self::CARRIER_LOGO_NAME_TEMPLATE, $logoFile->guessExtension());
                $path = $this->uploader->upload($logoFile, $logoPath, $logoName);
                $carrier->setLogoImage($path);
            }

            $entityManager->flush();

            return $this->json($carrier, context: [
                'groups' => [
                    'carrier:list',
                    'carrier:with_operator',
                    'user:list',
                ]
            ]);
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
