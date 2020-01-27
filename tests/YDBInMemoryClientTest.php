<?php


use Morebec\ValueObjects\Identity\UuidIdentifier;
use Morebec\YDB\Document;
use Morebec\YDB\YDBInMemoryClient;
use Morebec\YDB\YQL\Query;
use PHPUnit\Framework\TestCase;

class YDBInMemoryClientTest extends TestCase
{

    public function testCreateCollection()
    {
        $client = $this->createClient();
        $client->createCollection('test_collection');
    }

    public function testDropCollection()
    {
        $client = $this->createClient();
        $client->createCollection('test_collection');
        $client->dropCollection('test_collection');
    }

    public function testExecuteQuery()
    {
        $client = $this->createClient();
        $client->createCollection('test_collection');
        $query = new Query('FIND ALL FROM test_collection WHERE key === value');
        $result = $client->executeQuery($query);

        $this->assertCount(0, $result->fetchAll());
    }

    public function testClearCollection(): void
    {

    }

    public function testInsertDocument(): void
    {
        $client = $this->createClient();
        $client->createCollection('test_collection');
        $document = Document::create([
            'id' => (string)UuidIdentifier::generate(),
            'name' => 'Test'
        ]);
        $client->insertDocument('test_collection', $document);

        $query = new Query('FIND ALL FROM test_collection WHERE name === Test');
        $result = $client->executeQuery($query);

        $this->assertCount(1, $result->fetchAll());
    }

    public function testDeleteDocument()
    {

    }

    public function testUpdateDocument()
    {

    }

    /**
     * @return YDBInMemoryClient
     */
    private function createClient(): YDBInMemoryClient
    {
        $client = new YDBInMemoryClient();
        return $client;
    }
}
