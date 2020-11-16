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
 * LogManagerCompletion
 * Class used to add a message to the log file on process completion.
 */

namespace PHPStreamsAggregator\Controllers;

use PHPStreamsAggregator\Constants as Con;
use PHPStreamsAggregator\Data;
use PHPStreamsAggregator\Controllers\LogManager;

/**
 * LogManagerCompletion
 */
class LogManagerCompletion extends LogManager{

	/** @var string The path of the file. */
	static $logFilePath;

	/**
	 * Add a message to the log file.
	 * @param string The message to add.
	 */
	static public function addLog($message)
    {
		parent::addLogIn(self::$logFilePath, $message);
	}

    /**
     * Instanciate the class Singleton.
     * Warning: Please instanciate this class singleton AFTER instanciation
     * of the class "Data" singleton.
     */
    static public function instanciate()
    {
        self::$logFilePath = Data::$LOGS_ABSOLUTE_PATH . DIRECTORY_SEPARATOR . Con::COMPLETIONLOG_FILENAME;
    }

}