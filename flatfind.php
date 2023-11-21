#!/usr/local/bin/php
<?php
require 'bootstrap.php';
use App\DocumentSet;
use App\PlainSearcher;
$searcher = new PlainSearcher();
$documentSet = new DocumentSet($searcher);
$documentSet->readFile($argv[3]);
$found = $documentSet->lookUp($argv[1], $argv[2]);
print_r($found);
print_r($searcher->getCounter() . "\n");
