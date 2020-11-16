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
 * ErrorLogError
 * Class representing an Error in the error log.
 */

namespace PHPStreamsAggregator\Models;

/**
 * ErrorLogError
 */
class ErrorLogError{

	/** @var int The timestamp of the error. */
	private $timestamp;

	/** @var string The message of the error. */
	private $message;

	/**
	* Constructor
	*/
	function __construct(&$timestamp, &$message)
    {
		$this->timestamp = $timestamp;
		$this->message = $message;
	}

	/**
	* Get the message of the error.
	* @return string The message of the error.
	*/
	public function getMessage()
    {
		return $this->message;
	}

	/**
	* Get the timestamp of the error.
	* @return int The timestamp of the error.
	*/
	public function getTimestamp()
    {
		return $this->timestamp;
	}

}