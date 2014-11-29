<?php
namespace paslandau\ArrayUtility;

use paslandau\ComparisonUtility\ArrayComperator;

class ArrayUtil
{

    /**
     * splice the array and maintain index association.
     * @param mixed[] &$arr [reference]
     * @param int $start [optional]. Default: 0.
     * @param int $length [optional]. Default: count($arr)-$start.
     * @return mixed[]
     */
    public static function asplice(array &$arr, $start = null, $length = null)
    {
        if($start === null) {
            $start = 0;
        }
        $slice = array_slice($arr, $start, $length, true);
        if($start < 0){
            $start = 0;
        }else{
            $start = count($slice);
        }
        $arr = array_slice($arr, $start, count($arr) - count($slice), true);
        return $slice;
    }

    /**
     * Sorts an multidimensional array by multiple criteria. Array is passed by reference!
     * Example:
     * Input:
     * $test = array(
     *  "id" => 1, "name" => "Pascal", "age" = "15",
     *  "id" => 3, "name" => "Mark", "age" = "25",
     *  "id" => 5, "name" => "Hugo", "age" = "55",
     *  "id" => 2, "name" => "Angus", "age" = "25"
     * );
     * ArrayMultiSort($test, array("id" => "asc"));
     * Result:
     *    array(
     *    "id" => 1, "name" => "Pascal", "age" = "15",
     *  "id" => 2, "name" => "Angus", "age" = "25"
     *  "id" => 3, "name" => "Mark", "age" = "25",
     *  "id" => 5, "name" => "Hugo", "age" = "55"
     * );
     * ArrayMultiSort($test, array("age" => "asc", "id" => "desc"));
     * Result:
     *    array(
     *    "id" => 1, "name" => "Pascal", "age" = "15",
     *  "id" => 3, "name" => "Mark", "age" = "25",
     *  "id" => 2, "name" => "Angus", "age" = "25"
     *  "id" => 5, "name" => "Hugo", "age" = "55"
     * );
     * @param array &$array
     * @param array $criteria
     */
    public static function sortMulti(array &$array, array $criteria)
    {
        $comparer = function ($first, $second) use ($criteria) {
            foreach ($criteria as $key => $orderType) {
                if ($first[$key] < $second[$key]) {
                    return $orderType === "asc" ? -1 : 1;
                } else if ($first[$key] > $second[$key]) {
                    return $orderType === "asc" ? 1 : -1;
                }
            }
            // all elements were equal
            return 0;
        };
        usort($array, $comparer);
        //TODO compare performance to array_multisort();
    }

    /**
     * @param array|null $arrCompare
     * @param array|null $arrExpected
     * @param bool $keysMustMatch [optional]. Default: true.
     * @param bool $ignoreOrder [optional]. Default: false.
     * @param bool $canContainMixedTypes [optional]. Default: false.
     * @param bool $canBeNull [optional]. Default: false.
     * @return bool
     */
    public static function equals(array $arrCompare = null, array $arrExpected = null, $keysMustMatch = null, $ignoreOrder = null, $canContainMixedTypes = null, $canBeNull = null){
        $c = new ArrayComperator(ArrayComperator::COMPARE_FUNCTION_EQUALS,$keysMustMatch,$ignoreOrder,$canContainMixedTypes,$canBeNull);
        return $c->compare($arrCompare,$arrExpected);
    }

    /**
     * @param array|null $needle
     * @param array|null $haystack
     * @param bool $keysMustMatch [optional]. Default: true.
     * @param bool $ignoreOrder [optional]. Default: false.
     * @param bool $canContainMixedTypes [optional]. Default: false.
     * @param bool $canBeNull [optional]. Default: false.
     * @return bool
     */
    public static function containsArray(array $needle = null, array $haystack = null, $keysMustMatch = null, $ignoreOrder = null, $canContainMixedTypes = null, $canBeNull = null){
        $c = new ArrayComperator(ArrayComperator::COMPARE_FUNCTION_CONTAINS,$keysMustMatch,$ignoreOrder,$canContainMixedTypes,$canBeNull);
        return $c->compare($needle,$haystack);
    }

