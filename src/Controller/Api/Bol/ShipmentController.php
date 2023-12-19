<?php

namespace App\Controller\Api\Bol;

use SebastianBergmann\Type\TrueType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ShipmentController extends AbstractController
{
    public function __construct(
        private ParameterBagInterface $parameterBag,
        private HttpClientInterface $httpClient,
    ) {
    }
    #[Route('/api/bol/shipments', name: 'app_api_bol_shipment')]
    public function index(): Response
    {
        $authToken = $this->parameterBag->get('bol.access_token');

        $result = $this->httpClient->request("GET", "https://api.bol.com/retailer/orders", [
            'timeout' => 150,
            'headers' => [
                'Authorization' => 'Bearer ' . $authToken,
                'Accept' => 'application/vnd.retailer.v10+json',
                // 'Accept' => 'application/json',
            ],
        ]);

        // $responses = [$result];

        // foreach ($this->httpClient->stream($responses, 150) as $response => $chunk) {
        //     if ($chunk->isTimeout()) {
        //         // $response stale for more than 1.5 seconds
        //         echo "TIMEOUT\n\n\n"; 
        //         echo $response->getContent(throw: false);
        //     }else{
        //         echo "NON-TIMEOUT\n\n\n"; 
        //         echo $response->getContent(throw: false);
        //     }
        // }

        $data = $result->toArray(throw: true);
        return $this->json($data);
    }
}
