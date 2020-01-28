<?php


namespace Morebec\YDB\Utils;

/**
 * A Stack is a “last in, first out” or “LIFO” collection that only allows access to the value at the
 * top of the structure and iterates in that order, destructively.
 * Implemented using php arrays. It has no enforced limit
 * TODO Move to Morebec/Collections
 */
class Stack
{
    private $elements;

    public function __construct(array $elements = [])
    {
        $this->elements = $elements;
    }

    /**
     * Adds an item to the top of the stack
     * @param mixed $e
     */
    public function push($e) : void
    {
        $this->elements[] = $e;
    }

    /**
     * Removes the item on the top of the stack
     */
    public function pop()
    {
        return array_pop($this->elements);
    }

    /**
     * Looks at the item on the top of the stack
     * without removing it
     * @return mixed
     */
    public function peek() {
        $copy = $this->copy();
        return end($copy);
    }

    public function isEmpty() {
        return empty($this->elements);
    }

    /**
     * @return array
     */
    private function copy(): array
    {
        return array_values($this->elements);
    }
}