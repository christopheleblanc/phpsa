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
 * ErrorLogParser
 * Class intended to parse error logs. Can be used to monitor the program from 
 * external tools.
 */

namespace PHPStreamsAggregator\Controllers;

use PHPStreamsAggregator\Constants as Con;
use PHPStreamsAggregator\Library\Files;
use PHPStreamsAggregator\Models\ErrorLogError;

/**
 * ErrorLogParser
 */
class ErrorLogParser{

	/** @var int The total number of errors/lines contained in the log file. */
	private $totalErrors;

	/** @var array[] An array of the last errors. */
	private $lastErrors;

	/** @var array[] An array of chars used to parse error log messages. */
	private $parseChars = array("[", "]");

	/**
	* Constructor
	*/
	function __construct()
    {

		$this->lastErrors = [];

		$path = Con::DATA_DIR_NAME . DIRECTORY_SEPARATOR .Con::LOG_DIR_NAME . DIRECTORY_SEPARATOR .
        Con::ERRORLOG_FILENAME;

		if(file_exists($path)){

			$tmpTotalLines = Files\countNumberOfLines($path);

			$offset = 0;

			$lastLineContent = Files\getLine($path, $tmpTotalLines - 1);
			if(strcmp($lastLineContent, "") == 0){
				$offset++;
				$this->totalErrors = $tmpTotalLines - 1;
			}
			else{
				$this->totalErrors = $tmpTotalLines;
			}

			$start = $tmpTotalLines - (10 + $offset);
			$end = $tmpTotalLines - (0 + $offset);
			$lines = Files\getLines($path, $start, $end);

			foreach($lines as $line){

				$error = $this->createErrorFromMessage($line, $this->parseChars[0], $this->parseChars[1]);
				if($error !== false){
					array_push($this->lastErrors, $error);
				}

			}

		}
		else{

			$this->totalErrors = 0;

		}

	}

	/**
	* Get the date and the message from an Error Log line.
	* @param string The string to search in.
	* @param string The character delimiting the start of the string to return.
	* @param string The character delimiting the end of the string to return.
	* @return ErrorLogError The error object.
	*/
	private function createErrorFromMessage($haystack, $start, $end)
    {

		$haystack = ' ' . $haystack;
		$ini = strpos($haystack, $start);
		if ($ini == 0){
			 return false;
		}

		$ini += strlen($start);
		$len = strpos($haystack, $end, $ini) - $ini;
		if($len == 0){
			return false;
		}

		$dateStr = substr($haystack, $ini, $len);

		$mi = $len + 3;
		$message = substr($haystack, $mi, strlen($haystack) - $mi);

		$date = @\DateTime::createFromFormat('Y-m-d H:i:s', trim($dateStr));
		if($date === false){
			return false;
		}
		$timestamp = $date->getTimestamp();

		$message = trim($message);

		return new ErrorLogError($timestamp, $message);

	}

	/**
	* Get the number of errors.
	* @return int The number of errors.
	*/
	public function &getTotalErrors()
    {
		return $this->totalErrors;
	}

	/**
	* Get the last error log message.
	* @return int The last error log message, as a timestamp.
	*/
	public function &getLastError()
    {
		$total = count($this->lastErrors);
		if($total > 0){
			$last = $this->lastErrors[$total - 1];
			return $last;
		}
		else{
			$out = false;
			return $out;
		}
	}

}