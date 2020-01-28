<?php

namespace InMemory;

use Morebec\YDB\Document;
use Morebec\YDB\Exception\DocumentCollectionAlreadyExistsException;
use Morebec\YDB\Exception\DocumentCollectionNotFoundException;
use Morebec\YDB\Exception\QueryException;
use Morebec\YDB\InMemory\InMemoryStore;
use Morebec\YDB\YQL\Query;
use PHPUnit\Framework\TestCase;

class InMemoryStoreTest extends TestCase
{
    /**
     * @throws DocumentCollectionAlreadyExistsException
     * @throws DocumentCollectionNotFoundException
     * @throws QueryException
     */
    public function testDeleteOne(): void
    {
        $store = $this->createStore();
        $store->createCollection('test_collection');

        $store->insertOne('test_collection', Document::create([
            'name' => 'Hello'
        ]));

        $store->insertOne('test_collection', Document::create([
            'name' => 'World'
        ]));

        // Even if multiple match, it will only match the first one
        $result = $store->deleteOne(new Query('FIND ALL FROM test_collection WHERE name !== null'));
        $this->assertEquals(1, $result->getCount());
    }

    /**
     * @throws DocumentCollectionAlreadyExistsException
     * @throws DocumentCollectionNotFoundException
     * @throws QueryException
     */
    public function testDeleteMany(): void
    {
        $store = $this->createStore();
        $store->createCollection('test_collection');

        $store->insertOne('test_collection', Document::create([
            'name' => 'Hello'
        ]));

        $store->insertOne('test_collection', Document::create([
            'name' => 'World'
        ]));

        $result = $store->deleteMany(new Query('FIND ALL FROM test_collection WHERE name !== null'));
        $this->assertEquals(2, $result->getCount());
    }

    /**
     * @throws DocumentCollectionAlreadyExistsException
     * @throws DocumentCollectionNotFoundException
     * @throws QueryException
     */
    public function testFindBy(): void
    {
        $store = $this->createStore();
        $store->createCollection('test_collection');

        $store->insertMany('test_collection', [
            Document::create([
            'name' => 'My name is test'
            ]),
            Document::create([
                'name' => 'My name is not test'
            ])
        ]);

        $result = $store->findBy(new Query("FIND ALL FROM test_collection WHERE name === 'My name is test'"));
        $this->assertEquals(1, $result->getCount());
    }

    public function testReplaceOne()
    {
        $store = $this->createStore();
        $store->createCollection('test_collection');

        $doc = Document::create([
            'name' => 'My name is test'
        ]);
        $store->insertOne('test_collection', $doc);

        $doc['name'] = 'Replaced Name';

        $result = $store->findBy(new Query('FIND ALL FROM test_collection'));
        $count = 0;
        while ($document = $result->fetch()) {
            $count++;
            $this->assertEquals('Replaced Name', $document['name']);
        }
        $this->assertEquals(1, $count);
    }

    /**
     * @throws DocumentCollectionAlreadyExistsException
     * @throws DocumentCollectionNotFoundException
     * @throws QueryException
     */
    public function testUpdateOne(): void
    {
        $store = $this->createStore();
        $store->createCollection('test_collection');

        $doc = Document::create([
            'name' => 'My name is test'
        ]);
        $doc2 = Document::create([
            'name' => 'Do not update'
        ]);
        $store->insertMany('test_collection', [$doc, $doc2]);

        $store->updateOne(new Query("FIND ALL FROM test_collection WHERE name === 'My name is test'"), [
            'name' => 'Updated name',
            'age' => 127
        ]);

        $result = $store->findBy(new Query("FIND ALL FROM test_collection WHERE name === 'Updated name'"));
        $count = 0;
        while ($document = $result->fetch()) {
            $count++;
            $this->assertEquals('Updated name', $document['name']);
            $this->assertEquals(127, $document['age']);
        }
        $this->assertEquals(1, $count);

        $result = $store->findBy(new Query("FIND ALL FROM test_collection WHERE name === 'Do not update'"));
        $this->assertEquals(1, $result->getCount());
    }

    public function testUpdateMany(): void
    {
        $store = $this->createStore();
        $store->createCollection('test_collection');

        $doc = Document::create([
            'name' => 'My name is test'
        ]);
        $doc2 = Document::create([
            'name' => 'My name is test too'
        ]);
        $store->insertMany('test_collection', [$doc, $doc2]);

        $store->updateMany(new Query('FIND ALL FROM test_collection WHERE name !== null'), [
            'name' => 'Updated name',
            'age' => 127
        ]);

        $result = $store->findBy(new Query("FIND ALL FROM test_collection WHERE name === 'Updated name'"));
        $count = 0;
        while ($document = $result->fetch()) {
            $count++;
            $this->assertEquals('Updated name', $document['name']);
            $this->assertEquals(127, $document['age']);
        }
        $this->assertEquals(2, $count);

        $result = $store->findBy(new Query("FIND ALL FROM test_collection WHERE name === 'My name is test' OR name === 'My name is test too'"));
        $this->assertEquals(0, $result->getCount());
    }

