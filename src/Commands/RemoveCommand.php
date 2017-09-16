<?php

namespace Vhost\Commands;

use Vhost\VhostManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RemoveCommand extends Command
{
    /**
     * Configure the command.
     *
     * @return  void
     */
    protected function configure()
    {
        $this->setName('remove')
            ->setDescription("Remove the host.")
            ->addArgument(
                "domain",
                InputArgument::REQUIRED,
                "Virtual domain"
            );
    }

    /**
     * Execute the command.
     *
     * @param   \Symfony\Component\Console\Input\InputInterface    $input
     * @param   \Symfony\Component\Console\Output\OutputInterface  $output
     * @return  void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $domain = $input->getArgument('domain');

        if (VhostManager::removeHost($domain)) {
            $output->writeln(
                "<info>The domain http://{$domain} has been removed successfully from the host files.</info>"
            );
        } else {
            $output->writeln("<error>Something went wrong, please check your configuration.</error>");
        }
    }
}
