<?php

namespace App;

interface Searcher
{
    public function search(string $key, $value, DocumentSet $docmentSet);
    public function getCounter(): int;
}
