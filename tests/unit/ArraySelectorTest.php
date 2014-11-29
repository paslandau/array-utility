<?php

use paslandau\ArrayUtility\ArrayPath\ArrayMergingStrategy;
use paslandau\ArrayUtility\ArrayPath\ArrayMergingStrategyInterface;
use paslandau\ArrayUtility\ArrayPath\ArrayPath;
use paslandau\ArrayUtility\ArrayPath\ArraySelector;
use paslandau\ArrayUtility\ArrayUtil;

require_once __DIR__."/../ArrayUtilityTestHelper.php";

class ArraySelectorTest extends PHPUnit_Framework_TestCase {

    public function test_SetGetElements(){

        $foo = new stdClass();
        $foo->foo = "foo";

        $baz = [
            0 => $foo,
            4711 => 42,
            "test" => [
                "123" => "123"
            ]
        ];

        $input = [
            "foo" => "bar",
            "baz" => $baz
        ];

        $tests = [
            "select-string" =>
                [
                    "path" => '["foo"]',
                    "expected" => "bar",
                    "set" => "baz"
                ],
            "empty" =>
                [
                    "path" => "",
                    "expected" => $input
                ],
            "select-array" =>
                [
                    "path" => '["baz"]',
                    "expected" => $baz,
                    "set" => 0
                ],
            "select-int" =>
                [
                    "path" => '["baz"][4711]',
                    "expected" => $baz[4711],
                    "set" => "setting"
                ],
            "select-object" =>
                [
                    "path" => '["baz"][0]',
                    "expected" => $foo,
                    "set" => "setting"
                ],
            "select-deep" =>
                [
                    "path" => '["baz"]["test"]["123"]',
                    "expected" => "123",
                    "set" => "setting"
                ],
        ];

        foreach ($tests as $name => $data) {
            $path = new ArrayPath($data["path"]);
            $inputCpy = $input;
            $selector = new ArraySelector($inputCpy);
            $res = $selector->getElement($path);
            $msg = "Result error on test $name:\n" . ArrayUtilityTestHelper::getArrStr($data["expected"], $res, $data["path"]);
            if(is_array($data["expected"])) {
                $this->assertTrue(ArrayUtil::equals($res, $data["expected"], true, true, true), $msg);
            }else{
                $this->assertEquals($data["expected"], $res, $msg);
            }
            if(!array_key_exists("set",$data)){
                continue;
            }
            # test set
            $selector->setElement($path,$data["set"]);
            $res = $selector->getElement($path);
            $msg = "Result error after setting on test $name:\n" . ArrayUtilityTestHelper::getArrStr($data["set"], $res, $data["path"]);
            if(is_array($data["set"])) {
                $this->assertTrue(ArrayUtil::equals($res, $data["set"], true, true, true), $msg);
            }else{
                $this->assertEquals($data["set"], $res, $msg);
            }
        }

        $tests = [
            "set-empty" =>
                [
                    "path" => "",
                    "set" => "foo"
                ],
            "get-too-long" =>
                [
                    "path" => '["baz"]["test"]["123"]["too-long"]',
                ],
            "get-wrong" =>
                [
                    "path" => '["key-does-not-exist"]'
                ],
        ];

        foreach ($tests as $name => $data) {
            try{
                $path = new ArrayPath($data["path"]);
                $inputCpy = $input;
                $selector = new ArraySelector($inputCpy);
                $selector->getElement($path);
                $selector->setElement($path,$data["set"]);
            }catch(Exception $e){
//                echo $e->getMessage()."\n";
                $is = ($e instanceof UnexpectedValueException);
                $eS = $e === null ? "null" : get_class($e);
                $this->assertTrue($is,"Expected UnexpectedValueException got: $eS for ".$data["path"]);
            }
        }
    }

    public function test_Merge(){
        $foo = new stdClass();
        $foo->foo = "foo";

        $baz = [
            0 => $foo,
            4711 => 42,
            "test" => [
                "123" => "123"
            ]
        ];

        $input = [
            "foo" => "bar",
            "baz" => $baz,
            "only1" => "only1",
        ];

        $bar = new stdClass();
        $bar->bar = "bar";

        $baz2 = [
            0 => $bar,
            4711 => 50,
            "test" => [
                "123" => "new number"
            ]
        ];

        $input2 = [
            "foo" => "baz",
            "baz" => $baz2,
            "only2" => "only2",
        ];

        $tests = [
            ArrayMergingStrategyInterface::STRATEGY_MERGE =>
                [
                    "path" => "",
                    "expected" => [
                        "foo"=> "baz",
                        "baz"=> [
                            "0"=> $bar,
                            "4711"=> 50,
                            "test"=> [
                                "123"=> "new number"
                            ]
                        ],
                        "only1"=> "only1",
                        "only2"=> "only2"
                    ],
                ],
        ];
        foreach ($tests as $name => $data) {
            $path = new ArrayPath($data["path"]);
            $selector = new ArraySelector($input);
            $strategy = new ArrayMergingStrategy($name);
            $selector->merge($path,$input2,$strategy);
            $res = $selector->getArray();
            $msg = "Result error on test $name:\n" . ArrayUtilityTestHelper::getArrStr($data["expected"], $res, $input,$input2);
            $this->assertTrue(ArrayUtil::equals($res, $data["expected"], true, true, true), $msg);
        }
    }
}
 