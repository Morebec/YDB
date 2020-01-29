<?php


namespace Morebec\YDB\Server\Command;

use Morebec\Collections\HashMap;
use Morebec\YDB\Document;
use Morebec\YDB\InMemory\InMemoryStore;
use Morebec\YDB\Server\Server;
use Morebec\YDB\YQL\Query;
use React\Socket\ConnectionInterface;

/**
 * Class ExecuteQueryCommand
 * Executes a query
 */
class ExecuteQueryCommand implements ServerCommandInterface
{
    public const NAME = 'execute_query';

    /**
     * @var string
     */
    private $query;

    public function __construct(string $query)
    {
        $this->query = $query;
    }

    /**
     * @inheritDoc
     */
    public static function fromData(HashMap $data): ServerCommandInterface
    {
        $query = $data->get('query');
        return new static($query);
    }

    /**
     * @inheritDoc
     */
    public function execute(Server $server, ConnectionInterface $client, InMemoryStore $store)
    {
        $result = $store->findBy(new Query($this->query));

        return array_map(static function (Document $doc) {
            return $doc->toArray();
        }, $result->fetchAll());
    }

    /**
     * @inheritDoc
     */
    public function toArray(): array
    {
        return [
            'command' => self::NAME,
            'query' => $this->query
        ];
    }
}
