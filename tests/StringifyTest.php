<?php
namespace Kanti\Test;

use Kanti\LJSON;

class StringifyTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \Kanti\StringifyException
     */
    public function testConvertToInt()
    {
        LJSON::stringify(function ($a) {
            return (int)$a;
        });
    }

    /**
     * @expectedException \Kanti\StringifyException
     */
    public function testConvertToFloat()
    {
        LJSON::stringify(function ($a) {
            return (float)$a;
        });
    }

    /**
     * @expectedException \Kanti\StringifyException
     */
    public function testConvertToString()
    {
        LJSON::stringify(function ($a) {
            return (string)$a;
        });
    }

    /**
     * @expectedException \Kanti\StringifyException
     */
    public function testArrayGet()
    {
        LJSON::stringify(function ($a) {
            return $a[1];
        });
    }

    /**
     * @expectedException \Kanti\StringifyException
     */
    public function testArraySet()
    {
        LJSON::stringify(function ($a) {
            $a[1] = 1;
        });
    }

    /**
     * @expectedException \Kanti\StringifyException
     */
    public function testArrayIsset()
    {
        LJSON::stringify(function ($a) {
            return isset($a[1]);
        });
    }

    /**
     * @expectedException \Kanti\StringifyException
     */
    public function testArrayUnset()
    {
        LJSON::stringify(function ($a) {
            unset($a[1]);
        });
    }


    /**
     * @expectedException \Kanti\StringifyException
     */
    public function testAttributeGet()
    {
        LJSON::stringify(function ($a) {
            return $a->b;
        });
    }

    /**
     * @expectedException \Kanti\StringifyException
     */
    public function testAttributeSet()
    {
        LJSON::stringify(function ($a) {
            $a->b = 1;
        });
    }

    /**
     * @expectedException \Kanti\StringifyException
     */
    public function testAttributeIsset()
    {
        LJSON::stringify(function ($a) {
            return isset($a->b);
        });
    }

    /**
     * @expectedException \Kanti\StringifyException
     */
    public function testAttributeUnset()
    {
        LJSON::stringify(function ($a) {
            unset($a->b);
        });
    }

    /**
     * @expectedException \Kanti\StringifyException
     */
    public function testCallMethod()
    {
        LJSON::stringify(function ($a) {
            return $a->b();
        });
    }

    /**
     * @expectedException \Kanti\StringifyException
     */
    public function testIterator()
    {
        LJSON::stringify(function ($a) {
            foreach ($a as $item) {
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
        LJSON::stringify(function ($a) {
            return clone $a;
        });
    }

    /**
     * @expectedException \Kanti\StringifyException
     */
    public function testCount()
    {
        LJSON::stringify(function ($a) {
            return count($a);
        });
    }

    /**
     * @expectedException \PHPUnit_Framework_Error_Notice
     */
    public function testOldErrorHandler()
    {
        LJSON::stringify(function ($a) {
            $b = new \stdClass;
            return (int)$b;
        });
    }

    /**
     * @expectedException \PHPUnit_Framework_Error_Notice
     */
    public function testA()
    {
        $b = new \stdClass;
        return (int)$b;
    }
}
