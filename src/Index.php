<?php

namespace App;

class Index
{
    public ?int $root = null;
    private ?int $pointer = null;
    public array $index = [];
    public string $indexKey;
    private int $counter = 0;
    public function __construct($indexKey)
    {
        $this->indexKey = $indexKey;
    }
    public function addElement($key, $value)
    {
        $height = 1;
        if (is_null($this->root)) {
            $this->root = $key;
            $this->addNode($key, $value, $height);
            return $this;
        }
        $this->pointer = $this->root;
        while (true) {
            $height++;
            if ($value == $this->index[$this->pointer]->data) {
                return $this;
            }
            if ($value > $this->index[$this->pointer]->data) {
                if (is_null($this->index[$this->pointer]->right)) {
                    $this->index[$this->pointer]->right = $key;
                    $this->index[$this->pointer]->rightHeight = 1;
                    $this->addNode($key, $value, $height);
                    $unbalansedNode = $this->findUnbalancedNodeUp();
                    if (!is_null($unbalansedNode)) {
                        $this->balansing($unbalansedNode);
                    }
                    return $this;
                }
                $this->pointer = $this->index[$this->pointer]->right;
                continue;
            }
            if ($value < $this->index[$this->pointer]->data) {
                if (is_null($this->index[$this->pointer]->left)) {
                    $this->index[$this->pointer]->left = $key;
                    $this->index[$this->pointer]->leftHeight = 1;
                    $this->addNode($key, $value, $height);
                    $unbalansedNode = $this->findUnbalancedNodeUp();
                    if (!is_null($unbalansedNode)) {
                        $this->balansing($unbalansedNode);
                    }
                    return $this;
                }
                $this->pointer = $this->index[$this->pointer]->left;
                continue;
            }
        }
    }
    private function addNode($key, $value, $height)
    {
        $this->index[$key] = new class {
        };
        $this->index[$key]->data = $value;
        $this->index[$key]->left = null;
        $this->index[$key]->leftHeight = 0;
        $this->index[$key]->right = null;
        $this->index[$key]->rightHeight = 0;
        $this->index[$key]->height = $height;
        $this->index[$key]->parent = $this->pointer;
        $this->pointer = $key;
    }
    public function store($filename)
    {
        $json = json_encode($this, JSON_FORCE_OBJECT | JSON_PRETTY_PRINT);
        file_put_contents($filename, $json);
    }
    public function restore($filename)
    {
        $json = json_decode(file_get_contents($filename));
        $this->root = $json->root;
        $this->index = (array)$json->index;
        $this->indexKey = $json->indexKey;
    }
    public function find($value)
    {
        $this->counter = 0;
        $this->pointer = $this->root;
        for ($i = 0; $i < 10; $i++) {
            $this->counter++;
            if (is_null($this->pointer)) {
                return null;
            }
            if ($value == $this->index[$this->pointer]->data) {
                return $this->pointer;
            }
            if ($value < $this->index[$this->pointer]->data) {
                $this->pointer = $this->index[$this->pointer]->left;
                continue;
            }
            if ($value > $this->index[$this->pointer]->data) {
                $this->pointer = $this->index[$this->pointer]->right;
                continue;
            }
        }
    }
    public function getCounter()
    {
        return $this->counter;
    }
    private function findUnbalancedNodeUp()
    {
        $thisNodePointer = $this->pointer;
        var_dump('inserted', $thisNodePointer);
        $thisHeight = $this->index[$thisNodePointer]->height;
        $parentNodePointer = $this->index[$thisNodePointer]->parent;
        while (!is_null($parentNodePointer)) {
            $parentNode = $this->index[$parentNodePointer];
            if ($parentNode->left == $thisNodePointer) {
                if ($parentNode->leftHeight < $thisHeight - $parentNode->height) {
                    $parentNode->leftHeight = $thisHeight - $parentNode->height;
                }
            }
            if ($parentNode->right == $thisNodePointer) {
                if ($parentNode->rightHeight < $thisHeight - $parentNode->height) {
                    $parentNode->rightHeight = $thisHeight - $parentNode->height;
                }
            }
            if (abs($parentNode->leftHeight - $parentNode->rightHeight) > 1) {
                var_dump('unbalansed', $parentNodePointer);
                return $parentNodePointer;
            }
            $thisNodePointer = $parentNodePointer;
            $parentNodePointer = $this->index[$thisNodePointer]->parent;
        }
    }
    private function balansing($unbalansingNodeKey)
    {
        if (
            $this->index[$this->pointer]->data <
            $this->index[$unbalansingNodeKey]->data
        ) {
            if (
                $this->index[$this->pointer]->data <
                $this->index[$this->index[$unbalansingNodeKey]->left]->data
            ) {
                //LL - symptom
                var_dump('LL - symptom');
                $leftKey = $this->index[$unbalansingNodeKey]->left;
                $leftRightKey = $this->index[$leftKey]->right;
                $parentKey = $this->index[$unbalansingNodeKey]->parent;
                // left become top
                $this->index[$leftKey]->parent = $parentKey;
                if (is_null($parentKey)) {
                    $this->root = $leftKey;
                } else {
                    if ($this->index[$parentKey]->left == $unbalansingNodeKey) {
                        $this->index[$parentKey]->left = $leftKey;
                    }
                    if ($this->index[$parentKey]->right == $unbalansingNodeKey) {
                        $this->index[$parentKey]->right = $leftKey;
                    }
                }
                // unbalansed become right for top
                $this->index[$leftKey]->right = $unbalansingNodeKey;
                $this->index[$unbalansingNodeKey]->parent = $leftKey;
                // right for top become left for unbalansed
                $this->index[$unbalansingNodeKey]->left = $leftRightKey;
                if (!is_null($leftRightKey)) {
                    $this->index[$leftRightKey]->parent = $unbalansingNodeKey;
                }
                return;
            }
            if (
                $this->index[$this->pointer]->data >
                $this->index[$this->index[$unbalansingNodeKey]->left]->data
            ) {
                //LR - symptom
                var_dump('LR - symptom');
            }
        }
        if (
            $this->index[$this->pointer]->data >
            $this->index[$unbalansingNodeKey]->data
        ) {
            if (
                $this->index[$this->pointer]->data >
                $this->index[$this->index[$unbalansingNodeKey]->right]->data
            ) {
                //RR - symptom
                var_dump('RR - symptom');
            }
            if (
                $this->index[$this->pointer]->data <
                $this->index[$this->index[$unbalansingNodeKey]->right]->data
            ) {
                //RL - symptom
                var_dump('RL - symptom');
            }
        }
    }
}
