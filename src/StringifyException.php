<?php
namespace LJSON;

/**
 * Class StringifyException
 * @package LJSON
 */
class StringifyException extends \Exception
{
    /**
     * StringifyException constructor.
     * @param string $message
     * @param string $file
     * @param int $line
     * @param int $code
     * @param \Exception $previous
     * @internal
     */
    public function __construct($message, $file, $line, $code = 0, $previous = null)
    {
        LJSON::restoreErrorHandler();
        parent::__construct($message, $code, $previous);
        $this->file = $file;
        $this->line = $line;
    }
}
