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
 * UpdateOptionsParser
 * Class intended to parse options "update option" string.
 * Update option defines the time or time interval at which a group or feed should be updated.
 * Please read the program documentation to understand update options.
 *
 * Note 1: The parser will stop the execution of the program, display and add an error message
 * to the error logs, if the option is malformatted.
 *
 * Note 2: Remember that you will have to execute the program several times a day to allow
 * updates at the times defined in options. A "cron task" (or "scheduled task") will permit
 * the program to check for outdated groups/streams/feeds and process updates if applicable.
 *
 * Example of correctly formatted options:
 *  --------------------------------------------------------------------
 * | OPTION           | TRANSLATION                                     |
 *  --------------------------------------------------------------------
 * | "each"           | Update each time                                |
 * | "e"              | Update each time                                |
 * | "h{1}"           | Update at 01:00:00                              |
 * | "h{12:0}"        | Update at 12:00:00                              |
 * | "h{16:0:0}"      | Update at 16:00:00                              |
 * | "h{3:30:0}"      | Update at 03:30:00                              |
 * | "h{0:30};h{12}"  | Update at 00:30:00 AND 12:00:00                 |
 * | "every{15m}"     | Update every 15 minutes                         |
 * | "every{60M}"     | Update every 60 minutes                         |
 * | "every{1h}"      | Update every 1 hour                             |
 * | "every{6H}"      | Update every 6 hour                             |
 *  --------------------------------------------------------------------
 */

namespace PHPStreamsAggregator\Controllers;

use PHPStreamsAggregator\Controllers\LogManagerError as ErrorLog;
use PHPStreamsAggregator\Library\Text as TextTools;
use PHPStreamsAggregator\Models\UpdateOptionEach;
use PHPStreamsAggregator\Models\UpdateOptionEvery;
use PHPStreamsAggregator\Models\UpdateOptionEveryTimeTypes;
use PHPStreamsAggregator\Models\UpdateOptionHour;

/**
 * UpdateOptionsParser
 */
class UpdateOptionsParser{

    /**
     * Option string characters
     */

    private const OPTS_SEPARATOR = ";";  // @const string - Options separator
    private const TIME_SEPARATOR = ":";  // @const string - Time separator
    private const TIME_START_CHAR = "{"; // @const string - Time start character
    private const TIME_END_CHAR = "}";   // @const string - Time end character
    private const KEY_EACH_1 = "each";   // @const string - Detection string 1 for option "Each"
    private const KEY_EACH_2 = "e";      // @const string - Detection string 2 for option "Each"
    private const KEY_HOUR = "h";        // @const string - Detection string 1 for option "At hour"
    private const KEY_EVERY = "every";   // @const string - Detection string 1 for option "Every XX units"

    /** @var string - The value which has caused error. **/
    private $errorValue;

    /**
     * Associative array defining the relationship between time units characters
     * and the corresponding type (for options of type "update every xxx interval").
     * @var Array
     **/
    static private $timeUnits;

    /**
     * Get the associative array defining the relationship between time units characters
     * and the corresponding type (for options of type "update every xxx interval").
     * @returns Array
     **/
    static private function getTimeUnits()
    {
        if(!isset(self::$timeUnits)){
            self::$timeUnits = [
                "s" => UpdateOptionEveryTimeTypes::SECONDS,
                "S" => UpdateOptionEveryTimeTypes::SECONDS,
                "m" => UpdateOptionEveryTimeTypes::MINUTES,
                "M" => UpdateOptionEveryTimeTypes::MINUTES,
                "h" => UpdateOptionEveryTimeTypes::HOUR,
                "H" => UpdateOptionEveryTimeTypes::HOUR,
            ];
        }
        return self::$timeUnits;
    }

    /**
     * Check if a time unit character exists in the associative array $timeUnits.
     * @returns boolean - True if the character was found, or False.
     */
    static private function timeUnitExists($char)
    {
        return array_key_exists($char, self::getTimeUnits());
    }

    /**
     * Get the type corresponding to the time unit character.
     * Note: This function does not check if a time unit character exists in the
     * associative array $timeUnits and will throw an error if the time unit character
     * is not valid. Please test the validity of a time unit character by calling the
     * function "timeUnitExists()" before calling this function.
     * @returns number - The time unit
     */
    static private function getTimeUnitValue($char)
    {
        $arr = self::getTimeUnits();
        return $arr[$char];
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->errorValue = null;
    }

