<?php

namespace Vhost;

use Vhost\Commands\CreateHostCommand;
use Symfony\Component\Console\Application;

class VhostApplication extends Application
{
    /**
     * Create an instance of a symfony console application.
     *
     * @var Symfony\Component\Console\Application
     */
    protected $application;

    /**
     * Create a new class instance.
     *
     * @return void
     */
    public function __construct(Application $application)
    {
        $this->application = $application;

        $this->application->setName("Vhost Manager");

        $this->application->setVersion("1.0");
    }

    /**
     * Handle incoming console request.
     *
     * @return void
     */
    public function handle()
    {
        $this->registerCommands();

        $this->application->run();
    }

    /**
     * Register application commands.
     *
     * @return void
     */
    protected function registerCommands()
    {
        $this->application->addCommands([
            new CreateHostCommand,
        ]);
    }
}
