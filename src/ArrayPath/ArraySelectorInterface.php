<?php
namespace paslandau\ArrayUtility\ArrayPath;


interface ArraySelectorInterface {

    /**
     * Gets the element specified by $path.
     * @param ArrayPathInterface $path
     * @return mixed
     */
    public function getElement(ArrayPathInterface $path);

    /**
     * Set $value at the position specified by $path.
     * @param ArrayPathInterface $path
     * @param mixed $value
     * @return void
     */
    public function setElement(ArrayPathInterface $path, $value);

    /**
     * Merges $value with the element specified by $path.
     * @param ArrayPathInterface $path
     * @param mixed $value
     * @param ArrayMergingStrategyInterface $mergingStrategy
     * @return void
     */
    public function merge(ArrayPathInterface $path, $value, ArrayMergingStrategyInterface $mergingStrategy);

    /**
     * Returns the array
     * @return array
     */
    public function getArray();
} 