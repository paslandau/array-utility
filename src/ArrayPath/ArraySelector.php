<?php

namespace paslandau\ArrayUtility\ArrayPath;

class ArraySelector implements ArraySelectorInterface{
    /**
     * @var array
     */
    private $arr;

    /**
     * @param mixed[] $arr
     * @throws \InvalidArgumentException
     */
    function __construct(array $arr)
    {
        if(!is_array($arr)){
            throw new \InvalidArgumentException("arr has to be an array");
        }
        $this->arr = $arr;
    }

    /**
     * Gets the element specified by $path.
     * @param ArrayPathInterface $path
     * @throws \UnexpectedValueException
     * @return mixed - Caution: the element will be returned as reference!
     */
    public function &getElement(ArrayPathInterface $path)
    {
        return $this->_getElement($path,false);
    }

    /**
     * Gets the element specified by $path.
     * @param ArrayPathInterface $path
     * @param bool $getParent. If true, ignores the las part of $path
     * @return mixed - Caution: the element will be returned as reference!
     */
    private function &_getElement(ArrayPathInterface $path, $getParent)
    {
        $cur = &$this->arr;
        if($path->count() == 0 && $getParent){
            throw new \UnexpectedValueException("Path is empty, cannot select parent");
        }

        while($path->valid()){
            $part = $path->current();

            $path->next();
            if(!$path->valid() && $getParent){
                break;
            }

            if(!is_array($cur)){
                throw new \UnexpectedValueException("The currently selected element is not an array - cannot go deeper [key: '$part']");
            }
            if(!array_key_exists($part, $cur)){
                throw new \UnexpectedValueException("The key '$part' does not exits.");
            }
            $cur = &$cur[$part];
        }
        return $cur;
    }


    /**
     * Set $value at the position specified by $path.
     * @param ArrayPathInterface $path
     * @param mixed $value
     * @return void
     */
    public function setElement(ArrayPathInterface $path, $value)
    {
        $cur = &$this->_getElement($path,true);
        $key = $path->end();
        $cur[$key] = $value;
    }

    /**
     * Merges $value with the element specified by $path.
     * @param ArrayPathInterface $path
     * @param mixed $value
     * @param ArrayMergingStrategyInterface $mergingStrategy
     * @return void
     */
    public function merge(ArrayPathInterface $path, $value, ArrayMergingStrategyInterface $mergingStrategy)
    {
        if(!is_array($value)){
            $value = [$value];
        }
        $cur = &$this->getElement($path);
        $cur = $mergingStrategy->merge($cur,$value);
    }

    /**
     * Returns the array
     * @return array
     */
    public function getArray()
    {
        return $this->arr;
    }
}