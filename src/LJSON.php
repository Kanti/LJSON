<?php
namespace Kanti;

/**
 * Class LJSON
 * @package Kanti
 */
class LJSON
{
    /**
     * @param mixed $value
     * @return string
     * @throws \Exception
     */
    public static function stringify($value)
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
        if (is_object($value) && method_exists($value, 'jsonSerialize')) {
            $value = $value->jsonSerialize();
        }
        if (is_array($value)) {
            if ($value === []) {
                return '[]';
            }
            $value = array_map([__CLASS__, __FUNCTION__], $value);
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
            return (string)$value;
        }
        if (is_callable($value)) {
            $reflection = new \ReflectionFunction($value);

            $params = [];
            for ($i = 0; $i < count($reflection->getParameters()); $i++) {
                $params["v" . $i] = new Parameter("v" . $i);
            }
            $newValue = call_user_func_array($value, $params);
            return "(" . implode(',', array_keys($params)) . ") => (" . static::stringify($newValue) . ")";
        }
        throw new \Exception('type cannot be converted ', 1445505204);
    }

    /**
     * @param callable $library
     * @param callable $function
     * @return \Closure
     */
    public static function withLib(callable $library, callable $function)
    {
        return function () use ($library, $function) {
            return call_user_func_array($function, array_merge([$library], func_get_args()));
        };
    }

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
     * @param string $json
     * @param bool|false $assoc
     * @return \Closure
     * @throws \Exception
     */
    public static function parseWithLib(callable $lib, $json, $assoc = false)
    {
        return static::withLib($lib, static::parse($json, $assoc));
    }

    /**
     * @param string $json
     * @param bool|false $assoc
     * @return \Closure
     * @throws \Exception
     */
    public static function parseWithStdLib($json, $assoc = false)
    {
        return static::withStdLib(static::parse($json, $assoc));
    }

    /**
     * @param string $json
     * @param bool|false $assoc
     * @return mixed|\Closure
     * @throws \Exception
     */
    public static function parse($json, $assoc = false)
    {
        $i = 0;
        static::skipSpace($json, $i);
        $resultCode = static::parseValue($json, $i, $assoc);
        static::skipSpace($json, $i);
        if ($i == strlen($json) && $resultCode !== '') {
            return static::evaly($resultCode);
        }
        throw new \Exception('Could not get Parsed', 1445505229);
    }

    /**
     * @param $string
     * @return mixed
     * @throws \Exception
     */
    public static function evaly($string)
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
     * @param int $i
     */
    protected static function skipSpace($string, &$i)
    {
        $length = strlen($string);
        while ($length > $i && $string[$i] && $string[$i] <= ' ') {
            $i++;
        }
    }

    /**
     * @param string $json
     * @param int $i position in string
     * @param bool|false $assoc
     * @param array $variables
     * @return string
     */
    protected static function parseValue($json, &$i, $assoc = false, $variables = [])
    {
        $length = strlen($json);
        $result = '';
        //null
        if (static::isWord($json, $i, 'null')) {
            $i += 4;
            return "null";
        }

        //true
        if (static::isWord($json, $i, 'true')) {
            $i += 4;
            return "true";
        }

        //false
        if (static::isWord($json, $i, 'false')) {
            $i += 5;
            return "false";
        }

        //number
        if ($length > $i && (static::isDigit($json[$i]) || $json[$i] == '-')) {
            do {
                $result .= $json[$i];
                $i++;
            } while ($length > $i && static::isDigit($json[$i]));
            if ($length > $i && $json[$i] == '.' && static::isDigit($json[$i + 1])) {
                $result .= '.';
                $i++;
                do {
                    $result .= $json[$i];
                    $i++;
                } while ($length > $i && static::isDigit($json[$i]));
            }
            $exponent = '';
            if ($length > $i && strtolower($json[$i]) == 'e') {
                $exponent = 'e';
                $i++;
                if ($length > $i && $json[$i] == '+') {
                    $exponent .= $json[$i];
                    $i++;
                } else if ($length > $i && $json[$i] == '-') {
                    $exponent .= $json[$i];
                    $i++;
                }
                do {
                    $exponent .= $json[$i];
                    $i++;
                } while ($length > $i && static::isDigit($json[$i]));
            }
            $result .= $exponent;
            return $result;
        }
        //string
        if ($length > $i && $json[$i] == '"') {
            $i++;
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
            while ($length > $i && $json[$i] != '"') {
                if ($json[$i] == '\\') {
                    $i++;
                    if ($json[$i] === 'u') {
                        $code = "&#" . hexdec(substr($json, $i + 1, 4)) . ";";
                        $conVMap = [0x80, 0xFFFF, 0, 0xFFFF];
                        $result .= mb_decode_numericentity($code, $conVMap, 'UTF-8');
                        $i += 5;
                    } elseif (isset($escape[$json[$i]])) {
                        $result .= $escape[$json[$i]];
                        $i++;
                    }
                } else {
                    $result .= $json[$i];
                    $i++;
                }
            }
            $i++;
            return '"' . str_replace('"', '\"', $result) . '"';
        }
        //array
        if ($length > $i && $json[$i] == '[') {
            $i++;
            $elements = [];
            static::skipSpace($json, $i);
            if ($length > $i && $json[$i] == ']') {
                $i++;
                return '[]';
            }
            do {
                static::skipSpace($json, $i);
                $elements[] = static::parseValue($json, $i, $assoc, $variables);
                static::skipSpace($json, $i);
            } while ($length > $i && $json[$i] == ',' && $i++);
            static::skipSpace($json, $i);

            if ($length > $i && $json[$i] == ']') {
                $i++;
                return '[' . implode(',', $elements) . ']';
            }
        }
        //object
        if ($length > $i && $json[$i] == '{') {
            $i++;
            $elements = [];
            do {
                static::skipSpace($json, $i);
                $string = static::parseValue($json, $i, $assoc, $variables);
                static::skipSpace($json, $i);
                if (is_string($string) && $length > $i && $json[$i] == ':') {
                    $i++;
                    static::skipSpace($json, $i);

                    $elements[$string] = static::parseValue($json, $i, $assoc, $variables);
                    static::skipSpace($json, $i);
                }
            } while ($length > $i && $json[$i] == ',' && $i++);
            if ($length > $i && $json[$i] == '}') {
                $i++;
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
        if ($length > $i && $json[$i] == '(') {
            $i++;
            $parameters = [];
            $body = 1;

            $use = $variables;
            do {
                if ($length > $i && $json[$i] == 'v' && static::isDigit($json[$i + 1])) {
                    $parameter = '$v';
                    $i++;
                    $parameter .= $json[$i];
                    $i++;
                    while ($length > $i && static::isDigit($json[$i])) {
                        $parameter .= $json[$i];
                        $i++;
                    }
                    $parameters[] = $parameter;
                }

            } while ($length > $i && $json[$i] == ',' && $i++);
            $variables += $parameters;
            if ($length > $i && $json[$i] == ')') {
                $i++;

                static::skipSpace($json, $i);
                if ($length > $i && $json[$i] == '=' && $json[$i + 1] == '>') {
                    $i += 2;
                    static::skipSpace($json, $i);
                    if ($length > $i && $json[$i] == '(') {
                        $i++;
                        static::skipSpace($json, $i);
                        $body = static::parseValue($json, $i, $assoc, $variables);
                        static::skipSpace($json, $i);
                    }
                    if ($length > $i && $json[$i] == ')') {
                        $i++;
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
        if ($length > $i && $json[$i] == 'v' && static::isDigit($json[$i + 1])) {
            $result = '$v';
            $i++;
            $result .= $json[$i];
            $i++;
            while ($length > $i && static::isDigit($json[$i])) {
                $result .= $json[$i];
                $i++;
            }
            if (!in_array($result, $variables)) {
                //wrong;
                $i--;
                return '';
            }
            if ($length > $i && $json[$i] == '(') {
                $i++;
                $elements = [];
                static::skipSpace($json, $i);
                if ($length > $i && $json[$i] == ')') {
                    $i++;
                    $result .= '()';
                    return $result;
                }
                do {
                    static::skipSpace($json, $i);
                    $elements[] = static::parseValue($json, $i, $assoc, $variables);
                    static::skipSpace($json, $i);
                } while ($length > $i && $json[$i] == ',' && $i++);
                static::skipSpace($json, $i);

                if ($length > $i && $json[$i] == ')') {
                    $i++;
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
