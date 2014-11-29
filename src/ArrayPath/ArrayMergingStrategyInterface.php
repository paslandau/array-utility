<?php

namespace paslandau\ArrayUtility\ArrayPath;


interface ArrayMergingStrategyInterface {
    const STRATEGY_REPLACE = "replace";
    const STRATEGY_REPLACE_RECURSIVE = "replace_recursive";
    const STRATEGY_PLUS = "+";
    const STRATEGY_MERGE = "merge";
    const STRATEGY_MERGE_RECURSIVE = "merge_recursive";

    /**
     * Merges two arrays
     * @param array $arr1
     * @param array $arr2
     * @return mixed
     */
    public function merge(array $arr1, array $arr2);
} 