<?php

namespace App;

class PlainSearcher extends SearcherAbstract
{
    public function search(string $key, $value, DocumentSet $documentSet)
    {
        $this->counter = 0;
        foreach ($documentSet->getDocuments() as $document) {
            $this->counter++;
            if (property_exists($document, $key)) {
                if ($document->$key == $value) {
                    return $document;
                }
            }
        }
    }
}
