<?php

namespace App\Controller\Api;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AuthenticationController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        // private Security $security,
    ) {
    }

    #[Route('/api/me', name: 'app_api_current_user', methods: ['GET'])]
    public function index(): Response
    {
        // $user = $this->security->getUser();
        $user = $this->getUser();
        return $this->json($user, context: [
            'groups' => [
                'user:read',
                'user:with_roles',
            ]
        ]);
    }
}