    /**
     * Gets all ordered subsets of an array.
     * E.g. GetOrderedSubsets for [0,1,2,3,4] returns
     *
     *Window: 0
     *[empty]
     *
     *Window: 1
     *0
     *  1
     *    2
     *      3
     *        4
     *
     *Window: 2
     *0 1
     *0   2
     *0     3
     *0       4
     *  1 2
     *  1   3
     *  1     4
     *    2 3
     *   2   4
     *      3 4
     *
     *Window: 3
     *0 1 2
     *0 1   3
     *0 1     4
     *0   2 3
     *0   2   4
     *0     3 4
     *  1 2 3
     *  1 2   4
     *  1   3 4
     *    2 3 4
     *Window: 4
     *0 1 2 3
     *0 1 2   4
     *0 1   3 4
     *0   2 3 4
     *  1 2 3 4
     *Window: 5
     *0 1 2 3 4
     *Window: 6
     *[empty]
     * @param mixed[] $arr
     * @param int $window
     * @param bool $getKeys [optional]. Default: false. If true, the result contains the keys instead of the values of the input $arr.
     * @return int[][]
     */
    public static function getOrderedSubsets($arr, $window, $getKeys = false)
    {
        return self::_getOrderedSubsets($arr, $window, $getKeys, [], 0);
    }

    private static function _getOrderedSubsets($arr, $window, $getKeys, $refArr, $idx)
    {
        $max = count($arr);

        $result = array();
        if ($window > $max || $window < 1) {
            return $result;
        }
        for ($i = $idx; $i < $max; $i++) {
            $val = ($getKeys ? $i : $arr[$i]); // key or value in result?
            $newRefArr = array_merge($refArr, array(
                $val
            ));
            $next = ($i + 1);
            if (count($newRefArr) == $window) {
                $result [] = $newRefArr;
            } else {
                $subsets = self::_getOrderedSubsets($arr, $window, $getKeys, $newRefArr, $next);
                foreach ($subsets as $s) {
                    $result [] = $s;
                }
            }
        }
        return $result;
    }

    /**
     * Counts all values per column in the given multi dim $arr
     * Example:
     * $input = array(
     *    array("Name" => "Pascal", "gender" => "male"),
     *  array("Name" => "Susanne", "gender" => "female"),
     *  array("Name" => "Heinz", "gender" => "male"),
     * );
     * $res = self::MultiCountValues($input);
     * $res = array(
     *    "Name" => array(
     *        "Pascal" => 1,
     *        "Susanne" => 1,
     *        "Heinz" => 1,
     *  )
     *    "gender" => array(
     *        "male" => 2,
     *        "female" => 1,
     *  )
     * )
     *
     * @param mixed[][] $arr
     * @param string[] $keysToCount [optional]. Default: null. If set, only those columns are counted - otherwise all.
     * @param string $sumColumn [optional]. Default: null. If set, the value of this column is used instead of "1" for counting.
     * @return mixed[][]
     */
    public static function countElementsMulti($arr, array $keysToCount = null, $sumColumn = null)
    {
        $res = array();
        if ($keysToCount !== null) {
            $keysToCount = array_flip($keysToCount); // better performance on key lookups
        }
        foreach ($arr as $row) {
            $innerRes = null;
            foreach ($row as $key => $value) {
                if ($keysToCount !== null && !array_key_exists($key, $keysToCount)) {
                    continue;
                }
                if (!array_key_exists($key, $res)) {
                    $res[$key] = array();
                }
                if (!array_key_exists($value, $res[$key])) {
                    $res[$key][$value] = 0;
                }
                if ($sumColumn != null && array_key_exists($sumColumn, $row)) {
                    $res[$key][$value] += $row[$sumColumn];
                } else {
                    $res[$key][$value]++;
                }
            }
        }
        return $res;
    }

    /**
     * Makes sure each entry in the multidimensional array $arrToSort has the same keys in the same order
     * @param mixed[][] &$arrToSort [reference]. The array to be consolidated. E.g. [1 => ["name" => "Peter", "age" => 5], ["gender" => "male", "name" => "Markus"]]
     * @param mixed[] $expectedKeys [optional]. Default: null. The keys that have to be present in each line. E.g. ["name", "age"]. If null, self::getAllKeysMulti will be called in order to gell all keys.
     * @param mixed $defaultValue [optional]. Default: null. Optional value if the respective key is not present in the current line
     * @return void - ($arrToSort is a reference, output of the above example: ["name" => "Peter", "age" => 5], ["name" => "Markus", "age" => null]]
     */
    public static function consolidateMultiKeys(array &$arrToSort, $expectedKeys = null, $defaultValue = null)
    {
        if($expectedKeys === null){
            $expectedKeys = self::getAllKeysMulti($arrToSort);
        }
        foreach ($arrToSort as $key => $line) {
            $sortedLine = [];
            foreach ($expectedKeys as $akey) {
                if(array_key_exists($akey,$line)){
                    $sortedLine[$akey] = $line[$akey];
                }else{
                    $sortedLine[$akey] = $defaultValue;
                }
            }
            $arrToSort[$key] = $sortedLine;
        }
    }

