<?php

namespace App\Controller\Api\Admin\Catalog;

use App\Entity\Catalog\Product;
use App\Form\Catalog\ProductType;
use App\Repository\Catalog\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/admin/catalog/products')]
class ProductController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private ProductRepository $productRepository,
    ) {
    }

    #[Route('/', name: 'app_api_admin_catalog_product_index', methods: ['GET'])]
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

        $qb = $this->productRepository->createQueryBuilder('product');
        $adapter = new QueryAdapter($qb);
        $pagination = new Pagerfanta($adapter);

        $pagination->setMaxPerPage($limit);
        $pagination->setCurrentPage($page);

        return $this->json($pagination, context: [
            'groups' => [
                'product:list'
            ]
        ]);
    }

    #[Route('', name: 'app_api_admin_catalog_product_new', methods: ['POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product, ['csrf_protection' => false]);

        $data = json_decode($request->getContent(), true);
        $form->submit($data, false);

        if ($form->isValid()) {
            $entityManager->persist($product);
            $entityManager->flush();

            return $this->json($product, Response::HTTP_CREATED);
        }

        return $this->json(['errors' => $this->getFormErrors($form)], Response::HTTP_BAD_REQUEST);
    }

    #[Route('/{id}', name: 'app_api_admin_catalog_product_show', methods: ['GET'])]
    public function show(Product $product): Response
    {
        return $this->json($product);
    }

    #[Route('/{id}', name: 'app_api_admin_catalog_product_update', methods: ['PATCH'])]
    public function update(Request $request, Product $product, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ProductType::class, $product, ['csrf_protection' => false]);

        $data = json_decode($request->getContent(), true);
        $form->submit($data, false);

        if ($form->isValid()) {
            $entityManager->flush();

            return $this->json($product);
        }

        return $this->json(['errors' => $this->getFormErrors($form)], Response::HTTP_BAD_REQUEST);
    }

    #[Route('/{id}', name: 'app_api_admin_catalog_product_delete', methods: ['DELETE'])]
    public function delete(Product $product, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($product);
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
