<?php
namespace Kanti\Test;

use Kanti\LJSON;

class LibraryTest extends \PHPUnit_Framework_TestCase
{
    public function testWithLibFunction()
    {
        $lib = function ($function, $parameter1, $parameter2) {
            switch ($function) {
                case '+':
                    return $parameter1 + $parameter2;
            }
            return null;
        };
        $function = function ($lib, $parameter1, $parameter2) {
            return $lib('+', $parameter1, $parameter2);
        };
        $resultFunction = LJSON::withLib($lib, $function);

        $this->assertEquals(3, $resultFunction(1, 2));
        $this->assertEquals(13, $resultFunction(5, 8));
        $this->assertEquals(-12, $resultFunction(0, -12));
    }

    public function testWithStdLibFunction()
    {
        $function = function ($lib, $parameter1, $parameter2) {
            return [
                $lib('+', $parameter1, $parameter2),
                $lib('-', $parameter1, $parameter2),
                $lib('*', $parameter1, $parameter2),
                $lib('/', $parameter1, $parameter2),
                $lib('sqrt', $parameter1),
            ];
        };
        $resultFunction = LJSON::withStdLib($function);

        $this->assertEquals([3, -1, 2, 0.5, 1], $resultFunction(1, 2));
        $this->assertEquals([13, -3, 40, 5 / 8, sqrt(5)], $resultFunction(5, 8));
        $this->assertEquals([-12, 12, 0, 0, sqrt(0)], $resultFunction(0, -12));
    }

    public function _testParseWithLibFunction()
    {
        $lib = function ($function, $parameter1, $parameter2) {
            switch ($function) {
                case '+':
                    return $parameter1 + $parameter2;
            }
            return null;
        };
        $resultFunction = LJSON::parseWithLib(
            $lib,
            '(v0,v1,v2) => (v0("+",v1,v2))'
        );

        $this->assertEquals(3, $resultFunction(1, 2));
        $this->assertEquals(13, $resultFunction(5, 8));
        $this->assertEquals(-12, $resultFunction(0, -12));
    }

    public function _testParseWithStdLibFunction()
    {
        $resultFunction = LJSON::parseWithStdLib(
            '(v0,v1,v2) => ([v0("+",v1,v2),v0("-",v1,v2),v0("*",v1,v2),v0("\/",v1,v2),v0("sqrt",v1)])'
        );

        $this->assertEquals([3, -1, 2, 0.5, 1], $resultFunction(1, 2));
        $this->assertEquals([13, -3, 40, 5 / 8, sqrt(5)], $resultFunction(5, 8));
        $this->assertEquals([-12, 12, 0, 0, sqrt(0)], $resultFunction(0, -12));
    }
}