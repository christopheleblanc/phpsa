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
 * LogManager
 * Abstract class used as parent of different log managing classes used to store
 * log messages into a file.
 */

namespace PHPStreamsAggregator\Controllers;

/**
 * LogManager
 */
abstract class LogManager{

	/** @var int The timestamp of the message. */
	static private $timestamp;

	/**
	 * Add a message to the log file.
	 * @param string The path of the log file.
	 * @param string The message to add.
	 */
	static public function addLogIn($logFilePath, $insert)
    {

		if(!isset(self::$timestamp)){
			self::$timestamp = "[ " . date("Y-m-d H:i:s") . " ] ";
		}

		$timestamp = self::$timestamp;
		$txt = $timestamp . $insert;

		if(!file_exists($logFilePath)){
			
			$file = fopen($logFilePath, "w");
			if($file !== false){
				fwrite($file, $txt . PHP_EOL);
				fclose($file);
			
			}
		}
		else{

			$filePut = file_put_contents($logFilePath, $txt . PHP_EOL , FILE_APPEND | LOCK_EX);

		}

		// Apply chmod 777 to the file (Read, Write, Execute for all)
		chmod($logFilePath, 0777);

	}

}