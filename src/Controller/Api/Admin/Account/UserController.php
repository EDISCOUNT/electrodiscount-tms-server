<?php

namespace App\Controller\Api\Admin\Account;

use App\Entity\Account\User;
use App\Form\Account\UserType;
use App\Repository\Account\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/admin/account/users')]
class UserController extends AbstractController
{
    public const SERIALIZER_GROUPS = [
        'user:list',
        'user:with_address',
        'user:with_carrier',
        'address:list',
        'carrier:list',
        'product:list'
    ];

    public function __construct(
        private UserRepository $userRepository,
    ) {
    }

    #[Route('', name: 'app_api_admin_account_user_index', methods: ['GET'])]
    public function index(Request  $request): Response
    {

        $page = (int)$request->query->get('page', 1);
        $limit = (int)$request->query->get('limit', 10);

        if ($page < 1) {
            $page = 1;
        }
        if ($limit > 100) {
            $limit = 100;
        }

        $qb = $this->userRepository->createQueryBuilder('user');
        $adapter = new QueryAdapter($qb);
        $pagination = new Pagerfanta($adapter);

        $pagination->setMaxPerPage($limit);
        $pagination->setCurrentPage($page);

        return $this->json($pagination, context: [
            'groups' => [
                'user:list',
                ...self::SERIALIZER_GROUPS
            ],
        ]);
    }

    #[Route('', name: 'app_api_admin_account_user_new', methods: ['POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user, ['csrf_protection' => false]);
        $data = json_decode($request->getContent(), true);
        $form->submit($data, false);

        if ($form->isValid()) {

            $entityManager->persist($user);
            $entityManager->flush();

            return $this->json($user, Response::HTTP_CREATED, context: [
                'groups' => [
                    'user:read',
                    ...self::SERIALIZER_GROUPS
                ],
            ]);
        }

        return $this->json(['errors' => $this->getFormErrors($form)], Response::HTTP_BAD_REQUEST);
    }

    #[Route('/{id}', name: 'app_api_admin_account_user_show', methods: ['GET'])]
    public function show(User $user): Response
    {
        return $this->json($user, context: [
            'groups' => [
                'user:read',
                'user:with_carrier',
                'carrier:list',
                'user:with_fulfilment',
                'user_fulfilment:list',
                'additional_service:list',
                ...self::SERIALIZER_GROUPS
            ],
        ]);
    }

    #[Route('/{id}', name: 'app_api_admin_account_user_update', methods: ['PATCH'])]
    public function update(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(UserType::class, $user, ['csrf_protection' => false]);

        $data = json_decode($request->getContent(), true);
        $form->submit($data, false);

        if ($form->isValid()) {

            $entityManager->flush();

            return $this->json($user, context: [
                'groups' => [
                    'user:read',
                    ...self::SERIALIZER_GROUPS
                ],
            ]);
        }

        return $this->json(['errors' => $this->getFormErrors($form)], Response::HTTP_BAD_REQUEST);
    }

    #[Route('/{id}', name: 'app_api_admin_account_user_delete', methods: ['DELETE'])]
    public function delete(User $user, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($user);
        $entityManager->flush();

        return $this->json(null, Response::HTTP_NO_CONTENT);
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
