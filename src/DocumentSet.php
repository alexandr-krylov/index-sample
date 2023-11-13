<?php

namespace App;

use App\Searcher;

class DocumentSet
{
    private $documents = [];
    private Searcher $searcher;
    public function __construct(Searcher $searcher)
    {
        $this->searcher = $searcher;
    }
    public function readFile($fileName)
    {
        $this->documents = json_decode(file_get_contents($fileName), null, 512, JSON_THROW_ON_ERROR);
    }
    public function getDocuments()
    {
        return $this->documents;
    }
    public function lookUp($key, $value)
    {
        return $this->searcher->search($key, $value, $this);
    }
}
