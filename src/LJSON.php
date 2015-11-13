<?php
namespace Kanti;

/**
 * Class LJSON
 * @package Kanti
 */
class LJSON
{
    /**
     * @var bool
     */
    public static $errorHandlerSet = false;

    public static function restoreErrorHandler()
    {
        if (static::$errorHandlerSet) {
            static::$errorHandlerSet = false;
            restore_error_handler();
        }
    }

    /**
     * @param mixed $value
     * @param int $parameterCount
     * @return string
     * @throws \Exception
     * @api
     * @example example/example1.php 15 1
     */
    public static function stringify($value, $parameterCount = 0)
    {
        if (is_null($value) || is_bool($value)
            || is_int($value) || is_float($value) || is_double($value) || is_numeric($value)
            || is_string($value)
        ) {
            return json_encode($value);
        }
        if ($value instanceof \stdClass) {
            $value = (array)$value;
            if (empty($value)) {
                return '{}';
            }
        }
        if (is_object($value) && in_array("JsonSerializable", class_implements($value))) {
            /**
             * @var $value \JsonSerializable
             */
            $value = $value->jsonSerialize();
        }
        if (is_array($value)) {
            /**
             * @var $value \array
             */
            if ($value === []) {
                return '[]';
            }
            foreach ($value as $key => $item) {
                $value[$key] = static::stringify($item, $parameterCount);
            }
            if (array_keys($value) !== range(0, count($value) - 1)) {//isAssoc
                $result = '{';
                foreach ($value as $key => $v) {
                    $result .= '"' . $key . '":' . $v . ',';
                }
                return rtrim($result, ',') . '}';
            }
            return '[' . implode(',', $value) . ']';
        }
        if ($value instanceof Parameter) {
            /**
             * @var $value Parameter
             */
            return $value->getParameterResult();
        }
        if (is_callable($value)) {
            /**
             * @var $value \callable
             */
            $reflection = new \ReflectionFunction($value);

            $params = [];
            for ($i = 0; $i < count($reflection->getParameters()); $i++) {
                $paramName = "v" . ($i + $parameterCount);
                $params[$paramName] = new Parameter($paramName);
            }
            $parameterCount += count($params);


            static::$errorHandlerSet = true;
            $oldEH = function () {
            };
            $oldEH = set_error_handler(function ($severity, $message, $filename, $lineNumber) use (&$oldEH) {
                $message = preg_replace("/Object of class Kanti\\\\Parameter could not be converted to (.*)/", "Parameter's can not be converted (to $1)", $message, -1, $count);
                if ($count) {
                    throw new StringifyException($message, $filename, $lineNumber, $severity);
                }
                try {
                    $oldEH($severity, $message, $filename, $lineNumber);
                } catch (\Exception $e) {
                    LJSON::restoreErrorHandler();
                    throw $e;
                }
            });
            $newValue = call_user_func_array($value, $params);
            LJSON::restoreErrorHandler();

            return "(" . implode(',', array_keys($params)) . ") => (" . static::stringify($newValue, $parameterCount) . ")";
        }
        throw new \Exception('type cannot be converted ', 1445505204);
    }

    /**
     * @param callable $library
     * @param callable $function
     * @return \Closure
     * @api
     */
    public static function withLib(callable $library, callable $function)
    {
        return function () use ($library, $function) {
            return call_user_func_array($function, array_merge([$library], func_get_args()));
        };
    }

    /**
     * @param callable $function
     * @return \Closure
     * @api
     */
    public static function withStdLib(callable $function)
    {
        $stdLibrary = function ($function, $parameter1, $parameter2 = null) {
            switch ($function) {
                case 'sqrt':
                    return sqrt($parameter1);
            }
            if ($parameter2 === null) {
                return null;
            }
            switch ($function) {
                case '+':
                    return $parameter1 + $parameter2;
                case '-':
                    return $parameter1 - $parameter2;
                case '*':
                    return $parameter1 * $parameter2;
                case '/':
                    return $parameter1 / $parameter2;
            }
            return null;
        };
        return static::withLib($stdLibrary, $function);
    }

    /**
     * @param callable $lib
     * @param string $ljson
     * @param bool|false $assoc
     * @return \Closure
     * @throws \Exception
     * @api
     */
    public static function parseWithLib(callable $lib, $ljson, $assoc = false)
    {
        return static::withLib($lib, static::parse($ljson, $assoc));
    }

