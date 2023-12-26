<?php

namespace App\Controller\Api\Carrier\Shipment;

use App\Entity\Account\User;
use App\Entity\Carrier\Carrier;
use App\Repository\Shipment\ShipmentRepository;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Routing\Annotation\Route;

class ShipmentController extends AbstractController
{

    public const SERIALIZER_GROUPS = [
        'shipment:list',
        'shipment:with_items',
        'shipment:with_address',
        'shipment:with_carrier',
        'shipment:with_channel',
        'address:list',
        'carrier:list',
        'shipment_item:read',
        'shipment_item:with_product',
        'product:list'
    ];

    public function __construct(
        private ShipmentRepository $shipmentRepository,
    ) {
    }

    #[Route('/api/carrier/shipment/shipments', name: 'app_api_carrier_shipment_shipment')]
    public function index(Request  $request): Response
    {
        try {

            $page = $request->query->get('page', 1);
            $limit = $request->query->get('limit', 10);

            if ($page < 1) {
                $page = 1;
            }
            if ($limit > 100) {
                $limit = 100;
            }

            $carrier = $this->getCarrier();
            $qb = $this->shipmentRepository->createQueryBuilder('shipment');
            $qb
                ->innerJoin('shipment.carrier', 'carrier')
                ->andWhere('carrier.id = :carrier')
                ->setParameter('carrier', $carrier);

            $adapter = new QueryAdapter($qb);
            $pagination = new Pagerfanta($adapter);

            $pagination->setMaxPerPage($limit);
            $pagination->setCurrentPage($page);

            return $this->json($pagination, context: [
                'groups' => [
                    'shipment:list',
                    ...self::SERIALIZER_GROUPS
                ],
            ]);
        } catch (AccessDeniedHttpException $e) {
            return $this->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 403);
        } catch (\Throwable $e) {
            return $this->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }



    private function getCarrier(): Carrier
    {
        /**
         * @var User| null
         */
        $user = $this->getUser();
        if (null == $user) {
            throw $this->createAccessDeniedException();
        }
        $carrier = $user->getCarrier();
        if (null == $carrier) {
            throw new   AccessDeniedHttpException("You are not a carrier!");
        }
        return $carrier;
    }
}
