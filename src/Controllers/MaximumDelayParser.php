<?php
/**
 * PHP Streams Aggregator
 * Version 1.0.0
 * Author: Christophe Leblanc
 * - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
 * Copyright (C) 2018 - 2020 Christophe Leblanc
 * - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
 * This file is part of "PHP Streams Aggregator".
 *
 * "PHP Streams Aggregator" is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * "PHP Streams Aggregator" is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with "PHP Streams Aggregator".  If not, see
 * <https://www.gnu.org/licenses/>.
 */

/**
 * MaximumDelayParser
 * Class intended to parse options "max delay" string.
 * Max delay option defines the delay after which a stream/feed will be considered as
 * outdated, relating to the "update" option.
 *
 * Note: The parser will stop the execution of the program, display and add an error message
 * to the error logs, if the option is malformatted.
 *
 * Example of correctly formatted options:
 *  --------------------------------------------------------
 * | OPTION  | TRANSLATION  | CORRESPONDING VALUE (IN SECS) |
 *  --------------------------------------------------------
 * | "3600"  | 3600 seconds | 3600                          |
 * | "3600s" | 3600 seconds | 3600                          |
 * | "3600S" | 3600 seconds | 3600                          |
 * | "60m"   | 60 minutes   | 3600                          |
 * | "120M"  | 120 minutes  | 7200                          |
 * | "1h"    | 1 hour       | 3600                          |
 * | "6H"    | 6 hour       | 21600                         |
 * | "1d"    | 1 day        | 86400                         |
 * | "7D"    | 1 day        | 604800                        |
 *  --------------------------------------------------------
 */

namespace PHPStreamsAggregator\Controllers;

use PHPStreamsAggregator\Controllers\LogManagerError as ErrorLog;

/**
 * MaximumDelayParser
 */
class MaximumDelayParser{

    /**
     * Associative array defining the relationship between time units characters
     * and the corresponding number of seconds.
     * @var Array
     **/
    static private $timeUnits;

    /**
     * Get the associative array defining the relationship between time units
     * characters and the corresponding number of seconds.
     * @returns Array
     */
    static private function getTimeUnits()
    {
        if(!isset(self::$timeUnits)){
            self::$timeUnits = [
                "s" => 1,
                "S" => 1,
                "m" => 60,
                "M" => 60,
                "h" => 3600,
                "H" => 3600,
                "d" => 86400,
                "D" => 86400,
            ];
        }
        return self::$timeUnits;
    }

    /**
     * Check if a time unit character exists in the associative array $timeUnits.
     * @returns boolean - True if the character was found, or False.
     */
    static private function timeUnitsExists($char)
    {
        return array_key_exists($char, self::getTimeUnits());
    }

    /**
     * Get the value corresponding to the time unit character.
     * Note: This function does not check if a time unit character exists in the
     * associative array $timeUnits and will throw an error if the time unit character
     * is not valid. Please test the validity of a time unit character by calling the
     * function "timeUnitsExists()" before calling this function.
     * @returns number - The time unit
     */
    static private function getTimeUnitValue($char)
    {
        $arr = self::getTimeUnits();
        return $arr[$char];
    }

    /**
     * Parse the option string.
     * Note: This function will stop the execution of the program, display and add an error message
     * to the error logs, if the option is malformatted.
     * @throw Exception if an error occured while parsing.
     * @param string - The option string.
     */
    static public function parse($str)
    {

        $errs = 0;
        $str = trim($str);
        $len = strlen($str);

        if($len > 0){
            if(ctype_digit($str)){
                return intval($str);
            }
            else{
                $unit = substr($str, -1);
                $num = substr($str, 0, $len - 1);
                if(ctype_digit($num) && self::timeUnitsExists($unit)){
                    $int = intval($num);
                    $val = self::getTimeUnitValue($unit);
                    return intval($int * $val);
                }
                else{
                    $errs++;
                }
            }
        }
        else{
            $errs++;
        }

        if($errs > 0){
            throw new \Exception();
        }

        return 0;

    }

}