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
 * ParsedGroup
 * Class representing group of parsed streams in a ParsedList.
 */

namespace PHPStreamsAggregator\Models;

/**
 * ParsedGroup
 */
class ParsedGroup{

	/** @var StreamsGroup           - Instance of StreamsGroup of this group. */
	private $stream;

	/** @var StreamsGroupState  - Instance of StreamsGroupState of this group. */
	private $state;

	/** @var ParsedStream[]         - Array containing parsed objects/entities. */
	private $children;

	/** @var string                 - The id of the group. */
	private $id;

	/**
	 * Constructor
     * @param &StreamsGroup           - Instance of StreamsGroup of this group
     * @param &StreamsGroupState  - Instance of StreamsGroupState of this group
	 * @param string                  - The ID/name of the download
	 * @param ParsedStream[]          - Array containing parsed objects/entities
	 */
	public function __construct(&$stream, &$state, $id, &$children)
    {
        $this->stream = $stream;
        $this->state = $state;
		$this->id = $id;
		$this->children = $children;
	}

	/**
	 * Get the ID of the group.
	 * @return string
	 */
	public function getId()
    {
		return $this->id;
	}

	/**
	 * Get the children of this groups.
	 * @return &ParsedStream[]
	 */
	public function &getChildren()
    {
		return $this->children;
	}

	/**
	 * Get the instance of StreamsGroup of this group.
	 * @return &StreamsGroup
	 */
	public function &getStreamsListNode()
    {
		return $this->stream;
	}

	/**
	 * Get the instance of StreamsGroupState of this group.
	 * @return &StreamsGroupState
	 */
	public function &getStateNode()
    {
		return $this->state;
	}

	/**
	 * Get a child by its id/key or NULL if no child was found.
     * @param   string             - The id/key
	 * @returns ParsedStream|null  - The child, or NULL.
	 */
	public function &getChild($key)
    {
		if(array_key_exists($key, $this->children)){
            return $this->children[$key];
        }
        $r = null;
        return $r;
	}

}