<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;

#[AsCommand(
    name: 'app:mercure-send',
    description: 'Envia una notificación push de ejemplo',
)]
class MercureSendCommand extends Command
{
    private $hub;

    public function __construct(HubInterface $hub)
    {
        parent::__construct();
        $this->hub = $hub;
    }

    protected function configure(): void
    {
        $this
            ->addArgument('message', InputArgument::REQUIRED, 'Mensaje a ser enviado')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $message = $input->getArgument('message');

        $update = new Update(
            'https://localhost/books/1',
            json_encode([
                'message' => $message,
                'user' => 'jdo'
                ])
        );
        $this->hub->publish($update);

        $io->success('El comando a finalizado');

        return Command::SUCCESS;
    }
}
