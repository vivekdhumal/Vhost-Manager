<?php

namespace Vhost\Commands;

use Vhost\VhostManager;
use Vhost\VhostNotification;
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
        $output->writeln("Creating virtual host....");
        try {
            $hostCreated = VhostManager::createHost($input->getArgument('root'), $domain);

            if ($hostCreated) {
                VhostNotification::success("New virtual host \"http://{$domain}\" has been created.\nPlease restart your apache server.");

                $output->writeln("<info>New virtual host \"http://{$domain}\" has been created.</info>");
                $output->writeln("<info>Please restart your apache server.</info>");
            } else {
                VhostNotification::error("Something went wrong, please check your configuration.");

                $output->writeln("<error>Something went wrong, please check your configuration.</error>");
            }
        } catch (\Exception $e) {
            $output->writeln("<error>{$e->getMessage()}</error>");
        }

    }
}