    /**
     * @throws DocumentCollectionAlreadyExistsException
     * @throws DocumentCollectionNotFoundException
     */
    public function testToArray(): void
    {
        $store = $this->createStore();
        $store->createCollection('test_collection');

        $doc1 = Document::create([
            'name' => 'My name is test'
        ]);
        $store->insertMany('test_collection', [
            $doc1
        ]);

        $dump = $store->toArray();
        $expected = [
            'collections' => [
                'test_collection' => [
                    'documents' => [
                        ['_id' => (string)$doc1->getId(), 'name' => 'My name is test']
                    ],
                    'indexes' => [
                        'index__id_asc' => [
                            'order' => 1,
                            'field' => '_id',
                            'values' => [
                                (string)$doc1->getId() => [(string)$doc1->getId()],
                            ]
                        ]
                    ]
                ]
            ]
        ];
        $this->assertEquals(json_encode($expected), json_encode($dump));
    }

    /**
     * @throws DocumentCollectionNotFoundException
     * @throws DocumentCollectionAlreadyExistsException
     * @throws QueryException
     */
    public function testReplaceMany(): void
    {
        $store = $this->createStore();
        $store->createCollection('test_collection');

        $documents = [
            Document::create([
                'name' => 'My name is test'
            ]),
            Document::create([
                'name' => 'My name is test'
            ])
        ];
        $store->insertMany('test_collection', $documents);

        $documents[0]['name'] = 'Replaced Name';
        $documents[1]['name'] = 'Replaced Name';

        $result = $store->findBy(new Query('FIND ALL FROM test_collection'));
        $count = 0;
        while ($document = $result->fetch()) {
            $count++;
            $this->assertEquals('Replaced Name', $document['name']);
        }
        $this->assertEquals(2, $count);
    }

    /**
     * @throws DocumentCollectionAlreadyExistsException
     * @throws DocumentCollectionNotFoundException
     * @throws QueryException
     */
    public function testInsertMany(): void
    {
        $store = $this->createStore();
        $store->createCollection('test_collection');

        $store->insertMany('test_collection', [
            Document::create([
                'name' => 'My name is test'
            ]),
            Document::create([
                'name' => 'My name is test'
            ])
        ]);

        $result = $store->findBy(new Query('FIND ALL FROM test_collection'));

        $this->assertEquals(2, $result->getCount());
    }

    /**
     * @throws DocumentCollectionAlreadyExistsException
     * @throws DocumentCollectionNotFoundException
     * @throws QueryException
     */
    public function testRenameCollection(): void
    {
        $store = $this->createStore();
        $store->createCollection('test_collection');

        $store->renameCollection('test_collection', 'renamed_test_collection');

        $store->findBy(new Query('FIND ALL FROM renamed_test_collection'));

        $this->expectException(DocumentCollectionNotFoundException::class);
        $store->findBy(new Query('FIND ALL FROM test_collection'));
    }

    /**
     * @throws DocumentCollectionAlreadyExistsException
     * @throws DocumentCollectionNotFoundException
     * @throws QueryException
     */
    public function testDropCollection(): void
    {
        $store = $this->createStore();
        $store->createCollection('test_collection');

        $store->dropCollection('test_collection');

        $this->expectException(DocumentCollectionNotFoundException::class);
        $store->findBy(new Query('FIND ALL FROM test_collection'));
    }

    /**
     * @throws DocumentCollectionAlreadyExistsException
     * @throws DocumentCollectionNotFoundException
     * @throws QueryException
     */
    public function testClearCollection(): void
    {
        $store = $this->createStore();
        $store->createCollection('test_collection');

        $store->insertOne('test_collection', Document::create([]));

        $store->clearCollection('test_collection');

        $result = $store->findBy(new Query('FIND ALL FROM test_collection'));
        $this->assertEquals(0, $result->getCount());
    }

    public function testInsertOne(): void
    {
        $store = $this->createStore();
        $store->createCollection('test_collection');

        $store->insertOne('test_collection', Document::create([
            'name' => 'My name is test'
        ]));

        $result = $store->findBy(new Query("FIND ONE FROM test_collection WHERE name === 'My name is test'"));

        $this->assertNotNull($result->fetch());
    }

    private function createStore(): InMemoryStore
    {
        return new InMemoryStore();
    }
}
