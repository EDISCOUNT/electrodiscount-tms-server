<?php

namespace App\Service\Shipment;

use App\Entity\Carrier\Carrier;
use App\Entity\Channel\Channel;
use App\Entity\Mailing\Message;
use App\Entity\Order\Order;
use App\Entity\Shipment\Shipment;
use App\Entity\Shipment\ShipmentEvent;
use App\Service\Util\CodeGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class ShipmentEventLogger
{


    public const EVENT_SHIPMENT_CREATED = 'shipment.created';
    public const EVENT_SHIPMENT_CREATED_MANUALLY = 'shipment.created.manually';
    public const EVENT_SHIPMENT_IMPORTED = 'shipment.imported';
    public const EVENT_SHIPMENT_ASSIGNED = 'shipment.assigned';
    public const EVENT_SHIPMENT_PROCESSED = 'shipment.processed';
    public const EVENT_SHIPMENT_DISPATCHED = 'shipment.dispatched';
    public const EVENT_SHIPMENT_DELIVERED = 'shipment.delivered';
    public const EVENT_SHIPMENT_COMPLETED = 'shipment.completed';
    public const EVENT_SHIPMENT_CANCELLED = 'shipment.cancelled';

    public const EVENT_SHIPMENT_MAIL_SENT = 'shipment.mail.sent';
    public const EVENT_SHIPMENT_UPDATED = 'shipment.updated';

    public const SHIPMENT_TRANSITION_TEMPLATE = 'shipment.%s';


    public function __construct(
        private CodeGeneratorInterface $codeGenerator,
        private NormalizerInterface $normalizer,
    ) {
    }



    public function logCreated(Shipment $shipment): void
    {

        $event = new ShipmentEvent();
        $event
            ->setTitle("Shipment Created")
            ->setType(self::EVENT_SHIPMENT_CREATED);
        $shipment->addEvent($event);

        $this->finalize(
            event: $event,
            shipment: $shipment,
        );
    }

    public function logImported(Shipment $shipment, ?Channel $channel = null, ?Order $order = null): void
    {

        $event = new ShipmentEvent();
        $event
            ->setTitle("Shipment Imported")
            ->setType(self::EVENT_SHIPMENT_IMPORTED);

        if ($channel) {
            $subtitle = sprintf(
                "Shipment was import from %s for order %s",
                $channel?->getName() ?? '[N/A]',
                $order?->getChannelOrderId() ?? '[N/A]'
            );
            $event->setSubtitle($subtitle);
        }
        $shipment->addEvent($event);

        $this->finalize(
            event: $event,
            shipment: $shipment,
        );
    }


    public function logUpdated(Shipment $shipment, array $data = []): void
    {
        $event = new ShipmentEvent();
        $event
            ->setTitle("Shipment was updated")
            ->setType(self::EVENT_SHIPMENT_UPDATED)
            ->setMetadata([
                'data' => $data,
            ]);
        $shipment->addEvent($event);

        $this->finalize(
            event: $event,
            shipment: $shipment,
        );
    }

    public function logMail(Shipment $shipment, Message $mail,  array $data = []): void
    {
        $recipients = $this->normalizer->normalize($mail->getRecipients());
        $ccRecipients = $this->normalizer->normalize($mail->getCcRecipients());
        $bccRecipients = $this->normalizer->normalize($mail->getBccRecipients());

        $recipientsCount = count($recipients);

        $event = new ShipmentEvent();
        $event
            ->setTitle("E-Mail Was sent {$recipientsCount} recipients")
            ->setSubtitle($mail->getSubject())
            ->setType(self::EVENT_SHIPMENT_MAIL_SENT)
            ->setMetadata([
                'recipients' => $recipients,
                'ccRecipients' => $ccRecipients,
                'bccReceipients' => $bccRecipients,
                'subject' => $mail->getSubject(),
                'body' => $mail->getMessage(),
            ]);
        $shipment->addEvent($event);

        $this->finalize(
            event: $event,
            shipment: $shipment,
        );
    }

    public function logAssigned(Shipment $shipment, ?Carrier $carrier): void
    {

        $carrier ??= $shipment->getCarrier();
        $carrierName = $carrier?->getName();
        $subtitle = sprintf("Shipment Assigned to carrier, %s", $carrierName);

        $event = new ShipmentEvent();
        $event
            ->setTitle('Shipment Assigned')
            ->setSubtitle($subtitle)
            ->setType(self::EVENT_SHIPMENT_ASSIGNED);
        $shipment->addEvent($event);

        $this->finalize(
            event: $event,
            shipment: $shipment,
        );
    }


    public function logTransition(Shipment $shipment, string $transition, ?string $title = null, ?string $subtitle = null, ?iterable $attachments = null): void
    {
        $eventType = sprintf(self::SHIPMENT_TRANSITION_TEMPLATE, $transition);
        $title ??= sprintf("Shipment %s", ucfirst($transition));
        $subtitle ??= null;
        $event = new ShipmentEvent();
        $event
            ->setTitle($title,)
            ->setSubtitle($subtitle)
            ->setType($eventType);
        $shipment->addEvent($event);

        if ($attachments) {
            foreach ($attachments as $attachment) {
                $event->addAttachment($attachment);
            }
        }

        $this->finalize(
            event: $event,
            shipment: $shipment,
        );
    }
    public function logProcessed(Shipment $shipment, ?Carrier $carrier): void
    {

        $carrier ??= $shipment->getCarrier();
        $carrierName = $carrier?->getName();

        $event = new ShipmentEvent();
        $event
            ->setTitle(sprintf("Shipment Processed",))
            ->setType(self::EVENT_SHIPMENT_PROCESSED);
        $shipment->addEvent($event);

        $this->finalize(
            event: $event,
            shipment: $shipment,
        );
    }

    public function logDispatched(Shipment $shipment,): void
    {
        $event = new ShipmentEvent();
        $event
            ->setTitle(sprintf("Shipment dispatched"))
            ->setType(self::EVENT_SHIPMENT_DISPATCHED);
        $shipment->addEvent($event);
        $this->finalize(
            event: $event,
            shipment: $shipment,
        );
    }


    public function logDelivered(Shipment $shipment,): void
    {
        $event = new ShipmentEvent();
        $event
            ->setTitle(sprintf("Shipment Delivered"))
            ->setType(self::EVENT_SHIPMENT_DELIVERED);
        $shipment->addEvent($event);
        $this->finalize(
            event: $event,
            shipment: $shipment,
        );
    }

    public function logCompleted(Shipment $shipment,): void
    {
        $event = new ShipmentEvent();
        $event
            ->setTitle(sprintf("Shipment Completed"))
            ->setType(self::EVENT_SHIPMENT_COMPLETED);
        $shipment->addEvent($event);
        $this->finalize(
            event: $event,
            shipment: $shipment,
        );
    }

    public function logCancelled(Shipment $shipment,): void
    {
        $event = new ShipmentEvent();
        $event
            ->setTitle(sprintf("Shipment Cancelled"))
            ->setType(self::EVENT_SHIPMENT_CANCELLED);
        $shipment->addEvent($event);
        $this->finalize(
            event: $event,
            shipment: $shipment,
        );
    }






    private function finalize(ShipmentEvent $event, Shipment $shipment): void
    {
        $code = $this->codeGenerator->generateCode(length: 12);
        $event
            ->setCode($code);
    }
}
