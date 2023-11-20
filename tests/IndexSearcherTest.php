<?php

namespace Tests;

use App\IndexSearcher;
use App\DocumentSet;
use PHPUnit\Framework\TestCase;

class IndexSearcherTest extends TestCase
{
    private IndexSearcher $indexSearcher;
    private DocumentSet $documentSet;
    private string $testFileName = 'tests/data.json';
    private string $indexFileName = 'tests/index.json';
    protected function setUp(): void
    {
        $this->indexSearcher = new IndexSearcher();
        $this->documentSet = new DocumentSet($this->indexSearcher);
        $this->documentSet->readFile($this->testFileName);
    }
    public function testGenerateIndex()
    {
        $key = "reclong";
        $value = "-113.000000";
        $this->indexSearcher->generateIndex($key, $this->documentSet);
        $this->indexSearcher->saveIndexFile($this->indexFileName);
        $this->indexSearcher->readIndexFile($this->indexFileName);
        $searched = $this->documentSet->lookUp($key, $value);
        $this->assertIsObject($searched);
        $this->assertObjectHasProperty($key, $searched);
        $this->assertSame($value, $searched->$key);
        $this->assertSame(3, $this->indexSearcher->getCounter());
    }
}
