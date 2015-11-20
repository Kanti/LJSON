<?php
namespace Kanti;

/**
 * Class Parameter
 *
 * @package Kanti
 */
class Parameter implements \ArrayAccess, \IteratorAggregate, \Countable
{
    /**
     * @var string
     */
    protected $result = '';

    /**
     * Parameter constructor.
     * @param $result
     * @internal
     */
    public function __construct($result)
    {
        $this->result = $result;
    }

    /**
     * @return Parameter
     */
    public function __invoke()
    {
        $parameters = implode(',', array_map(['\Kanti\LJSON', 'stringify'], func_get_args()));
        return new self($this->result . '(' . $parameters . ')');
    }

    /**
     * @return string
     */
    public function getParameterResult()
    {
        return $this->result;
    }

    /**
     * @param $name
     * @param $arguments
     * @throws StringifyException
     */
    public function __call($name, $arguments)
    {
        $file = __FILE__;
        $line = __LINE__;
        $trace = (new \Exception)->getTrace();
        if (isset($trace[0])) {
            $file = $trace[0]['file'];
            $line = $trace[0]['line'];
        }
        throw new StringifyException('You can not call a method of a parameter', $file, $line, 1448007822);
    }

    /**
     * @param mixed $offset
     * @return null
     * @throws StringifyException
     */
    public function offsetExists($offset)
    {
        $file = __FILE__;
        $line = __LINE__;
        $trace = (new \Exception)->getTrace();
        if (isset($trace[0])) {
            $file = $trace[0]['file'];
            $line = $trace[0]['line'];
        }
        throw new StringifyException('You can not test existence of an array element', $file, $line, 1448007841);
    }

    /**
     * @param mixed $offset
     * @return null
     * @throws StringifyException
     */
    public function offsetGet($offset)
    {
        $file = __FILE__;
        $line = __LINE__;
        $trace = (new \Exception)->getTrace();
        if (isset($trace[0])) {
            $file = $trace[0]['file'];
            $line = $trace[0]['line'];
        }
        throw new StringifyException('You can not get an array element', $file, $line, 1448007850);
    }

    /**
     * @param mixed $offset
     * @param mixed $value
     * @throws StringifyException
     */
    public function offsetSet($offset, $value)
    {
        $file = __FILE__;
        $line = __LINE__;
        $trace = (new \Exception)->getTrace();
        if (isset($trace[0])) {
            $file = $trace[0]['file'];
            $line = $trace[0]['line'];
        }
        throw new StringifyException('You can not set an array element', $file, $line, 1448007858);
    }

    /**
     * @param mixed $offset
     * @throws StringifyException
     */
    public function offsetUnset($offset)
    {
        $file = __FILE__;
        $line = __LINE__;
        $trace = (new \Exception)->getTrace();
        if (isset($trace[0])) {
            $file = $trace[0]['file'];
            $line = $trace[0]['line'];
        }
        throw new StringifyException('You can not unset an array element', $file, $line, 1448007871);
    }

    /**
     * @param $name
     * @throws StringifyException
     */
    public function __get($name)
    {
        $file = __FILE__;
        $line = __LINE__;
        $trace = (new \Exception)->getTrace();
        if (isset($trace[0])) {
            $file = $trace[0]['file'];
            $line = $trace[0]['line'];
        }
        throw new StringifyException('You can not get an object attribute', $file, $line, 1448007884);
    }

    /**
     * @param $name
     * @param $value
     * @throws StringifyException
     */
    public function __set($name, $value)
    {
        $file = __FILE__;
        $line = __LINE__;
        $trace = (new \Exception)->getTrace();
        if (isset($trace[0])) {
            $file = $trace[0]['file'];
            $line = $trace[0]['line'];
        }
        throw new StringifyException('You can not set an object attribute', $file, $line, 1448007894);
    }

    /**
     * @param $name
     * @throws StringifyException
     */
    public function __isset($name)
    {
        $file = __FILE__;
        $line = __LINE__;
        $trace = (new \Exception)->getTrace();
        if (isset($trace[0])) {
            $file = $trace[0]['file'];
            $line = $trace[0]['line'];
        }
        throw new StringifyException('You can not test existence of an object attribute', $file, $line, 1448007908);
    }

    /**
     * @param $name
     * @throws StringifyException
     */
    public function __unset($name)
    {
        $file = __FILE__;
        $line = __LINE__;
        $trace = (new \Exception)->getTrace();
        if (isset($trace[0])) {
            $file = $trace[0]['file'];
            $line = $trace[0]['line'];
        }
        throw new StringifyException('You can not unset an object attribute', $file, $line, 1448007923);
    }

    /**
     * @throws StringifyException
     */
    public function getIterator()
    {
        $file = __FILE__;
        $line = __LINE__;
        $trace = (new \Exception)->getTrace();
        if (isset($trace[0])) {
            $file = $trace[0]['file'];
            $line = $trace[0]['line'];
        }
        throw new StringifyException("You can not iterate over a parameter", $file, $line, 1448007935);
    }


    /**
     * @throws StringifyException
     */
    public function __clone()
    {
        $file = __FILE__;
        $line = __LINE__;
        $trace = (new \Exception)->getTrace();
        if (isset($trace[0])) {
            $file = $trace[0]['file'];
            $line = $trace[0]['line'];
        }
        throw new StringifyException("You can not clone a parameter", $file, $line, 1448007948);
    }

    /**
     * @throws StringifyException
     */
    public function count()
    {
        $file = __FILE__;
        $line = __LINE__;
        $trace = (new \Exception)->getTrace();
        if (isset($trace[1])) {
            $file = $trace[1]['file'];
            $line = $trace[1]['line'];
        }
        throw new StringifyException("You can not count a parameter", $file, $line, 1448007955);
    }
}
