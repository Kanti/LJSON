<?php
namespace Kanti;

use Traversable;

/**
 * Class Parameter
 *
 * @internal
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
        $e = new \Exception;
        $trace = $e->getTrace();
        if (isset($trace[0])) {
            $file = $trace[0]['file'];
            $line = $trace[0]['line'];
        }
        throw new StringifyException('You can not call a method of a parameter', $file, $line);
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
        $e = new \Exception;
        $trace = $e->getTrace();
        if (isset($trace[0])) {
            $file = $trace[0]['file'];
            $line = $trace[0]['line'];
        }
        throw new StringifyException('You can not test existence of an array element', $file, $line);
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
        $e = new \Exception;
        $trace = $e->getTrace();
        if (isset($trace[0])) {
            $file = $trace[0]['file'];
            $line = $trace[0]['line'];
        }
        throw new StringifyException('You can not get an array element', $file, $line);
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
        $e = new \Exception;
        $trace = $e->getTrace();
        if (isset($trace[0])) {
            $file = $trace[0]['file'];
            $line = $trace[0]['line'];
        }
        throw new StringifyException('You can not set an array element', $file, $line);
    }

    /**
     * @param mixed $offset
     * @throws StringifyException
     */
    public function offsetUnset($offset)
    {
        $file = __FILE__;
        $line = __LINE__;
        $e = new \Exception;
        $trace = $e->getTrace();
        if (isset($trace[0])) {
            $file = $trace[0]['file'];
            $line = $trace[0]['line'];
        }
        throw new StringifyException('You can not unset an array element', $file, $line);
    }

    /**
     * @param $name
     * @throws StringifyException
     */
    public function __get($name)
    {
        $file = __FILE__;
        $line = __LINE__;
        $e = new \Exception;
        $trace = $e->getTrace();
        if (isset($trace[0])) {
            $file = $trace[0]['file'];
            $line = $trace[0]['line'];
        }
        throw new StringifyException('You can not get an object attribute', $file, $line);
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
        $e = new \Exception;
        $trace = $e->getTrace();
        if (isset($trace[0])) {
            $file = $trace[0]['file'];
            $line = $trace[0]['line'];
        }
        throw new StringifyException('You can not set an object attribute', $file, $line);
    }

    /**
     * @param $name
     * @throws StringifyException
     */
    public function __isset($name)
    {
        $file = __FILE__;
        $line = __LINE__;
        $e = new \Exception;
        $trace = $e->getTrace();
        if (isset($trace[0])) {
            $file = $trace[0]['file'];
            $line = $trace[0]['line'];
        }
        throw new StringifyException('You can not test existence of an object attribute', $file, $line);
    }

    /**
     * @param $name
     * @throws StringifyException
     */
    public function __unset($name)
    {
        $file = __FILE__;
        $line = __LINE__;
        $e = new \Exception;
        $trace = $e->getTrace();
        if (isset($trace[0])) {
            $file = $trace[0]['file'];
            $line = $trace[0]['line'];
        }
        throw new StringifyException('You can not unset an object attribute', $file, $line);
    }

    public function getIterator()
    {
        $file = __FILE__;
        $line = __LINE__;
        $e = new \Exception;
        $trace = $e->getTrace();
        if (isset($trace[0])) {
            $file = $trace[0]['file'];
            $line = $trace[0]['line'];
        }
        throw new StringifyException("You can not iterate over a parameter", $file, $line);
    }


    public function __clone()
    {
        $file = __FILE__;
        $line = __LINE__;
        $e = new \Exception;
        $trace = $e->getTrace();
        if (isset($trace[0])) {
            $file = $trace[0]['file'];
            $line = $trace[0]['line'];
        }
        throw new StringifyException("You can not clone a parameter", $file, $line);
    }

    public function count()
    {
        $file = __FILE__;
        $line = __LINE__;
        $e = new \Exception;
        $trace = $e->getTrace();
        if (isset($trace[1])) {
            $file = $trace[1]['file'];
            $line = $trace[1]['line'];
        }
        throw new StringifyException("You can not count a parameter", $file, $line);
    }
}
