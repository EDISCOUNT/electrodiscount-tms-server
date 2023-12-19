<?php
namespace App\Command\Auth;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[AsCommand(
    name: 'bol:auth:access-token:new',
    description: 'Add a short description for your command',
)]
class NewAccessTokenCommand extends Command
{
    public function __construct(
        private ParameterBagInterface $parameterBag,
        private HttpClientInterface $httpClient
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            // ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('client-id', null, InputOption::VALUE_OPTIONAL, 'The client id to use for this request')
            ->addOption('client-secret', null, InputOption::VALUE_OPTIONAL, 'The client secret to use for this request');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $clientId = $input->getOption('client-id') ?? $this->parameterBag->get('bol.client_id');
        $clientSecret = $input->getOption('client-secret') ?? $this->parameterBag->get('bol.client_secret');

        $basic = base64_encode($clientId . ':' . $clientSecret);
        var_dump([
            'clientId' => $clientId,
            'clientSecret' => $clientSecret,
            'basic' => $basic,
        ]
        );

        $response = $this->httpClient->request('POST', 'https://login.bol.com/token?grant_type=client_credentials', [
            // 'vars' => [
            //     'grant_type' => 'client_credentials',
            // ],
            'headers' => [
                // 'Content-Type' => 'text/plain',
                'Authorization' => 'Basic ' . $basic,
            ],
            // 'body' => [
            //     'client_id' => $clientId,
            //     'client_secret' => $clientSecret,
            // ],
        ]);

        // {
        //     "access_token": "<access_token>",
        //     "token_type": "Bearer",
        //     "expires_in": 299,
        //     "scope": "<scopes>"
        // }

        $result = $response->toArray(throw:false);
        var_dump($result);

        $accessToken = $result['access_token'];

        // Use the access token as needed
        // ...

        $io->success('Generated access token: ' . $accessToken . '');

        return Command::SUCCESS;
    }
}
// Compare this snippet from src/Command/Auth/AccessTokenCommand.php:
