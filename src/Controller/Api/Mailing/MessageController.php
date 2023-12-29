<?php

namespace App\Controller\Api\Mailing;

use App\Entity\Mailing\Message;
use App\Form\Mailing\MessageType;
use App\Repository\Mailing\MessageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/mailing/messages')]
class MessageController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private MessageRepository $messageRepository,
        private MailerInterface $mailer,
    ) {
    }

    #[Route('', name: 'app_api_mailing_message_index', methods: ['GET'])]
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

        $qb = $this->messageRepository->createQueryBuilder('message');
        $adapter = new QueryAdapter($qb);
        $pagination = new Pagerfanta($adapter);

        $pagination->setMaxPerPage($limit);
        $pagination->setCurrentPage($page);

        return $this->json($pagination, context: [
            'groups' => [
                'message:list',
                'message:with_operator',
                'user:list',
            ]
        ]);
    }

    #[Route('', name: 'app_api_mailing_message_new', methods: ['POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $message = new Message();
        $form = $this->createForm(MessageType::class, $message, ['csrf_protection' => false]);

        // $data = json_decode($request->getContent(), true);
        $data = $request->request->all();
        $form->submit($data, false);

        if ($form->isValid()) {
            $this->sendEmail($message);
            $entityManager->persist($message);
            $entityManager->flush();

            return $this->json(
                $message,
                Response::HTTP_CREATED,
                // context: [
                //     'groups' => [
                //         'message:list',
                //         'message:with_operator',
                //         'user:list',
                //     ]
                // ]
            );
        }

        return $this->json(['errors' => $this->getFormErrors($form)], Response::HTTP_BAD_REQUEST);
    }

    #[Route('/{id}', name: 'app_api_mailing_message_show', methods: ['GET'])]
    public function show(Message $message): Response
    {
        return $this->json($message, context: [
            'groups' => [
                'message:list',
                'message:with_operator',
                'user:list',
            ]
        ]);
    }

    #[Route('/{id}', name: 'app_api_mailing_message_update', methods: ['PATCH'])]
    public function update(Request $request, Message $message, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(MessageType::class, $message, ['csrf_protection' => false]);

        $data = json_decode($request->getContent(), true);
        $form->submit($data, false);

        if ($form->isValid()) {
            $entityManager->flush();

            return $this->json($message, context: [
                'groups' => [
                    'message:list',
                    'message:with_operator',
                    'user:list',
                ]
            ]);
        }

        return $this->json(['errors' => $this->getFormErrors($form)], Response::HTTP_BAD_REQUEST);
    }

    #[Route('/{id}', name: 'app_api_mailing_message_delete', methods: ['DELETE'])]
    public function delete(Message $message, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($message);
        $entityManager->flush();

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }




    private function sendEmail(Message $message): void
    {
        $email = (new Email());
        foreach ($message->getRecipients()  as $address) {
            $email->addTo(new Address($address->getEmailAddress(), $address->getFullName()?? ''));
        }
        foreach ($message->getCcRecipients()  as $address) {
            $email->addCC(new Address($address->getEmailAddress(), $address->getFullName()?? ''));
        }
        foreach ($message->getBccRecipients()  as $address) {
            $email->addBcc(new Address($address->getEmailAddress(), $address->getFullName()?? ''));
        }

        $email
            ->subject($message->getSubject() ?? '')
            ->html($message->getMessage());

        $this->mailer->send($email);
    }

    private function getFormErrors(FormInterface $form): array
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
