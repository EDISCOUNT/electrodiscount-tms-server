<?php

namespace App\Service\Shipment;

use App\Entity\Shipment\Shipment;
use Knp\Snappy\Pdf;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Part\DataPart;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Twig\Environment;

class ShipmentNotificationManager
{

    private array $options = [
        [
            'value' => 'shipment_is_safely_delivered',
            'text' => 'Shipment is safely delivered',
        ],
        [
            'value' => 'client_was_around_for_delivery',
            'text' => 'Client was around for delivery'
        ],
        [
            'value' => 'client_was_around_for_delivery',
            'text' => 'Client was around for delivery'
        ],
        [
            'value' => 'client_was_around_for_delivery',
            'text' => 'Client was around for delivery'
        ],
    ];
    public function __construct(
        private ShipmentEventLogger $logger,
        private MailerInterface $mailer,
        private Pdf $pdfGenerator,
        private Environment $twig,
    ) {
    }

    public function notifyClient(Shipment $shipment, string $event): void
    {
        // $this->logger->log($shipment, $event);
    }

    public function notifyCarrierOfAssignment(Shipment $shipment,): void
    {
        $this->doNotifyCarrierOfAssignment($shipment);
    }


    private function doNotifyCarrierOfAssignment(Shipment $shipment,): void
    {
        $carrier = $shipment->getCarrier();
        $emailAddress = $carrier?->getEmailAddress();
        if (null === $emailAddress) {
            return;
        }
        $email = (new TemplatedEmail());
        $email->addTo(new Address($emailAddress, $carrier?->getName() ?? ''));

        $prepend = $shipment->isDropship() ? 'DROP SHIPMENT' : 'SHIPMENT';

        $subject = sprintf("%s ASSIGNMENT [%s]", $prepend, $shipment->getCode());

        $email
            ->subject($subject)
            ->htmlTemplate('email/carrier/new_assignment.html.twig')
            ->context([
                'shipment' => $shipment,
                'carrier' => $carrier,
            ]);
        $pdfContent = $this->generatePDF([$shipment]);
        // Attach the PDF content as a data part
        $pdfDataPart = new DataPart($pdfContent, 'packlist.pdf', 'application/pdf');
        $email->addPart($pdfDataPart);

        $this->mailer->send($email);
    }



    public function generatePDF(array $shipments): string
    {
        $content =  $this->twig->render('test/shipment/index.html.twig', [
            'shipments' => $shipments,
            'options' => $this->options,
        ]);

        try {
            return $this->getFromWKPdf($content);
        } catch (\Exception $e) {
            return $this->getFromDomPdf($content);
            // throw $e;
        }
    }



    public function getFromWKPdf(string $html): string
    {
        $output = $this->pdfGenerator->getOutputFromHtml($html);
        return $output;
    }

    public function getFromDomPdf(string $html): string
    {
        //   instantiate and use the dompdf class
        $dompdf = new \Dompdf\Dompdf();
        $dompdf->loadHtml($html);

        // (Optional) Setup the paper size and orientation
        $dompdf->setPaper('A4', 'landscape');

        // Render the HTML as PDF
        $dompdf->render();

        // Output the generated PDF to Browser
        // $dompdf->stream();

        $output = $dompdf->output();
        return $output;
    }
}