    /**
     * @param string $ljson
     * @param bool|false $assoc
     * @return \Closure
     * @throws \Exception
     * @api
     */
    public static function parseWithStdLib($ljson, $assoc = false)
    {
        return static::withStdLib(static::parse($ljson, $assoc));
    }

    /**
     * @param string $ljson
     * @param bool|false $assoc
     * @return string|bool|int|float|null|array|\stdClass|\Closure
     * @throws \Exception
     * @api
     * @example example/example1.php 16 1
     */
    public static function parse($ljson, $assoc = false)
    {
        $pos = 0;
        static::skipSpace($ljson, $pos);
        $resultCode = static::parseValue($ljson, $pos, $assoc);
        static::skipSpace($ljson, $pos);
        if ($pos == strlen($ljson) && $resultCode !== '') {
            return static::evaly($resultCode);
        }
        throw new \Exception('Could not get Parsed', 1445505229);
    }

    /**
     * @param $string
     * @return mixed
     * @throws \Exception
     */
    protected static function evaly($string)
    {
        return eval('return ' . $string . ';');
    }

    /**
     * @param string $chr
     * @return bool
     */
    protected static function isDigit($chr)
    {
        $chr = ord($chr[0]);
        return $chr >= 48 && $chr <= 57;
    }

    /**
     * @param string $string
     * @param int $pos
     * @param string $word
     * @return bool
     */
    protected static function isWord($string, $pos, $word)
    {
        return strlen($string) >= ($pos + strlen($word)) && substr($string, $pos, strlen($word)) === $word;
    }

    /**
     * @param string $string
     * @param int $pos
     */
    protected static function skipSpace($string, &$pos)
    {
        $length = strlen($string);
        while ($length > $pos && $string[$pos] && $string[$pos] <= ' ') {
            $pos++;
        }
    }

