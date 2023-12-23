<?php

namespace App\Command;

use App\Entity\Account\User;
use App\Repository\Account\UserRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'account:user:list',
    description: 'Add a short description for your command',
)]
class AccountUserListCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserRepository $userRepository,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            // ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('limit', null, InputOption::VALUE_OPTIONAL, 'Maximum number of items to return', 10)
            ->addOption('offset', null, InputOption::VALUE_OPTIONAL, 'Offset to start from', 0);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $qb = $this->userRepository->createQueryBuilder('user');

        if ($limit = $input->getOption('limit')) {
            $qb->setMaxResults($limit);
        }
        if ($offset = $input->getOption('offset')) {
            $qb->setFirstResult($offset);
        }

        $end = $offset + $limit;

        $total = (clone $qb)->select('COUNT(user.id)')->getQuery()->getSingleScalarResult();
        $users = $qb->getQuery()->getResult();
        $table = $this->buildTable($users, $output);

        $table->setFooterTitle(sprintf('%d - %d of  %d items', $offset, $end, $total));

        $table->render();

        // $io->success('You have a new command! Now make it your own! Pass --help to see your options.');

        return Command::SUCCESS;
    }



    private function buildTable(iterable $users, OutputInterface $output): Table
    {
        $table = new Table($output);

        $rows = [];

        /** @var User */
        foreach ($users as $user) {
            $rows[] = [
                $user->getId(),
                $user->getFirstName(),
                $user->getLastName(),
                $user->getEmail(),
                $user->getRoles(),
            ];
        }

        $table
            ->setHeaders(['ID', 'First Name', 'Last Name', 'E-Mail Address', 'ROLES'])
            ->setRows($rows);

        return $table;
    }
}
