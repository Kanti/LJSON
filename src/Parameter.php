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
        $parameters = implode(',', array_map(['\Kanti\LJSON', 'stringify'], func_get_args()));
        return new self($this->result . '(' . $parameters . ')');
    }

    public function __toString()
    {
        return $this->result;
    }
}
