<?php

namespace Morebec\YDB\Contracts\Message;

/**
 * Interface for a MessageBus. A Message bus is responsible for dispatching messages to a chain of middlewares
 * that will in turn dispatch to the network or synchronous command handler and subscribers
 */
interface MessageSubscriber
{
    /**
     * Returns a list of the messages to be handled
     * @return iterable
     */
    public function getSubscribedMessages(): iterable;
}

