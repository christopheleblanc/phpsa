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
 * Stream
 * Class representing a stream in a StreamList.
 */

namespace PHPStreamsAggregator\Models;

/**
 * Stream
 */
class Stream{

	/** @var boolean Defines whether the download is enabled or not. */
	private $active;

	/** @var string The full name of the download. */
	private $name;

	/** @var string The id of the download. */
	private $id;

	/** @var string The name of the parser for this download. */
	private $parserName;

	/** @var int The type of this node. */
	private $type;

	/**
	 * Constructor
	 * @param int The type of download (0 = URL, 1 = FTP)
	 * @param string The full name of the download.
	 * @param string The ID/name of the download.
	 * @param boolean Whether the download is enabled or not.
	 * @param string The name of the parser for this download. 
	 */
	public function __construct($type, $name, $id, $active, $parserName)
    {
		$this->type = $type;
		$this->name = $name;
		$this->id = $id;
		$this->active = $active;
		$this->parserName = $parserName;
	}

	/**
	 * Get the ID of this download.
	 * @return string The ID of this download
	 */
	public function &getId()
    {
		return $this->id;
	}

	/**
	 * Get the full name this download.
	 * @return string The full name of this download
	 */
	public function &getName()
    {
		return $this->name;
	}

	/**
	 * Get download status. Active or inactive.
	 * @return boolean The status of this download
	 */
	public function isActive()
    {
		return $this->active;
	}

	/**
	 * Get the name of the parser class used to parse the downloaded file.
	 * @return string The name of the class.
	 */
	public function &getParserName()
    {
		return $this->parserName;
	}

	/**
	 * Get the type of the node.
	 * @return int The type of the download (URL of FTP)
	 */
	public function getDownloadType()
    {
		return $this->type;
	}

}