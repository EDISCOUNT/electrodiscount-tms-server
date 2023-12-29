<?php

namespace App\Controller\Api\Texting;

use App\Form\Texting\SmsType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Notifier\Message\SmsMessage;
use Symfony\Component\Notifier\TexterInterface;
use Symfony\Component\Routing\Annotation\Route;

class SmsController extends AbstractController
{
    public function __construct(
        private TexterInterface $texter,
    ) {
    }

    #[Route('/api/texting/sms', name: 'app_api_texting_sms_send')]
    public function sendSms(Request $request): Response
    {
        $form = $this->createForm(SmsType::class, null, ['csrf_protection' => false]);

        $data = json_decode($request->getContent(), true);
        $form->submit($data, false);

        if ($form->isValid()) {

            /** @var array<string> */
            $phoneNumbers = $form->get('phoneNumbers')->getData();
            $message = $form->get('message')->getData();

            $sentMessages = [];


            foreach ($phoneNumbers as $phoneNumber) {
                    // $options = (new ProviderOptions())
                    // ->setPriority('high')
                ;

                $sms = new SmsMessage(
                    // the phone number to send the SMS message to
                    $phoneNumber,
                    // the message
                    $message,
                    // optionally, you can override default "from" defined in transports
                    // '+1422222222',
                    // you can also add options object implementing MessageOptionsInterface
                    // $options
                );

                $sentMessage = $this->texter->send($sms);
                $sentMessages[] = $sentMessage;
            }

            return $this->json(
                [
                    'sentMessages' => $sentMessages,
                ],
                Response::HTTP_CREATED,
                // context: [
                //     'groups' => [
                //         'message_template:list',
                //         'message_template:with_owner',
                //         'user:list',
                //     ]
                // ]
            );
        }

        return $this->json(['errors' => $this->getFormErrors($form)], Response::HTTP_BAD_REQUEST);
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
