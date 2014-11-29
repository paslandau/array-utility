<?php

class ArrayUtilityTestHelper {
    public static function getArrStr($expected, $actual, $input1 = null, $input2 = null){
        $s = [];
        if($input1 !== null){
            $s[] = "Input1: ".json_encode($input1);
        }
        if($input2 !== null){
            $s[] = "Input2: ".json_encode($input2);
        }
        $s[] = "Expected: ".json_encode($expected);
        $s[] = "Actual  : ".json_encode($actual);
        return implode("\n",$s);
    }
} 