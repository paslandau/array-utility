<?php

use paslandau\ArrayUtility\ArrayUtil;

require_once __DIR__."/../ArrayUtilityTestHelper.php";

class ArrayUtilTest extends PHPUnit_Framework_TestCase {

    public function test_asplice(){
        $arr = [
            "foo" => "bar",
            "baz" => 42,
            4711 => "test",
        ];

        // splice default [all]
        $name = "default";
        $input = $arr;
        $inputCopy = $input;
        $expected = $arr;
        $expectedInput = [];
        $full = ArrayUtil::asplice($input);
        $this->assertTrue(ArrayUtil::equals($full,$expected,true),"Result error at $name:\n".ArrayUtilityTestHelper::getArrStr($expected,$full,$inputCopy));
        $this->assertTrue(ArrayUtil::equals($input,$expectedInput,true),"Input error at $name:\n".ArrayUtilityTestHelper::getArrStr($expectedInput,$input,$inputCopy));

        // splice part
        $name = "splice part";
        $input = $arr;
        $inputCopy = $input;
        $start = 0;
        $length = 2;
        $expected = array_slice($input,$start,$length,true);
        $expectedInput = array_slice($input,$length,null,true);
        $full = ArrayUtil::asplice($input,$start,$length);
        $this->assertTrue(ArrayUtil::equals($full,$expected,true),"Result error at $name:\n".ArrayUtilityTestHelper::getArrStr($expected,$full,$inputCopy));
        $this->assertTrue(ArrayUtil::equals($input,$expectedInput,true),"Input error at $name:\n".ArrayUtilityTestHelper::getArrStr($expectedInput,$input,$inputCopy));

        // splice negative
        $name = "splice negative";
        $input = $arr;
        $inputCopy = $input;
        $start = -2;
        $length = null;
        $expected = array_slice($input,$start,$length,true);
        $expectedInput = array_slice($input,0,count($input)-count($expected),true);
        $full = ArrayUtil::asplice($input,$start,$length);
        $this->assertTrue(ArrayUtil::equals($full,$expected,true),"Result error at $name:\n".ArrayUtilityTestHelper::getArrStr($expected,$full,$inputCopy));
        $this->assertTrue(ArrayUtil::equals($input,$expectedInput,true),"Input error at $name:\n".ArrayUtilityTestHelper::getArrStr($expectedInput,$input,$inputCopy));

        // splice negative
        $name = "splice over length";
        $input = $arr;
        $inputCopy = $input;
        $start = 1;
        $length = 1000;
        $expected = array_slice($input,$start,$length,true);
        $expectedInput = array_slice($input,count($expected),count($input)-count($expected),true);
        $full = ArrayUtil::asplice($input,$start,$length);
        $this->assertTrue(ArrayUtil::equals($full,$expected,true),"Result error at $name:\n".ArrayUtilityTestHelper::getArrStr($expected,$full,$inputCopy));
        $this->assertTrue(ArrayUtil::equals($input,$expectedInput,true),"Input error at $name:\n".ArrayUtilityTestHelper::getArrStr($expectedInput,$input,$inputCopy));
    }

    public function test_sortMulti()
    {
        $input = array(
            ["id" => 1, "name" => "Pascal", "age" => "15"],
            ["id" => 3, "name" => "Mark", "age" => "25"],
            ["id" => 5, "name" => "Hugo", "age" => "55"],
            ["id" => 2, "name" => "Angus", "age" => "25"]
        );

        $expected = [
            ["id" => 1, "name" => "Pascal", "age" => "15"],
            ["id" => 2, "name" => "Angus", "age" => "25"],
            ["id" => 3, "name" => "Mark", "age" => "25"],
            ["id" => 5, "name" => "Hugo", "age" => "55"],
        ];

        $criteria = ["id" => "asc"];
        $inputCopy = $input;
        ArrayUtil::sortMulti($inputCopy,$criteria);
        $this->assertTrue(ArrayUtil::equals($inputCopy,$expected,true),"Result error at ".json_encode($criteria).":\n".ArrayUtilityTestHelper::getArrStr($expected,$inputCopy,$input));

        $expected = [
            ["id" => 5, "name" => "Hugo", "age" => "55"],
            ["id" => 3, "name" => "Mark", "age" => "25"],
            ["id" => 2, "name" => "Angus", "age" => "25"],
            ["id" => 1, "name" => "Pascal", "age" => "15"],
        ];

        $criteria = ["age" => "desc", "id" => "desc"];
        $inputCopy = $input;
        ArrayUtil::sortMulti($inputCopy,$criteria);
        $this->assertTrue(ArrayUtil::equals($inputCopy,$expected,true),"Result error at ".json_encode($criteria).":\n".ArrayUtilityTestHelper::getArrStr($expected,$inputCopy,$input));
    }

