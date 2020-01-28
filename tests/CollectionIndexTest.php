<?php


use Morebec\YDB\CollectionIndex;
use Morebec\YDB\Document;
use PHPUnit\Framework\TestCase;

class CollectionIndexTest extends TestCase
{
    public function testIndexDocument()
    {
        $index = new CollectionIndex('age');
        $docs = [];
        for ($i = 0; $i < 50000; $i++) {
            $docs[] = Document::create([
                'age' => $i
            ]);
        }

        $index->indexDocuments($docs);
        $index->indexOneDocument(Document::create([
                'age' => 500
        ]));

        $documentIds = $index->findDocumentIdForValue(500);

        $this->assertCount(2, $documentIds);
    }
}
