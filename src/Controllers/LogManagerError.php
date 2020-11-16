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
 * LogManagerError
 * Class used to add a message to the log file during the various errors.
 */

namespace PHPStreamsAggregator\Controllers;

use PHPStreamsAggregator\Constants as Con;
use PHPStreamsAggregator\Data;
use PHPStreamsAggregator\Controllers\LogManager;

/**
 * LogManagerError
 */
class LogManagerError extends LogManager{

    /**
     * Enumeration of error codeStr
     */
    public const WARNING = 1;
    public const FATAL = 2;


	/** @var string The path of the file. */
	static $logFilePath;

    /**
     * Associative array defining the relationship between error codes
     * and the corresponding message.
     * @var Array
     **/
    static private $codeValues;

    /**
     * Get the associative array defining the relationship between error codes
     * and the corresponding message.
     * @returns Array
     */
    static private function getCodeValues()
    {
        if(!isset(self::$codeValues)){
            self::$codeValues = [
                self::WARNING => "[WARNING]",
                self::FATAL => "[FATAL]"
            ];
        }
        return self::$codeValues;
    }

    /**
     * Check if an error code exists.
     * @returns boolean - True if the error code was found, or False.
     */
    static private function codeExists($v)
    {
        return array_key_exists($v, self::getCodeValues());
    }

    /**
	 * Get error code value corresponding to an error code.
     * Note: This function does not check if an error code exists in the
     * associative array $codeValues and will throw an error if the error code
     * is not valid. Please test the validity of an error code by calling the
     * function "codeExists()" before calling this function.
	 * @param integer - The error code
     * @param string - The error message corresponding to the error code.
	 */
    static private function getCodeValue($code)
    {
        $arr = self::getCodeValues();
        return $arr[$code];
    }

	/**
	 * Add a message to the log file.
     * Note: You can add an optional error code to display a special error
     * code information at the start of the message.
	 * @param string The message to add.
     * @param integer|null The Error Code of the message, or Null.
	 */
	static public function addLog($message, $code = null)
    {
        
        $codeStr;
        if($code !== null && self::codeExists($code)){
            $codeStr = self::getCodeValue($code) . " ";
        }
        else{
            $codeStr = "";
        }
        
		parent::addLogIn(self::$logFilePath, $codeStr . $message);
	}

    /**
     * Instanciate the class Singleton.
     * Warning: Please instanciate this class singleton AFTER instanciation
     * of the class "Data" singleton.
     */
    static public function instanciate()
    {
        self::$logFilePath = Data::$LOGS_ABSOLUTE_PATH . DIRECTORY_SEPARATOR . Con::ERRORLOG_FILENAME;
    }

}