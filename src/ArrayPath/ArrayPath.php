<?php

namespace paslandau\ArrayUtility\ArrayPath;

class ArrayPath implements ArrayPathInterface {
    /**
     * @var array
     */
    private $parsedArrayPath;

    /**
     * @param string $path
     */
    public function __construct($path = null){
        $this->parsedArrayPath = array();
        if($path !== null){
            $this->parsePath($path);
        }
    }

    /**
     * Parses an ArrayPath expression in an internal array representation
     * @param $path
     * @return void
     */
    public function parsePath($path){
        $indexed = $this->parseArrayPath($path);
        $this->parsedArrayPath = $indexed;
    }

    /**
     * Valid paths examples:
     * [0]
     * ["foo"]
     * ["\"foo\""]
     * ["foo"][0][true][null][1]
     *
     * Invalid:
     * []          // path must not be empty
     * [foo]        // unescaped string
     * ['foo']      // only " is accepted as string char
     * ["foo"foo"]  // unescaped string char
     *
     * @param $path
     * @return array
     */
    private function parseArrayPath($path){

        $path = "<?php $path?>";
        $tokens = token_get_all($path);
        $result = array();
        $state = null;
        $states = array();
        $stateOpen = function($token, $value, &$result) use(&$state, &$states){
            switch($token){
                case "[":{
                    $state = $states[1];
                    break;
                }
                case T_OPEN_TAG:
                case T_CLOSE_TAG:break;
                default: {
                    $expected = array("[","<?php", "?>");
                    $expectedString = implode(", ",$expected);
                    $found = is_string($token)?"literal '$token'":"token '".token_name($token)."'";
                    throw new \UnexpectedValueException("Parse error. Found: $found. Expected: $expectedString");
                }
            }
        };
        $stateIn = function($token, $value, &$result) use(&$state, &$states){
            switch($token){
                case T_CONSTANT_ENCAPSED_STRING:
                case T_STRING:
                case T_LNUMBER:
                case T_DNUMBER:
                    $state = $states[2];
                    break;
                default: {
                    $expected = array("string (\"string\")", "number (1, 1.1)", "boolean (true, false)", "null (null)");
                    $expectedString = implode(", ",$expected);
                    $found = is_string($token)?"literal '$token'":"token '".token_name($token)."'";
                    throw new \UnexpectedValueException("Parse error. Found: $found. Expected: $expectedString");
                }
            }
            $res = @json_decode($value);
            if ($res === null) {
                $error = json_last_error();
                if($error !== JSON_ERROR_NONE){

                    throw new \UnexpectedValueException("Error occured at '$value'. Error code: $error. Error message: ".json_last_error_msg());
                }
            }
            if(!is_string($res)){
                $res = (int)$res; // convert float, bool and null to int as php does it when those types are stored as key
            }
            $result[] = $res;
        };
        $stateClose = function($token, $value, &$result) use(&$state, &$states){
            switch($token){
                case "]":{
                    $state = $states[0];
                    return;
                }
                default: {
                    $expected = array("]");
                    $expectedString = implode(", ",$expected);
                    $found = is_string($token)?"literal '$token'":"token '".token_name($token)."'";
                    throw new \UnexpectedValueException("Parse error. Found: $found. Expected: $expectedString");
                }
            }
        };
        $states[0] = $stateOpen;
        $states[1] = $stateIn;
        $states[2] = $stateClose;
        $state = $stateOpen;
        foreach($tokens as $tok){
            $token = null;
            $value = null;
            if(is_array($tok)){
                $token = $tok[0];
                $value = $tok[1];
            }else{
                $token = $tok;
            }
            $state($token,$value, $result);
        }
        return $result;
    }


    function rewind() {
        return reset($this->parsedArrayPath);
    }
    function current() {
        return current($this->parsedArrayPath);
    }
    function key() {
        return key($this->parsedArrayPath);
    }
    function next() {
        return next($this->parsedArrayPath);
    }
    function valid() {
        return key($this->parsedArrayPath) !== null;
    }
    function end(){
        return end($this->parsedArrayPath);
    }

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * Count elements of an object
     * @link http://php.net/manual/en/countable.count.php
     * @return int The custom count as an integer.
     * </p>
     * <p>
     * The return value is cast to an integer.
     */
    public function count()
    {
        return count($this->parsedArrayPath);
    }


    public function __clone(){
        $path = new self();
        $path->parsedArrayPath = $this->parsedArrayPath;
        return $path;
    }

    /**
     * @return array
     */
    public function getParsedArrayPath()
    {
        return $this->parsedArrayPath;
    }

    public function __toString(){
        return "[".implode($this->parsedArrayPath)."]";
    }
}