<?php
namespace Kanti\Test;

use Kanti\LJSON;
use Kanti\Test\Asset\Customer;

class LJSONTest extends \PHPUnit_Framework_TestCase
{
    protected $testDataNormalJson;
    protected $testDataLJson;

    protected function setUp()
    {
        $this->testDataNormalJson = [
            null, true, false,
            0, 1337, 1234567890,
            0.0, 0.1, 200000.222,
            0.43E+20,
            0.43E-20,
            "hallo", "❤ unicode Hearth", "❥ unicode Hearth 2", "\n newline", "\" escaped", "\b test \\b",
            [], [1, 2],
            ["a" => 2],
            new \stdClass(),
            (object)['a' => 1234],
            (object)['a' => 1234, 'aa' => 0.0],
            new Customer('hallo', 'git@kanti.de'),
        ];
        $this->testDataLJson = [
            "() => (null)" => function () {
            },
            "() => ([])" => function () {
                return [];
            },
            "(v0) => (v0)" => function ($a) {
                return $a;
            },
            "(v0) => ([v0,v0])" => function ($a) {
                return [$a, $a];
            },
            "(v0) => (v0(v0))" => function ($a) {
                return $a($a);
            },
        ];
    }

    public function testStringifyNormalJson()
    {
        foreach ($this->testDataNormalJson as $data) {
            $actual = LJSON::stringify($data);
            $expected = json_encode($data);
            $this->assertEquals($expected, $actual);
        }
    }

    public function testParseNormalJson()
    {
        foreach ($this->testDataNormalJson as $data) {
            $jsonString = json_encode($data);

            $actual = LJSON::parse($jsonString);
            $expected = json_decode($jsonString);
            $this->assertEquals($expected, $actual, 'normal');

            $actual = LJSON::parse($jsonString, true);
            $expected = json_decode($jsonString, true);
            $this->assertEquals($expected, $actual, 'assoc');

            $jsonString = json_encode($data, JSON_PRETTY_PRINT);

            $actual = LJSON::parse($jsonString);
            $expected = json_decode($jsonString);
            $this->assertEquals($expected, $actual, 'pretty ' . $jsonString);

            $actual = LJSON::parse($jsonString, true);
            $expected = json_decode($jsonString, true);
            $this->assertEquals($expected, $actual, 'pretty and assoc');
        }
    }

    public function testStringifyLJson()
    {
        foreach ($this->testDataLJson as $expected => $value) {
            $this->assertEquals($expected, LJSON::stringify($value));
        }
    }

    public function testParseLJson1()
    {
        $expectedFunction = function () {
            return null;
        };
        $actualFunction = LJSON::parse(LJSON::stringify($expectedFunction));
        $this->assertEquals($expectedFunction(), $actualFunction());
    }

    public function testParseLJson2()
    {
        $expectedFunction = function ($param1) {
            return $param1;
        };
        $actualFunction = LJSON::parse(LJSON::stringify($expectedFunction));
        $this->assertEquals($expectedFunction(1), $actualFunction(1));
        $this->assertEquals($expectedFunction("awf"), $actualFunction("awf"));
        $this->assertEquals($expectedFunction($this), $actualFunction($this));
    }

    public function testParseLJson3()
    {
        $expectedFunction = function ($param1, $param2) {
            return [$param1, $param2];
        };
        $actualFunction = LJSON::parse(LJSON::stringify($expectedFunction));
        $this->assertEquals($expectedFunction(1, 1234), $actualFunction(1, 1234));
        $this->assertEquals($expectedFunction("awf", "foo"), $actualFunction("awf", "foo"));
        $this->assertEquals($expectedFunction($this, []), $actualFunction($this, []));
    }

    public function testParseLJson4()
    {
        $a = $b = $c = $d = $e = $f = $g = $h = $i = $j = $k = $l = 1337;
        $expectedFunction = function ($a, $b, $c, $d, $e, $f, $g, $h, $i, $j, $k, $l) {
            return [$a, $b, $c, $d, $e, $f, $g, $h, $i, $j, $k, $l];
        };
        $actualFunction = LJSON::parse(LJSON::stringify($expectedFunction));
        $this->assertEquals(
            $expectedFunction($a, $b, $c, $d, $e, $f, $g, $h, $i, $j, $k, $l),
            $actualFunction($a, $b, $c, $d, $e, $f, $g, $h, $i, $j, $k, $l)
        );
    }

    public function testParseLJson5()
    {
        $a = function ($b = null) {
            return 1;
        };
        $expectedFunction = function ($c) {
            return $c($c());
        };
        $actualFunction = LJSON::parse(LJSON::stringify($expectedFunction));
        $this->assertEquals(
            $expectedFunction($a),
            $actualFunction($a)
        );
    }


    public function testParseLJson6()
    {
        $a = function ($x, $y) {
            return [$x, $y];
        };
        $expectedFunction = function ($a) {
            return $a($a, $a);
        };
        $actualFunction = LJSON::parse(LJSON::stringify($expectedFunction));
        $this->assertEquals(
            $expectedFunction($a),
            $actualFunction($a)
        );
    }

    /**
     * @expectedException \Exception
     */
    public function testStringifyException()
    {
        LJSON::stringify(new \Exception);
    }

    /**
     * @expectedException \Exception
     */
    public function testParseExceptionEvalError()
    {
        LJSON::parse('()=>v1');
    }

    /**
     * @expectedException \Exception
     */
    public function testParseExceptionWrongJson()
    {
        LJSON::parse('2.0.0');
    }

    /**
     * @expectedException \Exception
     */
    public function testParseExceptionNotCompleteArray()
    {
        LJSON::parse('[ ');
    }

    /**
     * @expectedException \Exception
     */
    public function testParseExceptionNotCompleteObject()
    {
        LJSON::parse('{ ');
    }

    /**
     * @expectedException \Exception
     */
    public function testParseExceptionNotCompleteVariable()
    {
        LJSON::parse('v1( ');
    }
}
