<?php

namespace Morebec\YDB\Contracts\Message;

/**
 * Interface for a MessageBus. A Message bus is responsible for dispatching messages to a chain of middlewares
 * that will in turn dispatch to the network or synchronous command handler and subscribers
 */
interface MessageBusInterface
{
    /**
     * Dispatches a given message to the different middlewares
     * @param MessageInterface $message The message to be dispatched
     * @return mixed
     */
    public function dispatch(MessageInterface $message);
}