    public function test_getOrderedSubsets(){
        $input = ["_0","_1","_2","_3"];

        // get keys
        $expecteds = [
            0 => [],
            1 => [
                ["_0"],["_1"],["_2"],["_3"]
            ],
            2 => [
                ["_0","_1"],["_0","_2"],["_0","_3"],
                ["_1","_2"],["_1","_3"],
                ["_2","_3"]
            ],
            3 => [
                ["_0","_1","_2"],["_0","_1","_3"],["_0","_2","_3"],
                ["_1","_2","_3"],
            ],
            4 => [
                ["_0","_1","_2","_3"]
            ],
            5 => [],
            -1 => [],
            100 => []
        ];
        foreach($expecteds as $window => $expected){
            $res = ArrayUtil::getOrderedSubsets($input,$window,false);
            $this->assertTrue(ArrayUtil::equals($res,$expected,false),"Result error at window $window:\n".ArrayUtilityTestHelper::getArrStr($expected,$res,$input));
        }

        // get keys
        $expecteds = [
            0 => [],
            1 => [
                [0],[1],[2],[3]
            ],
            2 => [
                [0,1],[0,2],[0,3],
                [1,2],[1,3],
                [2,3]
            ],
            3 => [
                [0,1,2],[0,1,3],[0,2,3],
                [1,2,3],
            ],
            4 => [
                [0,1,2,3]
                ],
            5 => [],
            100 => [],
            -1 => []
        ];
        foreach($expecteds as $window => $expected){
            $res = ArrayUtil::getOrderedSubsets($input,$window,true);
            $this->assertTrue(ArrayUtil::equals($res,$expected,false),"Result error at getting keys for window $window:\n".ArrayUtilityTestHelper::getArrStr($expected,$res,$input));
        }
    }

    public function test_countElementsMulti()
    {
        $input = [
            ["Name" => "Pascal", "gender" => "male", "Anzahl" => "50"],
            ["Name" => "Susanne", "gender" => "female", "Anzahl" => "70"],
            ["Name" => "Heinz", "gender" => "male", "Anzahl" => "50"],
        ];

        $tests = [
            "default" =>
                [
                    "keys" => null,
                    "sum" => null,
                    "expected" =>
                        [
                            "Name" => [
                                "Pascal" => 1,
                                "Susanne" => 1,
                                "Heinz" => 1,
                            ],
                            "gender" => [
                                "male" => 2,
                                "female" => 1,
                            ],
                            "Anzahl" => [
                                "50" => 2,
                                "70" => 1,
                            ]
                        ],
                ],
            "2 keys" =>
                [
                    "keys" => ["Name","gender"],
                    "sum" => null,
                    "expected" =>
                        [
                            "Name" => [
                                "Pascal" => 1,
                                "Susanne" => 1,
                                "Heinz" => 1,
                            ],
                            "gender" => [
                                "male" => 2,
                                "female" => 1,
                            ],
                        ],
                ],
            "count" =>
                [
                    "keys" => null,
                    "sum" => "Anzahl",
                    "expected" =>
                        [
                            "Name" => [
                                "Pascal" => 50,
                                "Susanne" => 70,
                                "Heinz" => 50,
                            ],
                            "gender" => [
                                "male" => 100,
                                "female" => 70,
                            ],
                            "Anzahl" => [
                                "50" => 100,
                                "70" => 70,
                            ]
                        ],
                ],
            "1 key and count" =>
                [
                    "keys" => ["gender"],
                    "sum" => "Anzahl",
                    "expected" =>
                        [
                            "gender" => [
                                "male" => 100,
                                "female" => 70,
                            ],
                        ],
                ],
        ];

        foreach ($tests as $name => $data){
            $res = ArrayUtil::countElementsMulti($input, $data["keys"], $data["sum"]);
            $this->assertTrue(ArrayUtil::equals($res, $data["expected"], true), "Result error on test $name:\n" . ArrayUtilityTestHelper::getArrStr($data["expected"], $res, $input));
        }
    }

