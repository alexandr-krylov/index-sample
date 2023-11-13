<?php

namespace Tests;

use App\DocumentSet;
use App\PlainSearcher;
use App\Searcher;
use App\IndexSearcher;
use PHPUnit\Framework\TestCase;

class DocumentSetTest extends TestCase
{
    private DocumentSet $documentSetPlain;
    private Searcher $plainSearcher;
    private $testFileName = 'tests/data.json';
    private $testBadsFileName = 'tests/bad_data.json';
    protected function setUp(): void
    {
        $this->plainSearcher = new PlainSearcher();
        $this->documentSetPlain = new DocumentSet($this->plainSearcher);
    }
    public function testDataFileExists()
    {
        $this->assertFileExists($this->testFileName);
    }
    public function testDetaFileRead()
    {
        $this->documentSetPlain->readFile($this->testFileName);
        $documents = $this->documentSetPlain->getDocuments();
        $this->assertNotNull($documents);
        $this->assertIsObject($documents[0]);
    }
    public function testBadDataFileRead()
    {
        $this->expectException('JsonException');
        $this->documentSetPlain->readFile($this->testBadsFileName);
    }
    public function testLookUpPlain()
    {
        $this->documentSetPlain->readFile($this->testFileName);
        $key = "name";
        $value = "Adhi Kot";
        $searched = $this->documentSetPlain->lookUp($key, $value);
        $this->assertObjectHasProperty($key, $searched);
        $this->assertSame($value, $searched->$key);
        $this->assertSame(6, $this->plainSearcher->getCounter());
    }
}
