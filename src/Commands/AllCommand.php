<?php

namespace Vhost\Commands;

use Vhost\VhostManager;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AllCommand extends Command
{
    /**
     * Configure the command.
     *
     * @return  void
     */
    protected function configure()
    {
        $this->setName('all')
            ->setDescription('Shows the list of the hosts.');
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
        $hosts = VhostManager::getHosts();

        $table = new Table($output);

        $table->setHeaders(['Document Root', 'Virtual Host']);

        foreach ($hosts as $host) {
            $table->addRow([
                $host['document_root'],
                $host['server_name'],
            ]);
        }

        $table->render();
    }
}