    /**
     * Get all keys from the 2 dimensional array $multiArr.
     * @param mixed [][] $multiArr
     * @return mixed[]
     */
    public static function getAllKeysMulti(array $multiArr)
    {
        $allKeys = array();
        foreach ($multiArr as $m) {
            foreach ($m as $k => $v) {
                $allKeys[$k] = $k;
            }
        }
        return $allKeys;
    }

    /**
     * Transforms the given XML String into an array.
     * Caution: Attributes are ignored!
     * @see http://stackoverflow.com/a/20431742/413531
     * @see http://stackoverflow.com/a/2970701/413531 >> solve CDATA problems
     * @param string $xmlString
     * @return mixed[]
     */
    public static function xmlToArray($xmlString)
    {
        // todo - have a look at https://github.com/gaarf/XML-string-to-PHP-array to support attributes
        $xml = simplexml_load_string($xmlString, null, LIBXML_NOCDATA);
        $json = json_encode($xml);
        $array = json_decode($json, TRUE);
        return $array;
    }

    /**
     * Performs a binary search on the $arr according to the $bitmask.
     * @param mixed[] $arr
     * @param int[] $bitmask. May only contain "0" and "1"
     * @return mixed[]
     * @throws \Exception
     */
    public static function searchByBitmask(array $arr, array $bitmask)
    {
        $leftSlice = $arr;
        while (count($bitmask) > 0 && count($leftSlice) > 1) {
            $length = ceil(count($leftSlice) / 2);
            $rightSlice = array_splice($leftSlice, $length);
            $b = array_shift($bitmask);
            if ($b === 1) {
                $leftSlice = $rightSlice;
            } elseif ($b === null && count($bitmask) == 0) {
                $leftSlice = array_merge($leftSlice, $rightSlice);
            } elseif ($b !== 0) {
                throw new \InvalidArgumentException("'$b' is not a valid bit flag, use either '0' or '1'");
            }
        }
        return $leftSlice;
    }

    /**
     * @todo Find a better solution
     * @param $arr
     * @param int $level
     * @return mixed
     */
    public static function toString($arr,$level = 3){
        self::$uniqueObject = new \stdClass;
        $res = self::_toString($arr,$level);
        self::$uniqueObject = null;
        return $res;
    }


    private static $uniqueObject = null;
    /**
     * @param &$arr
     * @param int $level
     * @param int $indent
     * @return mixed
     */
    private static function _toString(&$arr,$level = 3,$indent = 0){
        $w = "";
        if($indent > 0) {
            $filled = array_fill(0, $indent, " ");
            $w = implode("", $filled);
        }

        $last = end($arr);
        if($last === self::$uniqueObject){
            return "{$w}[... recursion ...]";
        }

        if($level <= 0){
            return "{$w}[... max level reached ...]";
        }
        $level--;
        $result = [];

//        $keys = array_keys($arr);
//        foreach($keys as $key){
//            $val = &$arr[$key];
        foreach($arr as $key => &$val){
            if(is_array($val)){
                // mark parent array
                $arr[] = self::$uniqueObject;
                $indent += mb_strlen("{$key} => ");
                $str = "(Array) [\n".self::_toString($val,$level,$indent)."\n{$w}]";
                //remove mark
                array_pop($arr);
            }
            elseif(is_object($val)){
                if(method_exists($val,"__toString")){
                    $str = $val->__toString();
                }else{
                    $str = "[Object of class (".get_class($val).") has no __toString() method]";
                }
            }elseif(is_bool($val)){
                $str = $val ? "[true]" : "[false]";
            }elseif($val === null){
                $str = "[null]";
            }else{
                $str = $val;
            }
            $result[] = "{$w}$key => $str";
        }
        return implode("\n",$result);
    }

//    /**
//     * @todo not tested
//     * @see http://stackoverflow.com/a/14752228/413531
//     * @param array $array
//     * @param array $alreadySeen
//     * @return bool
//     */
//    public static function isRecursive(array &$array, array &$alreadySeen = array())
//    {
//        static $uniqueObject;
//        if (!$uniqueObject) {
//            $uniqueObject = new \stdClass;
//        }
//
//        $alreadySeen[] = &$array;
//
//        foreach ($array as &$item) {
//            if (!is_array($item)) {
//                continue;
//            }
//
//            $item[] = $uniqueObject;
//            $recursionDetected = false;
//            foreach ($alreadySeen as $candidate) {
//                if (end($candidate) === $uniqueObject) {
//                    $recursionDetected = true;
//                    break;
//                }
//            }
//
//            array_pop($item);
//
//            if ($recursionDetected || self::isRecursive($item, $alreadySeen)) {
//                return true;
//            }
//        }
//
//        return false;
//    }
}