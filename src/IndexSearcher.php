<?php

namespace App;

use App\DocumentSet;

class IndexSearcher extends SearcherAbstract
{
    private ?Index $index;
    public function search(string $key, $value, DocumentSet $documentSet)
    {
        if ($this->index->indexKey !== $key) {
            $this->generateIndex($key, $documentSet);
        }
        $result = $documentSet->getDocuments()[$this->index->find($value)];
        $this->counter = $this->index->getCounter();
        return $result;
    }
    public function generateIndex(string $indexKey, DocumentSet $documentSet)
    {
        $this->index = new Index();
        $this->index->setIndexKey($indexKey);
        foreach ($documentSet->getDocuments() as $key => $document) {
            if (property_exists($document, $indexKey)) {
                $this->index->addElement($key, $document->$indexKey);
            }
        }
    }
    public function readIndexFile(string $filename)
    {
        $this->index = new Index();
        $this->index->restore($filename);
    }
    public function saveIndexFile(string $filename)
    {
        $this->index->store($filename);
    }
}
