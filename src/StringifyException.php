<?php
namespace Kanti;

class StringifyException extends \Exception
{
    public function __construct($message, $file, $line, $code = 0)
    {
        parent::__construct($message, $code, null);
        $this->file = $file;
        $this->line = $line;
    }
}
