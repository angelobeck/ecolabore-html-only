<?php

class eclIo_convert
{

    public static function json2array(string $json, string $fileName = '')
    {
        $index = 0;
        $line = 1;
        return self::json_any2array($json, $index, $fileName, $line);
    }

    private static function json_any2array(string $json, int &$index, string $fileName, int &$line)
    {
        while ($index < strlen($json)) {
            $char = $json[$index];
            switch ($char) {
                case "{":
                    return self::json_object2array($json, $index, $fileName, $line);

                case "[":
                    return self::json_array2array($json, $index, $fileName, $line);

                case '"':
                    $index++;
                    $start = $index;
                    $end = strpos($json, '"', $index);
                    if ($end === false)
                        throw new ErrorException("JSON decode error: missing string ending quote in file $fileName, on line $line");
                    $index = $end + 1;
                    return substr($json, $start, $end - $start);

                case 'f':
                    if (substr($json, $index, 5) === 'false') {
                        $index += 5;
                        return false;
                    }
                    throw new ErrorException("JSON decode error: invalid character in file $fileName on line $line");

                case 't':
                    if (substr($json, $index, 4) === 'true') {
                        $index += 4;
                        return true;
                    }
                    throw new ErrorException("JSON decode error: invalid character in file $fileName on line $line");

                case "\n":
                    $line++;
                case ' ':
                case "\r":
                case "\t":
                    $index++;
                    break;

                default:
                    $length = strspn($json, '-0.123456789', $index);
                    if ($length > 0) {
                        $number = substr($json, $index, $length);
                        $index += $length;
                        $isFloat = strpos($number, '.');
                        if ($isFloat === false)
                            return intval($number);
                        else
                            return floatval($number);
                    }
                    throw new ErrorException("JSON decode error: invalid character in file $fileName on line $line");
            }
        }
        return null;
    }

    private static function json_object2array(string $json, int &$index, string $fileName, int &$line)
    {
        $result = [];
        $index++;
        while ($index < strlen($json)) {
            $char = $json[$index];
            switch ($char) {
                case '}':
                    $index++;
                    return $result;

                case "\n":
                    $line++;
                case " ":
                case "\r":
                case "\t":
                    $index++;
                    break;

                case '"':
                    $index++;
                    $start = $index;
                    $end = strpos($json, '"', $index);
                    if ($end === false)
                        throw new ErrorException("JSON decode error: missing string ending quote in file $fileName, on line $line");
                    $index = $end + 1;
                    $key = substr($json, $start, $end - $start);
                    $dots = strpos($json, ':', $index);
                    if ($dots === false)
                        throw new ErrorException("JSON decode error: missing :  in fileName $fileName, on line $line");
                    $index = $dots + 1;
                    $value = self::json_any2array($json, $index, $fileName, $line);
                    $result[$key] = $value;
                    break;

                case ',':
                    $index++;
                    break;

                default:
                    throw new ErrorException("JSON decode error: invalid character in file $fileName on line $line");

            }
        }
        throw new ErrorException("JSON decode error: missing } in file $fileName on line $line");
    }

    private static function json_array2array(string $json, int &$index, string $fileName, int &$line)
    {
        $index++;
        $result = [];
        while (isset($json[$index])) {
            $char = $json[$index];
            switch ($char) {
                case ']':
                    $index++;
                    return $result;

                case ',':
                    $index++;
                    break;

                case "\n":
                    $line++;
                case ' ':
                case "\r":
                case "\t":
                    $index++;
                    break;

                default:
                    $result[] = self::json_any2array($json, $index, $fileName, $line);
            }
        }
        throw new ErrorException("JSON decode error: missing ] in file $fileName on line $line");
    }

}
