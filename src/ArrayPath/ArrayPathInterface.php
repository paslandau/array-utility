<?php

namespace paslandau\ArrayUtility\ArrayPath;

interface ArrayPathInterface extends \Iterator, \Countable{
    /**
     * @return mixed
     */
    public function end();

    /**
     * Parses an ArrayPath expression in an internal array representation
     * @param $path
     * @return mixed
     */
    public function parsePath($path);
}