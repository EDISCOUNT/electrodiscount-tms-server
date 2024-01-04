<?php

namespace App\Controller\Api\Mailing;

use App\Entity\Account\User;
use App\Entity\Mailing\Template\EmailMessageTemplate;
use App\Form\Mailing\Template\EmailMessageTemplateType;
use App\Repository\Mailing\Template\EmailMessageTemplateRepository;
use Doctrine\ORM\EntityManagerInterface;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/mailing/message_templates')]
class EmailMessageTemplateController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private EmailMessageTemplateRepository $templateRepository,
        private MailerInterface $mailer,
        private Security $security,
    ) {
    }

    #[Route('', name: 'app_api_mailing_message_template_index', methods: ['GET'])]
    public function index(Request $request): Response
    {
        $page = (int) $request->query->get('page', 1);
        $limit = (int) $request->query->get('limit', 10);
        $ownerId =  $request->query->get('owner_id');
        $search =  $request->query->get('search');

        if ($page < 1) {
            $page = 1;
        }
        if ($limit > 100) {
            $limit = 100;
        }

        $qb = $this->templateRepository->createQueryBuilder('template');

        if ($this->security->isGranted('ROLE_ADMIN')) {
            if ($ownerId == 'null') {
                $qb->andWhere('template.owner IS NULL');
            } elseif ($ownerId) {
                $qb->andWhere('template.owner = :owner')
                    ->setParameter('owner', $ownerId);
            }
        } else {
            $qb->andWhere('template.owner = :owner')
                ->setParameter('owner', $this->getUser());
        }
        if ($search) {
            $qb->andWhere('template.label LIKE :search')
                ->setParameter('search', '%' . $search . '%');
        }
        $adapter = new QueryAdapter($qb);
        $pagination = new Pagerfanta($adapter);

        $pagination->setMaxPerPage($limit);
        $pagination->setCurrentPage($page);

        return $this->json($pagination, context: [
            'groups' => [
                'message_template:list',
                'message_template:with_owner',
                'user:list',
            ]
        ]);
    }

    #[Route('', name: 'app_api_mailing_message_template_new', methods: ['POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $template = new EmailMessageTemplate();
        $form = $this->createForm(EmailMessageTemplateType::class, $template, ['csrf_protection' => false]);

        $data = json_decode($request->getContent(), true);
        $form->submit($data, false);

        if ($form->isValid()) {

            /** @var User */
            $user = $this->getUser();

            if ($this->security->isGranted('ROLE_ADMIN')) {
            } else {
                $template->setOwner($user);
            }

            $entityManager->persist($template);
            $entityManager->flush();

            return $this->json(
                $template,
                Response::HTTP_CREATED,
                context: [
                    'groups' => [
                        'message_template:list',
                        'message_template:with_owner',
                        'user:list',
                    ]
                ]
            );
        }

        return $this->json(['errors' => $this->getFormErrors($form)], Response::HTTP_BAD_REQUEST);
    }

    #[Route('/{id}', name: 'app_api_mailing_message_template_show', methods: ['GET'])]
    public function show(EmailMessageTemplate $template): Response
    {
        return $this->json($template, context: [
            'groups' => [
                'message_template:list',
                'message_template:with_owner',
                'user:list',
            ]
        ]);
    }

    #[Route('/{id}', name: 'app_api_mailing_message_template_update', methods: ['PATCH'])]
    public function update(Request $request, EmailMessageTemplate $template, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(EmailMessageTemplateType::class, $template, ['csrf_protection' => false]);

        $data = json_decode($request->getContent(), true);
        $form->submit($data, false);

        if ($form->isValid()) {
            $entityManager->flush();

            return $this->json($template, context: [
                'groups' => [
                    'message_template:list',
                    'message_template:with_owner',
                    'user:list',
                ]
            ]);
        }

        return $this->json(['errors' => $this->getFormErrors($form)], Response::HTTP_BAD_REQUEST);
    }

    #[Route('/{id}', name: 'app_api_mailing_message_template_delete', methods: ['DELETE'])]
    public function delete(EmailMessageTemplate $template, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($template);
        $entityManager->flush();

        return $this->json(null, Response::HTTP_NO_CONTENT);
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
