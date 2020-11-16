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
 * DateTimeNumeric
 * Class representing a date, especially used to compare different date/times.
 */

namespace PHPStreamsAggregator\Models;

/**
 * DateTimeNumeric
 */
class DateTimeNumeric{

	/** @var int - The timestamp of the date. */
	private $timestamp;

	/** @var int - Day. */
	private $day;

	/** @var int - Hour. */
	private $hours;

	/** @var int - Minutes. */
	private $minutes;

	/** @var int - Month. */
	private $month;

	/** @var int - Seconds. */
	private $seconds;

	/** @var int - Year. */
	private $year;

    /** @DateTime - An instance of DateTime corresponding to the current object **/
	private $datetime;

	/**
	* Constructor
    * @param integer|null - The timestamp of this object.
	*/
	function __construct(&$timestamp = null)
    {

		if(!isset($timestamp) || $timestamp == null){
			$timestamp = time();
		}

		$this->timestamp = $timestamp;

		$localtime = localtime($timestamp);

		$this->day = intval($localtime[3]);
		$this->month = 1 + intval($localtime[4]);
		$this->year = 1900 + intval($localtime[5]);
		$this->minutes = intval($localtime[1]);
		$this->hours = intval($localtime[2]);
		$this->seconds = intval($localtime[0]);

		$this->datetime = new \DateTime();
		$this->datetime->setDate($this->year, $this->month, $this->day);
		$this->datetime->setTime($this->hours, $this->minutes, $this->seconds);

	}

	/**
	* Get the day.
	* @return int The day.
	*/
	public function getDay()
    {
		return $this->day;
	}

	/**
	* Get the hours.
	* @return int The hours.
	*/
	public function getHours()
    {
		return $this->hours;
	}

	/**
	* Get the minutes.
	* @return int The minutes.
	*/
	public function getMinutes()
    {
		return $this->minutes;
	}

	/**
	* Get the month.
	* @return int The month.
	*/
	public function getMonth()
    {
		return $this->month;
	}

	/**
	* Get the seconds.
	* @return int The seconds.
	*/
	public function getSeconds()
    {
		return $this->seconds;
	}

	/**
	* Get the timestamp of the date.
	* @return int The timestamp.
	*/
	public function getTimestamp()
    {
		return $this->timestamp;
	}

	/**
	* Get the year.
	* @return int The year.
	*/
	public function getYear()
    {
		return $this->year;
	}

	/**
	* Get the current date as an instance of DateTime .
	* @return &DateTime A reference to the DateTime object.
	*/
	public function &getDateTime()
    {
		return $this->datetime;
	}

	/**
	* Return a string representation of the object created from this class.
	* @return string The representation of the object.
	*/
	public function __toString()
    {
		$s = get_class($this) . " [ ";
		$s .= "day : " . $this->day . ", ";
		$s .= "month : " . $this->month . ", ";
		$s .= "year : " . $this->year . ", ";
		$s .= "hours : " . $this->hours . ", ";
		$s .= "minutes : " . $this->minutes . ", ";
		$s .= "seconds : " . $this->seconds . " - ";
        $s .= "timestamp : " . $this->timestamp . " ]";
		return $s;
	}

}
