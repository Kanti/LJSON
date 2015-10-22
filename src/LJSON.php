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
        if (is_null($value) || is_bool($value) || is_int($value) || is_float($value) || is_double($value) || is_numeric($value) || is_string($value)) {
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
            } else {
                return '[' . implode(',', $value) . ']';
            }
        }
        if ($value instanceof Parameter) {
            return (string)$value;
        }
        if (is_callable($value)) {
            $reflection = new \ReflectionFunction($value);
            $x = count($reflection->getParameters());

            $params = [];
            for ($i = 0; $i < $x; $i++) {
                $params["v" . $i] = new Parameter("v" . $i);
            }
            $newValue = call_user_func_array($value, $params);
            return "(" . implode(',', array_keys($params)) . ") => (" . static::stringify($newValue) . ")";
        }
        throw new \Exception;
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
        $length = strlen($json);
        while ($length > $i && $json[$i] && $json[$i] <= ' ') $i++;
        $result = static::parseValue($json, $i, $assoc);
        while ($length > $i && $json[$i] && $json[$i] <= ' ') $i++;
        if ($i == strlen($json)) {
            $result = @eval('return ' . $result . ';');
            if (!error_get_last()) {
                return $result;
            }
            $error = error_get_last();
            throw new \Exception($error['type'] . ' ' . $error['message']);
        }
        throw new \Exception;
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
     * @param string $json
     * @param int $i position in string
     * @param bool|false $assoc
     * @return string
     */
    protected static function parseValue($json, &$i, $assoc = false)
    {
        $length = strlen($json);
        $result = '';
        //null
        if ($length > $i + 3 && $json[$i] == 'n' && $json[$i + 1] == 'u' && $json[$i + 2] == 'l' && $json[$i + 3] == 'l') {
            $i += 4;
            return "null";
        }

        //true
        if ($length > $i + 3 && $json[$i] == 't' && $json[$i + 1] == 'r' && $json[$i + 2] == 'u' && $json[$i + 3] == 'e') {
            $i += 4;
            return "true";
        }

        //false
        if ($length > $i + 4 && $json[$i] == 'f' && $json[$i + 1] == 'a' && $json[$i + 2] == 'l' && $json[$i + 3] == 's' && $json[$i + 4] == 'e') {
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
            $escape = array('"' => '"', '\\' => '\\', '/' => '/', 'b' => "\b", 'f' => "\f", 'n' => "\n", 'r' => "\r", 't' => "\t");
            while ($length > $i && $json[$i] != '"') {
                if ($json[$i] == '\\') {
                    $i++;
                    if ($json[$i] === 'u') {
                        $code = "&#" . hexdec(substr($json, $i + 1, 4)) . ";";
                        $conVMap = array(0x80, 0xFFFF, 0, 0xFFFF);
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
            return var_export($result, true);
        }
        //array
        if ($length > $i && $json[$i] == '[') {
            $i++;
            $elements = [];
            while ($length > $i && $json[$i] && $json[$i] <= ' ') $i++;
            if ($length > $i && $json[$i] == ']') {
                $i++;
                return '[]';
            }
            do {
                while ($length > $i && $json[$i] && $json[$i] <= ' ') $i++;
                $elements[] = static::parseValue($json, $i, $assoc);
                while ($length > $i && $json[$i] && $json[$i] <= ' ') $i++;
            } while ($length > $i && $json[$i] == ',' && $i++);
            while ($length > $i && $json[$i] && $json[$i] <= ' ') $i++;

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
                while ($length > $i && $json[$i] && $json[$i] <= ' ') $i++;
                $string = static::parseValue($json, $i, $assoc);
                while ($length > $i && $json[$i] && $json[$i] <= ' ') $i++;
                if (is_string($string) && $length > $i && $json[$i] == ':') {
                    $i++;
                    while ($length > $i && $json[$i] && $json[$i] <= ' ') $i++;

                    if ($assoc) {
                        $elements[$string] = static::parseValue($json, $i, $assoc);
                    } else {
                        $elements[$string] = static::parseValue($json, $i, $assoc);
                    }
                    while ($length > $i && $json[$i] && $json[$i] <= ' ') $i++;
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
            if ($length > $i && $json[$i] == ')') {
                $i++;

                while ($length > $i && $json[$i] && $json[$i] <= ' ') $i++;
                if ($length > $i && $json[$i] == '=' && $json[$i + 1] == '>') {
                    $i += 2;
                    while ($length > $i && $json[$i] && $json[$i] <= ' ') $i++;
                    if ($length > $i && $json[$i] == '(') {
                        $i++;
                        while ($length > $i && $json[$i] && $json[$i] <= ' ') $i++;
                        $body = static::parseValue($json, $i, $assoc);
                        while ($length > $i && $json[$i] && $json[$i] <= ' ') $i++;
                    }
                    if ($length > $i && $json[$i] == ')') {
                        $i++;
                        return 'function(' . implode(',', $parameters) . '){return ' . $body . ';}';
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
            return $result;
        }
        return '';
    }
}