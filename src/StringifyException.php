<?php
namespace Kanti;

class StringifyException extends \Exception
{
    public function __construct($message, $file, $line, $code = 0)
    {
        LJSON::restoreErrorHandler();
        parent::__construct($message, $code, null);
        $this->file = $file;
        $this->line = $line;
    }
}
