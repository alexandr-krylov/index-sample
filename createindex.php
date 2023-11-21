#!/usr/local/bin/php
<?php
require 'bootstrap.php';
use App\IndexSearcher;
use App\DocumentSet;
$searcher = new IndexSearcher();
$documentSet = new DocumentSet($searcher);
$documentSet->readFile($argv[2]);
$searcher->generateIndex($argv[1], $documentSet);
$searcher->saveIndexFile($argv[3]);
