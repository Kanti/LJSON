<?php
namespace Kanti;


class Parameter
{
    protected $result = '';
    protected $LJson = null;

    public function __construct($result, LJSON $LJson)
    {
        $this->LJson = $LJson;
        $this->result = $result;
    }

    /**
     * @return Parameter
     */
    public function __invoke()
    {
        return new self($this->result . '(' . implode(',', array_map([$this->LJson, "stringify"], func_get_args())) . ')', $this->LJson);
    }

    public function __toString()
    {
        return $this->result;
    }
}