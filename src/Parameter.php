<?php
namespace Kanti;

class Parameter
{
    protected $result = '';

    public function __construct($result)
    {
        $this->result = $result;
    }

    /**
     * @return Parameter
     */
    public function __invoke()
    {
        return new self($this->result . '(' . implode(',', array_map(['\Kanti\LJSON', "stringify"], func_get_args())) . ')');
    }

    public function __toString()
    {
        return $this->result;
    }
}
