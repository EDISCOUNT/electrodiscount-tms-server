<?php

namespace App\Controller\Shipment;

use App\Entity\Shipment\Shipment;
use App\Repository\Shipment\ShipmentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Snappy\Pdf;

class ShipmentPacklistRequestController extends AbstractController
{


    public function __construct(
        private ShipmentRepository $shipmentRepository,
        // private ContainerInterface $rcontainer,
        private Pdf $pdfGenerator,
    ) {
    }

    #[Route('/shipment/shipment/packlist/{code}/request', name: 'app_shipment_shipment_packlist_request', defaults: ['_signed' => true],)]
    public function index(string $code): Response
    {
        $json = base64_decode($code);
        $shipmentIds = json_decode($json, true);

        $shipments = [];

        foreach ($shipmentIds as $id) {
            $shipment = $this->shipmentRepository->find($id);
            if (!($shipment instanceof Shipment)) {
                throw $this->createNotFoundException('Shipment not found');
            }
            $shipments[] = $shipment;
        }

        $output = $this->generatePDF($shipments);

        return new Response($output, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="packlist.pdf"'
        ]);
    }




    private function generatePDF(array $shipments): string
    {
        $content =  $this->renderView('test/shipment/index.html.twig', [
            'shipments' => $shipments,
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
