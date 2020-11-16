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
 * UpdateOptionEvery
 * Class representing an update option of type "Every".
 * This type of update options means that an update must being processed at a
 * specific interval (in seconds, minutes).
 *
 * The update option is obtained by parsing the configuration files.
 *
 * Example of correctly formatted option in configuration files:
 *  --------------------------------------------------------------------
 * | OPTION           | TRANSLATION                                     |
 *  --------------------------------------------------------------------
 * | "every{15m}"     | Update every 15 minutes                         |
 * | "every{60M}"     | Update every 60 minutes                         |
 * | "every{1h}"      | Update every 1 hour                             |
 * | "every{6H}"      | Update every 6 hour                             |
 *  --------------------------------------------------------------------
 */

namespace PHPStreamsAggregator\Models;

use PHPStreamsAggregator\Models\UpdateOption;
use PHPStreamsAggregator\Models\UpdateOptionTypes;

/**
 * UpdateOptionEvery
 */
class UpdateOptionEvery extends UpdateOption{

    /** @var int The number of units between updates. */
    private $num;
    
    /**
     * @var int The type/unit between updates (seconds, minutes, hours).
     * @see class UpdateOptionEveryTimeTypes
     */
    private $timeType;

	/**
	* Constructor
    * @var int The number of units between updates
    * @var int The type/unit between updates (seconds, minutes, hours)
	*/
	function __construct($num, $timeType)
    {
        $this->num = $num;
        $this->timeType = $timeType;
		parent::__construct(UpdateOptionTypes::EVERY);
	}

    /**
     * Get the number of units between updates.
     * @returns int The number of units between updates.
     */
    public function &getNumber()
    {
        return $this->num;
    }

    /**
     * Get the type/unit between updates (seconds, minutes, hours).
     * @returns int The type/unit between updates (seconds, minutes, hours)
     * @see class UpdateOptionEveryTimeTypes
     */
    public function &getTimeType()
    {
        return $this->timeType;
    }
	
}