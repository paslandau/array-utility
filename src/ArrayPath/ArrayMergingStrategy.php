<?php

namespace paslandau\ArrayUtility\ArrayPath;

class ArrayMergingStrategy implements ArrayMergingStrategyInterface{
    /**
     * @var string
     */
    private $strategy;

    /**
     * @param string $strategy
     */
    function __construct($strategy)
    {
        $types = (new \ReflectionClass(__CLASS__))->getConstants();
        if(!in_array($strategy, $types)){
            throw new \InvalidArgumentException("strategy '$strategy' is unknown. Possible values: ".implode(", ",$types));
        }
        $this->strategy = $strategy;
    }

    /**
     * Merges two arrays
     * @param array $arr1
     * @param array $arr2
     * @return array
     */
    public function merge(array $arr1, array $arr2)
    {
        switch($this->strategy){
            case self::STRATEGY_MERGE:{
                return array_merge($arr1, $arr2);
            }
            case self::STRATEGY_PLUS:{
                return $arr1 + $arr2;
            }
            case self::STRATEGY_MERGE_RECURSIVE:{
                return array_merge_recursive($arr1, $arr2);
            }
            case self::STRATEGY_REPLACE:{
                return array_replace($arr1, $arr2);
            }
            case self::STRATEGY_REPLACE_RECURSIVE:{
                return array_replace_recursive($arr1, $arr2);
            }
        }
        $types = (new \ReflectionClass(__CLASS__))->getConstants();
        throw new \InvalidArgumentException("strategy '$this->strategy' is unknown. Possible values: ".implode(", ",$types));
    }

    /**
     * @return string
     */
    public function getStrategy()
    {
        return $this->strategy;
    }
}