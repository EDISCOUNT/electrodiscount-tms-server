<?php

namespace App\Controller\Api\Admin\Channel;

use App\Entity\Channel\Channel;
use App\Form\Channel\ChannelType;
use App\Repository\Channel\ChannelRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/admin/channel/channels')]
class ChannelController extends AbstractController
{
    #[Route('/', name: 'app_api_admin_channel_channel_index', methods: ['GET'])]
    public function index(ChannelRepository $channelRepository): Response
    {
        $channels = $channelRepository->findAll();

        return $this->json($channels);
    }

    #[Route('', name: 'app_api_admin_channel_channel_new', methods: ['POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $channel = new Channel();
        $form = $this->createForm(ChannelType::class, $channel, ['csrf_protection' => false]);
        
        $data = json_decode($request->getContent(), true);
        $form->submit($data, false);

        if ($form->isValid()) {
            $entityManager->persist($channel);
            $entityManager->flush();

            return $this->json($channel, Response::HTTP_CREATED);
        }

        return $this->json(['errors' => $this->getFormErrors($form)], Response::HTTP_BAD_REQUEST);
    }

    #[Route('/{id}', name: 'app_api_admin_channel_channel_show', methods: ['GET'])]
    public function show(Channel $channel): Response
    {
        return $this->json($channel);
    }

    #[Route('/{id}', name: 'app_api_admin_channel_channel_update', methods: ['PATCH'])]
    public function update(Request $request, Channel $channel, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ChannelType::class, $channel, ['csrf_protection' => false]);
        
        $data = json_decode($request->getContent(), true);
        $form->submit($data, false);

        if ($form->isValid()) {
            $entityManager->flush();

            return $this->json($channel);
        }

        return $this->json(['errors' => $this->getFormErrors($form)], Response::HTTP_BAD_REQUEST);
    }

    #[Route('/{id}', name: 'app_api_admin_channel_channel_delete', methods: ['DELETE'])]
    public function delete(Channel $channel, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($channel);
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
