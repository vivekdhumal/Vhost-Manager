<?php

namespace Vhost\Commands;

use Vhost\VhostManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CreateCommand extends Command
{
    /**
     * Configure the command.
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName('create')
            ->setDescription('Create a virtual host.')
            ->addArgument(
                "root",
                InputArgument::REQUIRED,
                "Which Document root / directory should we use?"
            )
            ->addArgument(
                "domain",
                InputArgument::REQUIRED,
                "Which Domain should we use?"
            );
    }

    /**
     * Execute the command.
     *
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $domain = $input->getArgument('domain');

        $hostCreated = VhostManager::createHost($input->getArgument('root'), $domain);

        if ($hostCreated) {
            $output->writeln("<info>New virtual host \"http://{$domain}\" has been created.</info>");
            $output->writeln("<info>Please restart your apache server.</info>");
        } else {
            $output->writeln("<error>Something went wrong, please check your configuration.</error>");
        }
    }
}
