<?php

namespace Vhost;

use Joli\JoliNotif\Notification;
use Joli\JoliNotif\NotifierFactory;

class VhostNotification {

    /**
     * Create a new instance of a notifier factory.
     *
     * @var \Joli\JoliNotif\NotifierFactory
     */
    protected $notifier;

    /**
     * Create a new instance of notification.
     *
     * @var \Joli\JoliNotif\Notification
     */
    protected $notification;

    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        $this->notifier = NotifierFactory::create();

        $this->notification = new Notification();

        $this->notification->setTitle("Vhost Manager");
    }

    /**
     * Display success notification
     *
     * @param string $message
     * @return void
     */
    public static function success($message)
    {
        $self = new static;

        $self->notify($message, __DIR__.'/icon/success.png');
    }

    /**
     * Display error notification.
     *
     * @param string $message
     * @return void
     */
    public static function error($message)
    {
        $self = new static;

        $self->notify($message, __DIR__.'/icon/error.png');
    }

    /**
     * Display a notification without icon.
     *
     * @param string $message
     * @param string $icon
     * @return void
     */
    public static function notify($message, $icon = null)
    {
        $self = new static;

        $self->notification->setBody($message);

        if(!is_null($icon)) {
            $self->notification->setIcon($icon);
        }

        $self->notifier->send($self->notification);
    }
}
