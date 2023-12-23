<?php

namespace App\Command;

use App\Entity\Account\User;
use App\Form\Account\UserType;
use App\Repository\Account\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Matthias\SymfonyConsoleForm\Console\Helper\FormHelper;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'account:user:create',
    description: 'Creates a new User',
)]
class AccountUserCreateCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserRepository $userRepository,
        private UserPasswordHasherInterface $passwordHasher,
        // private FormHelper $formHelper,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            // ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('first-name', null, InputOption::VALUE_REQUIRED, 'First name')
            ->addOption('last-name', null, InputOption::VALUE_REQUIRED, 'Last name')
            ->addOption('username', null, InputOption::VALUE_REQUIRED, 'Username')
            ->addOption('email', null, InputOption::VALUE_REQUIRED, 'Email')
            ->addOption('password', null, InputOption::VALUE_REQUIRED, 'Password')
            ->addOption('role', null, InputOption::VALUE_REQUIRED|InputOption::VALUE_IS_ARRAY, 'Role')
            ->addOption('enabled', null, InputOption::VALUE_NONE, 'Enabled');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);


        $firstName = $input->getOption('first-name');
        $lastName = $input->getOption('last-name');
        $email = $input->getOption('email');
        $username = $input->getOption('username');
        $password = $input->getOption('password');
        $roles = $input->getOption('role');
        $enabled = $input->getOption('enabled');


        $user = new User();
        $hashedPassword = $this->passwordHasher->hashPassword($user, $password);

        $user
            ->setFirstName($firstName)
            ->setLastName($lastName)
            ->setEmail($email)
            ->setUsername($username?? $email)
            ->setRoles($roles)
            ->setPassword($hashedPassword);


        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $io->success('User created successfully.');

        return Command::SUCCESS;
    }
}
