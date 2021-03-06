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
 * UpdateOption
 * Parent class for all types of Update options.
 */ 

namespace PHPStreamsAggregator\Models;

/**
 * UpdateOption
 */ 
abstract class UpdateOption{

	/** @var int The type of this option. */
	protected $optionType;

	/**
	* Constructor
    * @var int The type of this option.
	*/
	public function __construct($type)
    {
		$this->optionType = $type;
	}

    /**
	* Get the type of the UpdateOption.
    * @returns int The type ot the UpdateOption
	*/
    public function getType()
    {
        return $this->optionType;
    }

}