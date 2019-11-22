<?php

namespace Morebec\YDB\Service;

use Symfony\Component\EventDispatcher\EventDispatcher;

/**
 * DatabaseEventDispatcher
 */
class DatabaseEventDispatcher extends EventDispatcher
{
    public function __construct(Engine $engine)
    {
        parent::__construct();
        
        $subscribers = $this->buildSubscribersList($engine);
        foreach ($subscribers as $subscriber) {
            $this->addSubscriber($subscriber);
        }
    }

    /**
     * Builds the list of event subscribers and returns it
     * @return array
     */
    public function buildSubscribersList(Engine $engine): array
    {
        return [
            $engine
        ];
    }
}
