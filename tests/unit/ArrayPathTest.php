<?php

use paslandau\ArrayUtility\ArrayPath\ArrayPath;
use paslandau\ArrayUtility\ArrayUtil;

require_once __DIR__."/../ArrayUtilityTestHelper.php";

class ArrayPathTest extends PHPUnit_Framework_TestCase {

    public function test_ParseTest(){
        $tests = [
            "escaped-string-char" =>
                [
                    "path" => '["\"foo\""]',
                    "expected" => ["\"foo\""]
                ],
            "empty" =>
                [
                    "path" => "",
                    "expected" => []
                ],
            "string-key-double-quote" =>
                [
                    "path" => '["foo"]',
                    "expected" => ["foo"]
                ],
            "null" =>
                [
                    "path" => "[null]",
                    "expected" => [0]
                ],
            "true-false" =>
                [
                    "path" => "[true][false]",
                    "expected" => [1, 0]
                ],
            "combined" =>
                [
                    "path" => '["foo"][0][true][null][1]',
                    "expected" => ["foo",0,1,0,1]
                ],
        ];

        foreach ($tests as $name => $data) {
            $path = new ArrayPath($data["path"]);
            $res = $path->getParsedArrayPath();
            $this->assertTrue(ArrayUtil::equals($res, $data["expected"], false, true,true), "Result error on test $name:\n" . ArrayUtilityTestHelper::getArrStr($data["expected"], $res, $data["path"]));
        }

        $tests = [
            "empty" =>
                [
                    "path" => "[]"
                ],
            "string-key-no-quote" =>
                [
                    "path" => "[foo]"
                ],
            "string-key-single-quote" =>
                [
                    "path" => "['foo']"
                ],
            "unescaped-string-char" =>
                [
                    "path" => '["foo"foo"]'
                ],
        ];

        foreach ($tests as $name => $data) {
            try{
                new ArrayPath($data["path"]);
            }catch(Exception $e){
//                echo $e->getMessage()."\n";
                $is = ($e instanceof UnexpectedValueException);
                $eS = $e === null ? "null" : get_class($e);
                $this->assertTrue($is,"Expected UnexpectedValueException got: $eS for ".$data["path"]);
            }
        }
    }
}
 