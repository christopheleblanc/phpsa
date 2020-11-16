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
 * UpdateOptionHour
 * Class representing an update option of type "Hour".
 * This type of update options means that an update must being processed at a
 * specific hour of the day.
 *
 * The update option is obtained by parsing the configuration files.
 *
 * Example of correctly formatted option in configuration files:
 *  --------------------------------------------------------------------
 * | OPTION           | TRANSLATION                                     |
 *  --------------------------------------------------------------------
 * | "h{1}"           | Update at 01:00:00                              |
 * | "h{12:0}"        | Update at 12:00:00                              |
 * | "h{16:0:0}"      | Update at 16:00:00                              |
 * | "h{3:30:0}"      | Update at 03:30:00                              |
 * | "h{0:30};h{12}"  | Update at 00:30:00 AND 12:00:00                 |
 *  --------------------------------------------------------------------
 */

namespace PHPStreamsAggregator\Models;

use PHPStreamsAggregator\Models\DateTimeNumeric;
use PHPStreamsAggregator\Models\UpdateOption;
use PHPStreamsAggregator\Models\UpdateOptionTypes;

/**
 * UpdateOptionHour
 */
class UpdateOptionHour extends UpdateOption{

    /** @var int The hour of the update time. */
    private $hours;

    /** @var int The minutes of the update time. */
    private $minutes;
    
    /** @var int The seconds of the update time. */
    private $seconds;

	/**
	 * Constructor
     * @var int The hour of the update time
     * @var int The minutes of the update time
     * @var int The seconds of the update time
	 */
	public function __construct($hours = 0, $minutes = 0, $seconds = 0)
    {
        $this->hours = $hours;
        $this->minutes = $minutes;
        $this->seconds = $seconds;
		parent::__construct(UpdateOptionTypes::HOUR);
	}

    /**
	 * Get the hour of the update time
     * @returns int The hour of the update time
	 */
    public function &getHours()
    {
        return $this->hours;
    }

    /**
	 * Get the minutes of the update time
     * @returns int The minutes of the update time
	 */
    public function &getMinutes()
    {
        return $this->minutes;
    }

    /**
	 * Get the seconds of the update time
     * @returns int The seconds of the update time
	 */
    public function &getSeconds()
    {
        return $this->seconds;
    }

    /**
	 * Convert the UpdateOptionHour to an instance of DateTimeNumeric.
     * Note: Day is defined as the current day.
     * @returns DateTimeNumeric An instance of DateTimeNumeric
	 */
    public function toDateTimeNumeric()
    {
        $now = new \DateTime();
        $nowLocal = localtime($now->getTimestamp());
		
		$nowDay = intval($nowLocal[3]);
		$nowMonth = 1 + intval($nowLocal[4]);
		$nowYear = 1900 + intval($nowLocal[5]);
		$nowMinutes = intval($nowLocal[1]);
		$nowHours = intval($nowLocal[2]);
		$nowSeconds = intval($nowLocal[0]);

        $dt = new \DateTime();
		//$dt->setDate($nowYear, $nowMonth, $nowDay);
		$dt->setTime($this->hours, $this->minutes, $this->seconds);
        $ts = $dt->getTimestamp();
        return new DateTimeNumeric($ts);
    }

}