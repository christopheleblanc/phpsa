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
 * ParsedList
 * Class representing a parsed streams list.
 */

namespace PHPStreamsAggregator\Models;

/**
 * ParsedList
 */
class ParsedList{

	/** @var Array Parsed entities/objects. */
	private $children;

	/**
	 * Constructor
	 * @param ParsedGroup[] - Children
	 */
	public function __construct(&$children)
    {
		$this->children = $children;
	}

	/**
	 * Get a child by its name, or null if no child was found.
	 * @return &ParsedGroup|null
	 */
	public function &getChild($key)
    {
		if(array_key_exists($key, $this->children)){
            return $this->children[$key];
        }
        $r = null;
        return $r;
	}

	/**
	 * Get the array of children
	 * @return &ParsedGroup[]
	 */
	public function &getChildren()
    {
		return $this->children;
	}

}