    /**
     * @param string $json
     * @param int $pos position in string
     * @param bool|false $assoc
     * @param array $variables
     * @return string
     */
    protected static function parseValue($json, &$pos, $assoc = false, $variables = [])
    {
        $length = strlen($json);
        $result = '';
        //null
        if (static::isWord($json, $pos, 'null')) {
            $pos += 4;
            return "null";
        }

        //true
        if (static::isWord($json, $pos, 'true')) {
            $pos += 4;
            return "true";
        }

        //false
        if (static::isWord($json, $pos, 'false')) {
            $pos += 5;
            return "false";
        }

        //number
        if ($length > $pos && (static::isDigit($json[$pos]) || $json[$pos] == '-')) {
            do {
                $result .= $json[$pos];
                $pos++;
            } while ($length > $pos && static::isDigit($json[$pos]));
            if ($length > $pos && $json[$pos] == '.' && static::isDigit($json[$pos + 1])) {
                $result .= '.';
                $pos++;
                do {
                    $result .= $json[$pos];
                    $pos++;
                } while ($length > $pos && static::isDigit($json[$pos]));
            }
            $exponent = '';
            if ($length > $pos && strtolower($json[$pos]) == 'e') {
                $exponent = 'e';
                $pos++;
                if ($length > $pos && $json[$pos] == '+') {
                    $exponent .= $json[$pos];
                    $pos++;
                } else if ($length > $pos && $json[$pos] == '-') {
                    $exponent .= $json[$pos];
                    $pos++;
                }
                do {
                    $exponent .= $json[$pos];
                    $pos++;
                } while ($length > $pos && static::isDigit($json[$pos]));
            }
            $result .= $exponent;
            return $result;
        }
        //string
        if ($length > $pos && $json[$pos] == '"') {
            $pos++;
            $escape = [
                '"' => '"',
                '\\' => '\\',
                '/' => '/',
                'b' => "\b",
                'f' => "\f",
                'n' => "\n",
                'r' => "\r",
                't' => "\t"
            ];
            while ($length > $pos && $json[$pos] != '"') {
                if ($json[$pos] == '\\') {
                    $pos++;
                    if ($json[$pos] === 'u') {
                        $code = "&#" . hexdec(substr($json, $pos + 1, 4)) . ";";
                        $conVMap = [0x80, 0xFFFF, 0, 0xFFFF];
                        $result .= mb_decode_numericentity($code, $conVMap, 'UTF-8');
                        $pos += 5;
                    } elseif (isset($escape[$json[$pos]])) {
                        $result .= $escape[$json[$pos]];
                        $pos++;
                    }
                } else {
                    $result .= $json[$pos];
                    $pos++;
                }
            }
            $pos++;
            return '"' . str_replace('"', '\"', $result) . '"';
        }
        //array
        if ($length > $pos && $json[$pos] == '[') {
            $pos++;
            $elements = [];
            static::skipSpace($json, $pos);
            if ($length > $pos && $json[$pos] == ']') {
                $pos++;
                return '[]';
            }
            do {
                static::skipSpace($json, $pos);
                $elements[] = static::parseValue($json, $pos, $assoc, $variables);
                static::skipSpace($json, $pos);
            } while ($length > $pos && $json[$pos] == ',' && $pos++);
            static::skipSpace($json, $pos);

            if ($length > $pos && $json[$pos] == ']') {
                $pos++;
                return '[' . implode(',', $elements) . ']';
            }
        }
        //object
        if ($length > $pos && $json[$pos] == '{') {
            $pos++;
            $elements = [];
            do {
                static::skipSpace($json, $pos);
                $string = static::parseValue($json, $pos, $assoc, $variables);
                static::skipSpace($json, $pos);
                if (is_string($string) && $length > $pos && $json[$pos] == ':') {
                    $pos++;
                    static::skipSpace($json, $pos);

                    $elements[$string] = static::parseValue($json, $pos, $assoc, $variables);
                    static::skipSpace($json, $pos);
                }
            } while ($length > $pos && $json[$pos] == ',' && $pos++);
            if ($length > $pos && $json[$pos] == '}') {
                $pos++;
                $result = '[';
                foreach ($elements as $key => $val) {
                    $result .= $key . '=>' . $val . ',';
                }

                $result = trim($result, ',') . ']';
                if (!$assoc) {
                    $result = '(object)' . $result . '';
                }
                return $result;
            }

        }
        //function
        if ($length > $pos && $json[$pos] == '(') {
            $pos++;
            $parameters = [];
            $body = 1;

            $use = $variables;
            do {
                if ($length > $pos && $json[$pos] == 'v' && static::isDigit($json[$pos + 1])) {
                    $parameter = '$v';
                    $pos++;
                    $parameter .= $json[$pos];
                    $pos++;
                    while ($length > $pos && static::isDigit($json[$pos])) {
                        $parameter .= $json[$pos];
                        $pos++;
                    }
                    $parameters[] = $parameter;
                }

            } while ($length > $pos && $json[$pos] == ',' && $pos++);
            $variables += $parameters;
            if ($length > $pos && $json[$pos] == ')') {
                $pos++;

                static::skipSpace($json, $pos);
                if ($length > $pos && $json[$pos] == '=' && $json[$pos + 1] == '>') {
                    $pos += 2;
                    static::skipSpace($json, $pos);
                    if ($length > $pos && $json[$pos] == '(') {
                        $pos++;
                        static::skipSpace($json, $pos);
                        $body = static::parseValue($json, $pos, $assoc, $variables);
                        static::skipSpace($json, $pos);
                    }
                    if ($length > $pos && $json[$pos] == ')') {
                        $pos++;
                        $use = implode(',', $use);
                        if (strlen($use) > 0) {
                            $use = 'use(' . $use . ')';
                        }
                        return 'function(' . implode(',', $parameters) . ')' . $use . '{return ' . $body . ';}';
                    }
                }
            }
        }
        //variable
        if ($length > $pos && $json[$pos] == 'v' && static::isDigit($json[$pos + 1])) {
            $result = '$v';
            $pos++;
            $result .= $json[$pos];
            $pos++;
            while ($length > $pos && static::isDigit($json[$pos])) {
                $result .= $json[$pos];
                $pos++;
            }
            if (!in_array($result, $variables)) {
                //wrong;
                $pos--;
                return '';
            }
            if ($length > $pos && $json[$pos] == '(') {
                $pos++;
                $elements = [];
                static::skipSpace($json, $pos);
                if ($length > $pos && $json[$pos] == ')') {
                    $pos++;
                    $result .= '()';
                    return $result;
                }
                do {
                    static::skipSpace($json, $pos);
                    $elements[] = static::parseValue($json, $pos, $assoc, $variables);
                    static::skipSpace($json, $pos);
                } while ($length > $pos && $json[$pos] == ',' && $pos++);
                static::skipSpace($json, $pos);

                if ($length > $pos && $json[$pos] == ')') {
                    $pos++;
                    $result .= '(' . implode(',', $elements) . ')';
                    return $result;
                }
                return '';
            }
            return $result;
        }
        return '';
    }
}
