<?php

namespace Morebec\YDB\Contracts\Message;

/**
 * Interface for a Message bus middleware
 *
 */
interface MessageBusMiddlewareInterface
{
    /**
     * @param MessageInterface $message The current message going through the chain of command
     * @param MessageBusMiddlewareInterface $nextMiddleware Next Middleware to handle the message after this one
     * @return
     */
    public function __invoke(MessageInterface $message, MessageBusMiddlewareInterface $nextMiddleware): void;
}