    /**
     * Parse a single option.
     * Note: Update options can multiple and separated by comma ";". This function is intended to parse
     * a single option, while the function "parse()" is intended to parse all options.
     * @throw Exception when an error occured while parsing the string.
     * @param string - The option string.
     * @param &UpdateOption[] - A reference to the array in which the UpdateOption will be added.
     */
    private function parseSingle($string, &$options)
    {

        if(strcmp($string, self::KEY_EACH_1) === 0 || strcmp($string, self::KEY_EACH_2) === 0){
            $options[] = new UpdateOptionEach();
            return;
        }
        else{

            // Check if update is "every{something}"
            $evpos = strpos($string, self::KEY_EVERY);
            if($evpos !== false && $evpos == 0){

                $errs = 0;

                $hasStartChar = (strpos($string, self::TIME_START_CHAR) !== false);
                $hasEndChar = (strpos($string, self::TIME_END_CHAR) !== false);

                if($hasStartChar && $hasEndChar){
                    $timeString = TextTools\getBetween($string, self::TIME_START_CHAR, self::TIME_END_CHAR);
                    $len = strlen($timeString);
                    $last = substr($timeString, -1);
                    $bef = substr($timeString, 0, $len - 1);
                    if(ctype_digit($bef) && self::timeUnitExists($last)){
                        $num = intval($bef);
                        $val = self::getTimeUnitValue($last);
                        $options[] = new UpdateOptionEvery($num, $val);
                    }
                    else{
                        $errs++;
                    }
                }
                else{
                    $errs++;
                }

                if($errs > 0){
                    $this->errorValue = $string;
                    throw new \Exception();
                }
                return;

            }

            $hpos = strpos($string, self::KEY_HOUR);
            if($hpos !== false && $hpos == 0){

                $errs = 0;

                $hasStartChar = (strpos($string, self::TIME_START_CHAR) !== false);
                $hasEndChar = (strpos($string, self::TIME_END_CHAR) !== false);

                if($hasStartChar && $hasEndChar){
                    $timeString = TextTools\getBetween($string, self::TIME_START_CHAR, self::TIME_END_CHAR);
                    $exploded = explode(self::TIME_SEPARATOR, $timeString);
                    $countParts = count($exploded);
                    foreach($exploded as $part){
                        if(!is_numeric($part)){
                            $errs++;
                        }
                    }

                    if($countParts == 0){
                        $errs++;
                    }
                    else{
                        if($errs == 0){
                            $option;
                            if($countParts > 2){
                                $option = new UpdateOptionHour($exploded[0], $exploded[1], $exploded[2]);
                            }
                            else if($countParts == 2){
                                $option = new UpdateOptionHour($exploded[0], $exploded[1], 0);
                            }
                            else if($countParts == 1){
                                $option = new UpdateOptionHour($exploded[0], 0, 0);
                            }
                            $options[] = $option;
                        }

                    }

                }
                else{
                    $errs++;
                }

                if($errs > 0){
                    $this->errorValue = $string;
                    throw new \Exception();
                }
                return;
            }

        }

    }

    /**
     * Parse update options.
     * Note: Update options can multiple and separated by comma ";". This function is intended to parse
     * all options, while the function "parseSingle()" is intended to parse a single option.
     * @throw Exception when an error occured while parsing the string.
     * @param string - The option string.
     * @returns &UpdateOption[] - An array containing all instances of UpdateOption.
     */
    public function &parse($string)
    {

        $options = [];

        if(strpos($string, self::OPTS_SEPARATOR) !== false){
            // Possible multiple options
            $tmpOpts = explode(self::OPTS_SEPARATOR, $string);
            foreach($tmpOpts as $tmpOpt){
                try{
                    $this->parseSingle($tmpOpt, $options);
                }
                catch(\Exception $ex){
                    throw $ex;
                }
            }
        }
        else{
            // One options
            try{
                $this->parseSingle($string, $options);
            }
            catch(\Exception $ex){
                throw $ex;
            }
        }

        return $options;

    }

    /**
     * Get the value which has caused an error.
     @returns string|null - The value, or NULL.
     */
    public function getErrorValue()
    {
        return $this->errorValue;
    }

}