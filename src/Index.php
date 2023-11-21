<?php

namespace App;

class Index
{
    public ?int $root = null;
    private ?int $pointer = null;
    public array $index = [];
    public string $indexKey;
    private int $counter = 0;
    private int $maxHeight = 0;
    private int $height = 0;
    private ?int $unbalansedNodeKey = null;

    public function addElement($key, $value)
    {
        if (is_null($this->root)) {
            $this->root = $key;
            $this->addNode($key, $value);
            return $this;
        }
        $this->pointer = $this->root;
        while (true) {
            if ($value == $this->index[$this->pointer]->data) {
                return $this;
            }
            if ($value > $this->index[$this->pointer]->data) {
                if (is_null($this->index[$this->pointer]->right)) {
                    $this->index[$this->pointer]->right = $key;
                    $this->addNode($key, $value);
                    $this->findUnbalancedNodeUp();
                    if (!is_null($this->unbalansedNodeKey)) {
                        $this->balansing($this->unbalansedNodeKey);
                    }
                    return $this;
                }
                $this->pointer = $this->index[$this->pointer]->right;
                continue;
            }
            if ($value < $this->index[$this->pointer]->data) {
                if (is_null($this->index[$this->pointer]->left)) {
                    $this->index[$this->pointer]->left = $key;
                    $this->addNode($key, $value);
                    $this->findUnbalancedNodeUp();
                    if (!is_null($this->unbalansedNodeKey)) {
                        $this->balansing($this->unbalansedNodeKey);
                    }
                    return $this;
                }
                $this->pointer = $this->index[$this->pointer]->left;
                continue;
            }
        }
    }
    private function addNode($key, $value)
    {
        $this->index[$key] = new class {
            public $data;
            public $left;
            public $right;
            public $parent;
        };
        $this->index[$key]->data = $value;
        $this->index[$key]->left = null;
        $this->index[$key]->right = null;
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
        $this->unbalansedNodeKey = null;
        $this->traversal($this->root);
    }
    private function balansing($unbalansedNodeKey)
    {
        if (
            $this->index[$this->pointer]->data <
            $this->index[$unbalansedNodeKey]->data
        ) {
            if (
                $this->index[$this->pointer]->data <
                $this->index[$this->index[$unbalansedNodeKey]->left]->data
            ) {
                //LL - symptom
                $this->llRotate($unbalansedNodeKey);
                return;
            }
            if (
                $this->index[$this->pointer]->data >
                $this->index[$this->index[$unbalansedNodeKey]->left]->data
            ) {
                //LR - symptom

                $this->lrRotate($unbalansedNodeKey);
                return;
            }
        }
        if (
            $this->index[$this->pointer]->data >
            $this->index[$unbalansedNodeKey]->data
        ) {
            if (
                $this->index[$this->pointer]->data >
                $this->index[$this->index[$unbalansedNodeKey]->right]->data
            ) {
                //RR - symptom
                $this->rrRotate($unbalansedNodeKey);
                return;
            }
            if (
                $this->index[$this->pointer]->data <
                $this->index[$this->index[$unbalansedNodeKey]->right]->data
            ) {
                //RL - symptom
                $this->rlRotate($unbalansedNodeKey);
                return;
            }
        }
    }
    private function llRotate($nodeKey)
    {
        $leftKey = $this->index[$nodeKey]->left;
        $leftRightKey = $this->index[$leftKey]->right;
        $parentKey = $this->index[$nodeKey]->parent;
        // left become top
        $this->index[$leftKey]->parent = $parentKey;
        if (is_null($parentKey)) {
            $this->root = $leftKey;
        } else {
            if ($this->index[$parentKey]->left == $nodeKey) {
                $this->index[$parentKey]->left = $leftKey;
            }
            if ($this->index[$parentKey]->right == $nodeKey) {
                $this->index[$parentKey]->right = $leftKey;
            }
        }
        // unbalansed become right for top
        $this->index[$leftKey]->right = $nodeKey;
        $this->index[$nodeKey]->parent = $leftKey;
        // right for top become left for unbalansed
        $this->index[$nodeKey]->left = $leftRightKey;
        if (!is_null($leftRightKey)) {
            $this->index[$leftRightKey]->parent = $nodeKey;
        }
        return;
    }
    private function lrRotate($nodeKey)
    {
        $leftKey = $this->index[$nodeKey]->left;
        $leftRightKey = $this->index[$leftKey]->right;
        $parentKey = $this->index[$nodeKey]->parent;
        // left right become top
        $this->index[$leftRightKey]->parent = $parentKey;
        if (is_null($parentKey)) {
            $this->root = $leftRightKey;
        } else {
            if ($this->index[$parentKey]->left == $nodeKey) {
                $this->index[$parentKey]->left = $leftRightKey;
            }
            if ($this->index[$parentKey]->right == $nodeKey) {
                $this->index[$parentKey]->right = $leftRightKey;
            }
        }
        //right for leftRight bocome left for unbalansed
        $rightForLeftRightKey = $this->index[$leftRightKey]->right;
        $leftForLeftRightKey = $this->index[$leftRightKey]->left;
        $this->index[$nodeKey]->left = $rightForLeftRightKey;
        if (!is_null($rightForLeftRightKey)) {
            $this->index[$rightForLeftRightKey]->parent = $nodeKey;
        }
        //connect leftRight with unbalansed
        $this->index[$leftRightKey]->right = $nodeKey;
        $this->index[$nodeKey]->parent = $leftRightKey;
        //connect left with leftRight
        $this->index[$leftRightKey]->left = $leftKey;
        $this->index[$leftKey]->parent = $leftRightKey;
        //connect left with leftForLeftRight
        $this->index[$leftKey]->right = $leftForLeftRightKey;
        if (!is_null($leftForLeftRightKey)) {
            $this->index[$leftForLeftRightKey]->parent = $leftKey;
        }
        return;
    }
    private function rrRotate($nodeKey)
    {
        $rightKey = $this->index[$nodeKey]->right;
        $rightLeftKey = $this->index[$rightKey]->left;
        $parentKey = $this->index[$nodeKey]->parent;
        // right become top
        $this->index[$rightKey]->parent = $parentKey;
        if (is_null($parentKey)) {
            $this->root = $rightKey;
        } else {
            if ($this->index[$parentKey]->left == $nodeKey) {
                $this->index[$parentKey]->left = $rightKey;
            }
            if ($this->index[$parentKey]->right == $nodeKey) {
                $this->index[$parentKey]->right = $rightKey;
            }
        }
        // unbalansed become left for top
        $this->index[$rightKey]->left = $nodeKey;
        $this->index[$nodeKey]->parent = $rightKey;
        // left for top become right for unbalansed
        $this->index[$nodeKey]->right = $rightLeftKey;
        if (!is_null($rightLeftKey)) {
            $this->index[$rightLeftKey]->parent = $nodeKey;
        }
        return;
    }
    private function rlRotate($nodeKey)
    {
        $rightKey = $this->index[$nodeKey]->right;
        $rightLeftKey = $this->index[$rightKey]->left;
        $parentKey = $this->index[$nodeKey]->parent;
        // right left become top
        $this->index[$rightLeftKey]->parent = $parentKey;
        if (is_null($parentKey)) {
            $this->root = $rightLeftKey;
        } else {
            if ($this->index[$parentKey]->left == $nodeKey) {
                $this->index[$parentKey]->left = $rightLeftKey;
            }
            if ($this->index[$parentKey]->right == $nodeKey) {
                $this->index[$parentKey]->right = $rightLeftKey;
            }
        }
        //left for rightLeft bocome right for unbalansed
        $leftForRightLeftKey = $this->index[$rightLeftKey]->left;
        $rightForRightLeftKey = $this->index[$rightLeftKey]->right;
        $this->index[$nodeKey]->right = $leftForRightLeftKey;
        if (!is_null($leftForRightLeftKey)) {
            $this->index[$leftForRightLeftKey]->parent = $nodeKey;
        }
        //connect rightLeft with unbalansed
        $this->index[$rightLeftKey]->left = $nodeKey;
        $this->index[$nodeKey]->parent = $rightLeftKey;
        //connect right with rightLeft
        $this->index[$rightLeftKey]->right = $rightKey;
        $this->index[$rightKey]->parent = $rightLeftKey;
        //connect right with rightForRightLeft
        $this->index[$rightKey]->left = $rightForRightLeftKey;
        if (!is_null($rightForRightLeftKey)) {
            $this->index[$rightForRightLeftKey]->parent = $rightKey;
        }
        return;
    }
    private function traversal($nodeKey)
    {
        if (!is_null($this->index[$nodeKey]->left)) {
            $this->traversal($this->index[$nodeKey]->left);
        }
        if (!is_null($this->index[$nodeKey]->right)) {
            $this->traversal($this->index[$nodeKey]->right);
        }
        if (is_null($this->index[$nodeKey]->left)) {
            $leftDeep = 0;
        } else {
            $this->height = 0;
            $this->maxHeight = 0;
            $this->maxDeep($this->index[$nodeKey]->left);
            $leftDeep = $this->maxHeight + 1;
        }
        if (is_null($this->index[$nodeKey]->right)) {
            $rightDeep = 0;
        } else {
            $this->height = 0;
            $this->maxHeight = 0;
            $this->maxDeep($this->index[$nodeKey]->right);
            $rightDeep = $this->maxHeight + 1;
        }
        if (abs($rightDeep - $leftDeep) > 1) {
            $this->unbalansedNodeKey = $nodeKey;
        }
    }
    private function maxDeep($nodeKey)
    {
        if (!is_null($this->index[$nodeKey]->left)) {
            $this->height++;
            if ($this->height > $this->maxHeight) {
                $this->maxHeight = $this->height;
            }
            $this->maxDeep($this->index[$nodeKey]->left);
        }
        if (!is_null($this->index[$nodeKey]->right)) {
            $this->height++;
            if ($this->height > $this->maxHeight) {
                $this->maxHeight = $this->height;
            }
            $this->maxDeep($this->index[$nodeKey]->right);
        }
        $this->height--;
    }
    public function setIndexKey($indexKey)
    {
        $this->indexKey = $indexKey;
    }
}
