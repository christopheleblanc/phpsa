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
 * StreamPath
 * Class representing a stream loadable from local path in a StreamList.
 */

namespace PHPStreamsAggregator\Models;

use PHPStreamsAggregator\Constants as Con;
use PHPStreamsAggregator\Models\StreamTypes;
use PHPStreamsAggregator\Models\Stream;

/**
 * StreamPath
 */
class StreamPath extends Stream{

	/** @var string The absolute path of the local file. */
	private $path;

	/**
	 * Constructor
	 * @param string The full name of the download.
	 * @param string The ID/name of the download.
	 * @param string The absolute path of the local file.
	 * @param boolean Whether the download is enabled or not.
	 * @param string The name of the parser for this download.
	 */
	public function __construct($name, $id, $path, $active, $parserName){

		parent::__construct(StreamTypes::PATH, $name, $id, $active, $parserName);
		$this->path = $path;

	}

	/**
	 * Get the URL of this download.
	 * @return string
	 */
	public function getPath()
    {
		return $this->path;
	}

    /**
	 * Get a representation of this object in form of a string
	 * @return sring
	 */
    public function toString($lvl = 0)
    {

        $lvl = $lvl + 1;
        $lvlstr = "";
        for($i = 0; $i < $lvl; $i++){
            $lvlstr .= Con::TOSTRING_LVL_STR;
        }

        return $lvlstr . get_class($this) . " [id=\"" . $this->getId() . "\"]";

    }

}
