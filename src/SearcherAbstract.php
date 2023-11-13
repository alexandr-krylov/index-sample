<?php

namespace App;

abstract class SearcherAbstract implements Searcher
{
    protected int $counter = 0;
    public function getCounter(): int
    {
        return $this->counter;
    }
}
