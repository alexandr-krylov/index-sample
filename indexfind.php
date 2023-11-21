#!/usr/local/bin/php
<?php
require 'bootstrap.php';
use App\IndexSearcher;
use App\DocumentSet;
$searcher = new IndexSearcher();
$documentSet = new DocumentSet($searcher);
$documentSet->readFile($argv[3]);
$searcher->readIndexFile($argv[4]);
$found = $documentSet->lookUp($argv[1], $argv[2]);
print_r($found);
print_r($searcher->getCounter() . "\n");
