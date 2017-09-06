<?php

namespace Vhost\Commands;

use Vhost\VhostManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CreateHostCommand extends Command
{
    /**
     * Configure the command.
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName('create:host')
            ->setDescription('Create a virtual host.')
            ->addOption(
                'root',
                'r',
                InputOption::VALUE_REQUIRED,
                "Document root / directory for which virtual host need to be created."
            )
            ->addOption(
                'domain',
                'd',
                InputOption::VALUE_REQUIRED,
                "Virtual domain to serve the document root."
            );
    }

    /**
     * Execute the command.
     *
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $domain = $input->getOption('domain');

        $hostCreated = VhostManager::createHost($input->getOption('root'), $domain);

        if ($hostCreated) {
            $output->writeln("<info>This new virtual host is up and running at http://{$domain}</info>");
            $output->writeln("<info>Please restart your apache server.</info>");
        } else {
            $output->writeln("<error>Something went wrong, please check your configuration.</error>");
        }
    }
}