    public function test_getAllKeysMulti(){
        $input = [
            ["Name" => "Pascal"],
            ["Name" => "Susanne", "gender" => "female", "Anzahl" => "70"],
            ["Name" => "Heinz", "gender" => "male", "Anzahl" => "50"],
            ["Xpath" => "foo",1=>"",2=>"",3=>""],
        ];

        $tests = [
            "default" =>
                [
                    "expected" =>
                        [
                            "Name","gender","Anzahl","Xpath",1,2,3
                        ],
                ],
        ];

        foreach ($tests as $name => $data){
            $res = ArrayUtil::getAllKeysMulti($input);
            $this->assertTrue(ArrayUtil::equals($res, $data["expected"], false), "Result error on test $name:\n" . ArrayUtilityTestHelper::getArrStr($data["expected"], $res, $input));
        }
    }

    public function test_consolidateMultiKeys(){
        $input = [
            ["Name" => "Pascal"],
            ["Name" => "Susanne", "gender" => "female", "Anzahl" => "70"],
            ["Name" => "Heinz", "gender" => "male", "Anzahl" => "50"],
            ["Xpath" => "foo",1=>"",2=>"",3=>""],
        ];

        $tests = [
            "default" =>
                [
                    "expected" =>
                        [
                            ["Name" => "Pascal", "gender" => null, "Anzahl" => null, "Xpath" => null,1=> null,2=>null, 3=>null],
                            ["Name" => "Susanne", "gender" => "female", "Anzahl" => "70", "Xpath" => null,1=> null,2=>null, 3=>null],
                            ["Name" => "Heinz", "gender" => "male", "Anzahl" => "50", "Xpath" => null,1=> null,2=>null, 3=>null],
                            ["Name" => null, "gender" => null, "Anzahl" => null, "Xpath" => "foo",1=>"",2=>"",3=>""],
                        ],
                    "expectedKeys" => null,
                    "defaultValue" => null
                ],
            "3 keys" =>
                [
                    "expected" =>
                        [
                            ["gender" => "default", "Xpath" => "default","foo"=> "default"],
                            ["gender" => "female", "Xpath" => "default","foo"=> "default"],
                            ["gender" => "male", "Xpath" => "default","foo"=> "default"],
                            ["gender" => "default", "Xpath" => "foo","foo"=> "default"],
                        ],
                    "expectedKeys" => ["gender", "Xpath", "foo"],
                    "defaultValue" => "default"
                ],
        ];

        foreach ($tests as $name => $data){
            $res = $input;
            ArrayUtil::consolidateMultiKeys($res, $data["expectedKeys"], $data["defaultValue"]);
            $this->assertTrue(ArrayUtil::equals($res, $data["expected"], true, true), "Result error on test $name:\n" . ArrayUtilityTestHelper::getArrStr($data["expected"], $res, $input));
        }
    }

    public function test_XmlToArray(){
        $input = "<?xml version='1.0' encoding='utf-8' standalone='no'?>
        <!DOCTYPE adresse SYSTEM 'adresse.dtd'>

        <adresse>
        <einzeladresse>
        <name>Müller</name>
        <vorn>Lieschen</vorn>
        <strasse>Nirgendwostr. 10</strasse>
        <plz>10000</plz>
        <ort>Nirgendwo</ort>
        <tel art='priv'>00000/12345</tel>
        <tel art='off'>00000/54321</tel>
        </einzeladresse>
        <einzeladresse>
        <name>Müller</name>
        <vorn>Lieschen</vorn>
        <strasse>Nirgendwostr. 10</strasse>
        <plz>10000</plz>
        <ort>Nirgendwo</ort>
        <tel art='priv'>00000/12345</tel>
        <tel art='off'>00000/54321</tel>
        </einzeladresse>
        </adresse>";

        $tests = [
            "default" =>
                [
                    "expected" =>
                        [
                            "einzeladresse" => [
                                [
                                    "name" => "Müller",
                                    "vorn" => "Lieschen",
                                    "strasse" => "Nirgendwostr. 10",
                                    "plz" => "10000",
                                    "ort" => "Nirgendwo",
                                    "tel" => [
                                        "00000/12345",
                                        "00000/54321"
                                    ]
                                ],
                                    [
                                        "name" => "Müller",
                                        "vorn" => "Lieschen",
                                        "strasse" => "Nirgendwostr. 10",
                                        "plz" => "10000",
                                        "ort" => "Nirgendwo",
                                        "tel" => [
                                            "00000/12345",
                                            "00000/54321"
                                        ]
                                    ]
                            ],
                        ],
                ],
        ];

        foreach ($tests as $name => $data) {
            $res = ArrayUtil::xmlToArray($input);
            $this->assertTrue(ArrayUtil::equals($res, $data["expected"], true, true), "Result error on test $name:\n" . ArrayUtilityTestHelper::getArrStr($data["expected"], $res, $input));
        }
    }

