<?php
namespace Kanti\Test;

use Kanti\LJSON;

class StringifyTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \Kanti\StringifyException
     */
    public function testStringifyException()
    {
        LJSON::stringify(new \Exception);
    }

    /**
     * @expectedException \Kanti\StringifyException
     */
    public function testConvertToInt()
    {
        LJSON::stringify(function ($aaa) {
            return (int)$aaa;
        });
    }

    /**
     * @expectedException \Kanti\StringifyException
     */
    public function testConvertToFloat()
    {
        LJSON::stringify(function ($aaa) {
            return (float)$aaa;
        });
    }

    /**
     * @expectedException \Kanti\StringifyException
     */
    public function testConvertToString()
    {
        LJSON::stringify(function ($aaa) {
            return (string)$aaa;
        });
    }

    /**
     * @expectedException \Kanti\StringifyException
     */
    public function testArrayGet()
    {
        LJSON::stringify(function ($aaa) {
            return $aaa[1];
        });
    }

    /**
     * @expectedException \Kanti\StringifyException
     */
    public function testArraySet()
    {
        LJSON::stringify(function ($aaa) {
            $aaa[1] = 1;
        });
    }

    /**
     * @expectedException \Kanti\StringifyException
     */
    public function testArrayIsset()
    {
        LJSON::stringify(function ($aaa) {
            return isset($aaa[1]);
        });
    }

    /**
     * @expectedException \Kanti\StringifyException
     */
    public function testArrayUnset()
    {
        LJSON::stringify(function ($aaa) {
            unset($aaa[1]);
        });
    }


    /**
     * @expectedException \Kanti\StringifyException
     */
    public function testAttributeGet()
    {
        LJSON::stringify(function ($aaa) {
            return $aaa->b;
        });
    }

    /**
     * @expectedException \Kanti\StringifyException
     */
    public function testAttributeSet()
    {
        LJSON::stringify(function ($aaa) {
            $aaa->b = 1;
        });
    }

    /**
     * @expectedException \Kanti\StringifyException
     */
    public function testAttributeIsset()
    {
        LJSON::stringify(function ($aaa) {
            return isset($aaa->bbb);
        });
    }

    /**
     * @expectedException \Kanti\StringifyException
     */
    public function testAttributeUnset()
    {
        LJSON::stringify(function ($aaa) {
            unset($aaa->bbb);
        });
    }

    /**
     * @expectedException \Kanti\StringifyException
     */
    public function testCallMethod()
    {
        LJSON::stringify(function ($aaa) {
            return $aaa->bbb();
        });
    }

    /**
     * @expectedException \Kanti\StringifyException
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
     * @expectedException \Kanti\StringifyException
     */
    public function testClone()
    {
        LJSON::stringify(function ($aaa) {
            return clone $aaa;
        });
    }

    /**
     * @expectedException \Kanti\StringifyException
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
     * @expectedException \Kanti\StringifyException
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
