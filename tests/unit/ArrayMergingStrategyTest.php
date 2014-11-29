<?php

use paslandau\ArrayUtility\ArrayPath\ArrayMergingStrategy;
use paslandau\ArrayUtility\ArrayPath\ArrayMergingStrategyInterface;
use paslandau\ArrayUtility\ArrayUtil;

require_once __DIR__."/../ArrayUtilityTestHelper.php";

class ArrayMergingStrategyTest extends PHPUnit_Framework_TestCase {

    public function test_ShouldMerge(){

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
            ArrayMergingStrategyInterface::STRATEGY_MERGE_RECURSIVE =>
                [
                    "expected" => [
                        "foo"=> [
                            "bar",
                            "baz"
                        ],
                        "baz"=> [
                            "0"=> $foo,
                            "4711"=> 42,
                            "test"=> [
                                "123"=> "123",
                                "124"=> "new number"
                            ],
                            "4712"=> $bar,
                            "4713"=> 50
                        ],
                        "only1"=> "only1",
                        "only2"=> "only2"
                    ]
                ],
            ArrayMergingStrategyInterface::STRATEGY_PLUS =>
                [
                    "expected" => [
                        "foo"=> "bar",
                        "baz"=> [
                            "0"=> $foo,
                            "4711"=> 42,
                            "test"=> [
                                "123"=> "123"
                            ]
                        ],
                        "only1"=> "only1",
                        "only2"=> "only2"
                    ],
                ],
            ArrayMergingStrategyInterface::STRATEGY_REPLACE =>
                [
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
                    ]
                ],
            ArrayMergingStrategyInterface::STRATEGY_REPLACE_RECURSIVE =>
                [
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
            $ams = new ArrayMergingStrategy($name);
            $res = $ams->merge($input,$input2);
//            echo "$name\n".str_replace([":","{","}"],["=>","[","]"],json_encode($arr,JSON_PRETTY_PRINT))."\n\n";
            $msg = "Result error on test $name:\n" . ArrayUtilityTestHelper::getArrStr($data["expected"], $res, $input,$input2);
            $this->assertTrue(ArrayUtil::equals($res, $data["expected"], true, true, true), $msg);
        }
    }
}
 