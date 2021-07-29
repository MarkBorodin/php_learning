<?php

namespace App\Command;

use App\Repository\UserRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class CreatesuperuserCommand extends Command
{
    protected static $defaultName = 'app:createsuperuser';
    protected static $defaultDescription = 'Add a short description for your command';
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository, string $name = null)
    {
        parent::__construct($name);
        $this->userRepository = $userRepository;
    }

    protected function configure(): void
    {
        $this
            ->addArgument('name', InputArgument::REQUIRED, 'Argument description')
            ->addArgument('email', InputArgument::REQUIRED, 'Argument description')
            ->addArgument('password', InputArgument::REQUIRED, 'Argument description')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $name = $input->getArgument('name');
        $email = $input->getArgument('email');
        $password = $input->getArgument('password');

        if ($name) {
            $io->note(sprintf('Name: %s', $name));
        }
        if ($email) {
            $io->note(sprintf('Email: %s', $email));
        }
        if ($password) {
            $io->note(sprintf('Password: %s', $password));
        }

        $this->userRepository->createUser($name, $email, $password);

        if ($input->getOption('option1')) {
            // ...
        }

        $io->success('You have successfully created a superuser.');

        return Command::SUCCESS;
    }
}
