<?php
namespace Kanti\Test;

use Kanti\LJSON;
use Kanti\SpecialUndefinedIdentifierClass;
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
            "() => (undefined)" => function () {
                return new SpecialUndefinedIdentifierClass;
            },
            "() => (null)" => function () {
                return null;
            },
            "() => ([])" => function () {
                return [];
            },
            "(v0) => (v0)" => function ($aaa) {
                return $aaa;
            },
            "(v0) => ([v0,v0])" => function ($aaa) {
                return [$aaa, $aaa];
            },
            "(v0) => (v0(v0))" => function ($aaa) {
                return $aaa($aaa);
            },
            "(v0) => (() => (v0))" => function ($aaa) {
                return function () use ($aaa) {
                    return $aaa;
                };
            },
            "(v0) => (() => (v0()))" => function ($aaa) {
                return function () use ($aaa) {
                    return $aaa();
                };
            },
            "(v0) => ((v1,v2) => (v0(v1,v2)))" => function ($aaa) {
                return function ($bbb, $ccc) use ($aaa) {
                    return $aaa($bbb, $ccc);
                };
            },
            "(v0) => ([(v1,v2) => ([v0(v1,v2)])])" => function ($aaa) {
                return [
                    function ($bbb, $ccc) use ($aaa) {
                        return [$aaa($bbb, $ccc)];
                    }
                ];
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

    public function testParseLJsonNullFunction()
    {
        $expectedFunction = function () {
            return null;
        };
        $actualFunction = LJSON::parse(LJSON::stringify($expectedFunction));
        $this->assertEquals($expectedFunction(), $actualFunction());
    }

    public function testParseLJsonUndefinedFunction()
    {
        $expectedFunction = function () {
            return new SpecialUndefinedIdentifierClass;
        };
        $actualFunction = LJSON::parse(LJSON::stringify($expectedFunction));
        $this->assertEquals(null, $actualFunction());
    }

    public function testParseLJsonUndefinedAsSpecialClassFunction()
    {
        $expectedFunction = function () {
            return new SpecialUndefinedIdentifierClass;
        };
        $actualFunction = LJSON::parse(LJSON::stringify($expectedFunction), false, $options = LJSON::RETURN_UNDEFINED_AS_SPECIAL_CLASS);
        $this->assertInstanceOf('Kanti\SPECIAL_UNDEFINED_CONSTANT', $actualFunction());
    }

    public function testParseLJsonOneParameterFunction()
    {
        $expectedFunction = function ($param1) {
            return $param1;
        };
        $actualFunction = LJSON::parse(LJSON::stringify($expectedFunction));
        $this->assertEquals($expectedFunction(1), $actualFunction(1));
        $this->assertEquals($expectedFunction("awf"), $actualFunction("awf"));
        $this->assertEquals($expectedFunction($this), $actualFunction($this));
    }

    public function testParseLJsonTowParameterAndArrayFunction()
    {
        $expectedFunction = function ($param1, $param2) {
            return [$param1, $param2];
        };
        $actualFunction = LJSON::parse(LJSON::stringify($expectedFunction));
        $this->assertEquals($expectedFunction(1, 1234), $actualFunction(1, 1234));
        $this->assertEquals($expectedFunction("awf", "foo"), $actualFunction("awf", "foo"));
        $this->assertEquals($expectedFunction($this, []), $actualFunction($this, []));
    }

    public function testParseLJsonOver12ParameterAndArrayFunction()
    {
        $aaa = $bbb = $ccc = $ddd = $eee = $fff = $ggg = $hhh = $iii = $jjj = $kkkk = $lll = 1337;
        $expectedFunction = function ($aaa, $bbb, $ccc, $ddd, $eee, $fff, $ggg, $hhh, $iii, $jjj, $kkkk, $lll) {
            return [$aaa, $bbb, $ccc, $ddd, $eee, $fff, $ggg, $hhh, $iii, $jjj, $kkkk, $lll];
        };
        $actualFunction = LJSON::parse(LJSON::stringify($expectedFunction));
        $this->assertEquals(
            $expectedFunction($aaa, $bbb, $ccc, $ddd, $eee, $fff, $ggg, $hhh, $iii, $jjj, $kkkk, $lll),
            $actualFunction($aaa, $bbb, $ccc, $ddd, $eee, $fff, $ggg, $hhh, $iii, $jjj, $kkkk, $lll)
        );
    }

    public function testParseLJsonParameterAsFunctionFunction()
    {
        $aaa = function ($bbb = null) {
            if ($bbb === null) {
                $bbb = 2;
            }
            return $bbb * $bbb;
        };
        $expectedFunction = function ($ccc) {
            return $ccc($ccc());
        };
        $actualFunction = LJSON::parse(LJSON::stringify($expectedFunction));
        $this->assertEquals(
            $expectedFunction($aaa),
            $actualFunction($aaa)
        );
    }

    public function testParseLJsonParameterAsFunctionWith2ParameterFunction()
    {
        $aaa = function ($aaa, $bbb) {
            return [$aaa, $bbb];
        };
        $expectedFunction = function ($aaa) {
            return $aaa($aaa, $aaa);
        };
        $actualFunction = LJSON::parse(LJSON::stringify($expectedFunction));
        $this->assertEquals(
            $expectedFunction($aaa),
            $actualFunction($aaa)
        );
    }

    public function testParseLJsonFunctionInFunctionReturnArrayFunction()
    {
        $expectedFunction = function ($aaa) {
            return function () use ($aaa) {
                return [$aaa];
            };
        };
        $actualFunction = LJSON::parse(LJSON::stringify($expectedFunction));
        $expectedFunction2 = $expectedFunction(1);
        $actualFunction2 = $actualFunction(1);
        $this->assertEquals(
            $expectedFunction2(),
            $actualFunction2()
        );
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
    public function testParseExceptionVariableNotSet()
    {
        LJSON::parse('v1( ');
    }

    /**
     * @expectedException \Exception
     */
    public function testParseExceptionNotCompletedVariableCall()
    {
        LJSON::parse('(v0) => v0( ');
    }
}
