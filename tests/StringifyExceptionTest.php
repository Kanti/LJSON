<?php
namespace LJSON\Test;

use LJSON\LJSON;

class StringifyTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \LJSON\StringifyException
     */
    public function testStringifyException()
    {
        LJSON::stringify(new \Exception);
    }

    /**
     * @expectedException \LJSON\StringifyException
     */
    public function testConvertToInt()
    {
        LJSON::stringify(function ($aaa) {
            return (int)$aaa;
        });
    }

    /**
     * @expectedException \LJSON\StringifyException
     */
    public function testConvertToFloat()
    {
        LJSON::stringify(function ($aaa) {
            return (float)$aaa;
        });
    }

    /**
     * @expectedException \LJSON\StringifyException
     */
    public function testConvertToString()
    {
        LJSON::stringify(function ($aaa) {
            return (string)$aaa;
        });
    }

    /**
     * @expectedException \LJSON\StringifyException
     */
    public function testArrayGet()
    {
        LJSON::stringify(function ($aaa) {
            return $aaa[1];
        });
    }

    /**
     * @expectedException \LJSON\StringifyException
     */
    public function testArraySet()
    {
        LJSON::stringify(function ($aaa) {
            $aaa[1] = 1;
        });
    }

    /**
     * @expectedException \LJSON\StringifyException
     */
    public function testArrayIsset()
    {
        LJSON::stringify(function ($aaa) {
            return isset($aaa[1]);
        });
    }

    /**
     * @expectedException \LJSON\StringifyException
     */
    public function testArrayUnset()
    {
        LJSON::stringify(function ($aaa) {
            unset($aaa[1]);
        });
    }


    /**
     * @expectedException \LJSON\StringifyException
     */
    public function testAttributeGet()
    {
        LJSON::stringify(function ($aaa) {
            return $aaa->b;
        });
    }

    /**
     * @expectedException \LJSON\StringifyException
     */
    public function testAttributeSet()
    {
        LJSON::stringify(function ($aaa) {
            $aaa->b = 1;
        });
    }

    /**
     * @expectedException \LJSON\StringifyException
     */
    public function testAttributeIsset()
    {
        LJSON::stringify(function ($aaa) {
            return isset($aaa->bbb);
        });
    }

    /**
     * @expectedException \LJSON\StringifyException
     */
    public function testAttributeUnset()
    {
        LJSON::stringify(function ($aaa) {
            unset($aaa->bbb);
        });
    }

    /**
     * @expectedException \LJSON\StringifyException
     */
    public function testCallMethod()
    {
        LJSON::stringify(function ($aaa) {
            return $aaa->bbb();
        });
    }

    /**
     * @expectedException \LJSON\StringifyException
     */
    public function testIterator()
    {
        LJSON::stringify(function ($aaa) {
            foreach ($aaa as $item) {
                return $item;
            }
            return null;
        });
    }

    /**
     * @expectedException \LJSON\StringifyException
     */
    public function testClone()
    {
        LJSON::stringify(function ($aaa) {
            return clone $aaa;
        });
    }

    /**
     * @expectedException \LJSON\StringifyException
     */
    public function testCount()
    {
        LJSON::stringify(function ($aaa) {
            return count($aaa);
        });
    }

    /**
     * @expectedException \PHPUnit_Framework_Error_Notice
     */
    public function testOldErrorHandler()
    {
        LJSON::stringify(function () {
            $object = new \stdClass;
            return (int)$object;
        });
    }

    /**
     * @expectedException \LJSON\StringifyException
     */
    public function testNoErrorHandlerPreset()
    {
        if (PHP_MAJOR_VERSION < 5 || (PHP_MAJOR_VERSION == 5 && PHP_MINOR_VERSION < 5)) {
            $this->markTestSkipped('only works for php >=5.5');
            return;
        }
        set_error_handler(null);
        LJSON::stringify(function ($aaa) {
            return (float)$aaa;
        });
    }
}
