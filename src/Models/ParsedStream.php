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
 * ParsedStream
 */

namespace PHPStreamsAggregator\Models;

/**
 * ParsedStream
 * Class representing a parsed stream in a ParsedGroup.
 */
class ParsedStream{

	/** @var Array Parsed entities/objects. */
	private $stream;

	/** @var string The id of the stream. */
	private $state;

	/** @var Array Parsed entities/objects. */
	private $children;

	/** @var string The id of the stream. */
	private $id;

	/**
     * Defines if the stream is up to date (relating to the update option)
     * @var boolean
     */
    private $isUpToDate;

	/**
     * Defines if the stream is late (relating to the update option and maximum delay)
     * @var boolean
     */
    private $isLate;

	/**
     * Defines if the stream is outdated (relating to the update option and maximum delay)
     * @var boolean
     */
    private $isOutdated;

	/**
	 * Constructor
	 * @param int The type of download (0 = URL, 1 = FTP)
	 * @param string The full name of the download.
	 * @param string The ID/name of the download.
	 * @param boolean Whether the download is enabled or not.
	 * @param string The name of the parser for this download.
	 * @param boolean Defines if the loaded response must be validated before considered as "downloaded".
	 * @param string The name of the sorter, if boolean "sort" is true.
	 */
	public function __construct(&$stream, &$state, $id, &$children, $isUpToDate, $isLate, $isOutdated)
    {
        $this->stream = $stream;
        $this->state = $state;
		$this->id = $id;
		$this->children = $children;
        $this->isUpToDate = $isUpToDate;
        $this->isLate = $isLate;
        $this->isOutdated = $isOutdated;
	}

	/**
	 * Get the ID of this stream.
	 * @return string The ID of this download
	 */
	public function getId()
    {
		return $this->id;
	}

	/**
	 * Get the parsed children / objects / entities.
	 * @return Object[]
	 */
	public function &getChildren()
    {
		return $this->children;
	}

	/**
	 * Defines if the stream is late (relating to the update option and maximum delay)
	 * @return boolean - True if the stream is late, or False.
	 */
	public function getIsLate()
    {
		return $this->isLate;
	}

	/**
	 * Defines if the stream is up to date (relating to the update option)
	 * @return boolean - True if the stream is up to date, or False.
	 */
	public function getIsUptodate()
    {
		return $this->isUpToDate;
	}

	/**
	 * Defines if the stream is outdated (relating to the update option and maximum delay)
	 * @return boolean - True if the stream is outdated, or False.
	 */
	public function getIsOutdated()
    {
		return $this->isOutdated;
	}

	/**
	 * Get the instance of Stream of this node.
	 * @return &Stream
	 */
	public function &getStreamsListNode()
    {
		return $this->stream;
	}

	/**
	 * Get the instance of StreamState of this node.
	 * @return &StreamState
	 */
	public function &getStateNode()
    {
		return $this->state;
	}

}