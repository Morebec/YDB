<?php

namespace Morebec\YDB\Contracts\Message;

/**
 * Marker interface for message handlers. Message handlers must implement the __invoke  method receiving a message as parameter
 * A Message handler can handle only one type of message at a time. For multi message listening use a * MessageSubscriber
 */
interface MessageHandlerInterface
{
}

