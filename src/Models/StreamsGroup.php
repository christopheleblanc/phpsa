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
 * StreamsGroup
 * Class representing a group of streams in a StreamsList...
 */

namespace PHPStreamsAggregator\Models;

use PHPStreamsAggregator\Constants as Con;
use PHPStreamsAggregator\Models\StreamFTP;
use PHPStreamsAggregator\Models\StreamPath;
use PHPStreamsAggregator\Models\StreamURL;

/**
 * StreamsGroup
 */
class StreamsGroup{

	/** @var boolean Defines whether the download is enabled or not. */
	private $active;

    /** @var UpdateOption[] Array of nodes */
    private $updateOptions;

    /** @var int The maximum delay (in seconds). */
	private $maxDelay = 0;

	/** @var Stream[] Array of nodes */
	private $children;

	/** @var int The length of the list. */
	private $childrenLength = 0;

    /** @var string The id of the list. */
	private $listId;

	/**
	 * Constructor
     * @param boolean State "Active" of the group (True or false)
     * @param UpdateOption[] An array of instances of UpdateOption
     * @param int The maximum delay between updates (in seconds)
     * @param Stream[] An array of instances of files to download (instances of Stream)
     * @param int The size of the array of files to download
     * @param string|null The id of the group
     * @param string|null The output file name
     * @param string|null The name of the class used to create/export output file
	 */
	public function __construct($active, &$updateOptions, &$maxDelay, &$children, $childrenLength = null,
    $listId = null)
    {
        $this->active = $active;
        $this->updateOptions = $updateOptions;
        $this->maxDelay = $maxDelay;
		$this->children = $children;
		$this->childrenLength = ($childrenLength === null) ? count($childrenLength) : $childrenLength;
        $this->listId = $listId;
	}

    /**
	 * Get the array of nodes of this list
	 * @return string The id of the list
	 */
	public function isActive()
    {
		return $this->active;
	}

    /**
	 * Get the array of nodes of this list
	 * @return string The id of the list
	 */
	public function getId()
    {
		return $this->listId;
	}

    /**
	 * Get the array of UpdateOption
	 */
	public function &getUpdateOptions()
    {
		return $this->updateOptions;
	}

    /**
	 * Get the maximum delay between updates
	 */
	public function &getMaxDelay()
    {
		return $this->maxDelay;
	}

	/**
	 * Get the array of nodes of this list
	 * @return Stream[] The array of nodes
	 */
	public function &getChildren()
    {
		return $this->children;
	}

    /**
	 * Get a child by its key
	 * @return Stream[] The array of nodes
	 */
    public function &getChild($id)
    {
		if(array_key_exists($id, $this->children)){
            return $this->children[$id];
        }
        else{
            $out = null;
            return $out;
        }
	}

	/**
	 * Set/Replace the array of nodes of this list
	 * @param Stream[] The array of nodes
	 */
	public function setChildren(&$children)
    {
		$this->children = $children;
		$this->childrenLength = count($this->children);
	}

	/**
	 * Get the number of nodes contained in this list
	 * @return int The number of nodes of this list
	 */
	public function size()
    {
		return $this->childrenLength;
	}

    /**
	 * Get a representation of this object in form of a string
	 * @return sring The representation of this object in form of a string
	 */
    public function toString($lvl = 0)
    {
        
        $lvl = $lvl + 1;
        $lvlstr = "";
        for($i = 0; $i < $lvl; $i++){
            $lvlstr .= Con::TOSTRING_LVL_STR;
        }
        
        $str = get_class($this) . " (" . $this->childrenLength . ")" . PHP_EOL;
        $i = 0;
        foreach($this->children as $node){
            $str .= $lvlstr . $node->toString($lvl) . PHP_EOL;
            $i++;
        }
        return $str;
    }

}

