<?php

namespace App\Controller\Shipment;

use App\Entity\Account\User;
use App\Entity\Shipment\Shipment;
use App\Entity\Shipment\ShipmentItem;
use App\Repository\Account\UserRepository;
use App\Repository\Shipment\ShipmentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class ShipmentExportRequestController extends AbstractController
{
    public function __construct(
        private UserRepository $userRepository,
        private ShipmentRepository $shipmentRepository,
        // private ShipmentNotificationManager $notifier,
    ) {
    }

    #[Route('/shipment/shipment/export/{code}/request', name: 'app_shipment_shipment_export_request', defaults: ['_signed' => true],)]
    public function index(Request $request, string $code): Response
    {
        $json = base64_decode($code);
        $data = json_decode($json, true);
        $shipmentIds = $data['shipment_ids'] ?? [];
        $user_id = $data['user_id'] ?? null;
        $user = $this->userRepository->find($user_id);

        /** @var Shipment[] */
        $shipments = [];

        foreach ($shipmentIds as $id) {
            $shipment = $this->shipmentRepository->find($id);
            if (!($shipment instanceof Shipment)) {
                throw $this->createNotFoundException('Shipment not found');
            }
            $shipments[] = $shipment;
        }
        return $this->generateExcel($shipments, $user);
    }



    /**
     * @var Shipment[]
     */
    public function generateExcel(array $shipments, User $user): Response
    {
        // Dummy data for demonstration purposes
        $data = array_map(function (Shipment $shipment) use ($user) {
            $entry = [
                'Order Number' => $shipment->getChannelOrderId(),
                'Item ID' => implode(',', $shipment->getItems()
                    ->map(fn (ShipmentItem $item) => $item->getChannelOrderItemId())
                    ->filter(fn ($id) => $id != null)->toArray()),
                'Delivery Date' => $shipment->getDeliveredAt()?->format('Y-m-d H:i:s'),
                'Status' => $shipment->getStatus(),
            ];

            if ($this->isAdmin($user)) {
                $entry['Carrier ID'] = $shipment->getCarrier()?->getId();
            }

            return $entry;
        }, $shipments);

        // Create a new PhpSpreadsheet instance
        $spreadsheet = new Spreadsheet();

        // Add header row
        $sheet = $spreadsheet->getActiveSheet();
        $header = ['Order Number', 'Item ID', 'Delivery Date',  'Status'];
        if ($this->isAdmin($user)) {
            $header[] = 'Carrier ID';
        }
        $sheet->fromArray([$header], null, 'A1');

        // Add data rows
        $sheet->fromArray($data, null, 'A2');

        // Create a writer to save the spreadsheet
        $writer = new Xlsx($spreadsheet);

        // Save the spreadsheet to a file (you can also send it as a response)
        $fileName = 'orders.xlsx';
        $filePath = $this->getParameter('kernel.project_dir') . '/public/' . $fileName;
        // $writer->save($filePath);
        ob_start();
        $writer->save('php://output');
        $excelOutput = ob_get_clean();

        // Return a response, or do something else with the file
        return new Response(
            $excelOutput,
            200,
            [
                'content-type'        =>  'text/x-csv; charset=windows-1251',
                'Content-Disposition' => 'attachment; filename="price.xlsx"'
            ]
        );
    }


    private function isAdmin(User $user): bool
    {
        return in_array('ROLE_ADMIN', $user->getRoles());
    }
}