    public function test_searchByBitmask(){
        $input = [
          "el1","el2","el3","el4","el5","el6","el7",
        ];

        $tests = [
            "default" =>
                [
                    "bitmask" => [1,1,1],
                    "expected" => ["el7"]
                ],
            "empty" =>
                [
                    "bitmask" => [],
                    "expected" => $input
                ],
            "tooMany" =>
                [
                    "bitmask" => [1,1,1,1,1,1],
                    "expected" => ["el7"]
                ],
            "partial low" =>
                [
                    "bitmask" => [0],
                    "expected" => ["el1","el2","el3","el4"]
                ],
            "partial high" =>
                [
                    "bitmask" => [1],
                    "expected" => ["el5","el6","el7"]
                ],
            "mix" =>
                [
                    "bitmask" => [0,1],
                    "expected" => ["el3","el4"]
                ],
        ];

        foreach ($tests as $name => $data) {
            $res = ArrayUtil::searchByBitmask($input,$data["bitmask"]);
            $this->assertTrue(ArrayUtil::equals($res, $data["expected"], false, true), "Result error on test $name:\n" . ArrayUtilityTestHelper::getArrStr($data["expected"], $res, $input));
        }

        $e = null;
        try{
            ArrayUtil::searchByBitmask($input,["asd"]);
        }catch(Exception $e){
        }
        $is = ($e instanceof InvalidArgumentException);
        $eS = $e === null ? "null" : get_class($e);
        $this->assertTrue($is,"Expected InvalidArgumentException got: $eS");
    }

    public function test_toString()
    {

        $arr1 = [];
        $arr1["recursion"] = &$arr1;

        $arr2 = ["foo" => "bar"];
        $arr2["recursion"] = $arr2;

        $arr3 = ["foo" => "bar"];
        $arr4 = ["bar" => "foo"];
        $arr3["recursion3"] = &$arr4;
        $arr4["recursion4"] = &$arr3;
        $arr4["recursion4-2"] = &$arr4;

        $tests = [
            "empty" =>
                [
                    "input" => [],
                    "expected" => ""
                ],
            "value" =>
                [
                    "input" => ["foo"],
                    "expected" => "0 => foo"
                ],
            "key-value" =>
                [
                    "input" => ["foo" => "bar"],
                    "expected" => "foo => bar"
                ],
            "key-null" =>
                [
                    "input" => ["foo" => null],
                    "expected" => "foo => [null]"
                ],
            "key-true" =>
                [
                    "input" => ["foo" => true],
                    "expected" => "foo => [true]"
                ],
            "key-false" =>
                [
                    "input" => ["foo" => false],
                    "expected" => "foo => [false]"
                ],
            "key-number" =>
                [
                    "input" => ["foo" => 1.5],
                    "expected" => "foo => 1.5"
                ],
            "key-object" =>
                [
                    "input" => ["foo" => new stdClass()],
                    "expected" => "foo => [Object of class (stdClass) has no __toString() method]"
                ],
            "key-array" =>
                [
                    "input" => ["foo" => ["bar"]],
                    "expected" => "foo => (Array) [\n       0 => bar\n]"
                ],
            "key-array-recursive-copy" =>
                [
                    "input" => $arr2,
                    "expected" => "foo => bar
recursion => (Array) [
             foo => bar
]"
                ],
            "key-array-recursive-reference" =>
                [
                    "input" => $arr1,
                    "expected" => "recursion => (Array) [\n             recursion => (Array) [\n                          [... recursion ...]\n             ]\n]"
                ],
            "key-array-recursive-reference-multi" =>
                [
                    "input" => $arr3,
                    "expected" => "foo => bar
recursion3 => (Array) [
              bar => foo
              recursion4 => (Array) [
                            foo => bar
                            recursion3 => (Array) [
                                          [... recursion ...]
                            ]
              ]
              recursion4-2 => (Array) [
                                            [... recursion ...]
              ]
]"
                ],
        ];

        foreach ($tests as $name => $data) {
            $actual = ArrayUtil::toString($data["input"], 5, 0);
            $expected = str_replace("\r\n","\n",$data["expected"]); // unify whitespaces
            $this->assertEquals($expected, $actual, "Result error on test $name:\n" . ArrayUtilityTestHelper::getArrStr($expected, $actual, $data["input"]));
        }
    }
}
 